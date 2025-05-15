<?php include 'db/database.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Tareas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $cedula = $_POST['cedula'];
            $direccion = $_POST['direccion'];
            $email = $_POST['email'];
            $telefono = $_POST['telefono'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            
            # Todos los nuevos usuarios son "usuario" por defecto (rol_id = 3)
            $sql = "INSERT INTO usuarios (nombre, cedula, direccion, email, telefono, password, rol_id) 
                    VALUES (?, ?, ?, ?, ?, ?, 3)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $nombre, $cedula, $direccion, $email, $telefono, $password);
            
            if ($stmt->execute()) {
                echo "<p style='color:green;'>Registro exitoso. ¡Bienvenido!</p>";
            } else {
                echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
            }
        }
        ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Cédula</label>
                <input type="text" name="cedula" required>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="tel" name="telefono">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Registrarse</button>
        </form>
        
        <div class="link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</body>
</html>