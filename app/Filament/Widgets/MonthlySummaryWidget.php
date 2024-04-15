<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class MonthlySummaryWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->selectRaw("DATE_FORMAT(tasks.date, '%Y-%m') as month, SUM(tasks.total_seconds_spent) as total_seconds_spent, SUM(estimates.estimated_seconds) as estimated_seconds")
                    ->leftJoin('estimates', 'tasks.date', '=', 'estimates.date')
                    ->client()
                    ->groupBy('month')
                    ->orderBy('month', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Month'),
                Tables\Columns\TextColumn::make('total_seconds_spent')
                    ->label('Spent (Hours)')
                    ->formatStateUsing(fn($state) => number_format($state / 3600, 1))
                    ->alignRight(),
                Tables\Columns\TextColumn::make('estimated_seconds')
                    ->label('Estimated (Hours)')
                    ->formatStateUsing(fn($state) => number_format($state / 3600, 1))
                    ->alignRight(),
                Tables\Columns\TextColumn::make('difference')
                    ->label('Diff (Hours)')
                    ->getStateUsing(fn($record
                    ) => number_format(($record->total_seconds_spent - $record->estimated_seconds) / 3600, 1))
                    ->alignRight(),
            ]);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->month;
    }
}
