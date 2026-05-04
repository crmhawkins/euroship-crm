<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EscalaResource\Pages;
use App\Models\Escala;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EscalaResource extends Resource
{
    protected static ?string $model = Escala::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('Escalas');
    }

    public static function getModelLabel(): string
    {
        return __('Escala');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Escalas');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Datos de la escala'))->schema([
                Forms\Components\Select::make('barco_id')
                    ->label(__('Barco'))
                    ->relationship('barco', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nombre} — {$record->cliente?->nombre}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DatePicker::make('fecha')
                    ->label(__('Fecha'))
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                Forms\Components\TextInput::make('puerto')
                    ->label(__('Puerto'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('notas')
                    ->label(__('Notas'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')->label(__('Fecha'))->date('Y-m-d')->sortable(),
                Tables\Columns\TextColumn::make('puerto')->label(__('Puerto'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('barco.nombre')->label(__('Barco'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('barco.cliente.nombre')->label(__('Cliente'))->toggleable(),
                Tables\Columns\TextColumn::make('pedidos_count')
                    ->label(__('Pedidos'))
                    ->counts('pedidos')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('barco_id')
                    ->label(__('Barco'))
                    ->relationship('barco', 'nombre')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEscalas::route('/'),
            'create' => Pages\CreateEscala::route('/create'),
            'view'   => Pages\ViewEscala::route('/{record}'),
            'edit'   => Pages\EditEscala::route('/{record}/edit'),
        ];
    }
}
