<?php

namespace App\Controllers;

use App\Models\Regular;
use Core\UserException;
use Core\View;

class API extends \Core\Controller
{
    // TODO перенести в Model
    public const UNAUTHORIZED = 401;
    public const UNKNOWN_METHOD = 405;
    public const OBJECT_NOT_FOUND = 406;
    public const SCHEMA_ERROR_DATA = 407;
    public const FAILED_TO_ADD_AN_OBJECT = 408;
    public const THE_OBJECT_COULD_NOT_BE_DELETED = 409;
    public const FAILED_TO_UPDATE_THE_OBJECT = 410;
    public const THE_OBJECT_COULD_NOT_BE_REPLACED = 411;
    public const UNKNOWN_ERROR = 499;

    public function indexAction()
    {
        View::renderTemplate('api.twig', ['title' => 'API']);
    }

    /**
     * Send an error message
     * @param int $errorCode error code
     * @param string $errorString error message
     * @return void
     */
    private function sendError(int $errorCode, string $errorString): void
    {
        http_response_code($errorCode);
        echo json_encode(['error_code' => $errorCode, 'errorString' => $errorString]);
    }

    /**
     * Get token
     * @return void
     */
    public function getTokenAction(): void
    {
        $api = new \App\Models\API();
        $token = $api->getToken($this->route_params['hash']);
        echo json_encode(['token' => $token]);
    }

    /**
     * Calls the method specified in the POST request
     * @return void
     */
    public function chooseMethodAction(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $headers = getallheaders();
            if (!isset($headers['Authorization'])) {
                throw new UserException('No token', self::UNAUTHORIZED);
            }

            $api = new \App\Models\API();
            if (!$api->tokenVerification($headers['Authorization'])) {
                throw new UserException('Invalid token', self::UNAUTHORIZED);
            }

            if (!isset($data['method']) || !isset($data['params'])) {
                throw new UserException('Invalid data of the param field', self::SCHEMA_ERROR_DATA);
            }

            $method = $data['method'] . 'ActionDB';
            $params = $data['params'];
            if (!method_exists($api, $method)) {
                throw new UserException('There is no such method', self::UNKNOWN_METHOD);
            }

            if (!$this->isCorrectData($data['method'], $params)) {
                throw new UserException('Invalid data of the param field', self::SCHEMA_ERROR_DATA);
            }

            $result = $api->$method($params);

            header('Content-Type: application/json', true);
            echo json_encode($result);
        } catch (UserException $e) {
            $this->sendError($e->getCode(), $e->getMessage());
            return;
        }
    }

    /**
     * Checks the validity of the data for the method
     * @param string $method Name of the method
     * @param array $data Data
     * @return bool
     */
    private function isCorrectData(string $method, array $data): bool
    {
        $reg = new Regular();
        switch ($method) {
            case 'get':
            case 'delete':
                foreach ($data as $item) {
                    switch (true) {
                        case !array_key_exists('sku', $item):
                        case array_search('sku', $item) === '':
                            return false;
                    }
                }
                break;
            case 'add':
            case 'update':
            case 'replace':
                foreach ($data as $item) {
                    switch (true) {
                        case !array_key_exists('sku', $item):
                        case !array_key_exists('product_name', $item):
                        case !array_key_exists('supplier', $item):
                        case !array_key_exists('price', $item):
                        case !array_key_exists('cnt', $item):

                        case array_search('sku', $item) === '':
                        case array_search('product_name', $item) === '':
                        case array_search('supplier', $item) === '':
                        case array_search('price', $item) === null:
                        case array_search('cnt', $item) === null:

                        case !$reg->isValidSku($item['sku']):
                        case !$reg->isValidPrice($item['price']):
                        case !$reg->isValidCnt($item['cnt']):
                            return false;
                    }
                }
                break;
        }
        return true;
    }
}