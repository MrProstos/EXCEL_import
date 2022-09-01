<?php

namespace App\Models;

use Core\UserException;
use PDO;

class API extends \Core\Model
{
    /**
     * @var int
     */
    private int $userId;

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

            $result = $db->prepare('SELECT id FROM reg_user WHERE api_token = :token');
            $result->execute([':token' => $token]);
            if ($result->rowCount() != 1) {
                return false;
            }

            $row = $result->fetch();
            $this->userId = $row['id'];

            return true;
        }
        return false;
    }

    /**
     * Getting records from a database
     * @param array $data API params
     * @return array Get data
     * @throws UserException
     */
    public function getActionDB(array $data): array
    {
        $newData = $this->selectRequest($data);
        if (!$newData) {
            throw new UserException('UNKNOWN ERROR', \App\Controllers\API::UNKNOWN_ERROR);
        };
        return $newData;
    }

    /**
     * Adds entries to the database
     * @param array $data API params
     * @return array Added data
     * @throws UserException
     */
    public function addActionDB(array $data): array
    {
        if (!$this->insertRequest($data)) {
            throw new UserException('Failed to add an object', \App\Controllers\API::FAILED_TO_ADD_AN_OBJECT);
        }
        return $data;
    }

    /**
     * Updating data
     * @param array $data API params
     * @return array Update data
     * @throws UserException
     */
    public function updateActionDB(array $data): array
    {
        try {
            $db = $this->getDB();

            $response = [];
            foreach ($data as $item) {

                // If there is a sku entry
                if ($this->rowsFind($item['sku']) > 0) {
                    $result = $db->prepare('UPDATE price INNER JOIN reg_user on reg_user.id = price.user_id
                                            SET product_name = :product_name,
                                                supplier     = :supplier,
                                                price        = :price,
                                                cnt          = :cnt
                                            WHERE price.user_id = :userId
                                              AND price.sku = :sku');

                    $result->execute([
                        ':userId' => $this->userId,
                        ':sku' => $item['sku'],
                        ':product_name' => $item['product_name'],
                        ':supplier' => $item['supplier'],
                        ':price' => $item['price'],
                        ':cnt' => $item['cnt']
                    ]);

                    $response[] = ['sku' => $item['sku']];
                } else {
                    $this->insertRequest($data);

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
            throw new UserException('Failed to update the object', \App\Controllers\API::FAILED_TO_UPDATE_THE_OBJECT);
        }
    }

    /**
     * Delete data
     * @param array $data API params
     * @return array Delete data
     * @throws UserException
     */
    public function deleteActionDB(array $data): array
    {
        $result = $this->deleteRequest($data);
        if ($result == []) {
            throw new UserException('The object could not be deleted', \App\Controllers\API::THE_OBJECT_COULD_NOT_BE_DELETED);
        }

        return $result;
    }

    /**
     * Remove old values and add new ones
     * @param array $data API params
     * @return array
     * @throws UserException
     */
    public function replaceActionDB(array $data): array
    {
        try {
            $db = $this->getDB();
            $result = $db->prepare('DELETE price
                                            FROM price
                                                     INNER JOIN reg_user on price.user_id = reg_user.id
                                            WHERE reg_user.id = :userId');
            $result->execute([':userId' => $this->userId]);

            if (!$this->insertRequest($data)) {
                throw new UserException('Failed to add an object', \App\Controllers\API::FAILED_TO_ADD_AN_OBJECT);
            }

            return $data;
        } catch (\PDOException) {
            throw new UserException('The object could not be replaced', \App\Controllers\API::THE_OBJECT_COULD_NOT_BE_REPLACED);

        }
    }

    /**
     * Request to get a rows
     * @param array $data API params
     * @return array|bool
     */
    private function selectRequest(array $data): array|bool
    {
        try {
            $db = $this->getDB();
            $rowExists = $db->prepare('SELECT sku, product_name, supplier, price, cnt
                                                FROM price
                                                         INNER JOIN reg_user on price.user_id = reg_user.id
                                                WHERE price.user_id = :userId
                                                  AND sku = :sku');

            $newData = [];

            foreach ($data as $item) {
                $rowExists->execute([
                    ':sku' => $item['sku'],
                    ':userId' => $this->userId
                ]);

                if ($rowExists->rowCount() > 0) {
                    foreach ($rowExists->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $newData[] = $row;
                    }
                } else {
                    $newData[] = [
                        'sku' => $item['sku'],
                        'status' => \App\Controllers\API::OBJECT_NOT_FOUND,
                        'message' => 'Object not found'
                    ];
                }
            }
            return $newData;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }


    /**
     * Request to insert a rows
     * @param array $data API params
     * @return bool
     */
    private function insertRequest(array $data): bool
    {
        try {
            $db = $this->getDB();
            $result = $db->prepare('INSERT INTO price (user_id, sku, product_name, supplier, price, cnt)
                                            values (:userId, :sku, :product_name, :supplier, :price, :cnt)');

            foreach ($data as $item) {
                $result->execute([
                    ':userId' => $this->userId,
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
     */
    private function deleteRequest(array $data): array
    {
        try {
            $db = $this->getDB();
            /*TODO implode() DELETE FROM table WHERE sku IN (xxx, yyyy, bbbb) изменить.
             TODO Если удалять все записи через IN то как потом найти каких записей не было?*/
            $result = $db->prepare('DELETE price
                                            FROM price
                                                     INNER JOIN reg_user on price.user_id = reg_user.id
                                            WHERE reg_user.id = :userId AND price.sku = :sku');

            $response = [];
            foreach ($data as $item) {

                if ($this->rowsFind($item['sku']) > 0) {
                    $result->execute([
                        ':userId' => $this->userId,
                        ':sku' => $item['sku']
                    ]);

                    $response[] = ['sku' => $item['sku']];
                } else {
                    $response[] = [
                        'sku' => $item['sku'],
                        'status' => \App\Controllers\API::THE_OBJECT_COULD_NOT_BE_DELETED,
                        'message' => 'The object could not be deleted'
                    ];
                }
            }

            return $response;
        } catch (\PDOException) {
            return [];
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
}

