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
        $matches = [];
        preg_match_all("/\d+/", $_SERVER['REQUEST_URI'], $matches);
        $activePage = $matches[0][0] <= 0 ? 1 : $matches[0][0]; //The index is shifted by +1, for better readability to the user

        $data = $this->pageAction((int)$activePage - 1); //So we subtract -1 to find the right page
        View::renderTemplate('table.html.twig', ['title' => 'Таблица', 'data' => $data, 'activePage' => $activePage]);
    }

    private function pageAction(int $page = 0): bool|array
    {
        $usersDb = new Price();
        $dataTable = $usersDb->showTablePrays($page);

        if ($dataTable === []) {
            return false;
        }
        return $dataTable;
    }
}