<?php include 'db/database.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Tareas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>


    <?php 

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
    
            $sql = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
            // Iniciar sesión correctamente
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_role'] = $user['rol_id'];
                        $_SESSION['user_nombre'] = $user['nombre']; // <-- Añadido para el dashboard
            
            // Redirección basada en rol
                    if ($user['rol_id'] == 1) {
                        header("Location: admin/dashboard.php");
                    } else if ($user['rol_id'] == 3) {
                        header("Location: user/user_dashboard.php");
                    }
                    exit();
                } else {
                    $error = "Contraseña incorrecta";
                }

            } else {
                $error = "Usuario no encontrado";
            }
        }
    ?>


        
        <form method="POST">
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Ingresar</button>
        </form>
        
        <div class="link">
            ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
        </div>
    </div>
</body>
</html>