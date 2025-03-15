<?php
session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'models/Vuelo.php';
require_once 'models/Hotel.php';
require_once 'models/Reserva.php';

try {
    // Conexión a las bases de datos
    $dbAgencia = new DatabaseConnection(DB_NAME_AGENCIA);
    $dbViajes = new DatabaseConnection(DB_NAME_VIAJES);
    $dbTurismo = new DatabaseConnection(DB_NAME_TURISMO);

    // Instanciar modelos
    $vuelo = new Vuelo($dbAgencia->getConexion());
    $hotel = new Hotel($dbAgencia->getConexion());
    $reserva = new Reserva($dbAgencia->getConexion());

    // Procesar formularios
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['buscar_vuelos'])) {
            $vuelos = $vuelo->buscarVuelos(
                $_POST['origen'],
                $_POST['destino'],
                $_POST['fecha']
            );
        }

        if (isset($_POST['buscar_hoteles'])) {
            $hoteles = $hotel->buscarHoteles($_POST['ubicacion']);
        }

        if (isset($_POST['crear_reserva'])) {
            $reserva->crearReserva(
                $_POST['id_cliente'],
                $_POST['id_vuelo'],
                $_POST['id_hotel']
            );

            // Actualizar disponibilidad
            $vuelo->actualizarPlazas($_POST['id_vuelo'], 1);
            $hotel->actualizarHabitaciones($_POST['id_hotel'], 1);
        }
    }

} catch(Exception $e) {
    error_log("Error: " . $e->getMessage());
    $error = "Ha ocurrido un error en el sistema";
} finally {
    // Cerrar conexiones
    if (isset($dbAgencia)) $dbAgencia->cerrarConexion();
    if (isset($dbViajes)) $dbViajes->cerrarConexion();
    if (isset($dbTurismo)) $dbTurismo->cerrarConexion();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agencia de Viajes</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<header>
    <h1>Agencia de Viajes</h1>
</header>

<main>
    <!-- Formulario de búsqueda de vuelos -->
    <section class="search-section">
        <h2>Buscar Vuelos</h2>
        <form method="POST" action="">
            <input type="text" name="origen" placeholder="Origen" required>
            <input type="text" name="destino" placeholder="Destino" required>
            <input type="date" name="fecha" required>
            <button type="submit" name="buscar_vuelos">Buscar Vuelos</button>
        </form>
    </section>

    <!-- Formulario de búsqueda de hoteles -->
    <section class="search-section">
        <h2>Buscar Hoteles</h2>
        <form method="POST" action="">
            <input type="text" name="ubicacion" placeholder="Ubicación" required>
            <button type="submit" name="buscar_hoteles">Buscar Hoteles</button>
        </form>
    </section>

    <!-- Mostrar resultados -->
    <?php if (isset($vuelos)): ?>
        <section class="results-section">
            <h2>Vuelos Disponibles</h2>
            <div class="results-grid">
                <?php foreach ($vuelos as $vuelo): ?>
                    <div class="result-card">
                        <h3><?php echo $vuelo['origen'] . ' → ' . $vuelo['destino']; ?></h3>
                        <p>Fecha: <?php echo $vuelo['fecha']; ?></p>
                        <p>Precio: $<?php echo $vuelo['precio']; ?></p>
                        <p>Plazas disponibles: <?php echo $vuelo['plazas_disponibles']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (isset($hoteles)): ?>
        <section class="results-section">
            <h2>Hoteles Disponibles</h2>
            <div class="results-grid">
                <?php foreach ($hoteles as $hotel): ?>
                    <div class="result-card">
                        <h3><?php echo $hotel['nombre']; ?></h3>
                        <p>Ubicación: <?php echo $hotel['ubicacion']; ?></p>
                        <p>Tarifa por noche: $<?php echo $hotel['tarifa_noche']; ?></p>
                        <p>Habitaciones disponibles: <?php echo $hotel['habitaciones_disponibles']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2025 Agencia de Viajes. Todos los derechos reservados.</p>
</footer>
</body>
</html>
