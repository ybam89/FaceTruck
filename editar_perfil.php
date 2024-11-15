<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil del Operador - FaceTruck</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden; /* Evita que la página se desplace */
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            max-height: 90vh; /* Limita la altura máxima del contenedor */
            overflow-y: auto; /* Habilita el desplazamiento vertical */
        }
        h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        .section input, .section textarea {
            width: calc(100% - 20px);
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .section input[type="checkbox"] {
            width: auto;
        }
        .submit-button {
            text-align: center;
            margin-top: 20px;
        }
        .submit-button button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        .submit-button button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Información Personal</h2>
        <form action="actualizar_perfil.php" method="post">
            <div class="section">
                <label for="nombre_completo">Nombre completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo !empty($nombre_completo) ? $nombre_completo : ''; ?>" required>
                
                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" value="<?php echo !empty($edad) ? $edad : ''; ?>" required>
                
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo !empty($ciudad) ? $ciudad : ''; ?>" required>
                
                <label for="estado">Estado:</label>
                <input type="text" id="estado" name="estado" value="<?php echo !empty($estado) ? $estado : ''; ?>" required>
                
                <label for="telefono">Teléfono de contacto:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo !empty($telefono) ? $telefono : ''; ?>" required>
                
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" value="<?php echo !empty($correo) ? $correo : ''; ?>" required>

                <label for="experiencia_anos">Años de experiencia:</label>
                <input type="number" id="experiencia_anos" name="experiencia_anos" value="<?php echo !empty($experiencia_anos) ? $experiencia_anos : ''; ?>" required>

                <label for="tipos_unidades">Tipo de unidades que he manejado:</label>
                <textarea id="tipos_unidades" name="tipos_unidades" required><?php echo !empty($tipos_unidades) ? $tipos_unidades : ''; ?></textarea>

                <label for="empresas">Empresas anteriores y duración del empleo:</label>
                <textarea id="empresas" name="empresas" required><?php echo !empty($empresas) ? $empresas : ''; ?></textarea>

                <label for="rutas">Rutas manejadas:</label>
                <textarea id="rutas" name="rutas" required><?php echo !empty($rutas) ? $rutas : ''; ?></textarea>

                <label for="licencia_tipo">Tipo de licencia de conducir:</label>
                <input type="text" id="licencia_tipo" name="licencia_tipo" value="<?php echo !empty($licencia_tipo) ? $licencia_tipo : ''; ?>" required>

                <label for="licencia_vigencia">Vigencia de la licencia:</label>
                <input type="date" id="licencia_vigencia" name="licencia_vigencia" value="<?php echo !empty($licencia_vigencia) ? $licencia_vigencia : ''; ?>" required>

                <label for="materiales_peligrosos">¿Cuenta con certificación para manejo de materiales peligrosos?</label>
                <input type="checkbox" id="materiales_peligrosos" name="materiales_peligrosos" value="1" <?php if(!empty($materiales_peligrosos)) echo "checked"; ?>>

                <label for="otros_certificados">Otros certificados o capacitaciones relevantes:</label>
                <textarea id="otros_certificados" name="otros_certificados" required><?php echo !empty($otros_certificados) ? $otros_certificados : ''; ?></textarea>

                <label for="disponibilidad_viajar">¿Está dispuesto a viajar o hacer rutas nacionales?</label>
                <input type="checkbox" id="disponibilidad_viajar" name="disponibilidad_viajar" value="1" <?php if(!empty($disponibilidad_viajar)) echo "checked"; ?>>

                <label for="disponibilidad_horarios">Disponibilidad para horarios variables o nocturnos:</label>
                <input type="checkbox" id="disponibilidad_horarios" name="disponibilidad_horarios" value="1" <?php if(!empty($disponibilidad_horarios)) echo "checked"; ?>>

                <label for="nivel_mecanica">Nivel de conocimiento en mecánica básica:</label>
                <input type="text" id="nivel_mecanica" name="nivel_mecanica" value="<?php echo !empty($nivel_mecanica) ? $nivel_mecanica : ''; ?>" required>

                <label for="nivel_seguridad_vial">Nivel de conocimiento en seguridad vial y normas de tránsito:</label>
                <input type="text" id="nivel_seguridad_vial" name="nivel_seguridad_vial" value="<?php echo !empty($nivel_seguridad_vial) ? $nivel_seguridad_vial : ''; ?>" required>

                <label for="habilidad_gps">Habilidad para el uso de GPS y otras herramientas de navegación:</label>
                <input type="text" id="habilidad_gps" name="habilidad_gps" value="<?php echo !empty($habilidad_gps) ? $habilidad_gps : ''; ?>" required>

                <label for="manejo_bitacoras">Manejo de bitácoras o reportes de viaje:</label>
                <input type="text" id="manejo_bitacoras" name="manejo_bitacoras" value="<?php echo !empty($manejo_bitacoras) ? $manejo_bitacoras : ''; ?>" required>
            </div>

            <div class="submit-button">
                <button type="submit">Actualizar Información</button>
            </div>
        </form>
    </div>
</body>
</html>