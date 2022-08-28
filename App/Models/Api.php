<?php

namespace App\Models;

use PDO;

class Api extends \Core\Model
{
    /**
     * Get token
     * @param string $hash Cookies hash
     * @return string API token
     */
    public function getToken(string $hash): string
    {
        $db = $this->getDB();

        $result = $db->prepare('SELECT api_token FROM reg_user WHERE passwd = :hash');
        $result->execute([':hash' => $hash]);

        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        $token = $row[0]['api_token'];

        if ($token === '') {
            $token = uniqid('', true);
            $result = $db->prepare('UPDATE reg_user SET api_token = :token WHERE passwd = :hash');
            $result->execute([':token' => $token, ':hash' => $hash]);
        }
        return $token;
    }

    /**
     * Checks the validity of the token
     * @param string $token Authorization token
     * @return bool
     */
    public function tokenVerification(string $token): bool
    {
        if ($token != '') {
            $db = $this->getDB();

            $result = $db->prepare('SELECT * FROM reg_user WHERE api_token = :token');
            $result->execute([':token' => $token]);
            if ($result->rowCount() != 1) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Getting records from a database
     * @param array $data API params
     * @param string $token Authorization token
     * @return array Get data
     * @throws \Exception
     */
    public function getActionDB(array $data, string $token): array
    {
        $newData = $this->selectRequest($data, $token);
        if (!$newData) {
            throw new \Exception('UNKNOWN ERROR', \App\Controllers\Api::UNKNOWN_ERROR);
        };
        return $newData;
    }

    /**
     * Adds entries to the database
     * @param array $data API params
     * @param string $token Authorization token
     * @return array Added data
     * @throws \Exception
     */
    public function addActionDB(array $data, string $token): array
    {
        if (!$this->insertRequest($data, $token)) {
            throw new \Exception('Failed to add an object', \App\Controllers\Api::FAILED_TO_ADD_AN_OBJECT);
        }
        return $data;
    }

    /**
     * Updating data
     * @param array $data API params
     * @param string $token Authorization token
     * @return array Update data
     * @throws \Exception
     */
    public function updateActionDB(array $data, string $token): array
    {
        try {
            $db = $this->getDB();
            // Record search sku
            $rowExists = $db->prepare('SELECT *
                                                FROM price
                                                         INNER JOIN reg_user ru on price.user_id = ru.id
                                                WHERE price.user_id = (SELECT id FROM reg_user WHERE api_token = :token)
                                                  AND sku = :sku ');

            $response = [];

            foreach ($data as $item) {
                $rowExists->execute([':token' => $token, ':sku' => $item['sku']]);
                // If there is a sku entry
                if ($rowExists->rowCount() != 0) {
                    $result = $db->prepare('UPDATE price INNER JOIN reg_user on reg_user.id = price.user_id
                                            SET product_name = :product_name,
                                                supplier     = :supplier,
                                                price        = :price,
                                                cnt          = :cnt
                                            WHERE price.user_id = (SELECT reg_user.id FROM reg_user WHERE api_token = :token)
                                              AND price.sku = :sku');

                    $result->execute([
                        ':token' => $token,
                        ':sku' => $item['sku'],
                        ':product_name' => $item['product_name'],
                        ':supplier' => $item['supplier'],
                        ':price' => $item['price'],
                        ':cnt' => $item['cnt']
                    ]);

                    $response[] = ['sku' => $item['sku']];
                } else {
                    $this->insertRequest($data, $token);

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
        } catch (\PDOException) {
            throw new \Exception('Failed to update the object', \App\Controllers\Api::FAILED_TO_UPDATE_THE_OBJECT);
        }
    }

    /**
     * Delete data
     * @param array $data API params
     * @param string $token Authorization token
     * @return array Delete data
     * @throws \Exception
     */
    public function deleteActionDB(array $data, string $token): array
    {
        if (!$this->deleteRequest($data, $token)) {
            throw new \Exception('The object could not be deleted', \App\Controllers\Api::THE_OBJECT_COULD_NOT_BE_DELETED);
        }
        return $data;
    }

    /**
     * Remove old values and add new ones
     * @param array $data API params
     * @param string $token Authorization token
     * @return array
     * @throws \Exception
     */
    public function replaceActionDB(array $data, string $token): array
    {
        try {
            $db = $this->getDB();
            $result = $db->prepare('DELETE price
                                            FROM price
                                                     INNER JOIN reg_user on price.user_id = reg_user.id
                                            WHERE reg_user.id = (SELECT id FROM reg_user WHERE api_token = :token)');
            $result->execute([':token' => $token]);

            if (!$this->insertRequest($data, $token)) {
                throw new \Exception('Failed to add an object', \App\Controllers\Api::FAILED_TO_ADD_AN_OBJECT);
            }

            return $data;
        } catch (\PDOException) {
            throw new \Exception('The object could not be replaced', \App\Controllers\Api::THE_OBJECT_COULD_NOT_BE_REPLACED);

        }
    }

    /**
     * Request to get a rows
     * @param array $data API params
     * @param string $token Authorization token
     * @return array|bool
     */
    private function selectRequest(array $data, string $token): array|bool
    {
        try {
            $db = $this->getDB();
            $rowExists = $db->prepare('SELECT sku, product_name, supplier, price, cnt
                                                FROM price
                                                         INNER JOIN reg_user ru on price.user_id = ru.id
                                                WHERE price.user_id = (SELECT id FROM reg_user WHERE api_token = :token)
                                                  AND sku = :sku ');

            $newData = [];

            foreach ($data as $item) {
                $rowExists->execute([
                    ':token' => $token,
                    ':sku' => $item['sku']
                ]);

                if ($rowExists->rowCount() != 0) {
                    foreach ($rowExists->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $newData[] = $row;
                    }
                } else {
                    $newData[] = ['sku' => $item['sku'], 'status' => \App\Controllers\Api::OBJECT_NOT_FOUND];
                }
            }
            return $newData;
        } catch (\PDOException) {
            return false;
        }
    }


    /**
     * Request to insert a rows
     * @param array $data API params
     * @param string $token Authorization token
     * @return bool
     */
    private function insertRequest(array $data, string $token): bool
    {
        try {
            $db = $this->getDB();
            $result = $db->prepare('INSERT INTO price (user_id, sku, product_name, supplier, price, cnt)
                                            SELECT (SELECT reg_user.id FROM reg_user WHERE api_token = :token), 
                                                   :sku, :product_name, :supplier, :price, :cnt');

            foreach ($data as $item) {
                $result->execute([
                    ':token' => $token,
                    ':sku' => $item['sku'],
                    ':product_name' => $item['product_name'],
                    ':supplier' => $item['supplier'],
                    ':price' => $item['price'],
                    ':cnt' => $item['cnt']
                ]);
            }
            return true;
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * Request to delete a rows
     * @param array $data API params
     * @param string $token Authorization token
     * @return bool
     */
    private function deleteRequest(array $data, string $token): bool
    {
        try {
            $db = $this->getDB();
            $result = $db->prepare('DELETE price
                                            FROM price
                                                     INNER JOIN reg_user on price.user_id = reg_user.id
                                            WHERE reg_user.id = (SELECT id FROM reg_user WHERE api_token = :token)
                                              AND price.sku = :sku');

            foreach ($data as $item) {
                $result->execute([
                    ':token' => $token,
                    ':sku' => $item['sku']
                ]);
            }
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}

// TODO Данные в виде запроса к АПИ могут быть как в виде одиночных позиции, так и в виде массива товаров.
// TODO Дополнительно нужен standalone скрипт, который будет эммулировать запросы к API в части добавления данных. В качестве данных можно использовать случайные генерируенмые значения.