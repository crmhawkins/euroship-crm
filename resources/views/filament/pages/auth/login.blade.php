{{-- Overlay fijo: escapa completamente del contenedor Filament --}}
<div style="position:fixed;inset:0;display:flex;z-index:50;overflow:hidden;">

    {{-- ===== Panel izquierdo: branding ===== --}}
    <div class="hidden lg:flex flex-col items-center justify-center relative overflow-hidden"
         style="width:50%;flex-shrink:0;background:linear-gradient(145deg,#070f2b 0%,#0d1b4b 30%,#1a2f7a 60%,#1e5fa8 100%);">

        {{-- Patrón dots --}}
        <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(41,166,223,0.18) 1.5px,transparent 1.5px);background-size:36px 36px;pointer-events:none;"></div>

        {{-- Círculos glow --}}
        <div style="position:absolute;top:5rem;right:2.5rem;width:12rem;height:12rem;border-radius:50%;background:radial-gradient(circle,rgba(41,166,223,0.2),transparent);filter:blur(24px);pointer-events:none;"></div>
        <div style="position:absolute;bottom:8rem;left:2rem;width:16rem;height:16rem;border-radius:50%;background:radial-gradient(circle,rgba(41,60,142,0.35),transparent);filter:blur(32px);pointer-events:none;"></div>

        {{-- Olas bottom --}}
        <div style="position:absolute;bottom:0;left:0;right:0;pointer-events:none;">
            <svg viewBox="0 0 1440 180" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;display:block;">
                <path d="M0,90 C480,180 960,0 1440,90 L1440,180 L0,180 Z" fill="rgba(255,255,255,0.03)"/>
                <path d="M0,120 C360,50 1080,170 1440,110 L1440,180 L0,180 Z" fill="rgba(255,255,255,0.04)"/>
                <path d="M0,150 C600,90 900,180 1440,140 L1440,180 L0,180 Z" fill="rgba(41,166,223,0.07)"/>
            </svg>
        </div>

        {{-- Contenido --}}
        <div style="position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;text-align:center;padding:0 4rem;">

            {{-- Logo --}}
            <div style="margin-bottom:2rem;padding:1.5rem;border-radius:1.5rem;background:rgba(255,255,255,0.08);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.12);box-shadow:0 8px 32px rgba(0,0,0,0.3);">
                <img src="https://euroshipspain.com/wp-content/uploads/2025/08/cropped-logo_euroship-1-270x270.png"
                     alt="Euroship"
                     style="width:6.5rem;height:6.5rem;object-fit:contain;filter:drop-shadow(0 4px 16px rgba(41,166,223,0.45));"
                     onerror="this.style.display='none'" />
            </div>

            {{-- Título --}}
            <h1 style="font-size:2.75rem;font-weight:900;color:white;margin-bottom:0.5rem;letter-spacing:-0.03em;line-height:1.1;">
                Euroship CRM
            </h1>

            {{-- Separador --}}
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
                <div style="height:1px;width:3rem;background:rgba(41,166,223,0.5);border-radius:999px;"></div>
                <div style="width:0.4rem;height:0.4rem;border-radius:50%;background:#29A6DF;"></div>
                <div style="height:1px;width:3rem;background:rgba(41,166,223,0.5);border-radius:999px;"></div>
            </div>

            {{-- Subtitle --}}
            <p style="font-size:0.75rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:#29A6DF;margin-bottom:0.75rem;">
                Suministros Marítimos
            </p>
            <p style="font-size:0.875rem;color:rgba(255,255,255,0.5);max-width:22rem;line-height:1.6;">
                Gestión integral de clientes, barcos, escalas y pedidos para su operación marítima
            </p>

            {{-- Feature cards --}}
            <div style="margin-top:3rem;display:grid;grid-template-columns:repeat(3,1fr);gap:0.875rem;">
                @foreach([
                    ['🚢','Barcos','Gestión de flota'],
                    ['⚓','Escalas','Puertos y rutas'],
                    ['📦','Pedidos','Suministros'],
                ] as [$icon,$label,$sub])
                <div style="display:flex;flex-direction:column;align-items:center;gap:0.375rem;padding:1rem 0.875rem;border-radius:1rem;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <span style="font-size:1.5rem;">{{ $icon }}</span>
                    <span style="color:white;font-weight:600;font-size:0.8rem;">{{ $label }}</span>
                    <span style="color:rgba(255,255,255,0.38);font-size:0.7rem;">{{ $sub }}</span>
                </div>
                @endforeach
            </div>

            {{-- Badge --}}
            <div style="margin-top:2.5rem;padding:0.35rem 1rem;border-radius:999px;background:rgba(41,166,223,0.12);color:#29A6DF;font-size:0.7rem;font-weight:600;border:1px solid rgba(41,166,223,0.22);letter-spacing:0.05em;">
                Sistema de gestión v2.0
            </div>
        </div>
    </div>

    {{-- ===== Panel derecho: formulario ===== --}}
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem 1.5rem;background:#f1f5f9;overflow-y:auto;">

        {{-- Logo móvil (solo en pantallas < lg) --}}
        <div class="flex flex-col items-center lg:hidden" style="margin-bottom:2.5rem;">
            <div style="padding:1rem;border-radius:1.25rem;margin-bottom:1rem;background:linear-gradient(135deg,#1a2f7a,#293C8E);box-shadow:0 4px 20px rgba(41,60,142,0.35);">
                <img src="https://euroshipspain.com/wp-content/uploads/2025/08/cropped-logo_euroship-1-270x270.png"
                     alt="Euroship" style="width:4rem;height:4rem;object-fit:contain;"
                     onerror="this.style.display='none'" />
            </div>
            <h2 style="font-size:1.25rem;font-weight:700;color:#1e293b;">Euroship CRM</h2>
        </div>

        <div style="width:100%;max-width:22rem;">

            {{-- Header --}}
            <div style="margin-bottom:2rem;">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;">
                    <div style="width:3px;height:1.25rem;border-radius:999px;background:#293C8E;"></div>
                    <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#293C8E;">Portal de acceso</p>
                </div>
                <h2 style="font-size:2rem;font-weight:900;color:#0f172a;letter-spacing:-0.025em;margin-top:0.5rem;">Bienvenido</h2>
                <p style="color:#64748b;margin-top:0.25rem;font-size:0.875rem;">Introduce tus credenciales para continuar</p>
            </div>

            {{-- Card --}}
            <div style="background:white;border-radius:1.25rem;box-shadow:0 4px 6px -1px rgba(0,0,0,0.04),0 20px 60px -10px rgba(41,60,142,0.14);border:1px solid rgba(0,0,0,0.06);overflow:hidden;">
                <div style="padding:2rem;">
                    <x-filament-panels::form wire:submit="authenticate">
                        {{ $this->form }}
                        <div style="margin-top:1.5rem;">
                            <x-filament-panels::form.actions
                                :actions="$this->getCachedFormActions()"
                                :full-width="true"
                            />
                        </div>
                    </x-filament-panels::form>
                </div>
                <div style="padding:0.875rem 2rem;background:#f8fafc;border-top:1px solid rgba(0,0,0,0.05);">
                    <p style="text-align:center;font-size:0.75rem;color:#94a3b8;">
                        ¿Problemas para acceder?
                        <a href="mailto:soporte@hawkins.es" style="color:#293C8E;font-weight:600;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Contactar soporte</a>
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div style="margin-top:1.75rem;display:flex;align-items:center;gap:0.75rem;">
                <div style="flex:1;height:1px;background:#e2e8f0;border-radius:999px;"></div>
                <p style="font-size:0.7rem;color:#94a3b8;">© {{ date('Y') }} Euroship Supplies</p>
                <div style="flex:1;height:1px;background:#e2e8f0;border-radius:999px;"></div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Nuestro overlay fixed (z-50) cubre el shell de Filament — no ocultar main o el fixed div no se pinta */
    .fi-simple-layout { background: transparent !important; padding: 0 !important; }
    .fi-simple-main-ctn { background: transparent !important; }
    .fi-simple-main { background: transparent !important; box-shadow: none !important; border: none !important; padding: 0 !important; max-width: 100% !important; margin: 0 !important; border-radius: 0 !important; }
    body.fi-body { overflow: hidden; background: #070f2b !important; }

    /* Botón submit */
    .fi-ac-btn-action[type="submit"] {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
        border-radius: 0.75rem !important;
        font-size: 0.9rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.03em !important;
    }
    /* Inputs */
    .fi-input-wrp { border-radius: 0.625rem !important; }
</style>
