<?php

namespace App\Models;

use Core\UserException;
use PDO;

class API extends \Core\Model
{
    public const UNAUTHORIZED = 401;
    public const UNKNOWN_METHOD = 405;
    public const OBJECT_NOT_FOUND = 406;
    public const SCHEMA_ERROR_DATA = 407;
    public const FAILED_TO_ADD_AN_OBJECT = 408;
    public const THE_OBJECT_COULD_NOT_BE_DELETED = 409;
    public const FAILED_TO_UPDATE_THE_OBJECT = 410;
    public const THE_OBJECT_COULD_NOT_BE_REPLACED = 411;
    public const UNKNOWN_ERROR = 500;

    private Price $dbPrice;

    /**
     * @param string $ApiToken
     * @return void
     */
    public function setUserId(string $ApiToken): void
    {
        $dbPrice = new Price();
        $dbPrice->setUserIdByApiToken($ApiToken);
        $this->dbPrice = $dbPrice;
    }


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
     * @param string $apiToken Authorization token
     * @return bool
     */
    public function tokenVerification(string $apiToken): bool
    {
        if ($apiToken != '') {
            $db = $this->getDB();

            $result = $db->prepare('SELECT id FROM reg_user WHERE api_token = :token');
            $result->execute([':token' => $apiToken]);
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
     * @return array Get data
     * @throws UserException
     */
    public function getActionDB(array $data): array
    {
        return $this->dbPrice->select($data);
    }

    /**
     * Adds entries to the database
     * @param array $data API params
     * @return array Added data
     * @throws UserException
     */
    public function addActionDB(array $data): array
    {
        $this->dbPrice->insert($data);
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
        return $this->dbPrice->update($data);
    }

    /**
     * Delete data
     * @param array $data API params
     * @return array Delete data
     * @throws UserException
     */
    public function deleteActionDB(array $data): array
    {
        return $this->dbPrice->delete($data);
    }

    /**
     * Remove old values and add new ones
     * @param array $data API params
     * @return array
     * @throws UserException
     */
    public function replaceActionDB(array $data): array
    {

        return $this->dbPrice->replace($data);
    }

}
