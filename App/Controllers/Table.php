<?php

namespace App\Controllers;

use App\Models\Price;
use App\Models\Sphinx;
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
        $activePage = $this->route_params['page'];

        $data = $this->pageAction($activePage);
        View::renderTemplate('table.twig', ['title' => 'Таблица', 'data' => $data, 'activePage' => $activePage]);
    }

    /**
     * Returns page data
     * @param int $page Active page number
     * @return bool|array Page data
     */
    private function pageAction(int $page): bool|array
    {
        $dbPrice = new Price();
        $dataTable = $dbPrice->showTablePrice($page);

        if ($dataTable === []) {
            return false;
        }
        return $dataTable;
    }

    public function searchAction()
    {
        $word = $_POST['search_word'];
        $search = new Sphinx();
        echo json_encode($search->searchSku($word));
    }
}