<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportHelper
{
    /**
     * Generate a standard formatted Excel (.xls / .csv) download response with:
     * - Header background: Green (#10B981 / #059669)
     * - Header font: Bold, White (#FFFFFF)
     * - Header alignment: Center aligned
     * - Data cells: Centered, clean borders, auto-fitting nowrap columns
     *
     * @param string $filename
     * @param array $headers
     * @param array|\Illuminate\Support\Collection $dataRows
     * @return StreamedResponse
     */
    public static function streamDownload(string $filename, array $headers, $dataRows): StreamedResponse
    {
        // Ensure .xls extension for styled Excel spreadsheet
        if (!str_ends_with(strtolower($filename), '.xls') && !str_ends_with(strtolower($filename), '.xlsx') && !str_ends_with(strtolower($filename), '.csv')) {
            $filename .= '.xls';
        }

        $callback = function () use ($headers, $dataRows) {
            $out = fopen('php://output', 'w');
            
            // UTF-8 BOM
            fputs($out, "\xEF\xBB\xBF");

            // HTML spreadsheet template for native Excel opening with styling & auto-fit columns
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Sheet1</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>';
            echo '  table { border-collapse: collapse; font-family: Calibri, Arial, sans-serif; width: 100%; }';
            echo '  th { background-color: #10B981 !important; color: #FFFFFF !important; font-weight: bold !important; text-align: center !important; vertical-align: middle !important; height: 36px !important; font-size: 11pt !important; border: 1px solid #059669 !important; padding: 8px 16px !important; white-space: nowrap !important; }';
            echo '  td { text-align: center !important; vertical-align: middle !important; font-size: 10.5pt !important; border: 1px solid #E2E8F0 !important; padding: 6px 12px !important; white-space: nowrap !important; mso-number-format: "\@"; }';
            echo '  tr:nth-child(even) { background-color: #F8FAFC; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<table>';
            
            // Header Row
            echo '<thead><tr>';
            foreach ($headers as $header) {
                echo '<th>' . htmlspecialchars((string)$header, ENT_QUOTES, 'UTF-8') . '</th>';
            }
            echo '</tr></thead>';

            // Data Rows
            echo '<tbody>';
            $rowCount = 0;
            foreach ($dataRows as $row) {
                echo '<tr>';
                $cells = is_object($row) ? (array)$row : $row;
                foreach ($cells as $cell) {
                    $cellVal = htmlspecialchars((string)($cell ?? ''), ENT_QUOTES, 'UTF-8');
                    echo '<td>' . $cellVal . '</td>';
                }
                echo '</tr>';

                $rowCount++;
                if ($rowCount % 100 === 0) {
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }
            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';

            fclose($out);
        };

        return response()->stream($callback, 200, [
            "Content-Type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
