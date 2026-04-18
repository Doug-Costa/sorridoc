<?php

namespace App\Filament\Portal\WorkerResource;

use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Trabalhador';

    protected static ?string $pluralModelLabel = 'Trabalhadores';

    protected static ?string $navigationLabel = 'Trabalhadores';

    protected static ?string $navigationGroup = 'PORTAL SORRIMED';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Empresa')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'corporate_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Dados Pessoais')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cpf')
                            ->label('CPF')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Data de Nascimento'),
                        Forms\Components\Select::make('gender')
                            ->options(['M' => 'Masculino', 'F' => 'Feminino', 'Outro' => 'Outro'])
                            ->label('Sexo'),
                    ])->columns(2),

                Forms\Components\Section::make('Dados Profissionais')
                    ->schema([
                        Forms\Components\TextInput::make('role')
                            ->label('Cargo')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('department')
                            ->label('Departamento')
                            ->maxLength(100),
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

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(['Ativo' => 'Ativo', 'Inativo' => 'Inativo'])
                            ->default('Ativo')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF'),
                Tables\Columns\TextColumn::make('company.corporate_name')
                    ->label('Empresa')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Cargo')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'Ativo',
                        'danger' => 'Inativo',
                    ]),
                Tables\Columns\TextColumn::make('documents_count')
                    ->label('Documentos')
                    ->counts('documents'),
                Tables\Columns\TextColumn::make('last_access_at')
                    ->label('Último Acesso')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'corporate_name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['Ativo' => 'Ativo', 'Inativo' => 'Inativo']),
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
                            ->default(fn (Worker $record) => $record->user?->email ?? $record->email),
                        Forms\Components\TextInput::make('password')
                            ->label('Nova Senha')
                            ->password()
                            ->helperText('Deixe em branco para manter a senha atual (se o usuário já existir).')
                            ->required(fn (Worker $record) => !$record->user()->exists()),
                    ])
                    ->action(function (Worker $record, array $data) {
                        $user = $record->user;
                        
                        if ($user) {
                            $user->email = $data['email'];
                            if (filled($data['password'])) {
                                $user->password = \Illuminate\Support\Facades\Hash::make($data['password']);
                            }
                            $user->save();
                        } else {
                            \App\Models\User::create([
                                'name' => $record->name,
                                'email' => $data['email'],
                                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                                'role' => 'Funcionario',
                                'worker_id' => $record->id,
                                'company_id' => $record->company_id,
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
            'index' => Pages\ListWorkers::route('/'),
            'create' => Pages\CreateWorker::route('/create'),
            'view' => Pages\ViewWorker::route('/{record}'),
            'edit' => Pages\EditWorker::route('/{record}/edit'),
        ];
    }
}
