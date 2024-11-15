<?php
// editar_perfil.php
session_start();

// Aquí debes agregar la lógica para obtener la información actual del operador desde la base de datos

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - FaceTruck</title>
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
        }
        h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        .section input, .section textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .section textarea {
            height: 100px;
        }
        .section button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Información Personal</h2>
        <form action="actualizar_perfil.php" method="post">
            <div class="section">
                <label for="nombre_completo">Nombre completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo $nombre_completo; ?>" required>
                
                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" value="<?php echo $edad; ?>" required>
                
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo $ciudad; ?>" required>
                
                <label for="estado">Estado:</label>
                <input type="text" id="estado" name="estado" value="<?php echo $estado; ?>" required>
                
                <label for="telefono">Teléfono de contacto:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo $telefono; ?>" required>
                
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" value="<?php echo $correo; ?>" required>

                <label for="experiencia_años">Años de experiencia:</label>
                <input type="number" id="experiencia_años" name="experiencia_años" value="<?php echo $experiencia['años']; ?>" required>

                <label for="tipos_unidades">Tipo de unidades que he manejado:</label>
                <input type="text" id="tipos_unidades" name="tipos_unidades" value="<?php echo $experiencia['tipos_unidades']; ?>" required>

                <label for="empresas">Empresas anteriores y duración del empleo:</label>
                <textarea id="empresas" name="empresas" required><?php echo $experiencia['empresas']; ?></textarea>

                <label for="rutas">Rutas manejadas:</label>
                <textarea id="rutas" name="rutas" required><?php echo $experiencia['rutas']; ?></textarea>

                <label for="licencia_tipo">Tipo de licencia de conducir:</label>
                <input type="text" id="licencia_tipo" name="licencia_tipo" value="<?php echo $licencias['tipo']; ?>" required>

                <label for="licencia_vigencia">Vigencia de la licencia:</label>
                <input type="date" id="licencia_vigencia" name="licencia_vigencia" value="<?php echo $licencias['vigencia']; ?>" required>

                <label for="materiales_peligrosos">¿Cuenta con certificación para manejo de materiales peligrosos?</label>
                <input type="text" id="materiales_peligrosos" name="materiales_peligrosos" value="<?php echo $licencias['materiales_peligrosos']; ?>" required>

                <label for="otros_certificados">Otros certificados o capacitaciones relevantes:</label>
                <textarea id="otros_certificados" name="otros_certificados" required><?php echo $licencias['otros_certificados']; ?></textarea>

                <label for="disponibilidad_viajar">¿Está dispuesto a viajar o hacer rutas nacionales?</label>
                <input type="text" id="disponibilidad_viajar" name="disponibilidad_viajar" value="<?php echo $disponibilidad['viajar']; ?>" required>

                <label for="horarios_variables">Disponibilidad para horarios variables o nocturnos:</label>
                <input type="text" id="horarios_variables" name="horarios_variables" value="<?php echo $disponibilidad['horarios_var']; ?>" required>

                <label for="mecanica">Nivel de conocimiento en mecánica básica:</label>
                <input type="text" id="mecanica" name="mecanica" value="<?php echo $competencias['mecanica']; ?>" required>

                <label for="seguridad_vial">Nivel de conocimiento en seguridad vial y normas de tránsito:</label>
                <input type="text" id="seguridad_vial" name="seguridad_vial" value="<?php echo $competencias['seguridad_vial']; ?>" required>

                <label for="gps">Habilidad para el uso de GPS y otras herramientas de navegación:</label>
                <input type="text" id="gps" name="gps" value="<?php echo $competencias['gps']; ?>" required>

                <label for="bitacoras">Manejo de bitácoras o reportes de viaje:</label>
                <input type="text" id="bitacoras" name="bitacoras" value="<?php echo $competencias['bitacoras']; ?>" required>

            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>