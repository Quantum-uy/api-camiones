<?php

class CamionModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = "SELECT c.*, tr.nombre AS tipo_residuo_nombre
                FROM camion c
                LEFT JOIN tipo_residuo tr ON c.id_tipo_residuo = tr.id_tipo_residuo
                ORDER BY c.id_camion DESC";

        $result = mysqli_query($this->conn, $sql);
        $camiones = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $camiones[] = $row;
        }

        return $camiones;
    }

    public function getById($id)
    {
        $stmt = mysqli_prepare($this->conn,
            "SELECT c.*, tr.nombre AS tipo_residuo_nombre
             FROM camion c
             LEFT JOIN tipo_residuo tr ON c.id_tipo_residuo = tr.id_tipo_residuo
             WHERE c.id_camion = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        $checkStmt = mysqli_prepare($this->conn, "SELECT id_camion FROM camion WHERE matricula = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $data['matricula']);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            return ["error" => "Ya existe un camión con esa matrícula"];
        }

        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO camion (matricula, modelo, estado, id_tipo_residuo)
             VALUES (?, ?, ?, ?)"
        );

        $estado = $data['estado'] ?? 'disponible';
        $tipoResiduo = $data['id_tipo_residuo'] ?? null;

        mysqli_stmt_bind_param($stmt, "sssi",
            $data['matricula'],
            $data['modelo'],
            $estado,
            $tipoResiduo
        );

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Camión creado", "id" => mysqli_insert_id($this->conn)];
        }

        return ["error" => "No se pudo crear el camión"];
    }

    public function update($id, $data)
    {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE camion SET matricula = ?, modelo = ?, estado = ?, id_tipo_residuo = ?
             WHERE id_camion = ?"
        );

        $tipoResiduo = $data['id_tipo_residuo'] ?? null;

        mysqli_stmt_bind_param($stmt, "sssii",
            $data['matricula'],
            $data['modelo'],
            $data['estado'],
            $tipoResiduo,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Camión actualizado"];
        }

        return ["error" => "No se pudo actualizar el camión"];
    }

    public function delete($id)
    {
        $stmt = mysqli_prepare($this->conn, "DELETE FROM camion WHERE id_camion = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Camión eliminado"];
        }

        return ["error" => "No se pudo eliminar el camión"];
    }
}
