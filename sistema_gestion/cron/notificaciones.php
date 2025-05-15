<?php
require __DIR__ . '/../db/database.php';

// Obtener tareas que vencen en 24 horas y no han sido notificadas
$sql = "
    SELECT t.*, u.email, u.nombre 
    FROM tareas t
    JOIN usuarios u ON t.usuario_id = u.id
    WHERE 
        t.fecha_limite BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 DAY)
        AND (t.last_notified IS NULL OR t.last_notified < DATE_SUB(t.fecha_limite, INTERVAL 1 DAY))
";

$tareas = $conn->query($sql);

while ($tarea = $tareas->fetch_assoc()) {
    $para = $tarea['email'];
    $asunto = "⚠️ Tarea Próxima a Vencer: " . substr($tarea['descripcion'], 0, 30);
    $mensaje = "
        Hola {$tarea['nombre']},\n\n
        La tarea '{$tarea['descripcion']}' vence el " . date('d/m/Y H:i', strtotime($tarea['fecha_limite'])) . ".\n
        Estado actual: " . ucfirst($tarea['estado']) . "\n\n
        ¡No olvides completarla!\n
        Saludos,\n
        Equipo de Gestión de Tareas
    ";

    // Enviar email (configura tu servidor SMTP)
    if (mail($para, $asunto, $mensaje)) {
        // Actualizar last_notified
        $conn->query("UPDATE tareas SET last_notified = NOW() WHERE id = {$tarea['id']}");
    }
}

echo "Notificaciones procesadas: " . $tareas->num_rows;