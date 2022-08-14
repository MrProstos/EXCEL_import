<?php

namespace App\Controllers;

use App\Models\Users;
use Core\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use  PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Import extends \Core\Controller
{

    public function indexAction(): void
    {
        View::renderTemplate("import.html");
    }

    private function uploadFile() // TODO удалить или закоментить
    {
        $uploadDir = '/var/www/my_site/public/files/';
        $uploadFile = $uploadDir . basename($_FILES['uploadFile']['name']);

        if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadFile)) {
            echo "Файл корректен и был успешно загружен.\n";
        } else {
            echo "Возможная атака с помощью файловой загрузки!\n";
        }
    }

    public function parseUploadFileAction(): void
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

    public function insertTableAction()
    {
        header("Content-type:application/json");
        if (isset($_POST["data"])) {
            $dataArr = $_POST["data"];
            $usersDb = new Users();
            if (!$usersDb->insertDataImport($dataArr)) {
                echo json_encode(["status"=>"Ошибка импорта"]);
                return;
            }
            echo json_encode(["status"=>"Все хорошо"]);
        }
    }
}