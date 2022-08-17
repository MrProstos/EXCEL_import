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
     * @return int
     */
    public function insertDataImport(array $data): int
    {
        $db = static::getDB();
        $nRow = 0;

        for ($i = 0; $i < count($data['sku']['value']); $i++) {
            try {
                $sku = $data['sku']['value'][$i] ?? null;
                $product_name = $data['product_name']['value'][$i] ?? null;
                $supplier = $data['supplier']['value'][$i] ?? null;
                $price = $data['price']['value'][$i] ?? null;
                $cnt = $data['cnt']['value'][$i] ?? null;

                if (preg_match('/[a-zA-Z]/', $sku) !== 1) {
                    continue;
                }
                if (preg_match('/[a-zA-Z]/', $product_name) !== 1) {
                    continue;
                }
                if (preg_match('/[a-zA-Z]/', $supplier) !== 1) {
                    continue;
                }
                if (!is_int((int)$price)) {
                    continue;
                } elseif (!is_int((int)$cnt)) {
                    continue;
                }

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
     * @param int $nRow
     * @return array
     */
    public
    function showTablePrays(int $nRow): array
    {
        try {
            $dataArr = ['nAllRow' => null, 'data' => []];
            $db = static::getDB();
            $nRow *= 5;

            $result = $db->prepare("SELECT price.sku, price.product_name, price.supplier, price.price, price.cnt FROM price LIMIT 5 OFFSET ?");
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