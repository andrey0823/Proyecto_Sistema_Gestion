<?php
include '../db/database.php';

// Verificar si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Obtener usuarios normales
$usuarios = $conn->query("
    SELECT * FROM usuarios 
    WHERE rol_id = 3  -- Solo usuarios normales
");

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = $_POST['descripcion'];
    $fecha_limite = $_POST['fecha_limite'];
    $usuario_id = $_POST['usuario_id'];
    
    $stmt = $conn->prepare("INSERT INTO tareas (descripcion, fecha_limite, usuario_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $descripcion, $fecha_limite, $usuario_id);
    
    if ($stmt->execute()) {
        header("Location: tareas.php?success=1");
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Tarea</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .datetime-local { width: 100%; padding: 0.8rem; }
        select { width: 100%; padding: 0.8rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Asignar Nueva Tarea</h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Descripción de la Tarea</label>
                <textarea name="descripcion" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label>Fecha Límite</label>
                <input type="datetime-local" name="fecha_limite" class="datetime-local" required>
            </div>
            <div class="form-group">
                <label>Asignar a</label>
                <select name="usuario_id" required>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <option value="<?= $usuario['id'] ?>">
                            <?= htmlspecialchars($usuario['nombre']) ?> (<?= $usuario['cedula'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit">Asignar Tarea</button>
        </form>
        
        <div class="link" style="margin-top: 1rem;">
            <a href="tareas.php">← Ver Todas las Tareas</a><br>
            <a href="dashboard.php">← Volver al Panel</a>
        </div>
    </div>
</body>
</html>