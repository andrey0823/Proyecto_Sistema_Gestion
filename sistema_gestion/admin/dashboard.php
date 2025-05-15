<?php 
include '../db/database.php';

// Verificar si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Obtener estadísticas
$stats = [
    'usuarios' => $conn->query("SELECT COUNT(*) FROM usuarios")->fetch_row()[0],
    'tareas' => $conn->query("SELECT COUNT(*) FROM tareas")->fetch_row()[0],
    'formularios' => $conn->query("SELECT COUNT(*) FROM formularios")->fetch_row()[0]
];

// Obtener listado de usuarios
$usuarios = $conn->query("
    SELECT u.*, r.nombre_rol 
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-container { max-width: 1200px; }
        .stats-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .stat-card h3 { color: #1a73e8; margin-bottom: 0.5rem; }
        .stat-card p { font-size: 1.5rem; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #1a73e8; color: white; }
        .acciones a { margin-right: 0.5rem; color: #1a73e8; text-decoration: none; }
        .acciones a:hover { text-decoration: underline; }
    </style>)
</head>
<body>
    <div class="container dashboard-container">
        <h1>Panel de Administrador</h1>
        
        <!-- Estadísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>Usuarios</h3>
                <p><?= $stats['usuarios'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Tareas</h3>
                <p><?= $stats['tareas'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Formularios</h3>
                <p><?= $stats['formularios'] ?></p>
            </div>
        </div>

        <!-- Listado de Usuarios -->
        <h2>Gestión de Usuarios</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['cedula']) ?></td>
                    <td><?= htmlspecialchars($usuario['nombre_rol']) ?></td>
                    <td class="acciones">
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>">Editar</a>
                        <a href="#" onclick="confirmarEliminacion(<?= $usuario['id'] ?>)">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Enlaces adicionales -->
        <div class="link" style="margin-top: 2rem;">
            <a href="asignar_tarea.php">Asignar Tarea</a><br>
            <a href="tareas.php">Ver Tareas</a><br>          
            <a href="../login.php?logout=true">Cerrar Sesión</a>
        </div>
    </div>

    <script>
        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de eliminar este usuario?')) {
                window.location.href = `eliminar_usuario.php?id=${id}`;
            }
        }
    </script>
</body>
</html>