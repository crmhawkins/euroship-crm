<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Models\Escala;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 40;

    public static function getNavigationLabel(): string
    {
        return __('Pedidos');
    }

    public static function getModelLabel(): string
    {
        return __('Pedido');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Pedidos');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Asignación'))->schema([
                // Campo auxiliar (no persistido) para filtrar escalas por barco.
                Forms\Components\Select::make('barco_id')
                    ->label(__('Barco'))
                    ->options(function () {
                        return \App\Models\Barco::with('cliente')->get()
                            ->mapWithKeys(fn ($b) => [
                                $b->id => "{$b->nombre} — {$b->cliente?->nombre}",
                            ])->toArray();
                    })
                    ->searchable()
                    ->dehydrated(false)
                    ->live()
                    ->afterStateHydrated(function (Set $set, ?Pedido $record) {
                        if ($record?->escala) {
                            $set('barco_id', $record->escala->barco_id);
                        }
                    })
                    ->afterStateUpdated(fn (Set $set) => $set('escala_id', null))
                    ->required(),

                Forms\Components\Select::make('escala_id')
                    ->label(__('Escala'))
                    ->options(function (Get $get) {
                        $barcoId = $get('barco_id');
                        if (! $barcoId) {
                            return [];
                        }
                        return Escala::where('barco_id', $barcoId)
                            ->orderBy('fecha', 'desc')
                            ->get()
                            ->mapWithKeys(fn ($e) => [
                                $e->id => $e->puerto . ' (' . ($e->fecha?->format('Y-m-d') ?? '—') . ')',
                            ])->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->helperText(__('Solo se listan escalas del barco seleccionado. El pedido puede reasignarse a otra escala del mismo barco.'))
                    ->rule(function (Get $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $barcoId = $get('barco_id');
                            if (! $value || ! $barcoId) {
                                return;
                            }
                            $escala = Escala::find($value);
                            if (! $escala || (int) $escala->barco_id !== (int) $barcoId) {
                                $fail(__('La escala seleccionada no pertenece al barco elegido.'));
                            }
                        };
                    }),
            ])->columns(2),

            Forms\Components\Section::make(__('Datos del pedido'))->schema([
                Forms\Components\TextInput::make('numero_pedido')
                    ->label(__('Número de pedido'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha_pedido')
                    ->label(__('Fecha de pedido'))
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('puerto_entrega')
                    ->label(__('Puerto de entrega'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('estado_general')
                    ->label(__('Estado general'))
                    ->options([
                        'pendiente'  => __('Pendiente'),
                        'parcial'    => __('Parcial'),
                        'entregado'  => __('Entregado'),
                    ])
                    ->default('pendiente')
                    ->helperText(__('Se recalcula automáticamente según los pertrechos.')),
                Forms\Components\Textarea::make('notas')
                    ->label(__('Notas'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make(__('Pertrechos'))->schema([
                Forms\Components\Repeater::make('pertrechos')
                    ->label(__('Pertrechos'))
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('descripcion')
                            ->label(__('Descripción'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('cantidad')
                            ->label(__('Cantidad'))
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('unidad')
                            ->label(__('Unidad'))
                            ->maxLength(30)
                            ->placeholder(__('ud, kg, L, caja...')),
                        Forms\Components\Select::make('estado')
                            ->label(__('Estado'))
                            ->options([
                                'pendiente' => __('Pendiente'),
                                'entregado' => __('Entregado'),
                            ])
                            ->default('pendiente')
                            ->required(),
                        Forms\Components\Textarea::make('notas')
                            ->label(__('Notas'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(5)
                    ->defaultItems(1)
                    ->reorderable(false)
                    ->cloneable()
                    ->itemLabel(fn (array $state): ?string => $state['descripcion'] ?? null)
                    ->collapsible(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_pedido')->label(__('Número'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('fecha_pedido')->label(__('Fecha'))->date('Y-m-d')->sortable(),
                Tables\Columns\TextColumn::make('escala.barco.cliente.nombre')->label(__('Cliente'))->toggleable(),
                Tables\Columns\TextColumn::make('escala.barco.nombre')->label(__('Barco'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('escala.puerto')->label(__('Escala'))->toggleable(),
                Tables\Columns\TextColumn::make('puerto_entrega')->label(__('Puerto entrega'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('pertrechos_count')
                    ->label(__('Líneas'))
                    ->counts('pertrechos')
                    ->badge(),
                Tables\Columns\TextColumn::make('estado_general')
                    ->label(__('Estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __(ucfirst($state)))
                    ->color(fn (string $state) => match ($state) {
                        'pendiente' => 'warning',
                        'parcial'   => 'info',
                        'entregado' => 'success',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_general')
                    ->label(__('Estado'))
                    ->options([
                        'pendiente' => __('Pendiente'),
                        'parcial'   => __('Parcial'),
                        'entregado' => __('Entregado'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('fecha_pedido', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'view'   => Pages\ViewPedido::route('/{record}'),
            'edit'   => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
}
