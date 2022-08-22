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
        $db = static::getDB();
        $db->exec('TRUNCATE TABLE price');
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

                $result = $db->prepare("INSERT INTO price (sku, product_name, supplier, price, cnt) VALUES (?,?,?,?,?)");
                $result->execute([$sku, $product_name, $supplier, $price, $cnt]);
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
            $db = static::getDB();
            $nRow *= 5;

            $result = $db->prepare('SELECT price.sku, price.product_name, price.supplier, price.price, price.cnt FROM price LIMIT 5 OFFSET ?');
            $result->bindParam(1, $nRow, PDO::PARAM_INT);
            $result->execute();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $dataArr['data'][] = $row;
            }

            $result = $db->query('SELECT COUNT(*) AS nAllRow FROM price');
            foreach ($result as $item) {
                $dataArr['nAllRow'] = $item['nAllRow'] / 5 - 1;
            }

            return $dataArr;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }
}