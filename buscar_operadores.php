<?php
session_start();

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario y el tipo de usuario desde la sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo "Error: No se ha iniciado sesión correctamente.";
    exit;
}
$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

// Añadir el menú desplegable
switch ($tipo_usuario) {
    case 'operador':
        $menu = '<ul>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                    <li><a href="inicio_facetruck.php">Inicio FaceTruck</a></li>
                    <li><a href="ofertas_empleo.php">Ofertas de empleo</a></li>
                    <li><a href="universo_facetruck.php">Universo FaceTruck</a></li>
                 </ul>';
        break;
    case 'hombreCamion':
        $menu = '<ul>
                    <li><a href="perfil.php">Mi Perfil</a></li>
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
                    <li><a href="perfil.php">Mi Perfil</a></li>
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

// Recuperar los registros de operadores con disponibilidad 1 y 2, y hombres camión con disponibilidad 1
$sql = "(SELECT o.*, u.correo, 'operador' as tipo_usuario FROM operadores o
        JOIN usuarios u ON o.usuario_id = u.id
        WHERE o.disponibilidad IN (1, 2))
        UNION
        (SELECT h.*, u.correo, 'hombreCamion' as tipo_usuario FROM hombres_camion h
        JOIN usuarios u ON h.usuario_id = u.id
        WHERE h.disponibilidad = 1)";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Operadores - FaceTruck</title>
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
        <h2>Buscar Operadores</h2>
        <table id="tabla">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Nombre <input type="text" onkeyup="filterTable(0)"></th>
                    <th onclick="sortTable(1)">Correo <input type="text" onkeyup="filterTable(1)"></th>
                    <th onclick="sortTable(2)">Disponibilidad <input type="text" onkeyup="filterTable(2)"></th>
                    <th onclick="sortTable(3)">Tipo de Usuario <input type="text" onkeyup="filterTable(3)"></th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['correo']); ?></td>
                        <td><?php echo htmlspecialchars($row['disponibilidad'] == '1' ? 'Disponible para el trabajo' : 'Actualmente laborando, pero buscando una mejor oportunidad'); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_usuario']); ?></td>
                        <td>
                            <a href="ver_perfil.php?id=<?php echo $row['usuario_id']; ?>" class="button">Ver perfil</a>
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
    </script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>