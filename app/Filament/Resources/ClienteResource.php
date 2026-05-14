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

    protected static ?string $navigationGroup = 'Maestros';

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
        // Layout plan:
        // Row 1 (Grid 2): [Identificación] [Contacto]
        // Row 2 (full width): Notas (collapsible)
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Section::make(__('Identificación'))
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label(__('Nombre comercial'))
                            ->placeholder('Naviera Mediterráneo, S.L.')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('direccion')
                            ->label(__('Dirección fiscal'))
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Contacto'))
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->prefixIcon('heroicon-o-envelope')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telefono')
                            ->label(__('Teléfono'))
                            ->tel()
                            ->prefixIcon('heroicon-o-phone')
                            ->maxLength(50),
                    ]),
            ]),

            Forms\Components\Section::make(__('Notas internas'))
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    Forms\Components\Textarea::make('notas')
                        ->hiddenLabel()
                        ->rows(4)
                        ->placeholder(__('Observaciones, condiciones especiales, contactos secundarios...'))
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label(__('Cliente'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->label(__('Teléfono'))
                    ->icon('heroicon-m-phone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('barcos_count')
                    ->label(__('Barcos'))
                    ->counts('barcos')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('direccion')
                    ->label(__('Dirección'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Alta'))
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->tooltip(__('Ver')),
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('nombre')
            ->emptyStateHeading(__('Sin clientes'))
            ->emptyStateDescription(__('Crea el primer cliente para empezar a registrar barcos y escalas.'))
            ->emptyStateIcon('heroicon-o-building-office-2');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'email', 'telefono'];
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
