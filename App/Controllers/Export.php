<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends \Core\Controller
{

    /**
     * Export action
     */
    public function indexAction()
    {
        try {
            $nameFile = mt_rand(5, 15) . '.xlsx';

            $export = new \App\Models\Export();
            $data = $export->export();

            $spreadSheet = new Spreadsheet();
            $sheet = $spreadSheet->getActiveSheet();

            for ($i = 0; $i < count($data); $i++) {
                $sheet->setCellValue('A' . $i + 1, $data[$i]['sku']);
                $sheet->setCellValue('B' . $i + 1, $data[$i]['product_name']);
                $sheet->setCellValue('C' . $i + 1, $data[$i]['supplier']);
                $sheet->setCellValue('D' . $i + 1, $data[$i]['price']);
                $sheet->setCellValue('E' . $i + 1, $data[$i]['cnt']);
            }

            $writer = new Xlsx($spreadSheet);
            $writer->save($nameFile); // TODO не сохранять , а сразу отправлять

            $this->sendFile($nameFile);
        } catch (\Exception) {
            echo 'Ошибка экспорта';
        }

    }

    /**
     * Send file on user
     * @param string $file
     * @return void
     */
    private function sendFile(string $file): void
    {
        if (file_exists($file)) {

            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit();
        }
    }
}