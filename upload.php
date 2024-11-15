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
        echo "Conexión fallida: " . $conn->connect_error;
        header("Location: perfil.php");
        exit();
    }

    // Verificar si el ID del operador está definido en la sesión
    if (!isset($_SESSION['operador_id'])) {
        echo "Error: operador_id no está definido en la sesión.";
        header("Location: perfil.php");
        exit();
    }
    $operador_id = $_SESSION['operador_id'];

    // Verificar si se ha seleccionado un archivo
    if (!isset($_FILES["fileToUpload"]) || $_FILES["fileToUpload"]["error"] == UPLOAD_ERR_NO_FILE) {
        echo "No se ha seleccionado ningún archivo.";
        header("Location: perfil.php");
        exit();
    }

    // Directorio donde se subirán las imágenes
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es una imagen real
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
        header("Location: perfil.php");
        exit();
    }

    // Verificar si el archivo ya existe
    if (file_exists($target_file)) {
        echo "Lo sentimos, el archivo ya existe.";
        $uploadOk = 0;
        header("Location: perfil.php");
        exit();
    }

    // Verificar el tamaño del archivo
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Lo sentimos, tu archivo es demasiado grande.";
        $uploadOk = 0;
        header("Location: perfil.php");
        exit();
    }

    // Permitir ciertos formatos de archivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Lo sentimos, solo se permiten archivos JPG, JPEG, PNG y GIF.";
        $uploadOk = 0;
        header("Location: perfil.php");
        exit();
    }

    // Verificar si $uploadOk está establecido a 0 por un error
    if ($uploadOk == 0) {
        echo "Lo sentimos, tu archivo no fue subido.";
        header("Location: perfil.php");
        exit();
    // Si todo está bien, intenta subir el archivo
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            // Actualizar la base de datos con la nueva ruta de la imagen
            $sql = "UPDATE operadores SET foto_perfil = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $target_file, $operador_id);

            if ($stmt->execute()) {
                header("Location: perfil.php");
                exit();
            } else {
                echo "Error al actualizar la foto de perfil: " . $stmt->error;
                header("Location: perfil.php");
                exit();
            }

            $stmt->close();
        } else {
            echo "Lo sentimos, hubo un error al subir tu archivo.";
            header("Location: perfil.php");
            exit();
        }
    }

    $conn->close();
} else {
    echo "Método de solicitud no válido.";
    header("Location: perfil.php");
    exit();
}
?>