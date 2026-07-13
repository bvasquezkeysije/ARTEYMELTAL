<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ARTE Y METALES') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=4">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700|cinzel:600&display=swap" rel="stylesheet" />
        <style>
            html, body {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
            }
            [x-cloak] {
                display: none !important;
            }
            #app-shell {
                height: 100vh;
                overflow: hidden;
            }
            @supports (height: 100dvh) {
                #app-shell {
                    height: 100dvh;
                }
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="m-0 min-h-dvh overflow-hidden bg-[#f4f4f3] antialiased" style="font-family:'Manrope',sans-serif;">
        <div id="app-shell" x-data="{
                openSidebar: true,
                esDesktop: window.innerWidth >= 1024,
                initSidebar() {
                    const guardado = localStorage.getItem('sidebar_open');
                    if (this.esDesktop) {
                        this.openSidebar = guardado === null ? true : guardado === '1';
                    } else {
                        this.openSidebar = false;
                    }

                    window.addEventListener('resize', () => {
                        this.esDesktop = window.innerWidth >= 1024;
                        if (!this.esDesktop) {
                            this.openSidebar = false;
                            return;
                        }
                        const valor = localStorage.getItem('sidebar_open');
                        this.openSidebar = valor === null ? true : valor === '1';
                    });
                },
                toggleSidebar() {
                    if (this.esDesktop) {
                        this.openSidebar = !this.openSidebar;
                        localStorage.setItem('sidebar_open', this.openSidebar ? '1' : '0');
                        return;
                    }
                    this.openSidebar = !this.openSidebar;
                }
            }"
             x-init="initSidebar()"
             class="h-full bg-[#f4f4f3]">
            <div class="flex h-full overflow-hidden">
                <aside
                    :class="[
                        openSidebar ? 'translate-x-0' : (esDesktop ? 'translate-x-0' : '-translate-x-full'),
                        esDesktop ? (openSidebar ? 'w-72' : 'w-20') : 'w-72'
                    ]"
                    class="fixed inset-y-0 left-0 z-40 shrink-0 overflow-hidden border-r border-[#2d2d2d] bg-[#09090f] text-[#efe9d7] transition-all duration-200 lg:relative lg:h-full"
                >
                    <div class="flex h-full flex-col">
                        <div class="border-b border-[#1f1f28] px-2">
                            <div class="flex h-12 items-center" :class="(openSidebar || !esDesktop) ? 'justify-end pr-2' : 'justify-center'">
                                <button
                                    @click="toggleSidebar()"
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl text-white transition-colors hover:bg-[#1d1d27] hover:text-[#f2d791]"
                                >
                                    <svg x-show="openSidebar" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 6l12 12M18 6l-12 12" />
                                    </svg>
                                    <svg x-show="!openSidebar" style="display: none;" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                            </div>
                            <div class="border-y border-[#09090f] py-5">
                                <div class="mx-auto flex w-full items-center justify-center">
                                    <div class="grid w-max items-center justify-center" :class="esDesktop && !openSidebar ? 'grid-cols-1 gap-y-2' : 'grid-cols-[auto_auto] gap-x-3'">
                                        <img src="{{ asset('images/ARTE-Y-METALES.png') }}" alt="Logo Arte y Metales" class="h-12 w-12 rounded-lg border border-[#b9943d] object-cover p-1" />
                                        <div class="text-left" x-show="openSidebar || !esDesktop" style="display: none;">
                                            <p class="text-xs uppercase tracking-[0.25em] text-[#b9943d]">Sistema</p>
                                            <p class="text-sm font-semibold text-white" style="font-family:'Cinzel',serif;">Arte y Metales</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="h-[25px]"></div>
                        </div>

                        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5 text-sm">
                            @if(auth()->user()->tienePermiso('dashboard.ver'))
                                <a href="{{ route('dashboard') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Inicio" class="{{ request()->routeIs('dashboard') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M3 11.5L12 4l9 7.5M5 10.5V20h14v-9.5"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Inicio</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('caja.ver'))
                                <a href="{{ route('cajas.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Caja" class="{{ request()->routeIs('cajas.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <img src="{{ asset('icons/Caja-Blanco.png') }}" alt="" class="h-4 w-4 object-contain pointer-events-none" />
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Caja</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('diseno.ver'))
                                <a href="{{ route('diseno.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Diseños" class="{{ request()->routeIs('diseno.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <img src="{{ asset('icons/Disenos-Blanco.png') }}" alt="" class="h-4 w-4 object-contain pointer-events-none" />
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Diseños</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('produccion.ver'))
                                <a href="{{ route('produccion.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Produccion" class="{{ request()->routeIs('produccion.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <img src="{{ asset('icons/Produccion-Blanco.png') }}" alt="" class="h-4 w-4 object-contain pointer-events-none" />
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Produccion</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('repartidor.ver'))
                                <a href="{{ route('repartidor.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Repartidor" class="{{ request()->routeIs('repartidor.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Repartidor</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('ventas.ver'))
                                <a href="{{ route('ventas.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Ventas" class="{{ request()->routeIs('ventas.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <img src="{{ asset('icons/Ventas-Blanco.png') }}" alt="" class="h-4 w-4 object-contain pointer-events-none" />
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Ventas</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('pedidos.ver') && !in_array(auth()->user()->rol?->nombre, ['almacenero', 'disenador', 'orfebre', 'repartidor'], true))
                                <a href="{{ route('pedidos.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Pedidos" class="{{ request()->routeIs('pedidos.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m-8-4v10l8 4m0-10v10"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Pedidos</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('productos.ver') && !in_array(auth()->user()->rol?->nombre, ['disenador', 'orfebre', 'repartidor'], true))
                                <a href="{{ route('productos.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Productos" class="{{ request()->routeIs('productos.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 7h16M4 12h16M4 17h16"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Productos</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('clientes.ver') && !in_array(auth()->user()->rol?->nombre, ['disenador', 'orfebre', 'repartidor'], true))
                                <a href="{{ route('clientes.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Clientes" class="{{ request()->routeIs('clientes.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 11a3 3 0 100-6 3 3 0 000 6zM5.5 13.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM18.5 13.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM3 20a5 5 0 019.5-2M21 20a5 5 0 00-9.5-2"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Clientes</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('almacen.ver'))
                                <a href="{{ route('almacen.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Almacen" class="{{ request()->routeIs('almacen.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m-8-4v10l8 4m0-10v10"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Almacen</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('usuarios.ver'))
                                <a href="{{ route('usuarios.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Usuarios" class="{{ request()->routeIs('usuarios.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M16 11a4 4 0 10-8 0 4 4 0 008 0zM4 20a8 8 0 0116 0"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Usuarios</span>
                                </a>
                            @endif
                            @if(auth()->user()->tienePermiso('reportes.ver') && auth()->user()->rol?->nombre !== 'disenador')
                                <a href="{{ route('reportes.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Reportes" class="{{ request()->routeIs('reportes.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 19h16M7 15V9m5 6V6m5 9v-4"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Reportes</span>
                                </a>
                            @endif
                            <a href="{{ route('notificaciones.index') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Notificaciones" class="{{ request()->routeIs('notificaciones.*') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span x-show="openSidebar || !esDesktop" style="display: none;">Notificaciones</span>
                            </a>
                        </nav>

                        @if(auth()->user()->tienePermiso('configuracion.ver'))
                            <div class="border-t border-[#1f1f28] p-4">
                                <a href="{{ route('profile.edit') }}" :class="{ 'justify-center px-0': esDesktop && !openSidebar }" title="Configuracion" class="{{ request()->routeIs('profile.edit') ? 'bg-[#b9943d]/25 text-[#f2d791]' : 'text-[#d0d0d6] hover:bg-[#1d1d27]' }} flex w-full items-center gap-2 rounded-xl px-3 py-2.5 font-medium cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10.3 3.2h3.4l.8 2.2 2.3.9 2.1-1.1 1.7 2.9-1.8 1.5.2 2.4 2 1.3-1 3.2-2.4.1-1.9 1.6.2 2.4-3.2 1-1.3-2-2.4-.2-1.5 1.8-2.9-1.7 1.1-2.1-.9-2.3-2.2-.8v-3.4l2.2-.8.9-2.3-1.1-2.1 2.9-1.7 1.5 1.8 2.4-.2zM12 15.5A3.5 3.5 0 1012 8.5a3.5 3.5 0 000 7z"/></svg>
                                    <span x-show="openSidebar || !esDesktop" style="display: none;">Configuracion</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </aside>

                <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden lg:pl-0">
                    <header class="shrink-0 border-b border-[#e0e0de] bg-white">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-6">
                            <div class="flex min-w-0 items-center gap-3">
                                <button
                                    x-cloak
                                    x-show="!openSidebar"
                                    x-transition.opacity
                                    @click="openSidebar = true"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[#d3d3ce] text-[#2a2a2a] lg:hidden"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <div class="min-w-0">
                                    @isset($header)
                                        <div class="truncate text-lg font-semibold text-[#202020]">
                                            {{ $header }}
                                        </div>
                                    @else
                                        <div class="truncate text-lg font-semibold text-[#202020]">Inicio</div>
                                    @endisset
                                    <p class="truncate text-sm text-[#666]">Panel de gestion operativa</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div x-data="{ notifOpen: false, count: 0, items: [], polling: null }" x-init="
                                    fetch('{{ route('notificaciones.unread') }}')
                                        .then(r => r.json())
                                        .then(d => { count = d.count; items = d.notifications; });
                                    polling = setInterval(() => {
                                        fetch('{{ route('notificaciones.unread') }}')
                                            .then(r => r.json())
                                            .then(d => { count = d.count; items = d.notifications; });
                                    }, 30000);
                                    window.addEventListener('beforeunload', () => { if(polling) clearInterval(polling); });
                                " class="relative">
                                    <button type="button" @click="notifOpen = !notifOpen" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#d8cfb8] bg-[#fffdf7] text-[#7a5b25] hover:bg-[#fff7e7]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <template x-if="count > 0">
                                            <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white" x-text="count > 99 ? '99+' : count"></span>
                                        </template>
                                    </button>
                                    <div x-show="notifOpen" x-transition @click.outside="notifOpen = false" class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-2xl border border-[#e3d7bb] bg-white shadow-xl" style="display: none;">
                                        <div class="border-b border-[#efe7d1] bg-[#fff9ec] px-4 py-3">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-semibold text-[#3b2e11]">Notificaciones</p>
                                                <template x-if="count > 0">
                                                    <form action="{{ route('notificaciones.read_all') }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-[#7a5b25] hover:underline">Marcar todo leido</button>
                                                    </form>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="max-h-72 overflow-y-auto">
                                            <template x-for="item in items" :key="item.id">
                                                <div class="flex items-start gap-3 border-b border-[#f0ede3] px-4 py-3">
                                                    <span class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#b9943d]/20 text-[#7a5b25]">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                                                        </svg>
                                                    </span>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs font-medium text-[#3b2e11]" x-text="item.title"></p>
                                                        <p class="text-[10px] text-gray-400" x-text="item.created_at ? new Date(item.created_at).toLocaleDateString() : ''"></p>
                                                    </div>
                                                    <template x-if="item.action_url">
                                                        <a :href="item.action_url" class="shrink-0 rounded-lg border border-[#e3d7bb] px-2.5 py-1 text-[10px] font-medium text-[#7a5b25] hover:bg-[#fff5dd]">Ver</a>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                        <a href="{{ route('notificaciones.index') }}" class="block border-t border-[#f0ede3] px-4 py-2.5 text-center text-xs font-medium text-[#7a5b25] hover:bg-[#fffbee]">Ver todas las notificaciones</a>
                                    </div>
                                </div>

                            <div class="relative" x-data="{ openPerfil: false, openAyuda: false }" @keydown.escape.window="openPerfil = false; openAyuda = false">
                                <button
                                    type="button"
                                    @click="openPerfil = !openPerfil"
                                    class="inline-flex h-10 items-center gap-2 rounded-xl border border-[#d8cfb8] bg-[#fffdf7] px-3 text-sm font-medium text-[#3b2e11] hover:bg-[#fff7e7]"
                                >
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#111] text-xs font-semibold text-white">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                    <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4 text-[#7a5b25]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="openPerfil"
                                    x-transition
                                    @click.outside="openPerfil = false"
                                    class="absolute right-0 z-50 mt-2 w-64 overflow-hidden rounded-2xl border border-[#e3d7bb] bg-white shadow-xl"
                                    style="display: none;"
                                >
                                    <div class="border-b border-[#efe7d1] bg-[#fff9ec] px-4 py-3">
                                        <p class="text-sm font-semibold text-[#3b2e11]">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-[#6c603f]">{{ Auth::user()->email }}</p>
                                    </div>

                                    <div class="p-2 text-sm">
                                        @if(auth()->user()->tienePermiso('configuracion.ver'))
                                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-[#44351a] hover:bg-[#fff5dd]">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 4a4 4 0 100 8 4 4 0 000-8zm-7 15a7 7 0 0114 0"/></svg>
                                                <span>Configuracion</span>
                                            </a>
                                        @endif
                                        <button
                                            type="button"
                                            @click="openAyuda = true; openPerfil = false"
                                            class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-[#44351a] hover:bg-[#fff5dd]"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8.2 8.2a4 4 0 117.6 2.2c-.6 1.1-1.8 1.8-2.8 2.5-.8.5-1 1-1 1.6m0 2.5h.01"/></svg>
                                            <span>Ayuda</span>
                                        </button>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="mt-1 flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-[#7a1f1f] hover:bg-[#fff1f1]">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M17 16l4-4m0 0l-4-4m4 4H9m4 8H5a2 2 0 01-2-2V6a2 2 0 012-2h8"/></svg>
                                                <span>Cerrar sesion</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div
                                    x-show="openAyuda"
                                    x-transition.opacity
                                    x-cloak
                                    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/45 p-4"
                                    @click.self="openAyuda = false"
                                >
                                    <div class="w-full max-w-md overflow-hidden rounded-2xl border border-[#e3d7bb] bg-white shadow-2xl">
                                        <div class="flex items-center justify-between border-b border-[#efe7d1] bg-[#fff9ec] px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#b9943d]/20 text-[#7a5b25]">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8.2 8.2a4 4 0 117.6 2.2c-.6 1.1-1.8 1.8-2.8 2.5-.8.5-1 1-1 1.6m0 2.5h.01"/>
                                                    </svg>
                                                </span>
                                                <h3 class="text-base font-semibold text-[#3b2e11]">Centro de ayuda</h3>
                                            </div>
                                            <button
                                                type="button"
                                                @click="openAyuda = false"
                                                class="rounded-lg px-2 py-1 text-sm text-[#7a5b25] transition-colors hover:bg-[#fff1d6]"
                                            >
                                                Cerrar
                                            </button>
                                        </div>

                                        <div class="space-y-4 px-5 py-5">
                                            <p class="text-center text-sm leading-6 text-[#4e4127]">
                                                Si tuviste un problema, contactate al <span class="font-semibold text-[#7a5b25]">900889663</span>.
                                            </p>

                                            <a
                                                href="https://wa.me/51900889663"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#25D366] px-4 py-3 text-sm font-semibold text-white transition hover:brightness-95"
                                            >
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true">
                                                    <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122 0-221.5 99.5-221.5 221.5 0 39 10.2 77 29.6 111L0 480l117.7-30.9c32.4 17.7 68.6 27 106.1 27h.1c122 0 223.2-99.5 223.2-221.5 0-59.3-25.2-115-67.1-157zM223.9 438.7h-.1c-33.2 0-65.7-8.9-94-25.6l-6.7-4-69.8 18.3 18.6-68-4.4-7c-18.5-29.4-28.2-63.4-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.7 34.9 53.8 81.2 53.8 130.4-.1 101.8-84.9 184.5-184.6 184.5zm101.3-138.4c-5.5-2.8-32.8-16.1-37.9-17.9-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 17.9-17.6 21.5-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.3-4.5-10.8-9.1-9.4-12.5-9.6-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 18.9-19.4 46.1s19.9 53.5 22.6 57.2c2.8 3.7 39.1 59.7 94.8 83.7 35.1 15.1 48.8 16.4 66.3 13.9 10.7-1.6 32.8-13.4 37.4-26.3 4.6-12.9 4.6-23.9 3.2-26.3-1.3-2.4-5-3.8-10.5-6.6z"/>
                                                </svg>
                                                <span>Contactar por WhatsApp</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="min-h-0 flex-1 overflow-y-auto p-3 sm:p-4 lg:p-6" style="scrollbar-gutter: stable;">
                        {{ $slot }}
                    </main>
                </div>
            </div>

            <div
                x-cloak
                x-show="openSidebar"
                x-transition.opacity
                @click="openSidebar = false"
                class="fixed inset-0 z-30 bg-black/50 lg:hidden"
            ></div>
        </div>
    </body>
</html>

