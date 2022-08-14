<?php

namespace App\Controllers;

use App\Models\Users;
use Core\View;

class Table extends \Core\Controller
{
    public function indexAction(): void
    {
        View::renderTemplate("table.html");
    }

    public function updateAction()
    {
        $usersDb = new Users();
        $dataTable = $usersDb->showTablePrays();
        if ($dataTable === []) {
            return false;
        };

        echo json_encode($dataTable);
    }

}