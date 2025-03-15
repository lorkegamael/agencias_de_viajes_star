<?php
// Conexión a la base de datos
$conexion = new PDO(
    "mysql:host=localhost;dbname=AGENCIA;charset=utf8",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Consulta simple para mostrar todas las reservas
$consultaReservas = "SELECT 
    r.id_reserva,
    r.id_cliente,
    v.origen,
    v.destino,
    v.fecha as fecha_vuelo,
    h.nombre as hotel,
    h.ubicacion
FROM RESERVA r
JOIN VUELO v ON r.id_vuelo = v.id_vuelo
JOIN HOTEL h ON r.id_hotel = h.id_hotel
ORDER BY r.id_reserva";

// Consulta avanzada para hoteles con más de dos reservas
$consultaHoteles = "SELECT 
    h.id_hotel,
    h.nombre,
    h.ubicacion,
    COUNT(r.id_reserva) as total_reservas
FROM HOTEL h
LEFT JOIN RESERVA r ON h.id_hotel = r.id_hotel
GROUP BY h.id_hotel, h.nombre, h.ubicacion
HAVING COUNT(r.id_reserva) > 2
ORDER BY total_reservas DESC";
?>
