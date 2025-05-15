<?php
include '../db/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Eliminar usuario
$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=2");
} else {
    header("Location: dashboard.php?error=1");
}