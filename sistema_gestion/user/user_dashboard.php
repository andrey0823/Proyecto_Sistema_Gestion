<?php
include '../db/database.php';

// Verificar sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 3) {
    header("Location: ../login.php"); // <-- Redirige a login
    exit();
}
// Obtener tareas del usuario
$user_id = $_SESSION['user_id'];
$tareas = $conn->query("
    SELECT * FROM tareas 
    WHERE usuario_id = $user_id
    ORDER BY fecha_limite ASC
");

// Obtener estadísticas personales
$stats = $conn->query("
    SELECT 
        COUNT(*) AS total,
        SUM(estado = 'pendiente') AS pendientes,
        SUM(estado = 'en_progreso') AS en_progreso,
        SUM(estado = 'completada') AS completadas
    FROM tareas 
    WHERE usuario_id = $user_id
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-container { max-width: 1200px; }
        .stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #fff; padding: 1rem; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-card h3 { color: #333; font-size: 1rem; margin-bottom: 0.5rem; }
        .stat-card p { font-size: 1.5rem; font-weight: bold; }
        .estado-form { display: flex; gap: 0.5rem; align-items: center; }
        .estado-select { padding: 0.3rem; border-radius: 5px; width: 150px; }
        .btn-actualizar { padding: 0.3rem 0.8rem; background: #1a73e8; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_nombre']) ?></h1>
        
        <!-- Estadísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>Tareas Totales</h3>
                <p><?= $stats['total'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Pendientes</h3>
                <p><?= $stats['pendientes'] ?></p>
            </div>
            <div class="stat-card">
                <h3>En Progreso</h3>
                <p><?= $stats['en_progreso'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Completadas</h3>
                <p><?= $stats['completadas'] ?></p>
            </div>
        </div>

        <!-- Listado de Tareas -->
        <h2>Tus Tareas</h2>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Fecha Límite</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tarea = $tareas->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($tarea['fecha_limite'])) ?></td>
                    <td>
                        <form method="POST" action="actualizar_estado.php" class="estado-form">
                            <input type="hidden" name="tarea_id" value="<?= $tarea['id'] ?>">
                            <select name="estado" class="estado-select" required>
                                <option value="pendiente" <?= $tarea['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="en_progreso" <?= $tarea['estado'] == 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                                <option value="completada" <?= $tarea['estado'] == 'completada' ? 'selected' : '' ?>>Completada</option>
                            </select>
                            <button type="submit" class="btn-actualizar">Actualizar</button>
                        </form>
                    </td>
                    <td>
                        <?php if ($tarea['estado'] == 'completada'): ?>
                            ✅ Completada
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="link" style="margin-top: 1rem;">
            <a href="perfil.php">Actualizar Perfil</a><br>
            <a href="../logout.php">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>