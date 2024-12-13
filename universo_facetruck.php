<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "facetruck";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    echo "Error: No se ha iniciado sesión correctamente.";
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];

date_default_timezone_set('America/Mexico_City');

$publicaciones = [];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 35;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT * FROM publicaciones ORDER BY fecha_publicacion DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $publicaciones[] = $row;
}
$stmt->close();

// Paginación
$total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM publicaciones");
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_posts = $total_row['total'];
$total_pages = ceil($total_posts / $limit);
$total_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universo FaceTruck</title>
    <style>
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
        <a href="perfil.php" class="button">Regresar</a>
        <div class="post-section">
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
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script>
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