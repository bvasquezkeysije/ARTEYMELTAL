<x-app-layout>
    <x-slot name="header">
        <span>Notificaciones</span>
    </x-slot>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Historial de notificaciones</h2>
            <form action="{{ route('notificaciones.read_all') }}" method="POST">
                @csrf
                <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#262626]">Marcar todo leido</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[#e3d7bb] bg-white shadow-sm">
            @forelse($notifications as $notif)
                <div class="flex items-start gap-3 border-b border-[#f0ede3] px-5 py-4 {{ $notif->is_read ? '' : 'bg-[#fffbee]' }}">
                    <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $notif->is_read ? 'bg-gray-100 text-gray-400' : 'bg-[#b9943d]/20 text-[#7a5b25]' }}">
                        @if($notif->icon)
                            {!! $notif->icon !!}
                        @else
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                            </svg>
                        @endif
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-medium {{ $notif->is_read ? 'text-gray-600' : 'text-[#3b2e11]' }}">{{ $notif->title }}</p>
                            @if(!$notif->is_read)
                                <form action="{{ route('notificaciones.read', $notif) }}" method="POST" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="rounded-lg px-2 py-1 text-[11px] text-[#7a5b25] hover:bg-[#f5edd6] transition-colors" title="Marcar como leido">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                        @if($notif->body)
                            <p class="mt-0.5 text-xs text-gray-500">{{ $notif->body }}</p>
                        @endif
                        <p class="mt-1 text-[10px] text-gray-400">{{ $notif->created_at->locale('es')->diffForHumans() }}</p>
                    </div>

                    @if($notif->action_url)
                        <a href="{{ $notif->action_url }}" class="shrink-0 rounded-lg border border-[#e3d7bb] px-3 py-1.5 text-xs font-medium text-[#7a5b25] hover:bg-[#fff5dd] transition-colors">
                            Ver
                        </a>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
                    <svg class="mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-sm text-gray-500">No hay notificaciones</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
