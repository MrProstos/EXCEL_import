<?php

namespace App\Controllers;

use App\Models\Price;
use Core\View;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class serving the route Import
 */
class Import extends \Core\Controller
{
    const MAX_FILE_SIZE = 30 * 1024 * 1024;
    const INVALID_FILE_FORMAT = 1;
    const INVALID_FILE_SIZE = 2;

    /**
     * Show page
     * @return void
     */
    public function indexAction(): void
    {
        View::renderTemplate('import.twig', ['title' => 'Импорт']);
    }

    /**
     * Reading a file and sending an array
     * @return void
     * @throws Exception
     */
    public function parseUploadFileAction(): void
    {
        $spreadsheet = new Spreadsheet();
        try {
            $inputFileType = 'Xlsx';
            $inputFileName = $_FILES['file']['tmp_name'];

            if (filesize($inputFileName) > self::MAX_FILE_SIZE) {
                echo json_encode(self::INVALID_FILE_SIZE);
                return;
            }

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $worksheetData = $reader->listWorksheetInfo($inputFileName);

            $msg = [];

            foreach ($worksheetData as $worksheet) {
                $spreadsheet = $reader->load($inputFileName);
                $worksheet = $spreadsheet->getActiveSheet();

                $msg = $worksheet->toArray();
            }

            echo json_encode($msg);
        } catch (\Exception) {
            echo json_encode(self::INVALID_FILE_FORMAT);
        }
    }

    /**
     * Add a value to the database
     * @return void
     */
    public function insertTableAction(): void
    {
        header('Content-type:application/json');

        if (isset($_POST['data'])) {
            $db = new Price();
            $result = $db->insertDataImport($_POST['data']);

            if ($result === 0) {
                echo json_encode(['status' => 0]);
                return;
            }
            echo json_encode(['status' => $result]);
        }
    }
}