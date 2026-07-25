<?php

require_once 'config.php';
require_once 'controladores/CamionController.php';

$controller = new CamionController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/sigeru/api-camiones';
$endpoint = str_replace($basePath, '', $uri);

switch ($method) {
    case 'GET':
        if ($endpoint === '/camiones') {
            $controller->getAll();
        } elseif (preg_match('/^\/camiones\/(\d+)$/', $endpoint, $matches)) {
            $controller->getById($matches[1]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if ($endpoint === '/camiones') {
            $controller->create($data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (preg_match('/^\/camiones\/(\d+)$/', $endpoint, $matches)) {
            $controller->update($matches[1], $data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'DELETE':
        if (preg_match('/^\/camiones\/(\d+)$/', $endpoint, $matches)) {
            $controller->delete($matches[1]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
