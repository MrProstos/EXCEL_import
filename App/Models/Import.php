<?php

namespace App\Models;

use App\Models\Price;
use Core\UserException;
use PDO;

class Import extends \Core\Model
{

    private Price $dbPrice;

    public function __construct(string $hash)
    {
        $dbPrice = new Price();
        $dbPrice->setUserIdByHash($hash);
        $this->dbPrice = $dbPrice;
    }

    /**
     * Add data to the table Import
     * @param array $data JSON data from the user
     * @return int Number of recorded lines
     * @throws UserException
     */
    public function insertTable(array $data): int
    {
        return $this->dbPrice->insert($data);
    }

    /**
     * Show data from the table price
     * @param int $nRow Active page number
     * @return array Page data | Returns an empty array in case of an error
     */
    public function showTablePrice(int $nRow): array
    {
        try {
            $dataArr = ['nAllRow' => null, 'data' => []];
            $db = $this->getDB();
            $nRow *= 5;

            $result = $db->prepare('SELECT sku, product_name, supplier, price, cnt
                                            FROM price
                                                     INNER JOIN reg_user on price.user_id = reg_user.id
                                            WHERE price.user_id = :userId
                                            LIMIT 5 OFFSET :nRow');
            $result->bindParam(':nRow', $nRow, PDO::PARAM_INT);
            $result->bindParam(':userId', $this->dbPrice->userId, PDO::PARAM_INT);
            $result->execute();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $dataArr['data'][] = $row;
            }

            $result = $db->query('SELECT COUNT(*) AS nAllRow FROM price');
            foreach ($result as $item) {
                $dataArr['nAllRow'] = round($item['nAllRow'] / 5);
            }

            return $dataArr;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
