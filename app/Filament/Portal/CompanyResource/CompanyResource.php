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
                Tables\Actions\Action::make('configureAccess')
                    ->label('Configurar Acesso')
                    ->icon('heroicon-o-lock-closed')
                    ->color('info')
                    ->modalHeading('Configurar Acesso ao Portal')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->default(fn (Company $record) => $record->user?->email ?? $record->email),
                        Forms\Components\TextInput::make('password')
                            ->label('Nova Senha')
                            ->password()
                            ->helperText('Deixe em branco para manter a senha atual (se o usuário já existir).')
                            ->required(fn (Company $record) => !$record->user()->exists()),
                    ])
                    ->action(function (Company $record, array $data) {
                        $user = $record->user;
                        
                        if ($user) {
                            $user->email = $data['email'];
                            if (filled($data['password'])) {
                                $user->password = \Illuminate\Support\Facades\Hash::make($data['password']);
                            }
                            $user->save();
                        } else {
                            \App\Models\User::create([
                                'name' => $record->fantasy_name ?? $record->corporate_name,
                                'email' => $data['email'],
                                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                                'role' => 'Empresa',
                                'company_id' => $record->id,
                            ]);
                        }

                        Notification::make()
                            ->title('Acesso configurado com sucesso!')
                            ->success()
                            ->send();
                    }),
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
