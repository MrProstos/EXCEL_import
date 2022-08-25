<?php

namespace App\Controllers;

use Core\View;

class Api extends \Core\Controller
{
    private const UNAUTHORIZED = 401;
    private const UNKNOWN_METHOD = 405;
    private const OBJECT_NOT_FOUND = 406;
    private const SCHEMA_ERROR_DATA = 407;
    private const FAILED_TO_ADD_AN_OBJECT = 408;
    private const THE_OBJECT_COULD_NOT_BE_DELETED = 409;
    private const FAILED_TO_UPDATE_THE_OBJECT = 410;
    private const UNKNOWN_ERROR = 499;

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
        $api = new \App\Models\Api();
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
            $headers = getallheaders();
            $token = $headers['Authorization'];

            $api = new \App\Models\Api();
            if (!$api->tokenVerification($token)) {
                throw new \Exception('Invalid token', self::UNAUTHORIZED);
            }

            $method = $_POST['method'];
            if (!method_exists($this, $method)) {
                throw new \Exception('There is no such method', self::UNKNOWN_METHOD);
            }

            $this->$method($_POST['params'], $token);
        } catch (\Exception $e) {
            $this->sendError($e->getCode(), $e->getMessage());
            return;
        }
    }

    /**
     * Get data
     * @param array $data array of data from the post request
     * @param string $token user token
     * @return void
     */
    private function get(array $data, string $token): void
    {
        try {
            foreach ($data as $item) {
                switch (true) {
                    case !array_key_exists('sku', $item):
                    case array_search('sku', $item) === '':
                        throw new \Exception('Invalid data of the param field', self::SCHEMA_ERROR_DATA);
                }
            }

            $api = new \App\Models\Api();

            $result = $api->get($data, $token);
            if ($result === []) {
                throw new \Exception('Failed to get data', self::FAILED_TO_ADD_AN_OBJECT,);
            }

            echo json_encode($result);
        } catch (\Exception $e) {
            $this->sendError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Adding data
     * @param array $data array of data from the post request
     * @param string $token user token
     * @return void
     */
    private function add(array $data, string $token): void
    {
        try {
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
                    case array_search('price', $item) === '':
                    case array_search('cnt', $item) === '':
                        throw new \Exception('Invalid data of the param field', self::SCHEMA_ERROR_DATA);
                }
            }

            $api = new \App\Models\Api();

            $result = $api->add($data, $token);
            if ($result === []) {
                throw new \Exception('Failed to add data', self::FAILED_TO_ADD_AN_OBJECT,);
            }

            echo json_encode($result);
        } catch (\Exception $e) {
            $this->sendError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Updating data
     * @param array $data array of data from the post request
     * @param string $token user token
     * @return void
     */
    private function update(array $data, string $token): void
    {
        try {
            foreach ($data as $item) {
                switch (true) {
                    case !array_key_exists('sku', $item):
                    case array_search('sku', $item) === '':
                        throw new \Exception('Invalid data of the param field', self::SCHEMA_ERROR_DATA);
                }
            }

            $api = new \App\Models\Api();
            $resul = $api->update($data, $token);

            if ($resul === []) {
                throw new \Exception('Failed to update data', self::FAILED_TO_UPDATE_THE_OBJECT);
            }
            echo json_encode($resul);

        } catch (\Exception $e) {
            $this->sendError($e->getCode(), $e->getMessage());
        }
    }

    private function delete(array $data, string $token)
    {
        echo json_encode($data); //TODO сделать
    }


}