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

// Verificar el tipo de usuario
if ($tipo_usuario !== 'operador') {
    echo "Error: Acceso no autorizado.";
    exit;
}

// Añadir el menú desplegable
switch ($tipo_usuario) {
    case 'operador':
        $menu = '<ul>
                    <li><a href="inicio_facetruck.php">Inicio FaceTruck</a></li>
                    <li><a href="ofertas_empleo.php">Ofertas de empleo</a></li>
                    <li><a href="universo_facetruck.php">Universo FaceTruck</a></li>
                 </ul>';
        break;
    case 'hombreCamion':
        $menu = '<ul>
                    <li><a href="inicio_facetruck.php">Inicio FaceTruck</a></li>
                    <li><a href="universo_facetruck.php">Universo FaceTruck</a></li>
                    <li><a href="ofertas_empresas.php">Ofertas de empresas</a></li>
                    <li><a href="buscar_operadores.php">Buscar operadores</a></li>
                    <li><a href="buscar_fletes.php">Buscar fletes eventuales</a></li>
                    <li><a href="publicar_vacante.php">Publicar y consultar mis vacantes "operador"</a></li>
                 </ul>';
        break;
    case 'empresa':
        $menu = '<ul>
                    <li><a href="inicio_facetruck.php">Inicio FaceTruck</a></li>
                    <li><a href="universo_facetruck.php">Universo FaceTruck</a></li>
                    <li><a href="buscar_operadores.php">Buscar operadores</a></li>
                    <li><a href="buscar_hombres_camion.php">Buscar Hombres camión</a></li>
                    <li><a href="buscar_ofertas_rutas.php">Buscar ofertas de rutas</a></li>
                    <li><a href="publicar_vacante.php">Publicar y consultar mis vacantes "operador"</a></li>
                    <li><a href="publicar_flete.php">Publicar y consultar mis Fletes eventuales</a></li>
                    <li><a href="publicar_oferta_ruta.php">Publicar y consultar oferta de ruta</a></li>
                </ul>';
        break;
}

// Recuperar los registros de ofertas_empleo hechos por usuarios tipo "hombreCamion" y "empresa" con "vigente" en "1"
$sql = "SELECT oe.*, u.correo FROM ofertas_empleo oe
        JOIN usuarios u ON oe.usuario_id = u.id
        WHERE oe.vigente = 1 AND (u.tipo_usuario = 'hombreCamion' OR u.tipo_usuario = 'empresa')";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Ofertas de Empleo - FaceTruck</title>
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
        <h2>Consultar Ofertas de Empleo</h2>
        <table id="tabla">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Vigente <input type="text" onkeyup="filterTable(0)"></th>
                    <th onclick="sortTable(1)">Estado <input type="text" onkeyup="filterTable(1)"></th>
                    <th onclick="sortTable(2)">Municipio <input type="text" onkeyup="filterTable(2)"></th>
                    <th onclick="sortTable(3)">Fecha de Publicación <input type="text" onkeyup="filterTable(3)"></th>
                    <th onclick="sortTable(4)">Sueldo <input type="text" onkeyup="filterTable(4)"></th>
                    <th onclick="sortTable(5)">Tipo de Viaje <input type="text" onkeyup="filterTable(5)"></th>
                    <th onclick="sortTable(6)">Descripción de Ruta <input type="text" onkeyup="filterTable(6)"></th>
                    <th onclick="sortTable(7)">Tipo de Vehículo y Remolque <input type="text" onkeyup="filterTable(7)"></th>
                    <th onclick="sortTable(8)">Requisitos <input type="text" onkeyup="filterTable(8)"></th>
                    <th onclick="sortTable(9)">Prestaciones <input type="text" onkeyup="filterTable(9)"></th>
                    <th onclick="sortTable(10)">Contacto <input type="text" onkeyup="filterTable(10)"></th>
                    <th onclick="sortTable(11)">Correo <input type="text" onkeyup="filterTable(11)"></th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['vigente'] == '1' ? 'Sí' : 'No'); ?></td>
                        <td><?php echo htmlspecialchars($row['estado']); ?></td>
                        <td><?php echo htmlspecialchars($row['municipio']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_publicacion']); ?></td>
                        <td><?php echo htmlspecialchars($row['sueldo']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_viaje']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion_ruta']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_vehiculo_remolque']); ?></td>
                        <td><?php echo htmlspecialchars($row['requisitos']); ?></td>
                        <td><?php echo htmlspecialchars($row['prestaciones']); ?></td>
                        <td><?php echo htmlspecialchars($row['contacto']); ?></td>
                        <td><?php echo htmlspecialchars($row['correo']); ?></td>
                        <td>
                            <a href="detalles_oferta_op.php?id=<?php echo $row['id']; ?>" class="button">Ver detalles</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="edit-button">
            <button onclick="location.href='perfil.php'" class="button">Regresar</button>
        </div>
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
    </script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>