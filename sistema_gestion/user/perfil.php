<?php
include '../db/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$usuario = $conn->query("SELECT * FROM usuarios WHERE id = {$_SESSION['user_id']}")->fetch_assoc();

// Actualizar datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    
    // Actualizar contraseña si se proporciona
    $password_sql = "";
    if (!empty($_POST['new_password'])) {
        if (password_verify($_POST['old_password'], $usuario['password'])) {
            $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            $password_sql = ", password = '$new_password'";
        } else {
            $error = "Contraseña actual incorrecta";
        }
    }
    
    $sql = "UPDATE usuarios SET 
            nombre = '$nombre',
            direccion = '$direccion',
            telefono = '$telefono'
            $password_sql
        WHERE id = {$_SESSION['user_id']}";
    
    if ($conn->query($sql)) {
        $success = "Perfil actualizado correctamente";
        $usuario = $conn->query("SELECT * FROM usuarios WHERE id = {$_SESSION['user_id']}")->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Perfil</h1>
        
        <?php if (isset($error)): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php elseif (isset($success)): ?>
            <p style="color:green;"><?= $success ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion" value="<?= htmlspecialchars($usuario['direccion']) ?>">
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="tel" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>">
            </div>
            <div class="form-group">
                <label>Contraseña Actual (para cambiar)</label>
                <input type="password" name="old_password">
            </div>
            <div class="form-group">
                <label>Nueva Contraseña</label>
                <input type="password" name="new_password">
            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
        
        <div class="link" style="margin-top:1rem;">
            <a href="user_dashboard.php">← Volver al Panel</a>
        </div>
    </div>
</body>
</html>