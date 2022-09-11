<?php

namespace App\Controllers;

use App\Models\Import;
use App\Models\Sphinx;
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
        switch (true) {
            case isset($this->route_params['word']):
                $search = new Sphinx();
                $word = urldecode($this->route_params['word']);

                $data = $search->searchProductName($word, $activePage);
                View::renderTemplate('table.twig', [
                    'title' => 'Таблица',
                    'data' => $data,
                    'activePage' => $activePage,
                    'action' => 'search',
                    'word' => $word,
                ]);

                break;
            default:
                $data = $this->pageAction($activePage);
                View::renderTemplate('table.twig', [
                    'title' => 'Таблица',
                    'data' => $data,
                    'activePage' => $activePage,
                    'action' => 'table',
                ]);

                break;
        }
    }

    /**
     * Returns page data
     * @param int $page Active page number
     * @return bool|array Page data
     */
    private function pageAction(int $page): bool|array
    {
        $dbPrice = new Import($_COOKIE['hash']);
        $dataTable = $dbPrice->showTablePrice($page);

        if ($dataTable === []) {
            return false;
        }
        return $dataTable;
    }
}
