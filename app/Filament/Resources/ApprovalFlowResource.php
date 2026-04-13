<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalFlowResource\Pages;
use App\Models\ApprovalFlow;
use App\Models\Approval;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApprovalFlowResource extends Resource
{
    protected static ?string $model = ApprovalFlow::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $modelLabel = 'Fluxo de Aprovação';
    protected static ?string $pluralModelLabel = 'Fila de Aprovações';
    protected static ?string $navigationLabel = 'Aprovações Pendentes';
    protected static ?string $navigationGroup = 'PRINCIPAL';
    protected static ?int $navigationSort = 3;

    /* public static function getNavigationBadge(): ?string
    {
        $count = Approval::where('status', 'Pendente')->count();
        return $count > 0 ? (string)$count : null;
    } */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('approval.title')
                    ->label('Aprovação')
                    ->searchable(),
                Tables\Columns\TextColumn::make('step_name')
                    ->label('Etapa'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aprovado' => 'success',
                        'Rejeitado' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Responsável')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Data/Hora')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : '')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalFlows::route('/'),
        ];
    }
}
