<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 80;

    public static function getNavigationLabel(): string { return __('Usuarios'); }
    public static function getModelLabel(): string { return __('Usuario'); }
    public static function getPluralModelLabel(): string { return __('Usuarios'); }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Datos de acceso'))
                ->icon('heroicon-o-user')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Nombre'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label(__('Email'))
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('password')
                        ->label(__('Contraseña'))
                        ->password()
                        ->revealable()
                        ->minLength(8)
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation) => $operation === 'create')
                        ->helperText(__('Dejar en blanco para mantener la contraseña actual.')),

                    Forms\Components\Select::make('role')
                        ->label(__('Rol'))
                        ->options([
                            'admin' => __('Administrador'),
                            'agent' => __('Agente'),
                        ])
                        ->default('agent')
                        ->required()
                        ->helperText(__('Los administradores pueden eliminar registros.')),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nombre'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label(__('Rol'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'admin' ? __('Admin') : __('Agente'))
                    ->color(fn (string $state) => $state === 'admin' ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Alta'))
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('name')
            ->emptyStateHeading(__('Sin usuarios'))
            ->emptyStateDescription(__('Crea el primer usuario del sistema.'))
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
