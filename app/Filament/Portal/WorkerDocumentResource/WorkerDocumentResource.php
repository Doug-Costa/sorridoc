<?php

namespace App\Filament\Portal\WorkerDocumentResource;

use App\Models\WorkerDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkerDocumentResource extends Resource
{
    protected static ?string $model = WorkerDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Documento';

    protected static ?string $pluralModelLabel = 'Documentos';

    protected static ?string $navigationLabel = 'Documentos';

    protected static ?string $navigationGroup = 'PORTAL SORRIMED';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Documento')
                    ->schema([
                        Forms\Components\Select::make('worker_id')
                            ->relationship('worker', 'name', fn ($query) => $query->where('status', 'Ativo'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options(WorkerDocument::TYPES)
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Arquivo')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Arquivo PDF')
                            ->disk('private')
                            ->directory('worker-documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(102400)
                            ->visibility('private')
                            ->preserveFilenames()
                            ->downloadable()
                            ->previewable(false)
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if (!$state) return;
                                
                                $file = is_array($state) ? array_values($state)[0] : $state;
                                
                                if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                    $set('original_name', $file->getClientOriginalName());
                                    $set('file_size', $file->getSize());
                                    $set('mime_type', $file->getMimeType());
                                }
                            }),
                        Forms\Components\Hidden::make('original_name'),
                        Forms\Components\Hidden::make('file_size'),
                        Forms\Components\Hidden::make('mime_type'),

                        Forms\Components\Placeholder::make('file_info')
                            ->label('Informações do Arquivo')
                            ->content(function ($record) {
                                if (! $record) {
                                    return 'Nenhum arquivo selecionado';
                                }

                                return "Nome: {$record->original_name}\nTamanho: ".number_format($record->file_size / 1024, 2)." KB\nTipo: {$record->mime_type}";
                            })
                            ->visible(fn ($record) => $record !== null),
                    ]),

                Forms\Components\Section::make('Datas')
                    ->schema([
                        Forms\Components\DatePicker::make('issued_at')
                            ->label('Data de Emissão'),
                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Data de Vencimento'),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(['Pendente' => 'Pendente', 'Aprovado' => 'Aprovado', 'Rejeitado' => 'Rejeitado'])
                            ->default('Aprovado')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => WorkerDocument::TYPES[$state] ?? $state),
                Tables\Columns\TextColumn::make('worker.name')
                    ->label('Trabalhador')
                    ->searchable(),
                Tables\Columns\TextColumn::make('worker.company.corporate_name')
                    ->label('Empresa')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'Pendente',
                        'success' => 'Aprovado',
                        'danger' => 'Rejeitado',
                    ]),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Emissão')
                    ->date('d/m/Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Enviado por')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(WorkerDocument::TYPES),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['Pendente' => 'Pendente', 'Aprovado' => 'Aprovado', 'Rejeitado' => 'Rejeitado']),
                Tables\Filters\SelectFilter::make('worker')
                    ->relationship('worker', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListWorkerDocuments::route('/'),
            'create' => Pages\CreateWorkerDocument::route('/create'),
            'view' => Pages\ViewWorkerDocument::route('/{record}'),
            'edit' => Pages\EditWorkerDocument::route('/{record}/edit'),
        ];
    }
}
