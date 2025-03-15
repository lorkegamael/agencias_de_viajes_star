<?php
class Reserva {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function crearReserva($idCliente, $idVuelo, $idHotel) {
        $sql = "INSERT INTO RESERVA (id_cliente, fecha_reserva, id_vuelo, id_hotel) 
                VALUES (:id_cliente, CURDATE(), :id_vuelo, :id_hotel)";

        return $this->db->ejecutarConsulta($sql, [
            ':id_cliente' => $idCliente,
            ':id_vuelo' => $idVuelo,
            ':id_hotel' => $idHotel
        ]);
    }

    public function obtenerReservasCliente($idCliente) {
        $sql = "SELECT r.*, v.origen, v.destino, v.fecha as fecha_vuelo, 
                h.nombre as hotel_nombre, h.ubicacion 
                FROM RESERVA r 
                LEFT JOIN VUELO v ON r.id_vuelo = v.id_vuelo 
                LEFT JOIN HOTEL h ON r.id_hotel = h.id_hotel 
                WHERE r.id_cliente = :id_cliente";

        return $this->db->ejecutarConsulta($sql, [
            ':id_cliente' => $idCliente
        ])->fetchAll();
    }
}
?>
