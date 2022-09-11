<?php

namespace App\Models;

use Core\Model;
use Core\UserException;
use PDO;
use PDOException;

class Price extends Model
{
    public int $userId;
    const IS_VALID_SKU = '/^[^-][[:alnum:]А-Яа-я-][^\s]{1,15}$/';
    const IS_VALID_PRICE = '/[\d` ]+[.,]\d+|[\d `]+/';
    const IS_VALID_CNT = '/\d+[,.]\d+|\d+/';

    /**
     * Set value userId. Hash is used for the search
     * @param string $hash user hash
     * @return int
     */
    public function setUserIdByHash(string $hash): int
    {
        $db = $this->getDB();
        $result = $db->prepare('SELECT id FROM reg_user WHERE passwd = ?');
        $result->execute([$hash]);

        $row = $result->fetch();
        $this->userId = $row['id'];
        return $row['id'];
    }

    /**
     * Set value userId. The API token is used for the search
     * @param string $API user API token
     * @return int
     */
    public function setUserIdByApiToken(string $API): int
    {
        $db = $this->getDB();
        $result = $db->prepare('SELECT id FROM reg_user WHERE api_token = ?');
        $result->execute([$API]);

        $row = $result->fetch();
        $this->userId = $row['id'];
        return $row['id'];
    }

    /**
     * Sku validation
     * @param string $sku
     * @return bool
     */
    private function isValidSku(string $sku): bool
    {
        if (preg_match(self::IS_VALID_SKU, $sku) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Price validation
     * @param string $price
     * @return bool
     */
    private function isValidPrice(string $price): bool
    {
        if (preg_match(self::IS_VALID_PRICE, $price) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Valid price
     * @param string $price
     * @return string
     */
    private function validPrice(string $price): string
    {
        $newPrice = preg_replace(['/[\s`]+/', '/[A-zА-я]+/u', '/^\D+|\D+$/'], '', $price);
        return preg_replace('/(\d+)[,.](\d+)/', '$1.$2', $newPrice);
    }

    /**
     * Cnt validation
     * @param string $cnt
     * @return bool
     */
    private function isValidCnt(string $cnt): bool
    {
        if (preg_match(self::IS_VALID_CNT, $cnt) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Valid cnt
     * @param string $cnt
     * @return string
     */
    private function validCnt(string $cnt): string
    {
        $newCnt = preg_replace(['/[\s`]+/', '/[A-zА-я]+/u', '/^\D+|\D+$/'], '', $cnt);
        return preg_replace('/(\d+)[,.](\d+)/', '$1.$2', $newCnt);
    }

    /**
     * Corrects the data array and returns valid data
     * @param array $data data
     * @return array valid data
     */
    private function validData(array $data, string $method): array
    {
        $response = [];
        switch ($method) {
            case 'add':
            case 'update':
                foreach ($data as $item) {
                    $sku = $item['sku'] ?? null;
                    $product_name = $item['product_name'] ?? null;
                    $supplier = $item['supplier'] ?? null;
                    $price = $item['price'] ?? null;
                    $cnt = $item['cnt'] ?? null;

                    if (is_null($sku) || is_null($product_name) ||
                        is_null($supplier) || is_null($price) || is_null($cnt)) {
                        continue;
                    }

                    if (!$this->isValidSku($sku) || !$this->isValidPrice($price) || !$this->isValidCnt($cnt)) {
                        continue;
                    }

                    $price = $this->validPrice($price);
                    $cnt = $this->validCnt($cnt);

                    $response[] = [
                        'sku' => $sku,
                        'product_name' => $product_name,
                        'supplier' => $supplier,
                        'price' => $price,
                        'cnt' => $cnt
                    ];
                }
                break;
            case 'get':
            case 'delete':
                foreach ($data as $item) {
                    $sku = $item['sku'] ?? null;

                    if (is_null($sku)) {
                        continue;
                    }

                    if (!$this->isValidSku($sku)) {
                        continue;
                    }

                    $response[] = ['sku' => $sku];
                }
                break;
        }
        return $response;
    }

    /**
     * Insert data on table Price
     * @param array $data data
     * @return int number of recorded lines
     * @throws UserException
     */
    public function insert(array $data): int
    {
        try {
            $data = $this->validData($data, 'add');
            $cnt = 0;

            $result = $this->getDB()->prepare('INSERT INTO price (user_id, sku, product_name, supplier, price, cnt) 
                                                    VALUES (?,?,?,?,?,?)');
            foreach ($data as $item) {
                $result->execute([
                    $this->userId,
                    $item['sku'],
                    $item['product_name'],
                    $item['supplier'],
                    $item['price'],
                    $item['cnt']
                ]);
                $cnt++;
            }
            return $cnt;
        } catch (PDOException) {
            throw new UserException('UNKNOWN ERROR', API::FAILED_TO_ADD_AN_OBJECT);
        }
    }

    /**
     * Updating data
     * @param array $data API params
     * @return array Update data
     * @throws UserException
     */
    public function update(array $data): array
    {
        try {
            $data = $this->validData($data, 'update');
            $response = [];

            foreach ($data as $item) {
                if ($this->rowsFind($item['sku']) > 0) {
                    $result = $this->getDB()->prepare('UPDATE price
                                                    SET product_name = :product_name,
                                                        supplier     = :supplier,
                                                        price        = :price,
                                                        cnt          = :cnt
                                                    WHERE user_id = :user_id
                                                      AND sku = :sku');

                    $result->bindValue('product_name', $item['product_name']);
                    $result->bindValue('supplier', $item['supplier']);
                    $result->bindValue('price', $item['price']);
                    $result->bindValue('cnt', $item['cnt']);
                    $result->bindValue('user_id', $this->userId, PDO::PARAM_INT);
                    $result->bindValue('sku', $item['sku']);
                    $result->execute();

                    $response[] = ['sku' => $item['sku']];
                } else {
                    $result = $this->getDB()->prepare('INSERT INTO price 
                                                                (user_id, sku, product_name, supplier, price, cnt) 
                                                                 VALUES (?,?,?,?,?,?)');

                    $result->execute([
                        $this->userId,
                        $item['sku'],
                        $item['product_name'],
                        $item['supplier'],
                        $item['price'],
                        $item['cnt']
                    ]);

                    $response[] = [
                        ':sku' => $item['sku'],
                        ':product_name' => $item['product_name'],
                        ':supplier' => $item['supplier'],
                        ':price' => $item['price'],
                        ':cnt' => $item['cnt']
                    ];
                }
            }
            return $response;
        } catch (PDOException) {
            throw new UserException('UNKNOWN ERROR', API::FAILED_TO_UPDATE_THE_OBJECT);
        }
    }

    /**
     * Does the record exist
     * @param string $sku
     * @return int Number of lines
     */
    private function rowsFind(string $sku): int
    {
        $db = $this->getDB();

        $result = $db->prepare('SELECT * FROM price WHERE sku = :sku AND user_id = :userId');
        $result->execute([
            'sku' => $sku,
            'userId' => $this->userId
        ]);

        return $result->rowCount();
    }

    /**
     * Select rows
     * @param array $data array data
     * @return array response data
     * @throws UserException
     */
    public function select(array $data): array
    {
        try {
            $data = $this->validData($data, 'get');
            $response = [];

            $result = $this->getDB()->prepare('SELECT sku, product_name, supplier, price, cnt FROM price 
                                                    WHERE user_id = :userId AND sku = :sku');
            foreach ($data as $item) {
                $result->bindValue('sku', $item['sku']);
                $result->bindValue('userId', $this->userId, PDO::PARAM_INT);
                $result->execute();

                if ($result->rowCount() > 0) {
                    $response[] = $result->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $response[] = [
                        $item['sku'] => 'There is no such record',
                        'status' => API::OBJECT_NOT_FOUND
                    ];
                }
            }
            return $response;
        } catch (PDOException) {
            throw new UserException('UNKNOWN ERROR', API::UNKNOWN_ERROR);
        }
    }

    /**
     * Delete rows
     * @param array $data array data
     * @return array response data
     * @throws UserException
     */
    public function delete(array $data): array
    {
        try {
            $data = $this->validData($data, 'delete');
            $response = [];

            $result = $this->getDB()->prepare('DELETE price FROM price
                                                    WHERE user_id = :userId AND price.sku = :sku');
            foreach ($data as $item) {
                $sku = $item['sku'];

                if ($this->rowsFind($sku) > 0) {
                    $result->bindValue('userId', $this->userId, PDO::PARAM_INT);
                    $result->bindValue('sku', $sku);
                    $result->execute();

                    $response[] = [$sku];
                } else {
                    $response[] = [
                        $sku => 'There is no such record',
                        'status' => API::OBJECT_NOT_FOUND
                    ];
                }
            }
            return $response;
        } catch (PDOException) {
            throw new UserException('UNKNOWN ERROR', API::UNKNOWN_ERROR);
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws UserException
     */
    public function replace(array $data): array
    {
        $data = $this->validData($data, 'add');

        $result = $this->getDB()->prepare('DELETE price FROM price WHERE user_id = :userId');
        $result->bindValue('userId', $this->userId, PDO::PARAM_INT);
        $result->execute();

        $this->insert($data);
        return $data;
    }
}
