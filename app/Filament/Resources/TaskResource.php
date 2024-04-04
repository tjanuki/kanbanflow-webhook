<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kanbanflow_task_id')
                    ->label('Kanbanflow Task ID')
                    ->required(),
                Forms\Components\Datepicker::make('date')
                    ->label('Date')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Description'),
                // set projects as color
                Forms\Components\Select::make('color')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->required(),
                Forms\Components\TextInput::make('column_id')
                    ->label('Column ID')
                    ->required(),
                Forms\Components\TextInput::make('total_seconds_spent')
                    ->label('Total Seconds Spent')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->sortable(),
                Tables\Columns\TextColumn::make('project.name')->label('Project'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('total_seconds_spent')
                    ->formatStateUsing(fn(int $state) => number_format($state / 3600, 2)),
            ])
            ->defaultSort('date', 'desc')
            ->persistSortInSession()
            ->filters([
                // filter by project
                Tables\Filters\SelectFilter::make('color')
                    ->options(fn() => \App\Models\Project::pluck('name', 'color')->toArray())
                    ->label('Project')
                    ->default('cyan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
