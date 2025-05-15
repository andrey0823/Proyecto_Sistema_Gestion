<?php
include '../db/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Validar que tarea_id existe y es un número
if (!isset($_GET['tarea_id']) || !is_numeric($_GET['tarea_id'])) {
    die("ID de tarea inválido");
}

$tarea_id = (int)$_GET['tarea_id'];

// Consulta preparada para evitar inyección SQL
$stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ?");
$stmt->bind_param("i", $tarea_id);
$stmt->execute();
$tarea_result = $stmt->get_result();

// Validar que la tarea existe
if ($tarea_result->num_rows === 0) {
    die("Tarea no encontrada");
}

$tarea = $tarea_result->fetch_assoc(); // Guardar el resultado en una variable

// Validar acceso del usuario
if ($tarea['usuario_id'] != $_SESSION['user_id']) {
    die("Acceso no autorizado");
}

// Procesar comentarios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contenido = $conn->real_escape_string($_POST['contenido']); // Sanitizar entrada
    $conn->query("
        INSERT INTO comentarios (contenido, usuario_id, tarea_id)
        VALUES ('$contenido', {$_SESSION['user_id']}, $tarea_id)
    ");
}

// Obtener comentarios
$comentarios = $conn->query("
    SELECT c.*, u.nombre 
    FROM comentarios c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE tarea_id = $tarea_id
    ORDER BY fecha DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Comentarios</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Comentarios: <?= htmlspecialchars($tarea['descripcion']) ?></h1>
        
        <!-- Formulario de Comentario -->
        <form method="POST" style="margin-bottom: 2rem;">
            <div class="form-group">
                <textarea name="contenido" rows="3" placeholder="Escribe un comentario..." required></textarea>
            </div>
            <button type="submit">Publicar</button>
        </form>

        <!-- Lista de Comentarios -->
        <div class="comentarios">
            <?php while ($comentario = $comentarios->fetch_assoc()): ?>
                <div class="comentario">
                    <strong><?= htmlspecialchars($comentario['nombre']) ?></strong>
                    <small><?= date('d/m/Y H:i', strtotime($comentario['fecha'])) ?></small>
                    <p><?= nl2br(htmlspecialchars($comentario['contenido'])) ?></p>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="link">
            <a href="user_dashboard.php">← Volver al Panel</a>
        </div>
    </div>
</body>
</html>