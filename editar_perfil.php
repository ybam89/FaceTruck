<?php
session_start(); // Inicia la sesión para poder usar $_SESSION

// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck"; // Asegúrate de que el nombre de la base de datos sea correcto

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); // Termina el script si hay un error de conexión
}

// Obtener el ID del operador desde la sesión
if (!isset($_SESSION['operador_id'])) {
    die("No se ha iniciado sesión correctamente."); // Termina el script si no hay operador_id en la sesión
}
$operador_id = $_SESSION['operador_id']; // Asigna el ID del operador desde la sesión

// Consulta para obtener la información del operador
$sql = "SELECT * FROM operadores WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $operador_id); // Vincula el parámetro ID del operador
$stmt->execute();
$result = $stmt->get_result(); // Ejecuta la consulta y obtiene el resultado

// Verificar si se encontró algún resultado
if ($result->num_rows > 0) {
    // Obtener los datos del operador
    $row = $result->fetch_assoc();
    $nombre_completo = $row['nombre_completo'];
    $edad = $row['edad'];
    $ciudad = $row['ciudad'];
    $estado = $row['estado'];
    $telefono = $row['telefono'];
    $correo = $row['correo'];
    $experiencia_anos = $row['experiencia_anos'];
    $tipos_unidades = $row['tipos_unidades'];
    $empresas = $row['empresas'];
    $rutas = $row['rutas'];
    $licencia_tipo = $row['licencia_tipo'];
    $licencia_vigencia = $row['licencia_vigencia'];
    $materiales_peligrosos = $row['materiales_peligrosos'];
    $otros_certificados = $row['otros_certificados'];
    $disponibilidad_viajar = $row['disponibilidad_viajar'];
    $disponibilidad_horarios = $row['disponibilidad_horarios'];
    $nivel_mecanica = $row['nivel_mecanica'];
    $nivel_seguridad_vial = $row['nivel_seguridad_vial'];
    $habilidad_gps = $row['habilidad_gps'];
    $manejo_bitacoras = $row['manejo_bitacoras'];
} else {
    echo "No se encontró información del operador."; // Mensaje si no se encuentra información del operador
}

$stmt->close(); // Cierra la declaración
$conn->close(); // Cierra la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Define la codificación de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura la vista para dispositivos móviles -->
    <title>Editar Perfil del Operador - FaceTruck</title> <!-- Título de la página -->
    <style>
        body {
            font-family: Arial, sans-serif; /* Define la fuente de la página */
            background-color: #f0f0f0; /* Color de fondo de la página */
            margin: 0; /* Sin margen */
            padding: 20px; /* Espaciado interno */
            display: flex; /* Usar flexbox para centrar el contenido */
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
            height: 100vh; /* Altura de la página completa */
            overflow: hidden; /* Evita que la página se desplace */
        }
        .container {
            background-color: #ffffff; /* Color de fondo del contenedor de login */
            padding: 20px; /* Espaciado interno */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra del contenedor */
            max-width: 800px; /* Ancho máximo */
            width: 100%; /* Ancho completo */
            max-height: 90vh; /* Altura máxima del contenedor */
            overflow-y: auto; /* Habilita el desplazamiento vertical */
        }
        h2 {
            color: #007BFF; /* Color del texto del título */
            border-bottom: 2px solid #007BFF; /* Línea inferior */
            padding-bottom: 5px; /* Relleno inferior */
            margin-bottom: 20px; /* Margen inferior */
        }
        .section {
            margin-bottom: 15px; /* Margen inferior */
        }
        .section label {
            font-weight: bold; /* Texto en negrita */
            display: block; /* Mostrar como bloque */
            margin: 10px 0 5px; /* Margen */
        }
        .section input, .section textarea {
            width: calc(100% - 20px); /* Ancho de los inputs */
            padding: 8px; /* Relleno interno */
            margin: 5px 0; /* Margen */
            border: 1px solid #ccc; /* Bordes de los inputs */
            border-radius: 4px; /* Bordes redondeados */
            font-size: 14px; /* Tamaño de la fuente */
        }
        .section input[type="checkbox"] {
            width: auto; /* Ancho automático */
        }
        .submit-button {
            text-align: center; /* Centrar el texto */
            margin-top: 20px; /* Margen superior */
        }
        .submit-button button {
            background-color: #007BFF; /* Color de fondo */
            color: white; /* Color del texto */
            border: none; /* Sin borde */
            padding: 10px 20px; /* Relleno interno */
            cursor: pointer; /* Cambiar el cursor a mano */
            border-radius: 4px; /* Bordes redondeados */
            font-size: 16px; /* Tamaño de la fuente */
        }
        .submit-button button:hover {
            background-color: #0056b3; /* Color de fondo al pasar el ratón */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Información Personal</h2>
        <form action="actualizar_perfil.php" method="post"> <!-- Formulario de edición de perfil -->
            <div class="section">
                <label for="nombre_completo">Nombre completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo !empty($nombre_completo) ? $nombre_completo : ''; ?>" required> <!-- Campo de entrada para el nombre completo -->
                
                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" value="<?php echo !empty($edad) ? $edad : ''; ?>" required> <!-- Campo de entrada para la edad -->
                
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo !empty($ciudad) ? $ciudad : ''; ?>" required> <!-- Campo de entrada para la ciudad -->
                
                <label for="estado">Estado:</label>
                <input type="text" id="estado" name="estado" value="<?php echo !empty($estado) ? $estado : ''; ?>" required> <!-- Campo de entrada para el estado -->
                
                <label for="telefono">Teléfono de contacto:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo !empty($telefono) ? $telefono : ''; ?>" required> <!-- Campo de entrada para el teléfono de contacto -->
                
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" value="<?php echo !empty($correo) ? $correo : ''; ?>" required> <!-- Campo de entrada para el correo electrónico -->

                <label for="experiencia_anos">Años de experiencia:</label>
                <input type="number" id="experiencia_anos" name="experiencia_anos" value="<?php echo !empty($experiencia_anos) ? $experiencia_anos : ''; ?>" required> <!-- Campo de entrada para los años de experiencia -->

                <label for="tipos_unidades">Tipo de unidades que he manejado:</label>
                <textarea id="tipos_unidades" name="tipos_unidades" required><?php echo !empty($tipos_unidades) ? $tipos_unidades : ''; ?></textarea> <!-- Campo de texto para el tipo de unidades -->

                <label for="empresas">Empresas anteriores y duración del empleo:</label>
                <textarea id="empresas" name="empresas" required><?php echo !empty($empresas) ? $empresas : ''; ?></textarea> <!-- Campo de texto para las empresas anteriores -->

                <label for="rutas">Rutas manejadas:</label>
                <textarea id="rutas" name="rutas" required><?php echo !empty($rutas) ? $rutas : ''; ?></textarea> <!-- Campo de texto para las rutas -->

                <label for="licencia_tipo">Tipo de licencia de conducir:</label>
                <input type="text" id="licencia_tipo" name="licencia_tipo" value="<?php echo !empty($licencia_tipo) ? $licencia_tipo : ''; ?>" required> <!-- Campo de entrada para el tipo de licencia -->

                <label for="licencia_vigencia">Vigencia de la licencia:</label>
                <input type="date" id="licencia_vigencia" name="licencia_vigencia" value="<?php echo !empty($licencia_vigencia) ? $licencia_vigencia : ''; ?>" required> <!-- Campo de entrada para la vigencia de la licencia -->

                <label for="materiales_peligrosos">¿Cuenta con certificación para manejo de materiales peligrosos?</label>
                <input type="checkbox" id="materiales_peligrosos" name="materiales_peligrosos" value="1" <?php if(!empty($materiales_peligrosos)) echo "checked"; ?>> <!-- Campo de entrada para la certificación de materiales peligrosos -->

                <label for="otros_certificados">Otros certificados o capacitaciones relevantes:</label>
                <textarea id="otros_certificados" name="otros_certificados" required><?php echo !empty($otros_certificados) ? $otros_certificados : ''; ?></textarea> <!-- Campo de texto para otros certificados -->

                <label for="disponibilidad_viajar">¿Está dispuesto a viajar o hacer rutas nacionales?</label>
                <input type="checkbox" id="disponibilidad_viajar" name="disponibilidad_viajar" value="1" <?php if(!empty($disponibilidad_viajar)) echo "checked"; ?>> <!-- Campo de entrada para la disponibilidad para viajar -->

                <label for="disponibilidad_horarios">Disponibilidad para horarios variables o nocturnos:</label>
                <input type="checkbox" id="disponibilidad_horarios" name="disponibilidad_horarios" value="1" <?php if(!empty($disponibilidad_horarios)) echo "checked"; ?>> <!-- Campo de entrada para la disponibilidad de horarios -->

                <label for="nivel_mecanica">Nivel de conocimiento en mecánica básica:</label>
                <input type="text" id="nivel_mecanica" name="nivel_mecanica" value="<?php echo !empty($nivel_mecanica) ? $nivel_mecanica : ''; ?>" required> <!-- Campo de entrada para el nivel de mecánica básica -->

                <label for="nivel_seguridad_vial">Nivel de conocimiento en seguridad vial y normas de tránsito:</label>
                <input type="text" id="nivel_seguridad_vial" name="nivel_seguridad_vial" value="<?php echo !empty($nivel_seguridad_vial) ? $nivel_seguridad_vial : ''; ?>" required> <!-- Campo de entrada para el nivel de seguridad vial -->

                <label for="habilidad_gps">Habilidad para el uso de GPS y otras herramientas de navegación:</label>
                <input type="text" id="habilidad_gps" name="habilidad_gps" value="<?php echo !empty($habilidad_gps) ? $habilidad_gps : ''; ?>" required> <!-- Campo de entrada para la habilidad de uso de GPS -->

                <label for="manejo_bitacoras">Manejo de bitácoras o reportes de viaje:</label>
                <input type="text" id="manejo_bitacoras" name="manejo_bitacoras" value="<?php echo !empty($manejo_bitacoras) ? $manejo_bitacoras : ''; ?>" required> <!-- Campo de entrada para el manejo de bitácoras -->
            </div>

            <div class="submit-button">
                <button type="submit">Actualizar Información</button> <!-- Botón para enviar el formulario -->
            </div>
        </form>
    </div>
</body>
</html>