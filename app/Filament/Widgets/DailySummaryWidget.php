<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class DailySummaryWidget extends BaseWidget
{

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->selectRaw('date, SUM(total_seconds_spent) as total_seconds_spent')
                    ->whereBetween('date', [today()->startOfMonth(), today()->endOfMonth()])
                    ->where('color', 'cyan')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->label('Date'),
                Tables\Columns\TextColumn::make('total_seconds_spent')
                    ->label('Time Spent (Hours)')
                    ->formatStateUsing(fn($state) => sprintf('%02d:%02d', ($state / 3600), ($state / 60 % 60), $state % 60)), // Convert seconds to H:i:s
            ]);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->date;
    }

}
