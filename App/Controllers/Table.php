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
        $activePage = $matches[0][0];

        $data = $this->pageAction((int)$activePage);
        View::renderTemplate('table.html.twig', ['title' => 'Таблица', 'data' => $data, 'activePage' => $activePage]);
    }

    /**
     * Returns page data
     * @param int $page
     * @return bool|array
     */
    private function pageAction(int $page): bool|array
    {
        $usersDb = new Price();
        $dataTable = $usersDb->showTablePrays($page);

        if ($dataTable === []) {
            return false;
        }
        return $dataTable;
    }
}