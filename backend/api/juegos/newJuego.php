<?php
header("Content-Type: application/json");
require_once '../../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (!isset($data['usuario_id'], $data['categoria_id'], $data['titulo'], $data['ruta_archivos'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos. Se requieren usuario_id, categoria_id, titulo y ruta_archivos"]);
    exit;
}

$usuario_id = $data['usuario_id'];
$categoria_id = $data['categoria_id'];
$titulo = trim($data['titulo']);
$descripcion = isset($data['descripcion']) ? trim($data['descripcion']) : '';
$ruta_archivos = trim($data['ruta_archivos']);
$estado = 'pendiente';

try {
    $stmt = $pdo->prepare("INSERT INTO juegos (usuario_id, categoria_id, titulo, descripcion, ruta_archivos, estado) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $categoria_id, $titulo, $descripcion, $ruta_archivos, $estado]);
    echo json_encode(["success" => true, "message" => "Juego creado correctamente"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
