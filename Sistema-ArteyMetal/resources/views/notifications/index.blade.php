<x-app-layout>
    <x-slot name="header">
        <span>Notificaciones</span>
    </x-slot>

    <div class="space-y-5">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
        @endif

        <div class="rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-[#e5dec8] px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-800">Historial de notificaciones</h3>
                @if ($notifications->isNotEmpty())
                    <form action="{{ route('notificaciones.read_all') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-lg border border-[#e5dec8] px-3 py-1.5 text-xs font-medium text-[#7a5b25] hover:bg-[#fff5dd]">Marcar todo leido</button>
                    </form>
                @endif
            </div>

            <div class="divide-y divide-[#e5dec8]">
                @forelse ($notifications as $notification)
                    <div class="flex items-start gap-4 px-5 py-3 @if(!$notification->is_read) bg-[#fffbf5] @endif">
                        <span class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#b9943d]/20 text-[#7a5b25]">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-900">{{ $notification->title }}</p>
                                <span class="shrink-0 text-[10px] text-gray-400">{{ $notification->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="mt-0.5 text-sm text-gray-500">{{ $notification->body }}</p>
                            <div class="mt-2 flex items-center gap-3">
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" class="rounded-lg border border-[#e5dec8] px-3 py-1.5 text-xs font-medium text-[#7a5b25] hover:bg-[#fff5dd]">Ver detalle</a>
                                @endif
                                @if(!$notification->is_read)
                                    <form action="{{ route('notificaciones.read', $notification) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-[#7a5b25] hover:underline">Marcar releido</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">No tienes notificaciones</div>
                @endforelse
            </div>
        </div>

        @if ($notifications->hasPages())
            <div class="border-t border-[#e5dec8] px-4 py-3">{{ $notifications->links() }}</div>
        @endif
    </div>
</x-app-layout>
