<?php

namespace App\Filament\Widgets;

use App\Models\Approval;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingApprovals extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Pendentes para mim';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => Approval::where('status', 'Pendente')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Aprovação'),
                Tables\Columns\TextColumn::make('category')->label('Tipo'),
                Tables\Columns\TextColumn::make('owner.name')->label('Solicitante'),
                Tables\Columns\TextColumn::make('created_at')->label('Data')->dateTime(),
            ]);
    }
}
