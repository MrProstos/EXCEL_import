<?php

namespace App\Controllers;

use App\Models\Price;
use App\Models\Users;
use Core\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class serving the route Import
 */
class Import extends \Core\Controller
{
    /**
     * Show page
     * @return void
     */
    public function indexAction(): void
    {
        View::renderTemplate('import.html');
    }

    /**
     * Reading a file and sending an array
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function parseUploadFileAction(): void
    {
        $spreadsheet = new Spreadsheet();

        $inputFileType = 'Xlsx';
        $inputFileName = $_FILES['file']['tmp_name'];

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $worksheetData = $reader->listWorksheetInfo($inputFileName);

        $msg = [];

        foreach ($worksheetData as $worksheet) {
            $sheetName = $worksheet['worksheetName'];

            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();

            $msg = $worksheet->toArray();
        }

        echo json_encode($msg);
    }

    /**
     * Add a value to the database
     * @return void
     */
    public function insertTableAction()
    {
        header('Content-type:application/json');

        if (isset($_POST['data'])) {
            $usersDb = new Price();

            if (!$usersDb->insertDataImport( $_POST['data'])) {
                echo json_encode(['status'=>'Ошибка импорта']);
                return;
            }

            echo json_encode(['status'=>'Все хорошо']);
        }
    }
}