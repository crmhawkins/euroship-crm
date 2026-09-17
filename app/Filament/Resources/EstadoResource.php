<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadoResource\Pages;
use App\Models\Estado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class EstadoResource extends Resource
{
    protected static ?string $model = Estado::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 95;

    public static function getNavigationLabel(): string { return __('Estados'); }
    public static function getModelLabel(): string { return __('Estado'); }
    public static function getPluralModelLabel(): string { return __('Estados'); }

    public static function tipos(): array
    {
        return [
            Estado::TIPO_PEDIDO      => __('Pedidos'),
            Estado::TIPO_PRESUPUESTO => __('Presupuestos'),
        ];
    }

    public static function colores(): array
    {
        return [
            'gray'    => __('Gris'),
            'primary' => __('Azul (principal)'),
            'info'    => __('Celeste'),
            'success' => __('Verde'),
            'warning' => __('Naranja'),
            'danger'  => __('Rojo'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('tipo')
                ->label(__('Se aplica a'))
                ->options(static::tipos())
                ->required()
                ->native(false)
                ->disabledOn('edit'),
            Forms\Components\TextInput::make('nombre')
                ->label(__('Nombre'))
                ->required()
                ->maxLength(100)
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule, Forms\Get $get) => $rule->where('tipo', $get('tipo')),
                ),
            Forms\Components\Select::make('color')
                ->label(__('Color'))
                ->options(static::colores())
                ->default('gray')
                ->required()
                ->native(false),
            Forms\Components\TextInput::make('orden')
                ->label(__('Orden'))
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->helperText(__('Posición en los desplegables (menor primero).')),
            Forms\Components\Toggle::make('finalizado')
                ->label(__('Estado final'))
                ->helperText(__('Un pedido en este estado deja de aparecer en «Pedidos en curso» y en el reporte de pendientes.'))
                ->inline(false),
            Forms\Components\Toggle::make('activo')
                ->label(__('Activo'))
                ->helperText(__('Si lo desactivas deja de ofrecerse en los desplegables; los registros que ya lo usan lo conservan.'))
                ->default(true)
                ->onColor('success')
                ->inline(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->label(__('Se aplica a'))
                    ->formatStateUsing(fn (string $state) => static::tipos()[$state] ?? $state)
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label(__('Nombre'))
                    ->badge()
                    ->color(fn (Estado $record) => $record->color)
                    ->searchable(),
                Tables\Columns\TextColumn::make('orden')
                    ->label(__('Orden'))
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\IconColumn::make('finalizado')
                    ->label(__('Final'))
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('sistema')
                    ->label(__('Sistema'))
                    ->tooltip(__('Lo usa el cálculo automático del pedido; no se puede eliminar.'))
                    ->boolean()
                    ->trueIcon('heroicon-s-lock-closed')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('gray')
                    ->falseColor('gray')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('activo')
                    ->label(__('Activo'))
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label(__('Se aplica a'))
                    ->options(static::tipos()),
                Tables\Filters\TernaryFilter::make('activo')->label(__('Activo')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->defaultSort(fn ($query) => $query->orderBy('tipo')->orderBy('orden'))
            ->emptyStateHeading(__('Sin estados'))
            ->emptyStateIcon('heroicon-o-tag');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->isAdmin() ?? false)
            && ! $record->sistema
            && ! $record->enUso();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEstados::route('/'),
            'create' => Pages\CreateEstado::route('/create'),
            'edit'   => Pages\EditEstado::route('/{record}/edit'),
        ];
    }
}
