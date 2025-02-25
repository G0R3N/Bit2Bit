<?php
header("Content-Type: application/json");
require_once '../../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (!isset($data['email'], $data['password'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

$email = trim($data['email']);
$password = trim($data['password']);

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(["success" => true, "message" => "Login exitoso", "user" => $user]);
        } else {
            echo json_encode(["success" => false, "error" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Usuario no encontrado"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
