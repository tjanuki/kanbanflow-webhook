<?php

namespace App\Filament\Exports;

use App\Models\Task;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Model;

class TaskExporter extends Exporter
{
    protected static ?string $model = Task::class;

    protected $recordCount;
    protected $currentRecord = 0;
    protected $totalSecondsSpent = 0;

    public function __construct(
        protected Export $export,
        protected array $columnMap,
        protected array $options,
    ) {
        parent::__construct($export, $columnMap, $options);
        $this->recordCount = $this->export->total_rows ;
    }

    public function __invoke(Model $record): array
    {
        $this->currentRecord++;
        $this->record = $record;

        $columns = $this->getCachedColumns();

        $data = [];

        foreach (array_keys($this->columnMap) as $column) {
            $data[] = $columns[$column]->getFormattedState();
            if ($column === 'total_seconds_spent') {
                $this->totalSecondsSpent += $columns[$column]->getFormattedState();
            }
        }

        // Append the summary data once all records are processed.
        if ($this->currentRecord === $this->recordCount) {

            $data = [
                'date' => 'Grand Total',
                'name' => '',
                'total_seconds_spent' => number_format($this->totalSecondsSpent, 1),
            ];
        }

        return $data;
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('date'),
            ExportColumn::make('name')->label('Task Name'),
            ExportColumn::make('total_seconds_spent')
                ->formatStateUsing(function (int $state): string {
                    return number_format($state / 3600, 1);
                }),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your task export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
