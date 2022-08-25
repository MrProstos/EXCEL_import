<?php

namespace App\Controllers;

use Core\View;

class Api extends \Core\Controller
{

    private const OBJECT_NOT_FOUND = 100;
    private const SCHEMA_ERROR_DATA = 101;
    private const FAILED_TO_ADD_AN_OBJECT = 102;
    private const THE_OBJECT_COULD_NOT_BE_DELETED = 103;
    private const FAILED_TO_UPDATE_THE_OBJECT = 104;
    private const NOT_AUTHORIZED = 105;
    private const UNKNOWN_METHOD = 106;
    private const UNKNOWN_ERROR = 199;

    public function indexAction()
    {
        View::renderTemplate('api.twig', ['title' => 'API']);
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
        $headers = getallheaders();
        $api = new \App\Models\Api();

        if (!$api->tokenVerification($headers['Authorization'])) {
            echo self::NOT_AUTHORIZED;
            return;
        }

        $method = $_POST['method'];
        try {
            $this->$method($_POST['params']);
        } catch (\Exception) {
            echo self::UNKNOWN_METHOD;
            return;
        }
    }

    private function add(array $data)
    {
        echo json_encode($data);
    }

    private function update(array $data)
    {
        echo json_encode($data);
    }

    private function delete(array $data)
    {
        echo json_encode($data);
    }

    private function get(array $data)
    {
        echo json_encode($data);
    }


}