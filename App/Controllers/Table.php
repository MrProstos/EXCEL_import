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
        $page = $_POST["page"];

        $usersDb = new Users();

        $dataTable = $usersDb->showTablePrays($page);
        if ($dataTable === []) {
            return false;
        };

        echo json_encode($dataTable);

    }

}