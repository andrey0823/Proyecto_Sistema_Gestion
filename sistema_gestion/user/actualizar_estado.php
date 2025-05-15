<?php
// Incluir la conexión y asegurar que la sesión se inicia
include '../db/database.php'; // Ajusta la ruta según tu estructura

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Procesar la actualización del estado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tarea_id = $_POST['tarea_id'];
    $estado = $_POST['estado'];
    $user_id = $_SESSION['user_id'];

    // Validar que la tarea pertenece al usuario
    $stmt = $conn->prepare("
        UPDATE tareas 
        SET estado = ? 
        WHERE id = ? 
        AND usuario_id = ?  # ¡Clave para seguridad!
    ");
    $stmt->bind_param("sii", $estado, $tarea_id, $user_id);

    if ($stmt->execute()) {
        // Redirigir al dashboard del usuario
        header("Location: user_dashboard.php?success=1");
    } else {
        header("Location: user_dashboard.php?error=1");
    }
    exit();
}

// Si no es POST, redirigir al login
header("Location: ../login.php");
exit();
?>