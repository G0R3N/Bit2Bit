<?php
header("Content-Type: application/json");
require_once '../../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (!isset($data['username'], $data['email'], $data['password'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

$username = trim($data['username']);
$email = trim($data['email']);
$password = trim($data['password']);
$rol = isset($data['rol']) ? trim($data['rol']) : 'usuario';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Email inválido"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashedPassword, $rol]);
    echo json_encode(["success" => true, "message" => "Usuario creado correctamente"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
