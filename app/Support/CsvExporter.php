<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * Stream a CSV file from an iterable of rows.
     *
     * @param  string  $filename
     * @param  array<int, string>  $headers   Column headings
     * @param  iterable  $rows                Each row is an array of values in header order
     */
    public static function stream(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens the file with correct encoding
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, $headers);

            foreach ($rows as $row) {
                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}