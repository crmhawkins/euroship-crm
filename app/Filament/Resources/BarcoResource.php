<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarcoResource\Pages;
use App\Models\Barco;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BarcoResource extends Resource
{
    protected static ?string $model = Barco::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string
    {
        return __('Barcos');
    }

    public static function getModelLabel(): string
    {
        return __('Barco');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Barcos');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Datos del barco'))->schema([
                Forms\Components\Select::make('cliente_id')
                    ->label(__('Cliente'))
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                    ->label(__('Nombre'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bandera')
                    ->label(__('Bandera'))
                    ->maxLength(100),
                Forms\Components\TextInput::make('imo_number')
                    ->label(__('Número IMO'))
                    ->maxLength(20),
                Forms\Components\TextInput::make('tipo')
                    ->label(__('Tipo'))
                    ->maxLength(100),
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
                Tables\Columns\TextColumn::make('nombre')->label(__('Nombre'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')->label(__('Cliente'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('bandera')->label(__('Bandera'))->toggleable(),
                Tables\Columns\TextColumn::make('imo_number')->label(__('IMO'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('tipo')->label(__('Tipo'))->toggleable(),
                Tables\Columns\TextColumn::make('escalas_count')
                    ->label(__('Escalas'))
                    ->counts('escalas')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cliente_id')
                    ->label(__('Cliente'))
                    ->relationship('cliente', 'nombre')
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
            ->defaultSort('nombre');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBarcos::route('/'),
            'create' => Pages\CreateBarco::route('/create'),
            'view'   => Pages\ViewBarco::route('/{record}'),
            'edit'   => Pages\EditBarco::route('/{record}/edit'),
        ];
    }
}
