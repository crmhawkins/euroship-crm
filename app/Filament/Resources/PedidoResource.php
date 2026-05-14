<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Models\Escala;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Operaciones';

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
        // Layout plan:
        // Resumen (placeholder, solo en edición) en la parte superior
        // Tabs:
        //   - General: Asignación (barco/escala) + Datos del pedido (lado a lado)
        //   - Pertrechos: Repeater colapsado con itemLabel
        return $form->schema([
            Forms\Components\Placeholder::make('resumen')
                ->label('')
                ->content(fn (?Pedido $record) => $record
                    ? sprintf(
                        '%s · %s · %d líneas · Estado: %s',
                        $record->numero_pedido,
                        $record->fecha_pedido?->format('d/m/Y'),
                        $record->pertrechos()->count(),
                        ucfirst($record->estado_general ?? '—')
                    )
                    : null
                )
                ->hiddenOn('create')
                ->columnSpanFull(),

            Tabs::make('Pedido')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tabs\Tab::make(__('General'))
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Section::make(__('Asignación'))
                                    ->icon('heroicon-o-link')
                                    ->description(__('Selecciona el barco y luego una de sus escalas.'))
                                    ->schema([
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
                                                        $e->id => $e->puerto . ' (' . ($e->fecha?->format('d/m/Y') ?? '—') . ')',
                                                    ])->toArray();
                                            })
                                            ->searchable()
                                            ->required()
                                            ->helperText(__('Solo se listan escalas del barco seleccionado.'))
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
                                    ]),

                                Forms\Components\Section::make(__('Datos del pedido'))
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Forms\Components\TextInput::make('numero_pedido')
                                            ->label(__('Número'))
                                            ->placeholder('PED-2026-0001')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Forms\Components\DatePicker::make('fecha_pedido')
                                            ->label(__('Fecha'))
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->default(now()),
                                        Forms\Components\TextInput::make('puerto_entrega')
                                            ->label(__('Puerto de entrega'))
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('estado_general')
                                            ->label(__('Estado'))
                                            ->options([
                                                'pendiente'  => __('Pendiente'),
                                                'parcial'    => __('Parcial'),
                                                'entregado'  => __('Entregado'),
                                            ])
                                            ->default('pendiente')
                                            ->helperText(__('Se recalcula automáticamente según los pertrechos.'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                            Forms\Components\Section::make(__('Notas'))
                                ->icon('heroicon-o-pencil-square')
                                ->schema([
                                    Forms\Components\Textarea::make('notas')
                                        ->hiddenLabel()
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])
                                ->collapsible()
                                ->collapsed(),
                        ]),

                    Tabs\Tab::make(__('Pertrechos'))
                        ->icon('heroicon-o-cube')
                        ->badge(fn (?Pedido $record) => $record?->pertrechos()->count() ?: null)
                        ->schema([
                            Forms\Components\Repeater::make('pertrechos')
                                ->hiddenLabel()
                                ->relationship()
                                ->schema([
                                    Forms\Components\TextInput::make('descripcion')
                                        ->label(__('Descripción'))
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('cantidad')
                                        ->label(__('Cant.'))
                                        ->numeric()
                                        ->default(1)
                                        ->required(),
                                    Forms\Components\TextInput::make('unidad')
                                        ->label(__('Unidad'))
                                        ->maxLength(30)
                                        ->placeholder('ud, kg, L, caja...'),
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
                                ->itemLabel(fn (array $state): ?string => isset($state['descripcion'])
                                    ? trim(($state['cantidad'] ?? '') . ' ' . ($state['unidad'] ?? '') . ' — ' . $state['descripcion'])
                                    : null)
                                ->collapsible()
                                ->collapsed()
                                ->addActionLabel(__('Añadir pertrecho')),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_pedido')
                    ->label(__('Número'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('fecha_pedido')
                    ->label(__('Fecha'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('escala.barco.cliente.nombre')
                    ->label(__('Cliente'))
                    ->searchable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('escala.barco.nombre')
                    ->label(__('Barco'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('escala.puerto')
                    ->label(__('Escala'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('puerto_entrega')
                    ->label(__('Entrega'))
                    ->searchable()
                    ->icon('heroicon-m-map-pin')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pertrechos_count')
                    ->label(__('Líneas'))
                    ->counts('pertrechos')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('estado_general')
                    ->label(__('Estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __(ucfirst($state)))
                    ->color(fn (string $state) => match ($state) {
                        'pendiente' => 'warning',
                        'parcial'   => 'info',
                        'entregado' => 'success',
                        default     => 'gray',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'pendiente' => 'heroicon-m-clock',
                        'parcial'   => 'heroicon-m-arrow-path',
                        'entregado' => 'heroicon-m-check-circle',
                        default     => null,
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

                Tables\Filters\SelectFilter::make('escala_id')
                    ->label(__('Escala'))
                    ->options(fn () => \App\Models\Escala::with('barco')
                        ->orderBy('fecha', 'desc')
                        ->get()
                        ->mapWithKeys(fn ($e) => [
                            $e->id => ($e->barco?->nombre ?? '—') . ' — ' . $e->puerto . ' (' . ($e->fecha?->format('d/m/Y') ?? '—') . ')',
                        ]))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('barco')
                    ->label(__('Barco'))
                    ->relationship('escala.barco', 'nombre')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('cliente')
                    ->label(__('Cliente'))
                    ->relationship('escala.barco.cliente', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->tooltip(__('Ver')),
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('fecha_pedido', 'desc')
            ->emptyStateHeading(__('Sin pedidos'))
            ->emptyStateDescription(__('Crea el primer pedido vinculado a una escala.'))
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['numero_pedido', 'puerto_entrega', 'escala.barco.nombre', 'escala.barco.cliente.nombre', 'escala.puerto'];
    }
}
