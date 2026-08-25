<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * @param  array<int, string>  $headers
     * @param  iterable<int, array<int, scalar|null>>  $rows
     */
    public function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $out = fopen('php://output', 'wb');

            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            fputcsv($out, $headers);

            foreach ($rows as $row) {
                fputcsv($out, array_map(fn ($value) => (string) $value, $row));
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
