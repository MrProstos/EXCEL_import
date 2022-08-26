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
     */
    public function get(array $data, string $token): array
    {
        try {
            $db = $this->getDB();
            $result = $db->prepare('SELECT sku, product_name, supplier, price, cnt FROM price
                                            INNER JOIN reg_user on price.user_id = (SELECT id FROM reg_user WHERE api_token = :token)
                                            WHERE sku = :sku');

            foreach ($data as &$item) {
                $result->execute([':token' => $token, ':sku' => $item['sku']]);
                foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $item = $row;
                }
            }

            return $data;
        } catch (\PDOException) {
            return [];
        }
    }

    /**
     * Adds entries to the database
     * @param array $data API params
     * @param string $token Authorization token
     * @return array Added data
     */
    public function add(array $data, string $token): array
    {
        try {
            $db = $this->getDB();
            $result = $db->prepare('INSERT INTO price (user_id, sku, product_name, supplier, price, cnt) 
                                            SELECT (SELECT reg_user.id
                                                FROM reg_user
                                                WHERE reg_user.api_token = :token),
                                               :sku,
                                               :product_name,
                                               :supplier,
                                               :price,
                                               :cnt');

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
            return $data;
        } catch (\PDOException) {
            return [];
        }
    }

    /**
     * Updating data
     * @param array $data API params
     * @param string $token Authorization token
     * @return array Update data
     */
    public function update(array $data, string $token): array
    {
        try {
            $db = $this->getDB();
            // Record search sku
            $rowExists = $db->prepare('SELECT sku
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
                    $result = $db->prepare('INSERT INTO price (user_id, sku, product_name, supplier, price, cnt)
                                                    SELECT (SELECT reg_user.id 
                                                            FROM reg_user 
                                                            WHERE api_token = :token), 
                                                            :sku, 
                                                            :product_name, 
                                                            :supplier, 
                                                            :price, 
                                                            :cnt');

                    $result->execute([
                        ':token' => $token,
                        ':sku' => $item['sku'],
                        ':product_name' => $item['product_name'],
                        ':supplier' => $item['supplier'],
                        ':price' => $item['price'],
                        ':cnt' => $item['cnt']
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
        } catch (\PDOException) {
            return [];
        }
    }

    /**
     * Delete data
     * @param array $data API params
     * @param string $token Authorization token
     * @return array Delete data
     */
    public function delete(array $data, string $token): array
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
            return $data;
        } catch (\PDOException) {
            return [];
        }
    }

    public function replace(array $data, string $token)
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

                $result = $db->prepare('INSERT INTO price (user_id, sku, product_name, supplier, price, cnt)
                                                    SELECT (SELECT reg_user.id 
                                                            FROM reg_user 
                                                            WHERE api_token = :token), 
                                                            :sku, 
                                                            :product_name, 
                                                            :supplier, 
                                                            :price, 
                                                            :cnt');

                $result->execute([
                    ':token' => $token,
                    ':sku' => $item['sku'],
                    ':product_name' => $item['product_name'],
                    ':supplier' => $item['supplier'],
                    ':price' => $item['price'],
                    ':cnt' => $item['cnt']
                ]);
            }
        } catch (\PDOException) {
            return [];
        }
    }

    /**
     * @param array $data
     * @param string $token
     * @return bool
     */
    private function insertRequest(array $data, string $token): bool
    {
        try {
            $db = $this->getDB();
            $result = $db->prepare('INSERT INTO price (user_id, sku, product_name, supplier, price, cnt)
                                                    SELECT (SELECT reg_user.id 
                                                            FROM reg_user 
                                                            WHERE api_token = :token), 
                                                            :sku, 
                                                            :product_name, 
                                                            :supplier, 
                                                            :price, 
                                                            :cnt');

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
     * @param array $data
     * @param string $token
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