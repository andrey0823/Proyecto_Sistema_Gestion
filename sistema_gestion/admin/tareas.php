<?php
include '../db/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Obtener todas las tareas con nombre de usuario
$tareas = $conn->query("
    SELECT t.*, u.nombre AS usuario_nombre 
    FROM tareas t
    JOIN usuarios u ON t.usuario_id = u.id
    ORDER BY t.fecha_limite DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Tareas</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-container { max-width: 1200px; }
        .estado-select { padding: 0.3rem; border-radius: 5px; }
        .estado-pendiente { color: #d35400; }
        .estado-en_progreso { color: #2980b9; }
        .estado-completada { color: #27ae60; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #1a73e8; color: white; }
        .acciones a { margin-right: 0.5rem; color: #1a73e8; text-decoration: none; }
        .acciones a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1>Tareas Asignadas</h1>
        
        <!-- Botón para nueva tarea -->
        <div style="margin-bottom: 1rem;">
            <a href="asignar_tarea.php" class="button">➕ Nueva Tarea</a>
        </div>

        <!-- Listado de Tareas -->
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Asignado a</th>
                    <th>Fecha Límite</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tarea = $tareas->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                    <td><?= htmlspecialchars($tarea['usuario_nombre']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($tarea['fecha_limite'])) ?></td>
                    <td>
                        <span class="estado-<?= $tarea['estado'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $tarea['estado'])) ?>
                        </span>
                    </td>
                    <td class="acciones">
                        <a href="editar_tarea.php?id=<?= $tarea['id'] ?>">Editar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="link" style="margin-top: 1rem;">
            <a href="dashboard.php">← Volver al Panel</a>
        </div>
    </div>
</body>
</html>