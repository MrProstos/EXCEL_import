<?php

namespace App\Controllers;

use Core\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Import extends \Core\Controller
{

    public function indexAction(): void
    {
        View::renderTemplate("import.html");
    }

    private function loadfile() // TODO удалить или закоментить
    {
        $uploaddir = '/var/www/my_site/public/files/';
        $uploadfile = $uploaddir . basename($_FILES['uploadFile']['name']);

        echo '<pre>';
        if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadfile)) {
            echo "Файл корректен и был успешно загружен.\n";
        } else {
            echo "Возможная атака с помощью файловой загрузки!\n";
        }
    }

    public function importAction(): void
    {
        $spreadsheet = new Spreadsheet();

        $inputFileType = 'Xlsx';
        $inputFileName = $_FILES["file"]["tmp_name"];


        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

        $reader->setReadDataOnly(true);

        $worksheetData = $reader->listWorksheetInfo($inputFileName);

        $msg = [];

        foreach ($worksheetData as $worksheet) {

            $sheetName = $worksheet["worksheetName"];

            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($inputFileName);

            $worksheet = $spreadsheet->getActiveSheet();

            $msg = $worksheet->toArray();
        }

        echo json_encode($msg);
    }
}