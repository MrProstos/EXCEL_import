<?php

namespace App\Models;

use PDO;

/**
 *  Methods for the Price table
 */
class Price extends \Core\Model
{
    /**
     * Add data to the table Price
     * @param array $data JSON data from the user
     * @return int Number of recorded lines
     */
    public function insertDataImport(array $data): int
    {
        $db = $this->getDB();
        $result = $db->prepare('DELETE price
                                FROM price
                                         INNER JOIN reg_user on price.user_id = reg_user.id
                                WHERE reg_user.id = (SELECT id FROM reg_user WHERE reg_user.passwd = ?)');
        $result->execute([$_COOKIE['hash']]);

        $nRow = 0;

        foreach ($data as $item) {
            if (!isset($item['value'])) {
                return 0;
            }
        }

        for ($i = 0; $i < count($data['sku']['value']); $i++) {
            try {
                $sku = $data['sku']['value'][$i] ?? null;
                $product_name = $data['product_name']['value'][$i] ?? null;
                $supplier = $data['supplier']['value'][$i] ?? null;
                $price = $data['price']['value'][$i] ?? null;
                $cnt = $data['cnt']['value'][$i] ?? null;

                $result = $db->prepare('INSERT INTO price (user_id, sku, product_name, supplier, price, cnt)
                                                SELECT (SELECT id FROM reg_user WHERE passwd = ?), ?, ?, ?, ?, ?');

                $result->execute([$_COOKIE['hash'], $sku, $product_name, $supplier, $price, $cnt]);
                $nRow++;
            } catch
            (\PDOException $e) {
                echo $e->getMessage();
                return 0;
            }
        }
        return $nRow;
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
                                            WHERE price.user_id = (SELECT id FROM reg_user WHERE reg_user.passwd = :hash)
                                            LIMIT 5 OFFSET :nRow');
            $result->bindParam(':nRow', $nRow, PDO::PARAM_INT);
            $result->bindParam(':hash', $_COOKIE['hash']);
            $result->execute();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $dataArr['data'][] = $row;
            }

            $result = $db->query('SELECT COUNT(*) AS nAllRow FROM price');
            foreach ($result as $item) {
                $dataArr['nAllRow'] = round($item['nAllRow'] / 5); // TODO если удалить ( - 1 ) TWIG отваливается ( Argument #3 ($step) must not exceed the specified range )
            }

            return $dataArr;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }
}