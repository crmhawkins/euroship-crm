<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioResource\Pages;
use App\Models\Barco;
use App\Models\Cliente;
use App\Models\Courier;
use App\Models\Escala;
use App\Models\EstatusAduanero;
use App\Models\Servicio;
use App\Models\Ubicacion;
use Filament\Forms;
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
        return $form->schema([
            Forms\Components\Section::make(__('Asignación'))->schema([
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
                                $e->id => $e->puerto . ' (' . ($e->fecha?->format('Y-m-d') ?? '—') . ')',
                            ]);
                    })
                    ->searchable()
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make(__('Conocimiento'))->schema([
                Forms\Components\Select::make('courier_id')
                    ->label(__('Courier'))
                    ->options(fn () => Courier::activos()->pluck('nombre', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\TextInput::make('number')
                    ->label(__('Number / Tracking'))
                    ->maxLength(100),

                Forms\Components\TextInput::make('bx')
                    ->label(__('BX (bultos)'))
                    ->numeric()
                    ->minValue(0),

                Forms\Components\TextInput::make('kg')
                    ->label(__('KG'))
                    ->numeric()
                    ->minValue(0),

                Forms\Components\DatePicker::make('llegada')
                    ->label(__('Llegada'))
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->nullable(),

                Forms\Components\Select::make('estatus_aduanero_id')
                    ->label(__('Estatus Aduanero'))
                    ->options(fn () => EstatusAduanero::activos()->pluck('nombre', 'id'))
                    ->searchable()
                    ->nullable(),
            ])->columns(3),

            Forms\Components\Section::make(__('Ubicación y estado'))->schema([
                Forms\Components\Select::make('ubicacion_id')
                    ->label(__('Ubicación'))
                    ->options(fn () => Ubicacion::activos()->pluck('nombre', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\Textarea::make('comentarios')
                    ->label(__('Comentarios'))
                    ->rows(2)
                    ->columnSpan(2),

                Forms\Components\Toggle::make('entrada')
                    ->label(__('ENT (entrada)'))
                    ->inline(false),

                Forms\Components\Toggle::make('facturado')
                    ->label(__('Facturado'))
                    ->inline(false),

                Forms\Components\Toggle::make('incidencia')
                    ->label(__('Incidencia'))
                    ->inline(false),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('entrada')
                    ->label('ENT')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\IconColumn::make('facturado')
                    ->label('FACTU')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('escala.id')
                    ->label('ID Escala')
                    ->sortable(),

                Tables\Columns\TextColumn::make('escala.barco.cliente.nombre')
                    ->label(__('Cliente'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('escala.barco.nombre')
                    ->label(__('Buque'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('courier.nombre')
                    ->label('Courier')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('number')
                    ->label('Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('bx')
                    ->label('BX')
                    ->numeric(),

                Tables\Columns\TextColumn::make('kg')
                    ->label('KG')
                    ->numeric(2),

                Tables\Columns\TextColumn::make('llegada')
                    ->label('Llegada')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ubicacion.nombre')
                    ->label('Ubicación')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('estatusAduanero.nombre')
                    ->label('Estatus Aduanero')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('incidencia')
                    ->label('INC')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-minus'),
            ])
            ->recordClasses(fn (Servicio $record) => match (true) {
                $record->incidencia    => 'bg-red-50 dark:bg-red-950',
                $record->llegada !== null => 'bg-green-50 dark:bg-green-950',
                default                => '',
            })
            ->filters([
                Tables\Filters\SelectFilter::make('escala_id')
                    ->label(__('Escala'))
                    ->options(fn () => Escala::with('barco')
                        ->orderBy('fecha', 'desc')
                        ->get()
                        ->mapWithKeys(fn ($e) => [
                            $e->id => $e->barco?->nombre . ' — ' . $e->puerto . ' (' . ($e->fecha?->format('d/m/Y') ?? '—') . ')',
                        ])),

                Tables\Filters\SelectFilter::make('barco')
                    ->label(__('Barco'))
                    ->relationship('escala.barco', 'nombre'),

                Tables\Filters\SelectFilter::make('courier_id')
                    ->label('Courier')
                    ->options(fn () => Courier::activos()->pluck('nombre', 'id')),

                Tables\Filters\TernaryFilter::make('llegada')
                    ->label(__('Con llegada'))
                    ->nullable(),

                Tables\Filters\TernaryFilter::make('incidencia')
                    ->label(__('Incidencia')),
            ])
            ->actions([
                Tables\Actions\Action::make('nota_entrega')
                    ->label(__('Nota PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn (Servicio $record) => route('servicio.nota-entrega', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('id', 'desc');
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
