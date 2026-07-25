<?php

require_once __DIR__ . '/../modelos/CamionModel.php';

class CamionController
{
    private $modelo;

    public function __construct($conn)
    {
        $this->modelo = new CamionModel($conn);
    }

    public function getAll()
    {
        echo json_encode($this->modelo->getAll());
    }

    public function getById($id)
    {
        $camion = $this->modelo->getById($id);

        if ($camion) {
            echo json_encode($camion);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Camión no encontrado"]);
        }
    }

    public function create($data)
    {
        if (empty($data['matricula']) || empty($data['modelo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos obligatorios: matricula, modelo"]);
            return;
        }

        $result = $this->modelo->create($data);

        if (isset($result['error'])) {
            http_response_code(400);
        } else {
            http_response_code(201);
        }

        echo json_encode($result);
    }

    public function update($id, $data)
    {
        if (empty($data['matricula']) || empty($data['modelo']) || empty($data['estado'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos obligatorios: matricula, modelo, estado"]);
            return;
        }

        $result = $this->modelo->update($id, $data);

        if (isset($result['error'])) {
            http_response_code(400);
        }

        echo json_encode($result);
    }

    public function delete($id)
    {
        $result = $this->modelo->delete($id);

        if (isset($result['error'])) {
            http_response_code(400);
        }

        echo json_encode($result);
    }
}
