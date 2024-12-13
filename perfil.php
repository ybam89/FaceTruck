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

date_default_timezone_set('America/Mexico_City');

// Incluir al inicio del archivo, después de la conexión a la base de datos y la obtención del usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contenido'])) {
    $contenido = $_POST['contenido'];
    $imagen = ''; // Manejar la carga de imágenes aquí si es necesario
    $fecha_publicacion = date('Y-m-d H:i:s'); // Fecha y hora actual

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = 'uploads/' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
    }

    $stmt = $conn->prepare("INSERT INTO publicaciones (usuario_id, contenido, imagen, fecha_publicacion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $usuario_id, $contenido, $imagen, $fecha_publicacion);
    $stmt->execute();
    $stmt->close();

    // Redirigir después de procesar el formulario para evitar duplicaciones
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch publicaciones del usuario
$publicaciones = [];
$stmt = $conn->prepare("SELECT * FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC LIMIT 10");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $publicaciones[] = $row;
}
$stmt->close();

// Consulta para obtener la información del usuario según el tipo
switch ($tipo_usuario) {
    case 'operador':
        $sql = "SELECT pregunta_uno_operadores, pregunta_dos_operadores, pregunta_tres_operadores FROM operadores WHERE usuario_id = ?";
        break;
    case 'hombreCamion':
        $sql = "SELECT pregunta_uno_hombres_camion, pregunta_dos_hombres_camion, pregunta_tres_hombres_camion FROM hombres_camion WHERE usuario_id = ?";
        break;
    case 'empresa':
        $sql = "SELECT pregunta_uno_empresas, pregunta_dos_empresas, pregunta_tres_empresas FROM empresas WHERE usuario_id = ?";
        break;
    default:
        echo "Error: Tipo de usuario no válido.";
        exit;
}

$stmt = $conn->prepare($sql); // Prepara la consulta SQL
$stmt->bind_param("i", $usuario_id); // Vincula el parámetro ID del usuario
$stmt->execute(); // Ejecuta la consulta
$result = $stmt->get_result(); // Obtiene el resultado de la consulta

// Verificar si se encontró algún resultado
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // Obtener los datos del usuario
} else {
    echo "No se encontró información del usuario.";
    exit;
}

$stmt->close(); // Cierra la declaración preparada

// Consulta para obtener el correo y foto de perfil del usuario desde la tabla Usuarios
$sql_email = "SELECT correo, foto_perfil FROM Usuarios WHERE id = ?";
$stmt_email = $conn->prepare($sql_email);
$stmt_email->bind_param("i", $usuario_id);
$stmt_email->execute();
$result_email = $stmt_email->get_result();

if ($result_email->num_rows > 0) {
    $row_email = $result_email->fetch_assoc();
    $correo = $row_email['correo'];
    $foto_perfil = $row_email['foto_perfil']; // Obtener la ruta de la foto de perfil
} else {
    $correo = "Correo no encontrado";
    $foto_perfil = 'img/camion.jpg'; // Imagen predeterminada
}

$stmt_email->close(); // Cierra la declaración preparada

// Fetch job offers for 'empresa' users before closing the connection
$job_offers = [];
if ($tipo_usuario == 'empresa') {
    $sql_ofertas = "SELECT * FROM ofertas_empleo";
    $result_ofertas = $conn->query($sql_ofertas);
    if ($result_ofertas->num_rows > 0) {
        while($row_oferta = $result_ofertas->fetch_assoc()) {
            $job_offers[] = $row_oferta;
        }
    }
}

$conn->close(); // Mueve esta línea aquí para cerrar la conexión después de obtener las ofertas de trabajo
// Establecer la imagen de perfil predeterminada si no hay una imagen de perfil
$foto_perfil = $foto_perfil ?? 'img/camion.jpg'; // Usa un valor predeterminado si no está definido

// Menús según el tipo de usuario
$menu = '';
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario - FaceTruck</title>
    <style>
        /* Estilos CSS */
        body {
            font-family: Arial, sans-serif; /* Establece la fuente del texto a Arial, y si no está disponible, a sans-serif */
            background-color: #f0f0f0; /* Establece el color de fondo de la página a un gris claro */
            margin: 0; /* Elimina el margen por defecto alrededor del cuerpo de la página */
            padding: 20px; /* Añade un relleno interno de 20 píxeles alrededor del contenido del cuerpo de la página */
        }
        .container {
            background-color: #ffffff; /* Establece el color de fondo del contenedor a blanco */
            padding: 20px; /* Añade un relleno interno de 20 píxeles alrededor del contenido del contenedor */
            border-radius: 8px; /* Redondea las esquinas del contenedor con un radio de 8 píxeles */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Añade una sombra alrededor del contenedor para darle profundidad */
            max-width: 800px; /* Limita el ancho máximo del contenedor a 800 píxeles */
            margin: 0 auto; /* Centra el contenedor horizontalmente */
            position: relative; /* Establece una posición relativa para el contenedor */
        }
        h2 {
            color: #007BFF; /* Establece el color del texto del encabezado a azul */
            border-bottom: 2px solid #007BFF; /* Añade una línea inferior de 2 píxeles de grosor y color azul */
            padding-bottom: 5px; /* Añade un relleno interno de 5 píxeles debajo del texto del encabezado */
            margin-bottom: 20px; /* Añade un margen inferior de 20 píxeles debajo del encabezado */
        }
        .logout-button {
            background-color: #FF0000; /* Establece el color de fondo del botón a rojo */
            color: white; /* Establece el color del texto del botón a blanco */
            padding: 3px 3px; /* Añade un relleno interno de 6 píxeles verticalmente y 15 píxeles horizontalmente */
            border: none; /* Elimina el borde del botón */
            border-radius: 4px; /* Redondea las esquinas del botón con un radio de 4 píxeles */
            cursor: pointer; /* Cambia el cursor a un puntero al pasar sobre el botón */
            text-align: center; /* Alinea el texto al centro del botón */
            display: inline-block; /* Muestra el botón como un bloque en línea */
            position: absolute; /* Posiciona el botón de forma absoluta respecto a su contenedor */
            top: 20px; /* Desplaza el botón 20 píxeles desde la parte superior del contenedor */
            right: 20px; /* Desplaza el botón 20 píxeles desde el lado derecho del contenedor */
            text-decoration: none;
        }
        .logout-button:hover {
            background-color: #cc0000; /* Cambia el color de fondo del botón a un rojo más oscuro al pasar sobre él */
        }
        .dropdown-menu {
            position: absolute; /* Posiciona el menú de forma absoluta respecto a su contenedor */
            top: 10px; /* Desplaza el menú 10 píxeles desde la parte superior del contenedor */
            left: 10px; /* Desplaza el menú 10 píxeles desde el lado izquierdo del contenedor */
            background-color: #007BFF; /* Establece el color de fondo del menú a azul */
            color: white; /* Establece el color del texto del menú a blanco */
            padding: 15px; /* Añade un relleno interno de 10 píxeles alrededor del contenido del menú */
            border-radius: 4px; /* Redondea las esquinas del menú con un radio de 4 píxeles */
            cursor: pointer; /* Cambia el cursor a un puntero al pasar sobre el menú */
        }
        .dropdown-content {
            display: none; /* Esconde el contenido del menú desplegable por defecto */
            padding: 2px 2px;
            position: absolute; /* Posiciona el contenido del menú de forma absoluta respecto a su contenedor */
            background-color: white; /* Establece el color de fondo del contenido del menú a blanco */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Añade una sombra alrededor del contenido del menú para darle profundidad */
            z-index: 1; /* Asegura que el contenido del menú se muestre por encima de otros elementos */
        }
        .dropdown-content a {
            color: black; /* Establece el color del texto de los enlaces a negro */
            padding: 10px 40px; /* Añade un relleno interno de 12 píxeles verticalmente y 16 píxeles horizontalmente */
            text-decoration: none; /* Elimina la subrayado del texto de los enlaces */
            display: block; /* Muestra cada enlace como un bloque */
            white-space: nowrap;
            text-align: left;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1; /* Cambia el color de fondo del enlace a gris claro al pasar sobre él */
        }
        .dropdown-menu:hover .dropdown-content {
            display: block; /* Muestra el contenido del menú desplegable cuando se pasa sobre el menú */
        }
        .profile-picture {
            text-align: center; /* Alinea el contenido al centro */
            margin-bottom: 20px; /* Añade un margen inferior de 20 píxeles debajo del elemento */
        }
        .profile-picture img {
            display: block; /* Muestra la imagen como un bloque */
            margin: auto; /* Centra la imagen horizontalmente */
            width: 150px; /* Establece el ancho de la imagen a 150 píxeles */
            height: 150 px; /* Establece la altura de la imagen a 150 píxeles */
            border-radius: 50%; /* Hace que la imagen sea circular */
            object-fit: cover; /* Asegura que la imagen cubra el área de visualización sin estirarse */
        }
        .profile-picture form {
            display: inline-block; /* Muestra el formulario como un bloque en línea */
            margin-top: 10px; /* Añade un margen superior de 10 píxeles encima del formulario */
        }
        .profile-picture input[type="file"] {
            display: none; /* Esconde el campo de entrada de archivo */
        }
        .profile-picture label {
            background-color: #007BFF; /* Establece el color de fondo de la etiqueta a azul */
            color: white; /* Establece el color del texto de la etiqueta a blanco */
            padding: 10px 20px; /* Añade un relleno interno de 10 píxeles verticalmente y 20 píxeles horizontalmente */
            border: none; /* Elimina el borde de la etiqueta */
            border-radius: 4px; /* Redondea las esquinas de la etiqueta con un radio de 4 píxeles */
            cursor: pointer; /* Cambia el cursor a un puntero al pasar sobre la etiqueta */
            text-align: center; /* Alinea el texto al centro de la etiqueta */
            display: inline-block; /* Muestra la etiqueta como un bloque en línea */
            margin-top: 10 px; /* Añade un margen superior de 10 píxeles encima de la etiqueta */
        }
        .profile-picture label:hover {
            background-color: #0056b3; /* Cambia el color de fondo de la etiqueta a un azul más oscuro al pasar sobre ella */
        }
        .form-container {
            display: flex; /* Establece un contenedor flexible */
            flex-direction: column; /* Coloca los elementos dentro del contenedor en una columna */
            gap: 20px; /* Añade un espacio de 20 píxeles entre cada elemento dentro del contenedor */
        }
        .form-container label {
            font-weight: bold; /* Establece el peso de la fuente a negrita */
        }
        .form-container input[type="text"] {
            width: 100%; /* Establece el ancho del campo de entrada a 100% del contenedor */
            padding: 10px; /* Añade un relleno interno de 10 píxeles alrededor del contenido del campo de entrada */
            border: 1 px solid #ccc; /* Establece un borde de 1 píxel de color gris claro alrededor del campo de entrada */
            border-radius: 4 px; /* Redondea las esquinas del campo de entrada con un radio de 4 píxeles */
        }
        .button {
            background-color: #007BFF; /* Establece el color de fondo del botón a azul */
            color: white; /* Establece el color del texto del botón a blanco */
            padding: 10px 20px; /* Añade un relleno interno de 10 píxeles verticalmente y 20 píxeles horizontalmente */
            border: none; /* Elimina el borde del botón */
            border-radius: 4 px; /* Redondea las esquinas del botón con un radio de 4 píxeles */
            cursor: pointer; /* Cambia el cursor a un puntero al pasar sobre el botón */
            text-align: center; /* Alinea el texto al centro del botón */
            display: inline-block; /* Muestra el botón como un bloque en línea */
            margin-top: 10 px; /* Añade un margen superior de 10 píxeles encima del botón */
        }
        .button:hover {
            background-color: #0056b3; /* Cambia el color de fondo del botón a un azul más oscuro al pasar sobre él */
        }
        .edit-button {
            text-align: center; /* Alinea el contenido al centro */
            margin-top: 20 px; /* Añade un margen superior de 20 píxeles encima del elemento */
        }

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
        .profile-picture {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-picture img {
            display: block;
            margin: auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-picture form {
            display: inline-block;
            margin-top: 10px;
        }
        .profile-picture input[type="file"] {
            display: none;
        }
        .profile-picture label {
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
        .profile-picture label:hover {
            background-color: #0056b3;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-container label {
            font-weight: bold;
        }
        .form-container input[type="text"] {
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
        .post img {
            width: 300px;
            height: 300px;
            object-fit: cover;
        }
</style>
</head>
<body>
    <div class="container">
        <div class="dropdown-menu">Menú
            <div class="dropdown-content">
                <?php echo $menu; ?>
            </div>
        </div>
        <a href="logout.php" class="logout-button">Cerrar sesión</a>
        <div class="profile-picture">
            <img src="<?php echo $foto_perfil; ?>" alt="Foto de Perfil">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="file" name="fileToUpload" id="fileToUpload">
                <label for="fileToUpload" class="button">Cambiar foto de perfil</label>
                <button type="submit" class="button">Actualizar foto</button>
            </form>
            <p><?php echo $correo; ?></p>
            <p><?php echo ucfirst($tipo_usuario); ?></p>
        </div>

        <!-- Mostrar el formulario según el tipo de usuario -->
        <?php if ($tipo_usuario == 'operador'): ?>
            <h2>Formulario para Operadores</h2>
            <div class="form-container">
                <label for="pregunta_uno">Pregunta uno operadores</label>
                <input type="text" id="pregunta_uno" name="pregunta_uno" value="<?php echo $row['pregunta_uno_operadores']; ?>" readonly>
                
                <label for="pregunta_dos">Pregunta dos operadores</label>
                <input type="text" id="pregunta_dos" name="pregunta_dos" value="<?php echo $row['pregunta_dos_operadores']; ?>" readonly>
                
                
                <label for="pregunta_tres">Pregunta tres operadores</label>
                <input type="text" id="pregunta_tres" name="pregunta_tres" value="<?php echo $row['pregunta_tres_operadores']; ?>" readonly>
            </div>
        <?php elseif ($tipo_usuario == 'hombreCamion'): ?>
            <h2>Formulario para Hombres Camión</h2>
            <div class="form-container">
                <label for="pregunta_uno">Pregunta uno hombres camión</label>
                <input type="text" id="pregunta_uno" name="pregunta_uno" value="<?php echo $row['pregunta_uno_hombres_camion']; ?>" readonly>
                
                <label for="pregunta_dos">Pregunta dos hombres camión</label>
                <input type="text" id="pregunta_dos" name="pregunta_dos" value="<?php echo $row['pregunta_dos_hombres_camion']; ?>" readonly>
                
                <label for="pregunta_tres">Pregunta tres hombres camión</label>
                <input type="text" id="pregunta_tres" name="pregunta_tres" value="<?php echo $row['pregunta_tres_hombres_camion']; ?>" readonly>
            </div>
        <?php elseif ($tipo_usuario == 'empresa'): ?>
            <h2>Formulario para Empresas</h2>
            <div class="form-container">
                <label for="pregunta_uno">Pregunta uno empresas</label>
                <input type="text" id="pregunta_uno" name="pregunta_uno" value="<?php echo $row['pregunta_uno_empresas']; ?>" readonly>
                
                <label for="pregunta_dos">Pregunta dos empresas</label>
                <input type="text" id="pregunta_dos" name="pregunta_dos" value="<?php echo $row['pregunta_dos_empresas']; ?>" readonly>
                
                <label for="pregunta_tres">Pregunta tres empresas</label>
                <input type="text" id="pregunta_tres" name="pregunta_tres" value="<?php echo $row['pregunta_tres_empresas']; ?>" readonly>
            </div>
        <?php endif; ?>

        <div class="edit-button">
            <button onclick="location.href='editar_perfil.php'" class="button">Editar perfil</button>
        </div>

        <?php
        // Existing code...

        // Additional code for fetching job offers for 'empresa' users
        if ($tipo_usuario == 'empresa') {
            $sql_ofertas = "SELECT * FROM ofertas_empleo";
            $result_ofertas = $conn->query($sql_ofertas);

        }
        ?>

    <title>Tabla Ordenable y Filtrable</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
            cursor: pointer;
        }
        th.sortable:hover {
            background-color: #ddd;
        }
        input[type="text"] {
            width: 100%;
            padding: 5px;
            margin: 5px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="post-section">
    <form id="post-form" method="post" enctype="multipart/form-data">
        <textarea name="contenido" placeholder="¿Qué estás pensando?" required></textarea>
        <input type="file" name="imagen" accept="image/*">
        <button type="submit" class="button">Publicar</button>
    </form>
<div id="posts">
<div id="posts">
    <?php foreach ($publicaciones as $publicacion): ?>
    <div class="post">
        <p><?php echo htmlspecialchars($publicacion['contenido']); ?></p>
        <?php if ($publicacion['imagen']): ?>
        <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen de publicación">
        <?php endif; ?>
        <p><?php echo date('d \d\e F \d\e Y, H:i \h\r\s', strtotime($publicacion['fecha_publicacion'])); ?></p>
        <button class="like-button" data-id="<?php echo $publicacion['id']; ?>">👍 Me gusta (<?php echo $publicacion['likes']; ?>)</button>
    </div>
    <?php endforeach; ?>
</div>
<script>
document.getElementById('post-form').addEventListener('submit', function(event) {
    event.preventDefault();
    var formData = new FormData(this);
    fetch('perfil.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text()).then(data => {
        var parser = new DOMParser();
        var doc = parser.parseFromString(data, 'text/html');
        var newPosts = doc.getElementById('posts').innerHTML;
        document.getElementById('posts').innerHTML = newPosts;
        this.reset();
    });
});

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('like-button')) {
        var button = event.target;
        var postId = button.getAttribute('data-id');
        fetch('like.php?id=' + postId).then(response => response.text()).then(data => {
            button.innerHTML = '👍 Me gusta (' + data + ')';
        });
    }
});
</script>
</body>
</html>