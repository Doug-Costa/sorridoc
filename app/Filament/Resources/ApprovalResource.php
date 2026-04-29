<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalResource\Pages;
use App\Models\Approval;
use App\Models\ApprovalFlow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ApprovalResource extends Resource
{
    protected static ?string $model = Approval::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $modelLabel = 'Aprovação';
    protected static ?string $pluralModelLabel = 'Aprovações';
    protected static ?string $navigationLabel = 'Gerir Aprovações';
    protected static ?string $navigationGroup = 'PRINCIPAL';
    protected static ?int $navigationSort = 2;

    /* public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? (string)$count : null;
    } */

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\ViewEntry::make('progress')
                            ->view('filament.components.approval-progress')
                            ->viewData(fn (Approval $record) => ['record' => $record])
                            ->columnSpanFull(),
                        
                        Infolists\Components\ViewEntry::make('multiple_progress')
                            ->view('filament.components.multiple-approval-progress')
                            ->viewData(fn (Approval $record) => ['record' => $record])
                            ->columnSpanFull(),
                        
                        Infolists\Components\Grid::make(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('protection_notice')
                                    ->label('')
                                    ->default('Registro sob proteção LGPD. Ação registrada em log imutável.')
                                    ->extraAttributes(['class' => 'bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-lg flex items-center'])
                                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),
                            ]),

                        Infolists\Components\Section::make('Conteúdo da Aprovação')
                            ->schema([
                                Infolists\Components\TextEntry::make('description')
                                    ->label('Acordado / Descrição')
                                    ->markdown(),
                                Infolists\Components\TextEntry::make('preview')
                                    ->label('Anexo (se houver)')
                                    ->visible(fn (Approval $record) => $record->file_path !== null)
                                    ->default('Documento associado disponível para visualização'),
                            ]),
                        
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('owner.name')
                                    ->label('Solicitado por'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Data de Início')
                                    ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
                                Infolists\Components\TextEntry::make('hash_sha256')
                                    ->label('Hash de Integridade')
                                    ->limit(12)
                                    ->visible(fn (Approval $record) => $record->file_path !== null),
                            ]),
                    ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título da Aprovação')
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->label('Descrição / Acordo Rápido')
                    ->placeholder('Descreva o que está sendo acordado nesta aprovação...')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file_path')
                    ->label('Anexo de Documento (Opcional)')
                    ->disk('private')
                    ->directory('approvals')
                    ->maxSize(102400)
                    ->acceptedFileTypes(['application/pdf']),
                Forms\Components\Select::make('category')
                    ->label('Categoria')
                    ->options(['Contrato' => 'Contrato', 'Ordem' => 'Ordem', 'Compliance' => 'Compliance', 'Acordo' => 'Acordo/Ação'])
                    ->required(),
                Forms\Components\Select::make('flow_type')
                    ->label('Fluxo de aprovação')
                    ->options([
                        'Simples' => 'Aprovação Única',
                        'Dupla' => 'Dupla Aprovação — Diretor + Advogada',
                        'Múltipla' => 'Múltipla Aprovação — Vários aprovadores',
                    ])
                    ->required()
                    ->default('Simples')
                    ->reactive(),
                Forms\Components\Select::make('sensitivity_level')
                    ->label('Nível de Sigilo')
                    ->options(['Normal' => 'Normal', 'Sigiloso' => 'Sigiloso', 'LGPD' => 'LGPD'])
                    ->required()->default('Normal'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(['Pendente' => 'Pendente', 'Em Aprovação' => 'Em Aprovação', 'Aprovado' => 'Aprovado', 'Rejeitado' => 'Rejeitado'])
                    ->required()->default('Pendente'),
                Forms\Components\DatePicker::make('deadline_at')
                    ->label('Prazo limite')
                    ->required(),
                Forms\Components\Select::make('owner_id')
                    ->label('Solicitante')
                    ->relationship('owner', 'name')
                    ->default(fn () => Auth::id())
                    ->required(),
                Forms\Components\Select::make('assigned_to')
                    ->label('Atribuído a')
                    ->relationship('assignedTo', 'name')
                    ->required(fn ($get) => $get('flow_type') !== 'Múltipla')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('flow_type') !== 'Múltipla'),
                
                Forms\Components\CheckboxList::make('multiple_assignees')
                    ->label('Aprovadores Múltiplos')
                    ->relationship('assignees.user', 'name')
                    ->required(fn ($get) => $get('flow_type') === 'Múltipla')
                    ->searchable()
                    ->visible(fn ($get) => $get('flow_type') === 'Múltipla')
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($get('flow_type') === 'Múltipla' && is_array($state)) {
                            $set('assigned_to', null);
                        }
                    }),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->role === 'Super Admin') {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('assigned_to', Auth::id())
                ->orWhere('owner_id', Auth::id())
                ->orWhereHas('assignees', function ($subQuery) {
                    $subQuery->where('user_id', Auth::id());
                });
        });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable(),
                Tables\Columns\TextColumn::make('category')->label('Categoria')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendente' => 'warning',
                        'Em Aprovação' => 'info',
                        'Aprovado' => 'success',
                        'Rejeitado' => 'danger',
                    }),
                Tables\Columns\IconColumn::make('has_file')
                    ->label('Anexo')
                    ->boolean()
                    ->state(fn (Approval $record) => $record->file_path !== null),
                Tables\Columns\TextColumn::make('owner.name')->label('Solicitante'),
                Tables\Columns\TextColumn::make('assignedTo.name')->label('Atribuído a'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : '')
                    ->sortable(),
            ])
            ->paginated([10, 25, 50])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['Pendente' => 'Pendente', 'Em Aprovação' => 'Em Aprovação', 'Aprovado' => 'Aprovado', 'Rejeitado' => 'Rejeitado']),
            ])
            ->actions([
                Tables\Actions\Action::make('aprovar')
                    ->label('Assinar/Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('Comentários de Aprovação')
                            ->placeholder('Opcional: Adicione observações sobre esta assinatura...'),
                        Forms\Components\ViewField::make('pin')
                            ->view('filament.components.pin-input')
                            ->required(),
                    ])
                    ->action(function (Approval $record, array $data) {
                        try {
                            app(\App\Domain\Services\ApprovalService::class)->approve($record, $data['pin'], $data['comment']);
                            
                            \Filament\Notifications\Notification::make()
                                ->title($record->status === 'Aprovado' ? 'Aprovação Concluída' : '1ª Assinatura Registrada')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erro na Operação')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn (Approval $record) => $record->status !== 'Aprovado' && $record->status !== 'Rejeitado'),

                Tables\Actions\Action::make('rejeitar')
                    ->label('Rejeitar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('Motivo da Rejeição')
                            ->placeholder('Obrigatório: Descreva o motivo da recusa...')
                            ->required(),
                        Forms\Components\ViewField::make('pin')
                            ->view('filament.components.pin-input')
                            ->required(),
                    ])
                    ->action(function (Approval $record, array $data) {
                        try {
                            app(\App\Domain\Services\ApprovalService::class)->reject($record, $data['pin'], $data['comment']);

                            \Filament\Notifications\Notification::make()
                                ->title('Aprovação Rejeitada')
                                ->danger()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erro na Operação')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn (Approval $record) => $record->status !== 'Aprovado' && $record->status !== 'Rejeitado'),
                
                Tables\Actions\Action::make('view_document')
                    ->label('Visualizar Documento')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Approval $record) => route('approvals.view-document', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Approval $record) => $record->file_path !== null),

                Tables\Actions\Action::make('download_certificate')
                    ->label('Certificado PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn (Approval $record) => route('approvals.download', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Approval $record) => $record->status === 'Aprovado'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Auth::user()->role === 'Super Admin'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovals::route('/'),
            'create' => Pages\CreateApproval::route('/create'),
            'view' => Pages\ViewApproval::route('/{record}'),
            'edit' => Pages\EditApproval::route('/{record}/edit'),
        ];
    }
}
