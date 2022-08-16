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
     * @param array $data
     * @return bool
     */
    public function insertDataImport(array $data): bool
    {
        $db = static::getDB();

        for ($i = 0; $i < count($data['sku']['value']); $i++) {
            try {
                $sku = $data['sku']['value'][$i];
                $product_name = $data['product_name']['value'][$i];
                $supplier = $data['supplier']['value'][$i];
                $price = $data['price']['value'][$i];
                $cnt = $data['cnt']['value'][$i];

                $result = $db->prepare("INSERT INTO price (sku, product_name, supplier, price, cnt) VALUES (?,?,?,?,?)");
                $result->execute([$sku, $product_name, $supplier, $price, $cnt]);
            } catch (\PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return true;
    }

    /**
     * Show data from the table price
     * @param int $nRow
     * @return array
     */
    public function showTablePrays(int $nRow = 0): array
    {
        try {
            $dataArr = ['nAllRow' => null, 'data' => []];
            $db = static::getDB();

            $result = $db->prepare("SELECT * FROM price LIMIT 5 OFFSET ?");
            $result->bindParam(1, $nRow, PDO::PARAM_INT);
            $result->execute();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $dataArr['data'][] = $row;
            }

            $result = $db->query("SELECT ROUND(COUNT(*)/5) AS nAllRow FROM price");
            foreach ($result as $item) {
                $dataArr['nAllRow'] = $item;
            }

            return $dataArr;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }
}