<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordResetCodeController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.reset-password-code', [
            'email' => $request->input('email', old('email')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = $request->input('email');
        $code = $request->input('code');

        $resetCode = PasswordResetCode::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if (! $resetCode) {
            return back()->withInput($request->only('email'))
                ->withErrors(['code' => 'El codigo es invalido o ya expiro.']);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! $user->activo) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'No encontramos una cuenta activa con ese correo.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        PasswordResetCode::where('email', $email)->delete();

        return redirect()->route('login')->with('status', 'Tu contrasena ha sido restablecida. Inicia sesion.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        return redirect()->route('password.request', ['email' => $request->input('email')])
            ->with('status', 'Solicita un nuevo codigo desde el formulario anterior.');
    }
}
