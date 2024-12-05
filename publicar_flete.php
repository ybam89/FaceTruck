<?php
session_start(); // Inicia la sesión

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); // Termina la ejecución si hay un error en la conexión
}

// Obtener el ID del usuario y el tipo de usuario desde la sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo "Error: No se ha iniciado sesión correctamente.";
    exit;
}
$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

// Menús según el tipo de usuario
$menu = '';
if ($tipo_usuario == 'empresa') {
    $menu = '<ul>
                <li><a href="buscar_operadores.php">Buscar operadores</a></li>
                <li><a href="buscar_hombres_camion.php">Buscar Hombres camión</a></li>
                <li><a href="buscar_ofertas_rutas.php">Buscar ofertas de rutas</a></li>
                <li><a href="publicar_vacante.php">Publicar y consultar mis vacantes "operador"</a></li>
                <li><a href="publicar_flete.php">Publicar y consultar mis Fletes eventuales</a></li>
                <li><a href="publicar_oferta_ruta.php">Publicar y consultar oferta de ruta</a></li>
             </ul>';
} else {
    echo "Error: Acceso no autorizado.";
    exit;
}

// Procesar el formulario de publicación de flete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'publicar') {
    // Validar y recibir los datos del formulario
    $vigencia = isset($_POST['vigencia']) ? $_POST['vigencia'] : '';
    $estado_partida = isset($_POST['estado_partida']) ? $_POST['estado_partida'] : '';
    $municipio_partida = isset($_POST['municipio_partida']) ? $_POST['municipio_partida'] : '';
    $estado_destino = isset($_POST['estado_destino']) ? $_POST['estado_destino'] : '';
    $municipio_destino = isset($_POST['municipio_destino']) ? $_POST['municipio_destino'] : '';
    $fecha_publicacion = isset($_POST['fecha_publicacion']) ? $_POST['fecha_publicacion'] : '';
    $tipo_viaje = isset($_POST['tipo_viaje']) ? $_POST['tipo_viaje'] : '';
    $kilometraje_aproximado = isset($_POST['kilometraje_aproximado']) ? $_POST['kilometraje_aproximado'] : '';
    $tipo_vehiculo_solicitado = isset($_POST['tipo_vehiculo_solicitado']) ? $_POST['tipo_vehiculo_solicitado'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $pago_ofrecido = isset($_POST['pago_ofrecido']) ? $_POST['pago_ofrecido'] : '';

    // Verifica que no falten valores obligatorios
    if (empty($vigencia) || empty($estado_partida) || empty($municipio_partida) || empty($estado_destino) || empty($municipio_destino) || empty($fecha_publicacion) || empty($tipo_viaje) || empty($kilometraje_aproximado) || empty($tipo_vehiculo_solicitado) || empty($descripcion) || empty($pago_ofrecido)) {
        echo "Error: Todos los campos son obligatorios.";
        exit;
    }

    // Insertar los datos en la tabla buscar_fletes
    $sql = "INSERT INTO buscar_fletes (vigencia, estado_partida, municipio_partida, estado_destino, municipio_destino, fecha_publicacion, tipo_viaje, kilometraje_aproximado, tipo_vehiculo_solicitado, descripcion, pago_ofrecido, usuario_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", $vigencia, $estado_partida, $municipio_partida, $estado_destino, $municipio_destino, $fecha_publicacion, $tipo_viaje, $kilometraje_aproximado, $tipo_vehiculo_solicitado, $descripcion, $pago_ofrecido, $usuario_id);

    if ($stmt->execute()) {
        echo "Flete publicado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Recuperar los registros de buscar_fletes para el usuario actual
$sql = "SELECT * FROM buscar_fletes WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Flete - FaceTruck</title>
    <style>
        /* Estilos CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        .logout-button {
            background-color: #FF0000;
            color: white;
            padding: 3px 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            position: absolute;
            top: 20px;
            right: 20px;
            text-decoration: none;
        }
        .logout-button:hover {
            background-color: #cc0000;
        }
        .dropdown-menu {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #007BFF;
            color: white;
            padding: 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            padding: 2px 2px;
            position: absolute;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 10px 40px;
            text-decoration: none;
            display: block;
            white-space: nowrap;
            text-align: left;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown-menu:hover .dropdown-content {
            display: block;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-container label {
            font-weight: bold;
        }
        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .edit-button {
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }
        th input {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
    </style>
    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="dropdown-menu">Menú
            <div class="dropdown-content">
                <?php echo $menu; ?>
            </div>
        </div>
        <a href="logout.php" class="logout-button">Cerrar sesión</a>
        <h2>Publicar Flete</h2>
        <form method="post" class="form-container">
            <input type="hidden" name="action" value="publicar">
            <label for="vigencia">Vigente:</label>
            <select id="vigencia" name="vigencia">
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>

            <label for="estado_partida">Estado de Partida:</label>
            <input type="text" id="estado_partida" name="estado_partida" required>

            <label for="municipio_partida">Municipio de Partida:</label>
            <input type="text" id="municipio_partida" name="municipio_partida" required>

            <label for="estado_destino">Estado de Destino:</label>
            <input type="text" id="estado_destino" name="estado_destino" required>

            <label for="municipio_destino">Municipio de Destino:</label>
            <input type="text" id="municipio_destino" name="municipio_destino" required>

            <label for="fecha_publicacion">Fecha de Publicación:</label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion" required>

            <label for="tipo_viaje">Tipo de Viaje:</label>
            <select id="tipo_viaje" name="tipo_viaje">
                <option value="Local">Local</option>
                <option value="Foráneo">Foráneo</option>
            </select>

            <label for="kilometraje_aproximado">Kilometraje Aproximado:</label>
            <input type="text" id="kilometraje_aproximado" name="kilometraje_aproximado" required>

            <label for="tipo_vehiculo_solicitado">Tipo de Vehículo Solicitado:</label>
            <input type="text" id="tipo_vehiculo_solicitado" name="tipo_vehiculo_solicitado">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

            <label for="pago_ofrecido">Pago Ofrecido:</label>
            <input type="text" id="pago_ofrecido" name="pago_ofrecido" required>

            <button type="submit" class="button">Guardar cambios</button>
        </form>
        <div class="edit-button">
            <button onclick="location.href='perfil.php'" class="button">Regresar</button>
        </div>
        <!-- Nueva sección para mostrar la tabla de registros -->
        <h2>Registros de Fletes</h2>
        <table id="tabla">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Vigente <input type="text" onkeyup="filterTable(0)"></th>
                    <th onclick="sortTable(1)">Estado de Partida <input type="text" onkeyup="filterTable(1)"></th>
                    <th onclick="sortTable(2)">Municipio de Partida <input type="text" onkeyup="filterTable(2)"></th>
                    <th onclick="sortTable(3)">Estado de Destino <input type="text" onkeyup="filterTable(3)"></th>
                    <th onclick="sortTable(4)">Municipio de Destino <input type="text" onkeyup="filterTable(4)"></th>
                    <th onclick="sortTable(5)">Fecha de Publicación <input type="text" onkeyup="filterTable(5)"></th>
                    <th onclick="sortTable(6)">Tipo de Viaje <input type="text" onkeyup="filterTable(6)"></th>
                    <th onclick="sortTable(7)">Kilometraje Aproximado <input type="text" onkeyup="filterTable(7)"></th>
                    <th onclick="sortTable(8)">Tipo de Vehículo Solicitado <input type="text" onkeyup="filterTable(8)"></th>
                    <th onclick="sortTable(9)">Descripción <input type="text" onkeyup="filterTable(9)"></th>
                    <th onclick="sortTable(10)">Pago Ofrecido <input type="text" onkeyup="filterTable(10)"></th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['vigencia'] == '1' ? 'Sí' : 'No'); ?></td>
                        <td><?php echo htmlspecialchars($row['estado_partida']); ?></td>
                        <td><?php echo htmlspecialchars($row['municipio_partida']); ?></td>
                        <td><?php echo htmlspecialchars($row['estado_destino']); ?></td>
                        <td><?php echo htmlspecialchars($row['municipio_destino']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_publicacion']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_viaje']); ?></td>
                        <td><?php echo htmlspecialchars($row['kilometraje_aproximado']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_vehiculo_solicitado']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($row['pago_ofrecido']); ?></td>
                        <td>
                            <button onclick="editRecord(<?php echo $row['id']; ?>)">Editar</button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="eliminar">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este flete?');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Función para ordenar las filas de la tabla por una columna
        function sortTable(n) {
            var table = document.getElementById("tabla");
            var rows = table.rows;
            var switching = true;
            var dir = "asc"; // Dirección de ordenación
            var switchCount = 0;

            while (switching) {
                switching = false;
                var rowsArray = Array.from(rows).slice(1); // Saltamos la primera fila (cabecera)
                for (var i = 0; i < (rowsArray.length - 1); i++) {
                    var x = rowsArray[i].getElementsByTagName("TD")[n];
                    var y = rowsArray[i + 1].getElementsByTagName("TD")[n];
                    if (dir == "asc" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase() || 
                        dir == "desc" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        rowsArray[i].parentNode.insertBefore(rowsArray[i + 1], rowsArray[i]);
                        switching = true;
                        switchCount++;
                        break;
                    }
                }
                if (switchCount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }

        // Función para filtrar las filas de la tabla por una columna
        function filterTable(n) {
            var input, filter, table, tr, td, i, txtValue;
            input = document.querySelectorAll('th input')[n];
            filter = input.value.toLowerCase();
            table = document.getElementById("tabla");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) { // Empezamos desde 1 para no filtrar la cabecera
                td = tr[i].getElementsByTagName("td")[n];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // Función para editar un registro
        function editRecord(id) {
            // Redirigir a la página de edición con el ID del registro
            window.location.href = 'editar_flete.php?id=' + id;
        }
    </script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$stmt->close();
$conn->close();
?>