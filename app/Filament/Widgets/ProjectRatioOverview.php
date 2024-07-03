<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;

class ProjectRatioOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $targetProjects = Project::where('is_default', true)->get();
        $dailyTasks = $targetProjects->mapWithKeys(function ($project) {
            return [
                $project->name => number_format($project->tasks()->whereDate('date', today())->sum('total_seconds_spent') / 3600, 1)];
        });
        $weeklyTasks = $targetProjects->mapWithKeys(function ($project) {
            return [
                $project->name => number_format($project->tasks()->whereBetween('date', [today()->startOfWeek(), today()->endOfWeek()])->sum('total_seconds_spent') / 3600, 1)];
        });
        $monthlyTasks = $targetProjects->mapWithKeys(function ($project) {
            return [
                $project->name => number_format($project->tasks()->whereBetween('date', [today()->startOfMonth(), today()->endOfMonth()])->sum('total_seconds_spent') / 3600, 1)];
        });

        $dailyRatio = $this->getRatio($dailyTasks);
        $weeklyRatio = $this->getRatio($weeklyTasks);
        $monthlyRatio = $this->getRatio($monthlyTasks);

        return [
            Stat::make('Daily Ratio', $dailyRatio->map(fn($ratio) => $ratio . '%')->join(' / '))
                ->chart($dailyRatio->values()->toArray())
                ->color('success'),
            Stat::make('Weekly Ratio', $weeklyRatio->map(fn($ratio) => $ratio . '%')->join(' / '))
                ->chart($weeklyRatio->values()->toArray())
                ->color('success'),
            Stat::make('Monthly Ratio', $monthlyRatio->map(fn($ratio) => $ratio . '%')->join(' / '))
                ->chart($monthlyRatio->values()->toArray())
                ->color('success'),
        ];
    }

    private function getRatio(Collection $tasks): Collection
    {
        $totalSum = $tasks->sum() ?: 1;
        return $tasks->map(function ($value) use ($totalSum) {
            return number_format($value / $totalSum * 100, 0);
        });
    }
}
