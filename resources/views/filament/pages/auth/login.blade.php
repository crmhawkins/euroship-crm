<style>
    /* Strip Filament's simple layout so our custom layout takes over fully */
    body.fi-body { background: #f8fafc !important; }
    .fi-simple-layout { padding: 0 !important; min-height: 100vh !important; align-items: stretch !important; }
    .fi-simple-main-ctn { display: block !important; width: 100% !important; flex: 1 !important; }
    .fi-simple-main {
        all: unset !important;
        display: block !important;
        width: 100% !important;
        min-height: 100vh !important;
    }
</style>

<div class="min-h-screen flex flex-col lg:flex-row">

    {{-- ===== Panel izquierdo: branding azul ===== --}}
    <div class="relative hidden lg:flex lg:w-1/2 flex-col items-center justify-center overflow-hidden"
         style="background: linear-gradient(145deg, #070f2b 0%, #0d1b4b 30%, #1a2f7a 60%, #1e5fa8 100%); min-height: 100vh;">

        {{-- Fondo: patrón de puntos --}}
        <div class="absolute inset-0 pointer-events-none"
             style="background-image: radial-gradient(circle, rgba(41,166,223,0.18) 1.5px, transparent 1.5px); background-size: 36px 36px;"></div>

        {{-- Fondo: olas bottom --}}
        <div class="absolute bottom-0 left-0 right-0 pointer-events-none" style="z-index:1;">
            <svg viewBox="0 0 1440 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;display:block;">
                <path d="M0,100 C480,200 960,0 1440,100 L1440,200 L0,200 Z" fill="rgba(255,255,255,0.03)"/>
                <path d="M0,130 C360,60 1080,190 1440,120 L1440,200 L0,200 Z" fill="rgba(255,255,255,0.05)"/>
                <path d="M0,160 C600,100 900,200 1440,150 L1440,200 L0,200 Z" fill="rgba(41,166,223,0.08)"/>
            </svg>
        </div>

        {{-- Círculos decorativos --}}
        <div class="absolute top-20 right-10 w-32 h-32 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(41,166,223,0.15), transparent); filter: blur(20px);"></div>
        <div class="absolute bottom-40 left-10 w-48 h-48 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(41,60,142,0.3), transparent); filter: blur(30px);"></div>

        {{-- Contenido --}}
        <div class="relative flex flex-col items-center text-center px-16" style="z-index:2;">

            {{-- Logo --}}
            <div class="mb-8 p-6 rounded-3xl relative" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                <img src="https://euroshipspain.com/wp-content/uploads/2025/08/cropped-logo_euroship-1-270x270.png"
                     alt="Euroship"
                     class="w-28 h-28 object-contain"
                     style="filter: drop-shadow(0 4px 16px rgba(41,166,223,0.4));"
                     onerror="this.style.display='none'" />
            </div>

            {{-- Título --}}
            <h1 class="text-5xl font-black text-white mb-3 tracking-tight" style="letter-spacing: -0.02em;">
                Euroship CRM
            </h1>

            {{-- Separador --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="h-px w-12 rounded-full" style="background: rgba(41,166,223,0.5);"></div>
                <div class="w-2 h-2 rounded-full" style="background: #29A6DF;"></div>
                <div class="h-px w-12 rounded-full" style="background: rgba(41,166,223,0.5);"></div>
            </div>

            {{-- Subtítulo --}}
            <p class="text-xl font-semibold mb-3" style="color: #29A6DF; letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.85rem;">
                Suministros Marítimos
            </p>
            <p class="text-sm max-w-xs leading-relaxed" style="color: rgba(255,255,255,0.55);">
                Gestión integral de clientes, barcos, escalas y pedidos para su operación marítima
            </p>

            {{-- Feature cards --}}
            <div class="mt-14 grid grid-cols-3 gap-4">
                @foreach([
                    ['🚢', 'Barcos', 'Gestión de flota'],
                    ['⚓', 'Escalas', 'Puertos y rutas'],
                    ['📦', 'Pedidos', 'Suministros'],
                ] as [$icon, $label, $sub])
                <div class="flex flex-col items-center gap-2 p-4 rounded-2xl transition-transform hover:scale-105"
                     style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
                    <span class="text-2xl">{{ $icon }}</span>
                    <span class="text-white font-semibold text-sm">{{ $label }}</span>
                    <span class="text-xs" style="color: rgba(255,255,255,0.4);">{{ $sub }}</span>
                </div>
                @endforeach
            </div>

            {{-- Badge versión --}}
            <div class="mt-12 px-4 py-1.5 rounded-full text-xs font-medium"
                 style="background: rgba(41,166,223,0.15); color: #29A6DF; border: 1px solid rgba(41,166,223,0.25);">
                Sistema de gestión v2.0
            </div>
        </div>
    </div>

    {{-- ===== Panel derecho: formulario ===== --}}
    <div class="w-full lg:w-1/2 flex flex-col items-center justify-center min-h-screen px-6 py-12"
         style="background: #f8fafc;">

        {{-- Logo móvil --}}
        <div class="lg:hidden flex flex-col items-center mb-10">
            <div class="p-4 rounded-2xl mb-4 shadow-lg" style="background: linear-gradient(135deg, #1a2f7a, #293C8E);">
                <img src="https://euroshipspain.com/wp-content/uploads/2025/08/cropped-logo_euroship-1-270x270.png"
                     alt="Euroship" class="w-16 h-16 object-contain"
                     onerror="this.style.display='none'" />
            </div>
            <h2 class="text-xl font-bold text-gray-800">Euroship CRM</h2>
            <p class="text-sm text-gray-500 mt-1">Suministros marítimos</p>
        </div>

        <div class="w-full max-w-sm">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-1 h-6 rounded-full" style="background: #293C8E;"></div>
                    <p class="text-sm font-semibold uppercase tracking-widest" style="color: #293C8E;">Portal de acceso</p>
                </div>
                <h2 class="text-3xl font-black text-gray-900 mt-2" style="letter-spacing: -0.02em;">Bienvenido</h2>
                <p class="text-gray-500 mt-1 text-sm">Introduce tus credenciales para continuar</p>
            </div>

            {{-- Card formulario --}}
            <div class="rounded-2xl overflow-hidden" style="background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 20px 60px -10px rgba(41,60,142,0.12); border: 1px solid rgba(0,0,0,0.06);">
                <div class="px-8 py-8">
                    <x-filament-panels::form wire:submit="authenticate">
                        {{ $this->form }}

                        <div class="mt-6">
                            <x-filament-panels::form.actions
                                :actions="$this->getCachedFormActions()"
                                :full-width="true"
                            />
                        </div>
                    </x-filament-panels::form>
                </div>

                {{-- Footer card --}}
                <div class="px-8 py-4 border-t" style="background: #f8fafc; border-color: rgba(0,0,0,0.05);">
                    <p class="text-center text-xs text-gray-400">
                        ¿Problemas para acceder?
                        <a href="mailto:soporte@hawkins.es" class="font-medium hover:underline" style="color: #293C8E;">Contactar soporte</a>
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-8 flex items-center justify-center gap-2">
                <div class="h-px flex-1 rounded-full bg-gray-200"></div>
                <p class="text-xs text-gray-400 px-3">© {{ date('Y') }} Euroship Supplies</p>
                <div class="h-px flex-1 rounded-full bg-gray-200"></div>
            </div>

        </div>
    </div>

</div>

<style>
    /* Override Filament button color to match brand */
    .fi-btn-color-primary {
        --c-400: 105, 119, 176 !important;
        --c-500: 41, 60, 142 !important;
        --c-600: 37, 54, 128 !important;
    }
    /* Form inputs styling */
    .fi-input {
        border-radius: 0.625rem !important;
    }
    .fi-input-wrp {
        border-radius: 0.625rem !important;
    }
    /* Enlarge submit button */
    .fi-ac-btn-action[type="submit"] {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
        border-radius: 0.75rem !important;
        font-size: 0.95rem !important;
        letter-spacing: 0.02em !important;
    }
</style>
