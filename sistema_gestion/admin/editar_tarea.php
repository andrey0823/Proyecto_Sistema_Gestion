<?php
include '../db/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Obtener tarea a editar
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tarea = $stmt->get_result()->fetch_assoc();

// Obtener usuarios para reasignación
$usuarios = $conn->query("SELECT * FROM usuarios WHERE rol_id = 3");

// Actualizar tarea
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $estado = $_POST['estado'];
    $usuario_id = $_POST['usuario_id'];
    
    $stmt = $conn->prepare("UPDATE tareas SET estado = ?, usuario_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $estado, $usuario_id, $id);
    
    if ($stmt->execute()) {
        header("Location: tareas.php?success=2");
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Tarea</h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="estado-select" required>
                    <option value="pendiente" <?= $tarea['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en_progreso" <?= $tarea['estado'] == 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                    <option value="completada" <?= $tarea['estado'] == 'completada' ? 'selected' : '' ?>>Completada</option>
                </select>
            </div>
            <div class="form-group">
                <label>Reasignar a</label>
                <select name="usuario_id" required>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <option value="<?= $usuario['id'] ?>" <?= $usuario['id'] == $tarea['usuario_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit">Guardar Cambios</button>
        </form>
        
        <div class="link" style="margin-top: 1rem;">
            <a href="tareas.php">← Volver a Tareas</a>
        </div>
    </div>
</body>
</html>