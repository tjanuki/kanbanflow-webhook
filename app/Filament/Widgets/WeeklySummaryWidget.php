<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class WeeklySummaryWidget extends BaseWidget
{

    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->selectRaw("DATE_FORMAT(STR_TO_DATE(CONCAT(YEARWEEK(date), ' Monday'), '%X%V %W'), '%Y-%m-%d') as week, SUM(total_seconds_spent) as total_seconds_spent")
                    ->whereDate('date', '>=', today()->startOfMonth())
                    ->groupBy('week')
                    ->orderBy('week', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('week')
                    ->label('Week'),
                Tables\Columns\TextColumn::make('total_seconds_spent')
                    ->label('Time Spent (Hours)')
                    ->formatStateUsing(fn($state) => sprintf('%02d:%02d', ($state / 3600), ($state / 60 % 60), $state % 60)), // Convert seconds to H:i:s
            ]);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->week;
    }
}
