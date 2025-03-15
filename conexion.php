<?php
require_once 'conexion.php';

try {
    // Insertar vuelos de ejemplo
    $vuelos = [
        ['Madrid', 'Barcelona', '2024-02-15', 150, 89.99],
        ['Barcelona', 'París', '2024-02-20', 200, 159.99],
        ['Madrid', 'Londres', '2024-02-25', 180, 199.99]
    ];

    $sqlVuelo = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) 
                 VALUES (?, ?, ?, ?, ?)";
    $stmtVuelo = $conexion->prepare($sqlVuelo);

    foreach ($vuelos as $vuelo) {
        $stmtVuelo->execute($vuelo);
    }

    // Insertar hoteles de ejemplo
    $hoteles = [
        ['Hotel Plaza', 'Madrid', 50, 120.00],
        ['Grand Hotel', 'Barcelona', 75, 150.00],
        ['Royal Palace', 'París', 100, 200.00]
    ];

    $sqlHotel = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) 
                 VALUES (?, ?, ?, ?)";
    $stmtHotel = $conexion->prepare($sqlHotel);

    foreach ($hoteles as $hotel) {
        $stmtHotel->execute($hotel);
    }

    echo "Datos de ejemplo insertados correctamente";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
