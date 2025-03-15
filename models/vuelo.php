<?php
class Vuelo {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function buscarVuelos($origen, $destino, $fecha) {
        $sql = "SELECT * FROM VUELO 
                WHERE origen = :origen 
                AND destino = :destino 
                AND fecha = :fecha 
                AND plazas_disponibles > 0";

        $params = [
            ':origen' => $origen,
            ':destino' => $destino,
            ':fecha' => $fecha
        ];

        return $this->db->ejecutarConsulta($sql, $params)->fetchAll();
    }

    public function actualizarPlazas($idVuelo, $cantidad) {
        $sql = "UPDATE VUELO 
                SET plazas_disponibles = plazas_disponibles - :cantidad 
                WHERE id_vuelo = :id_vuelo";

        return $this->db->ejecutarConsulta($sql, [
            ':cantidad' => $cantidad,
            ':id_vuelo' => $idVuelo
        ]);
    }
}
?>
