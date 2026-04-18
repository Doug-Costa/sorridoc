<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $navigationGroup = 'CONFIGURAÇÕES';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn (?string $state) => filled($state)),
                Forms\Components\TextInput::make('pin_code')
                    ->label('PIN de Assinatura (4 dígitos)')
                    ->password()
                    ->length(4)
                    ->helperText('Defina um código numérico de 4 dígitos para assinar eletronicamente.')
                    ->dehydrated(fn (?string $state) => filled($state)),
                Forms\Components\Select::make('role')
                    ->label('Papel')
                    ->options(User::ROLES)
                    ->required()
                    ->live()
                    ->default('Operacional'),
                Forms\Components\Select::make('company_id')
                    ->label('Empresa Vinculada')
                    ->relationship('company', 'fantasy_name')
                    ->visible(fn (Forms\Get $get) => $get('role') === 'Empresa' || $get('role') === 'Gestor RH')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('worker_id')
                    ->label('Funcionário Vinculado')
                    ->relationship('worker', 'name')
                    ->visible(fn (Forms\Get $get) => $get('role') === 'Funcionario')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('unit')
                    ->label('Unidade')
                    ->options([
                        'Maringá' => 'Maringá',
                        'Sorriso' => 'Sorriso',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('role')->sortable(),
                Tables\Columns\TextColumn::make('unit')->sortable(),

            ])
            ->paginated([10, 25, 50])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()])
            /* ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]) */;
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
