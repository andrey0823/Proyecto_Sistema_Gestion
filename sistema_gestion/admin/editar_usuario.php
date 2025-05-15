<?php
include '../db/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Obtener usuario a editar
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Obtener roles disponibles
$roles = $conn->query("SELECT * FROM roles");

// Actualizar datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rol_id = $_POST['rol_id'];
    $stmt = $conn->prepare("UPDATE usuarios SET rol_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $rol_id, $id);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1");
    } else {
        echo "<p style='color:red;'>Error al actualizar: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Usuario: <?= htmlspecialchars($usuario['nombre']) ?></h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Rol</label>
                <select name="rol_id" required>
                    <?php while ($rol = $roles->fetch_assoc()): ?>
                    <option value="<?= $rol['id'] ?>" <?= $rol['id'] == $usuario['rol_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($rol['nombre_rol']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit">Actualizar Rol</button>
        </form>
        
        <div class="link" style="margin-top: 1rem;">
            <a href="dashboard.php">← Volver al Panel</a>
        </div>
    </div>
</body>
</html>