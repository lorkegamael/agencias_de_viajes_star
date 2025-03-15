<?php
require_once 'conexion.php';

function validarDatos($datos) {
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim(htmlspecialchars($valor));
    }
    return $datos;
}

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tipo = $_POST['tipo'];
        $datos = validarDatos($_POST);

        if ($tipo === 'vuelo') {
            $sql = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) 
                    VALUES (:origen, :destino, :fecha, :plazas_disponibles, :precio)";

            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':origen' => $datos['origen'],
                ':destino' => $datos['destino'],
                ':fecha' => $datos['fecha'],
                ':plazas_disponibles' => $datos['plazas_disponibles'],
                ':precio' => $datos['precio']
            ]);

        } elseif ($tipo === 'hotel') {
            $sql = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) 
                    VALUES (:nombre, :ubicacion, :habitaciones_disponibles, :tarifa_noche)";

            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':ubicacion' => $datos['ubicacion'],
                ':habitaciones_disponibles' => $datos['habitaciones_disponibles'],
                ':tarifa_noche' => $datos['tarifa_noche']
            ]);
        }

        echo "Registro insertado correctamente";

        // Mostrar registros actuales
        if ($tipo === 'vuelo') {
            $sql = "SELECT * FROM VUELO ORDER BY fecha";
        } else {
            $sql = "SELECT * FROM HOTEL ORDER BY nombre";
        }

        $stmt = $conexion->query($sql);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3>Registros Actuales:</h3>";
        echo "<table border='1'>";

        // Encabezados de tabla
        if ($registros) {
            echo "<tr>";
            foreach (array_keys($registros[0]) as $columna) {
                echo "<th>$columna</th>";
            }
            echo "</tr>";

            // Datos
            foreach ($registros as $registro) {
                echo "<tr>";
                foreach ($registro as $valor) {
                    echo "<td>$valor</td>";
                }
                echo "</tr>";
            }
        }
        echo "</table>";

    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
