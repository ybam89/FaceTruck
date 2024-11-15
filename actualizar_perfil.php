<?php
session_start();

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conexión a la base de datos
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

    // Obtener el ID del operador desde la sesión
    $operador_id = $_SESSION['operador_id'];

    // Obtener los datos del formulario
    $nombre_completo = $_POST['nombre_completo'];
    $edad = $_POST['edad'];
    $ciudad = $_POST['ciudad'];
    $estado = $_POST['estado'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $experiencia_anos = $_POST['experiencia_anos'];
    $tipos_unidades = $_POST['tipos_unidades'];
    $empresas = $_POST['empresas'];
    $rutas = $_POST['rutas'];
    $licencia_tipo = $_POST['licencia_tipo'];
    $licencia_vigencia = $_POST['licencia_vigencia'];
    $materiales_peligrosos = isset($_POST['materiales_peligrosos']) ? 1 : 0;
    $otros_certificados = $_POST['otros_certificados'];
    $disponibilidad_viajar = isset($_POST['disponibilidad_viajar']) ? 1 : 0;
    $disponibilidad_horarios = isset($_POST['disponibilidad_horarios']) ? 1 : 0;
    $nivel_mecanica = $_POST['nivel_mecanica'];
    $nivel_seguridad_vial = $_POST['nivel_seguridad_vial'];
    $habilidad_gps = $_POST['habilidad_gps'];
    $manejo_bitacoras = $_POST['manejo_bitacoras'];

    // Actualizar la información del operador
    $sql = "UPDATE operadores SET 
                nombre_completo = ?, 
                edad = ?, 
                ciudad = ?, 
                estado = ?, 
                telefono = ?, 
                correo = ?, 
                experiencia_anos = ?, 
                tipos_unidades = ?, 
                empresas = ?, 
                rutas = ?, 
                licencia_tipo = ?, 
                licencia_vigencia = ?, 
                materiales_peligrosos = ?, 
                otros_certificados = ?, 
                disponibilidad_viajar = ?, 
                disponibilidad_horarios = ?, 
                nivel_mecanica = ?, 
                nivel_seguridad_vial = ?, 
                habilidad_gps = ?, 
                manejo_bitacoras = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssissssssisssssi", 
        $nombre_completo, $edad, $ciudad, $estado, $telefono, $correo, 
        $experiencia_anos, $tipos_unidades, $empresas, $rutas, 
        $licencia_tipo, $licencia_vigencia, $materiales_peligrosos, 
        $otros_certificados, $disponibilidad_viajar, $disponibilidad_horarios, 
        $nivel_mecanica, $nivel_seguridad_vial, $habilidad_gps, $manejo_bitacoras, 
        $operador_id);

    if ($stmt->execute()) {
        // Redirigir al perfil después de la actualización exitosa
        header("Location: perfil.php");
        exit();
    } else {
        echo "Error al actualizar el perfil: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Método de solicitud no válido.";
}
?>