<?php

namespace App\Filament\Widgets;

use App\Models\Estimate;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class WeeklySummaryWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        $dailyEstimates = Estimate::query()
            ->selectRaw('estimates.date, SUM(tasks.total_seconds_spent) as total_seconds_spent, MAX(estimates.estimated_seconds) as estimated_seconds')
            ->leftJoin('tasks', function ($join) {
                $join->on('estimates.date', '=', 'tasks.date')
                    ->where('tasks.color', 'cyan');
            })
            ->whereBetween('estimates.date', [today()->startOfMonth(), today()->endOfMonth()])
            ->groupBy('estimates.date')
            ->orderBy('estimates.date', 'desc');

        return $table
            ->query(
                Estimate::query()
                    ->selectRaw("DATE_FORMAT(STR_TO_DATE(CONCAT(YEARWEEK(tasks.date), ' Monday'), '%X%V %W'), '%Y-%m-%d') as week, SUM(tasks.total_seconds_spent) as total_seconds_spent, SUM(estimates.estimated_seconds) as estimated_seconds")
                    ->leftJoinSub($dailyEstimates, 'tasks', function ($join) {
                        $join->on('estimates.date', '=', 'tasks.date');
                    })
                    ->groupBy('week')
                    ->orderBy('week', 'desc')
            )->columns([
                Tables\Columns\TextColumn::make('week')
                    ->label('Week'),
                Tables\Columns\TextColumn::make('total_seconds_spent')
                    ->label('Spent (Hours)')
                    ->formatStateUsing(fn ($state) => number_format($state / 3600, 1))
                    ->alignRight(),
                Tables\Columns\TextColumn::make('estimated_seconds')
                    ->label('Estimated (Hours)')
                    ->formatStateUsing(fn ($state) => number_format($state / 3600, 1))
                    ->alignRight(),
                Tables\Columns\TextColumn::make('difference')
                    ->label('Diff (Hours)')
                    ->getStateUsing(fn ($record
                    ) => number_format(($record->total_seconds_spent - $record->estimated_seconds) / 3600, 1))
                    ->alignRight(),
            ])->paginated(false);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->week ?? 'unknown';
    }
}
