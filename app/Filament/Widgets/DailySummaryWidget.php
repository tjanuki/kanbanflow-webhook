<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class DailySummaryWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->selectRaw('date, SUM(total_seconds_spent) as total_seconds_spent')
                    ->whereBetween('date', [today()->startOfMonth(), today()->endOfMonth()])
                    ->client()
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('Y-m-d [D]');
                    })
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
