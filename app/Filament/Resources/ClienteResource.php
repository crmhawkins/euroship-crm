<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('Clientes');
    }

    public static function getModelLabel(): string
    {
        return __('Cliente');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Clientes');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Datos del cliente'))->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label(__('Nombre'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telefono')
                    ->label(__('Teléfono'))
                    ->tel()
                    ->maxLength(50),
                Forms\Components\TextInput::make('direccion')
                    ->label(__('Dirección'))
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
                Tables\Columns\TextColumn::make('nombre')->label(__('Nombre'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label(__('Email'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('telefono')->label(__('Teléfono'))->toggleable(),
                Tables\Columns\TextColumn::make('barcos_count')
                    ->label(__('Barcos'))
                    ->counts('barcos')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Creado'))
                    ->dateTime('Y-m-d')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index'  => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'view'   => Pages\ViewCliente::route('/{record}'),
            'edit'   => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
