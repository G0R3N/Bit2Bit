<?php
header("Content-Type: application/json");
require_once '../../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (!isset($data['juego_id'], $data['usuario_id'], $data['comentario'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos. Se requieren juego_id, usuario_id y comentario"]);
    exit;
}

$juego_id = $data['juego_id'];
$usuario_id = $data['usuario_id'];
$comentario = trim($data['comentario']);

try {
    $stmt = $pdo->prepare("INSERT INTO comentarios (juego_id, usuario_id, comentario) VALUES (?, ?, ?)");
    $stmt->execute([$juego_id, $usuario_id, $comentario]);
    echo json_encode(["success" => true, "message" => "Comentario creado correctamente"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
