<?php

namespace App\Controllers;

use Core\UserException;
use Core\View;

class API extends \Core\Controller
{

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
                throw new UserException('No token', \App\Models\API::UNAUTHORIZED);
            }

            $api = new \App\Models\API();
            if (!$api->tokenVerification($headers['Authorization'])) {
                throw new UserException('Invalid token', \App\Models\API::UNAUTHORIZED);
            }

            if (!isset($data['method']) || !isset($data['params'])) {
                throw new UserException('Invalid data of the param field', \App\Models\API::SCHEMA_ERROR_DATA);
            }

            $method = $data['method'] . 'ActionDB';
            if (!method_exists($api, $method)) {
                throw new UserException('There is no such method', \App\Models\API::UNKNOWN_METHOD);
            }

            $headers = getallheaders();

            $data = json_decode(file_get_contents('php://input'), true);
            $method = $data['method'] . 'ActionDB';

            $api = new \App\Models\API();
            $api->setUserId($headers['Authorization']);

            $result = $api->$method($data['params']);

            header('Content-Type: application/json', true);
            echo json_encode($result);
        } catch (UserException $e) {
            $this->sendError($e->getCode(), $e->getMessage());
            return;
        }
    }
}
