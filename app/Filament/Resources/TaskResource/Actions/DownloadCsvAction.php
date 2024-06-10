<?php

namespace App\Filament\Resources\TaskResource\Actions;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadCsvAction extends Action
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->label('Download CSV')
            ->action(function (): StreamedResponse {
                return response()->streamDownload(function () {
                    $handle = fopen('php://output', 'w');
                    fputcsv($handle, ['Header 1', 'Header 2', 'Header 3']);
                    // Add your data retrieval and CSV generation logic here
                    fclose($handle);
                }, 'filename.csv');
            });
        ;
    }

    public function handle(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Header 1', 'Header 2', 'Header 3']);
            // Add your data retrieval and CSV generation logic here
            fclose($handle);
        }, 'filename.csv');
    }
}
