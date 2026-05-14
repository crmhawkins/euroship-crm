<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioResource\Pages;
use App\Models\Barco;
use App\Models\Courier;
use App\Models\Escala;
use App\Models\EstatusAduanero;
use App\Models\Servicio;
use App\Models\Ubicacion;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Operaciones';

    protected static ?int $navigationSort = 45;

    public static function getNavigationLabel(): string { return __('Servicios'); }
    public static function getModelLabel(): string { return __('Servicio'); }
    public static function getPluralModelLabel(): string { return __('Servicios'); }

    public static function form(Form $form): Form
    {
        // Layout plan:
        // Tabs: Asignación | Conocimiento | Estado y ubicación
        // Resumen placeholder en edit mostrando flags ENT/FACTU/INC y llegada
        return $form->schema([
            Forms\Components\Placeholder::make('resumen')
                ->label('')
                ->content(fn (?Servicio $record) => $record
                    ? sprintf(
                        '%s · BX: %s · KG: %s · Llegada: %s · ENT: %s · FACTU: %s · INC: %s',
                        $record->number ?? '—',
                        $record->bx ?? '—',
                        $record->kg ?? '—',
                        $record->llegada?->format('d/m/Y') ?? '—',
                        $record->entrada ? '✓' : '·',
                        $record->facturado ? '✓' : '·',
                        $record->incidencia ? '⚠' : '·',
                    )
                    : null
                )
                ->hiddenOn('create')
                ->columnSpanFull(),

            Tabs::make('Servicio')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tabs\Tab::make(__('Asignación'))
                        ->icon('heroicon-o-link')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Select::make('barco_id')
                                    ->label(__('Barco'))
                                    ->options(fn () => Barco::with('cliente')->get()
                                        ->mapWithKeys(fn ($b) => [$b->id => "{$b->nombre} — {$b->cliente?->nombre}"]))
                                    ->searchable()
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateHydrated(function (Set $set, ?Servicio $record) {
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
                                        if (! $barcoId) return [];
                                        return Escala::where('barco_id', $barcoId)
                                            ->orderBy('fecha', 'desc')
                                            ->get()
                                            ->mapWithKeys(fn ($e) => [
                                                $e->id => $e->puerto . ' (' . ($e->fecha?->format('d/m/Y') ?? '—') . ')',
                                            ]);
                                    })
                                    ->searchable()
                                    ->required()
                                    ->helperText(__('Solo se listan escalas del barco seleccionado.')),
                            ]),
                        ]),

                    Tabs\Tab::make(__('Conocimiento'))
                        ->icon('heroicon-o-truck')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Section::make(__('Envío'))
                                    ->icon('heroicon-o-paper-airplane')
                                    ->schema([
                                        Forms\Components\Select::make('courier_id')
                                            ->label(__('Courier'))
                                            ->options(fn () => Courier::activos()->pluck('nombre', 'id'))
                                            ->searchable()
                                            ->nullable(),

                                        Forms\Components\TextInput::make('number')
                                            ->label(__('Tracking / Número'))
                                            ->placeholder('Número de conocimiento o tracking del courier')
                                            ->maxLength(100),

                                        Forms\Components\DatePicker::make('llegada')
                                            ->label(__('Fecha de llegada'))
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->nullable(),
                                    ]),

                                Forms\Components\Section::make(__('Mercancía'))
                                    ->icon('heroicon-o-cube')
                                    ->schema([
                                        Forms\Components\TextInput::make('bx')
                                            ->label(__('Bultos (BX)'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->placeholder('0'),

                                        Forms\Components\TextInput::make('kg')
                                            ->label(__('Peso (KG)'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->suffix('kg')
                                            ->placeholder('0.00'),

                                        Forms\Components\Select::make('estatus_aduanero_id')
                                            ->label(__('Estatus aduanero'))
                                            ->options(fn () => EstatusAduanero::activos()->pluck('nombre', 'id'))
                                            ->searchable()
                                            ->nullable(),
                                    ]),
                            ]),
                        ]),

                    Tabs\Tab::make(__('Estado y ubicación'))
                        ->icon('heroicon-o-map')
                        ->schema([
                            Forms\Components\Section::make(__('Marcadores'))
                                ->icon('heroicon-o-flag')
                                ->description(__('Estado operacional del servicio.'))
                                ->schema([
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\Toggle::make('entrada')
                                            ->label(__('ENT (entrada)'))
                                            ->helperText(__('Mercancía recibida en almacén'))
                                            ->onColor('success')
                                            ->inline(false),
                                        Forms\Components\Toggle::make('facturado')
                                            ->label(__('FACTU (facturado)'))
                                            ->helperText(__('Servicio facturado al cliente'))
                                            ->onColor('info')
                                            ->inline(false),
                                        Forms\Components\Toggle::make('incidencia')
                                            ->label(__('INC (incidencia)'))
                                            ->helperText(__('Marca cualquier anomalía'))
                                            ->onColor('danger')
                                            ->inline(false),
                                    ]),
                                ]),

                            Forms\Components\Section::make(__('Ubicación interna'))
                                ->icon('heroicon-o-archive-box')
                                ->schema([
                                    Forms\Components\Select::make('ubicacion_id')
                                        ->label(__('Ubicación en almacén'))
                                        ->options(fn () => Ubicacion::activos()->pluck('nombre', 'id'))
                                        ->searchable()
                                        ->nullable(),

                                    Forms\Components\Textarea::make('comentarios')
                                        ->label(__('Comentarios'))
                                        ->rows(3)
                                        ->columnSpanFull(),

                                    Forms\Components\TextInput::make('enlace')
                                        ->label(__('Enlace'))
                                        ->url()
                                        ->placeholder('https://')
                                        ->maxLength(2048)
                                        ->suffixIcon('heroicon-m-arrow-top-right-on-square')
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('escala.fecha')
                    ->label(__('Fecha'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('escala.barco.cliente.nombre')
                    ->label(__('Cliente'))
                    ->searchable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('escala.barco.nombre')
                    ->label(__('Buque'))
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('courier.nombre')
                    ->label(__('Courier'))
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('number')
                    ->label(__('Tracking'))
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->url(fn (Servicio $record): ?string => $record->enlace ?: null)
                    ->openUrlInNewTab()
                    ->icon(fn (Servicio $record): ?string => $record->enlace ? 'heroicon-m-arrow-top-right-on-square' : null)
                    ->color(fn (Servicio $record): ?string => $record->enlace ? 'primary' : null),

                Tables\Columns\TextColumn::make('bx')
                    ->label(__('BX'))
                    ->numeric()
                    ->alignEnd()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('kg')
                    ->label(__('KG'))
                    ->numeric(2)
                    ->suffix(' kg')
                    ->alignEnd()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('llegada')
                    ->label(__('Llegada'))
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\IconColumn::make('entrada')
                    ->label('ENT')
                    ->tooltip(__('Mercancía recibida'))
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->falseColor('gray')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('facturado')
                    ->label('FACTU')
                    ->tooltip(__('Facturado al cliente'))
                    ->boolean()
                    ->trueIcon('heroicon-s-banknotes')
                    ->trueColor('info')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->falseColor('gray')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('incidencia')
                    ->label('INC')
                    ->tooltip(__('Incidencia registrada'))
                    ->boolean()
                    ->trueIcon('heroicon-s-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-minus')
                    ->falseColor('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('ubicacion.nombre')
                    ->label(__('Ubicación'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('estatusAduanero.nombre')
                    ->label(__('Estatus aduanero'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordClasses(fn (Servicio $record) => match (true) {
                $record->incidencia       => 'bg-red-50 dark:bg-red-950/30',
                $record->llegada !== null => 'bg-emerald-50/50 dark:bg-emerald-950/20',
                default                   => '',
            })
            ->filters([
                Tables\Filters\SelectFilter::make('escala_id')
                    ->label(__('Escala'))
                    ->options(fn () => Escala::with('barco')
                        ->orderBy('fecha', 'desc')
                        ->get()
                        ->mapWithKeys(fn ($e) => [
                            $e->id => $e->barco?->nombre . ' — ' . $e->puerto . ' (' . ($e->fecha?->format('d/m/Y') ?? '—') . ')',
                        ]))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('barco')
                    ->label(__('Barco'))
                    ->relationship('escala.barco', 'nombre')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('courier_id')
                    ->label(__('Courier'))
                    ->options(fn () => Courier::activos()->pluck('nombre', 'id')),

                Tables\Filters\TernaryFilter::make('llegada')
                    ->label(__('Con llegada'))
                    ->nullable(),

                Tables\Filters\TernaryFilter::make('entrada')->label(__('ENT')),
                Tables\Filters\TernaryFilter::make('facturado')->label(__('Facturado')),
                Tables\Filters\TernaryFilter::make('incidencia')->label(__('Incidencia')),
            ])
            ->actions([
                Tables\Actions\Action::make('nota_entrega')
                    ->label(__('PDF'))
                    ->tooltip(__('Generar nota de entrega'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->iconButton()
                    ->url(fn (Servicio $record) => route('servicio.nota-entrega', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make()->iconButton()->tooltip(__('Ver')),
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('id', 'desc')
            ->emptyStateHeading(__('Sin servicios'))
            ->emptyStateDescription(__('Registra el primer conocimiento o envío vinculado a una escala.'))
            ->emptyStateIcon('heroicon-o-inbox-arrow-down');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListServicios::route('/'),
            'create' => Pages\CreateServicio::route('/create'),
            'view'   => Pages\ViewServicio::route('/{record}'),
            'edit'   => Pages\EditServicio::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'escala.barco.nombre', 'escala.barco.cliente.nombre', 'courier.nombre'];
    }
}
