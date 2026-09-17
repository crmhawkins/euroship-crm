<?php

namespace App\Filament\Support;

use App\Mail\ServicioNotificacion;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envío manual de un aviso por email al cliente de un servicio (llegada de pertrecho,
 * cambio de ubicación...). Nunca se dispara solo: siempre lo lanza un usuario.
 * Configura tanto la acción de tabla como la de cabecera de página, que comparten API.
 */
class NotificarCliente
{
    public static function tipos(): array
    {
        return [
            'llegada'   => __('Llegada de pertrecho'),
            'ubicacion' => __('Cambio de ubicación'),
            'otro'      => __('Otro (mensaje libre)'),
        ];
    }

    /**
     * @template T of \Filament\Actions\Action|\Filament\Tables\Actions\Action
     * @param  T  $action
     * @return T
     */
    public static function configurar($action)
    {
        return $action
            ->label(__('Notificar al cliente'))
            ->tooltip(__('Enviar email al cliente'))
            ->icon('heroicon-o-envelope')
            ->color('success')
            ->modalHeading(__('Notificar al cliente por email'))
            ->modalDescription(__('Revisa el texto antes de enviar. El email sale desde la cuenta not-reply de Euroship.'))
            ->modalSubmitActionLabel(__('Enviar email'))
            ->fillForm(fn (Servicio $record) => [
                'destinatarios' => array_filter([$record->escala?->barco?->cliente?->email]),
                'tipo'          => 'llegada',
                'asunto'        => static::asunto($record, 'llegada'),
                'mensaje'       => static::mensaje($record, 'llegada'),
            ])
            ->form([
                Forms\Components\TagsInput::make('destinatarios')
                    ->label(__('Destinatarios'))
                    ->placeholder('email@cliente.com')
                    ->helperText(__('Por defecto el email de la ficha del cliente. Puedes añadir más: escribe y pulsa Enter.'))
                    ->nestedRecursiveRules(['email'])
                    ->required(),
                Forms\Components\Select::make('tipo')
                    ->label(__('Motivo'))
                    ->options(static::tipos())
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, Servicio $record) {
                        $set('asunto', static::asunto($record, $get('tipo')));
                        $set('mensaje', static::mensaje($record, $get('tipo')));
                    }),
                Forms\Components\TextInput::make('asunto')
                    ->label(__('Asunto'))
                    ->required()
                    ->maxLength(200),
                Forms\Components\Textarea::make('mensaje')
                    ->label(__('Mensaje'))
                    ->rows(12)
                    ->required(),
            ])
            ->action(function (Servicio $record, array $data) {
                try {
                    Mail::to($data['destinatarios'])->send(new ServicioNotificacion($data['asunto'], $data['mensaje']));
                } catch (Throwable $e) {
                    Log::error('Fallo al notificar al cliente', ['servicio_id' => $record->id, 'error' => $e->getMessage()]);

                    Notification::make()
                        ->title(__('No se pudo enviar el email'))
                        ->body($e->getMessage())
                        ->danger()
                        ->persistent()
                        ->send();

                    return;
                }

                Log::info('Cliente notificado', [
                    'servicio_id'   => $record->id,
                    'usuario_id'    => auth()->id(),
                    'destinatarios' => $data['destinatarios'],
                    'asunto'        => $data['asunto'],
                ]);

                Notification::make()
                    ->title(__('Email enviado'))
                    ->body(implode(', ', $data['destinatarios']))
                    ->success()
                    ->send();
            });
    }

    public static function asunto(Servicio $servicio, ?string $tipo): string
    {
        $barco = strtoupper($servicio->escala?->barco?->nombre ?? '');
        $ref = $servicio->number ? " - {$servicio->number}" : '';

        return match ($tipo) {
            'ubicacion' => "M/V {$barco} - Shipment location update{$ref}",
            'otro'      => "M/V {$barco} - Shipment update{$ref}",
            default     => "M/V {$barco} - Shipment arrival notice{$ref}",
        };
    }

    public static function mensaje(Servicio $servicio, ?string $tipo): string
    {
        $barco = strtoupper($servicio->escala?->barco?->nombre ?? '—');

        $intro = match ($tipo) {
            'ubicacion' => "Please be informed that the location of the following shipment for M/V {$barco} has been updated:",
            'otro'      => "Please find below an update regarding the following shipment for M/V {$barco}:",
            default     => "Please be informed that the following shipment for M/V {$barco} has arrived at our warehouse:",
        };

        $detalle = array_filter([
            'Courier'            => $servicio->courier?->nombre,
            'AWB / Tracking no.' => $servicio->number,
            'Packages (BX)'      => $servicio->bx,
            'Weight (KG)'        => $servicio->kg,
            'Arrival date'       => $servicio->llegada?->format('d/m/Y'),
            'Customs status'     => $servicio->estatusAduanero?->nombre,
            'Current location'   => $tipo === 'ubicacion' ? $servicio->ubicacion?->nombre : null,
            'Port of call'       => $servicio->escala?->puerto,
        ], fn ($valor) => filled($valor));

        $lineas = collect($detalle)->map(fn ($valor, $etiqueta) => "{$etiqueta}: {$valor}")->implode("\n");

        return "Dear Sirs,\n\n{$intro}\n\n{$lineas}\n\nBest regards,\n" . config('euroship.nombre');
    }
}
