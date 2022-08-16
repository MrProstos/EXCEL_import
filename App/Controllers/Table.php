<?php

namespace App\Controllers;

use App\Models\Price;
use App\Models\Users;
use Core\View;

/**
 * Class serving the route Table
 */
class Table extends \Core\Controller
{
    /**
     * Show the Table page
     * @return void
     */
    public function indexAction(): void
    {
        View::renderTemplate('table.html');
    }

    /**
     * Update Table page
     * @return false|void
     */
    public function updateAction()
    {
        $usersDb = new Price();
        $dataTable = $usersDb->showTablePrays($_POST['page']);

        if ($dataTable === []) {
            return false;
        }
        echo json_encode($dataTable);
    }

}