<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No encontramos una cuenta activa con ese correo.',
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (! $user || ! $user->activo) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'No encontramos una cuenta activa con ese correo.']);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetCode::where('email', $email)->delete();
        PasswordResetCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($email)->send(new PasswordResetCodeMail($code));

        return redirect()->route('password.code.form', ['email' => $email])
            ->with('success', 'Te hemos enviado un codigo de 6 digitos a tu correo.');
    }
}
