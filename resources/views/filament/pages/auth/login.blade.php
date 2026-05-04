<style>
    /* Escape Filament's simple layout container */
    .fi-simple-layout { padding: 0 !important; align-items: stretch !important; }
    .fi-simple-main-ctn { display: block !important; padding: 0 !important; }
    .fi-simple-main {
        max-width: 100% !important;
        margin: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        ring: none !important;
        padding: 0 !important;
        background: transparent !important;
        min-height: 100vh;
    }
</style>

<div class="min-h-screen flex flex-col lg:flex-row">

    {{-- Panel izquierdo: branding --}}
    <div class="relative hidden lg:flex lg:w-1/2 flex-col items-center justify-center overflow-hidden"
         style="background: linear-gradient(150deg, #0b1538 0%, #1a2d6e 45%, #293C8E 75%, #1e6fa3 100%);">

        <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
            <svg viewBox="0 0 1440 160" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0,80 C360,160 1080,0 1440,80 L1440,160 L0,160 Z" fill="rgba(255,255,255,0.04)"/>
                <path d="M0,110 C360,40 1080,170 1440,100 L1440,160 L0,160 Z" fill="rgba(255,255,255,0.06)"/>
            </svg>
        </div>

        <div class="absolute inset-0 pointer-events-none"
             style="background-image: radial-gradient(circle, rgba(41,166,223,0.15) 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative z-10 flex flex-col items-center text-center px-14">
            <div class="mb-8 p-5 rounded-2xl" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                <img src="https://euroshipspain.com/wp-content/uploads/2025/08/cropped-logo_euroship-1-270x270.png"
                     alt="Euroship"
                     class="w-32 h-32 object-contain drop-shadow-xl"
                     onerror="this.style.display='none'" />
            </div>

            <h1 class="text-4xl font-extrabold text-white mb-3 tracking-tight">Euroship CRM</h1>

            <div class="w-16 h-1 rounded-full mb-5" style="background: #29A6DF;"></div>

            <p class="text-lg font-semibold mb-3" style="color: #29A6DF;">Suministros marítimos</p>
            <p class="text-white/65 text-sm max-w-xs leading-relaxed">
                Gestión integral de clientes, barcos, escalas y pedidos para su operación marítima
            </p>

            <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                @foreach([['🚢','Barcos'],['⚓','Escalas'],['📦','Pedidos']] as [$icon, $label])
                <div class="flex flex-col items-center gap-2">
                    <span class="text-2xl">{{ $icon }}</span>
                    <span class="text-white/50 text-xs font-medium">{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="w-full lg:w-1/2 flex flex-col items-center justify-center min-h-screen bg-gray-50 px-6 py-12">

        {{-- Logo móvil --}}
        <div class="lg:hidden flex flex-col items-center mb-10">
            <div class="p-4 rounded-xl mb-4" style="background: #293C8E;">
                <img src="https://euroshipspain.com/wp-content/uploads/2025/08/cropped-logo_euroship-1-270x270.png"
                     alt="Euroship" class="w-16 h-16 object-contain"
                     onerror="this.style.display='none'" />
            </div>
            <h2 class="text-xl font-bold" style="color: #293C8E;">Euroship CRM</h2>
        </div>

        <div class="w-full max-w-md">

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Bienvenido</h2>
                <p class="text-gray-500 mt-1">Accede a tu cuenta para continuar</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
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

            <p class="text-center text-xs text-gray-400 mt-8">
                © {{ date('Y') }} Euroship Supplies · Todos los derechos reservados
            </p>
        </div>
    </div>

</div>
