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
                    ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, SUM(total_seconds_spent) as total_seconds_spent")
                    ->client()
                    ->groupBy('month')
                    ->orderBy('month', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Month'),
                Tables\Columns\TextColumn::make('total_seconds_spent')
                    ->label('Time Spent (Hours)')
                    ->formatStateUsing(fn($state) => number_format($state / 3600, 1)),
            ]);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->month;
    }
}
