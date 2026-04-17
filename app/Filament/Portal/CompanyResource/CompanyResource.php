<?php

namespace App\Filament\Portal\CompanyResource;

use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $pluralModelLabel = 'Empresas';

    protected static ?string $navigationLabel = 'Empresas';

    protected static ?string $navigationGroup = 'PORTAL SORRIMED';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados da Empresa')
                    ->schema([
                        Forms\Components\TextInput::make('corporate_name')
                            ->label('Razão Social')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('fantasy_name')
                            ->label('Nome Fantasia')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cnpj')
                            ->label('CNPJ')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record),
                        Forms\Components\TextInput::make('ie')
                            ->label('Inscrição Estadual')
                            ->maxLength(50),
                    ])->columns(2),

                Forms\Components\Section::make('Contato')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Endereço')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Endereço')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('Cidade')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('state')
                            ->label('UF')
                            ->maxLength(2),
                        Forms\Components\TextInput::make('zip_code')
                            ->label('CEP')
                            ->maxLength(10),
                    ])->columns(2),

                Forms\Components\Section::make('Responsável')
                    ->schema([
                        Forms\Components\TextInput::make('responsible_name')
                            ->label('Nome do Responsável')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('responsible_role')
                            ->label('Cargo')
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(['Ativo' => 'Ativo', 'Inativo' => 'Inativo', 'Pendente' => 'Pendente'])
                            ->default('Pendente')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Token de Acesso')
                    ->schema([
                        Forms\Components\Placeholder::make('registration_token')
                            ->label('Token de Registro')
                            ->content(fn ($record) => $record?->registration_token ? '****'.substr($record->registration_token, -8) : 'Nenhum'),
                        Forms\Components\Placeholder::make('token_expires_at')
                            ->label('Expira em')
                            ->content(fn ($record) => $record?->token_expires_at?->format('d/m/Y H:i') ?? 'N/A'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('corporate_name')
                    ->label('Razão Social')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fantasy_name')
                    ->label('Nome Fantasia')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Ativo',
                        'warning' => 'Pendente',
                        'danger' => 'Inativo',
                    ]),
                Tables\Columns\TextColumn::make('workers_count')
                    ->label('Trabalhadores')
                    ->counts('workers'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['Ativo' => 'Ativo', 'Inativo' => 'Inativo', 'Pendente' => 'Pendente']),
            ])
            ->actions([
                Tables\Actions\Action::make('generateToken')
                    ->label('Gerar Token')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Company $record) {
                        $plainToken = $record->generateRegistrationToken(30);
                        $url = url('/rh/'.$plainToken);

                        Notification::make()
                            ->title('Token Gerado')
                            ->body("URL de acesso: {$url}")
                            ->success()
                            ->persistent()
                            ->send();
                    })
                    ->visible(fn () => Auth::user()->role === 'Super Admin'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
