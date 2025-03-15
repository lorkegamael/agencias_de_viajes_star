<?php
session_start();
require_once 'config.php';
require_once 'database.php';

class ReservasManager {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Insertar reservas de ejemplo
    public function insertarReservasEjemplo() {
        $reservas = [
            ['1', '1', '1', '2025-02-20'],
            ['2', '1', '2', '2025-02-21'],
            ['3', '2', '1', '2025-02-22'],
            ['4', '2', '3', '2025-02-23'],
            ['5', '3', '2', '2025-02-24'],
            ['6', '1', '1', '2025-02-25'],
            ['7', '2', '1', '2025-02-26'],
            ['8', '3', '1', '2025-02-27'],
            ['9', '1', '3', '2025-02-28'],
            ['10', '2', '2', '2025-03-01']
        ];

        $sql = "INSERT INTO RESERVA (id_cliente, id_vuelo, id_hotel, fecha_reserva) 
                VALUES (:id_cliente, :id_vuelo, :id_hotel, :fecha_reserva)";

        $stmt = $this->db->prepare($sql);

        foreach ($reservas as $reserva) {
            try {
                $stmt->execute([
                    ':id_cliente' => $reserva[0],
                    ':id_vuelo' => $reserva[1],
                    ':id_hotel' => $reserva[2],
                    ':fecha_reserva' => $reserva[3]
                ]);
            } catch (PDOException $e) {
                error_log("Error al insertar reserva: " . $e->getMessage());
            }
        }
    }

    // Obtener todas las reservas con detalles
    public function obtenerReservasDetalladas() {
        $sql = "SELECT 
                    r.id_reserva,
                    r.id_cliente,
                    v.origen,
                    v.destino,
                    v.fecha as fecha_vuelo,
                    h.nombre as hotel,
                    h.ubicacion,
                    r.fecha_reserva
                FROM RESERVA r
                JOIN VUELO v ON r.id_vuelo = v.id_vuelo
                JOIN HOTEL h ON r.id_hotel = h.id_hotel
                ORDER BY r.fecha_reserva DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener hoteles con más de dos reservas
    public function obtenerHotelesPopulares() {
        $sql = "SELECT 
                    h.id_hotel,
                    h.nombre,
                    h.ubicacion,
                    COUNT(r.id_reserva) as total_reservas
                FROM HOTEL h
                LEFT JOIN RESERVA r ON h.id_hotel = r.id_hotel
                GROUP BY h.id_hotel, h.nombre, h.ubicacion
                HAVING COUNT(r.id_reserva) > 2
                ORDER BY total_reservas DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Página principal
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Reservas - Agencia de Viajes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stats-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Sistema de Reservas - Agencia de Viajes</h1>

    <?php
    try {
        $db = new PDO(
            "mysql:host=localhost;dbname=AGENCIA;charset=utf8",
            "root",
            "",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $reservasManager = new ReservasManager($db);

        // Mostrar todas las reservas
        $reservas = $reservasManager->obtenerReservasDetalladas();
        ?>

        <h2>Listado de Reservas</h2>
        <table>
            <thead>
            <tr>
                <th>ID Reserva</th>
                <th>Cliente</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Fecha Vuelo</th>
                <th>Hotel</th>
                <th>Ubicación</th>
                <th>Fecha Reserva</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reserva['id_reserva']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['id_cliente']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['origen']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['destino']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['fecha_vuelo']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['hotel']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['ubicacion']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['fecha_reserva']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Hoteles Populares (Más de 2 reservas)</h2>
        <?php
        $hotelesPopulares = $reservasManager->obtenerHotelesPopulares();
        ?>
        <table>
            <thead>
            <tr>
                <th>Hotel</th>
                <th>Ubicación</th>
                <th>Total Reservas</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($hotelesPopulares as $hotel): ?>
                <tr>
                    <td><?php echo htmlspecialchars($hotel['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($hotel['ubicacion']); ?></td>
                    <td><?php echo htmlspecialchars($hotel['total_reservas']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php
    } catch (PDOException $e) {
        echo "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>
</div>
</body>
</html>
