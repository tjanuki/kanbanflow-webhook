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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\Select::make('color')
                    ->label('Color')
                    ->options([
                        'red' => 'Red',
                        'yellow' => 'Yellow',
                        'green' => 'Green',
                        'blue' => 'Blue',
                        'purple' => 'Purple',
                        'gray' => 'Gray',
                    ])
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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('color'),
                Tables\Columns\TextColumn::make('column_id'),
                Tables\Columns\TextColumn::make('total_seconds_spent'),
            ])
            ->filters([
                //
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
