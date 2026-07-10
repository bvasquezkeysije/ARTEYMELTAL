<x-app-layout>
    <x-slot name="header">Notificaciones</x-slot>

    <div class="mx-auto max-w-3xl space-y-4">
        @forelse ($notifications as $notification)
            <div class="flex items-start gap-4 rounded-2xl border border-[#e3d7bb] bg-white p-4 shadow-sm @if(!$notification->is_read) border-l-4 border-l-[#b9943d] bg-[#fffdf7] @endif">
                <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#b9943d]/20 text-[#7a5b25]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-[#3b2e11]">{{ $notification->title }}</p>
                        <span class="shrink-0 text-[10px] text-gray-400">{{ $notification->created_at?->diffForHumans() }}</span>
                    </div>
                    <p class="mt-0.5 text-sm text-gray-500">{{ $notification->body }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" class="rounded-lg border border-[#e3d7bb] px-3 py-1 text-xs font-medium text-[#7a5b25] hover:bg-[#fff5dd]">Ver detalle</a>
                        @endif
                        @if(!$notification->is_read)
                            <form action="{{ route('notificaciones.read', $notification) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-[#7a5b25] hover:underline">Marcar leido</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-[#e3d7bb] bg-white p-8 text-center">
                <p class="text-sm text-gray-400">No tienes notificaciones</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
