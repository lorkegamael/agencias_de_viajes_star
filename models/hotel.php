<?php
class Hotel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function buscarHoteles($ubicacion) {
        $sql = "SELECT * FROM HOTEL 
                WHERE ubicacion = :ubicacion 
                AND habitaciones_disponibles > 0";

        return $this->db->ejecutarConsulta($sql, [
            ':ubicacion' => $ubicacion
        ])->fetchAll();
    }

    public function actualizarHabitaciones($idHotel, $cantidad) {
        $sql = "UPDATE HOTEL 
                SET habitaciones_disponibles = habitaciones_disponibles - :cantidad 
                WHERE id_hotel = :id_hotel";

        return $this->db->ejecutarConsulta($sql, [
            ':cantidad' => $cantidad,
            ':id_hotel' => $idHotel
        ]);
    }
}
?>
