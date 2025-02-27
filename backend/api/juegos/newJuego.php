<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once '../../config/config.php';

// Validar que se hayan enviado los datos obligatorios
if (!isset($_POST['usuario_id'], $_POST['categoria_id'], $_POST['titulo'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos. Se requieren usuario_id, categoria_id y titulo"]);
    exit;
}

$usuario_id   = $_POST['usuario_id'];
$categoria_id = $_POST['categoria_id'];
$titulo       = trim($_POST['titulo']);
$descripcion  = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$estado       = 'pendiente';

// Directorios de subida (ajusta las rutas según tu estructura)
$uploadDirJuegos = '../../uploads/juegos/';  // Para el ZIP y la extracción
$uploadDirLogos  = '../../uploads/logos/';

// Asegurarse de que existan los directorios
if (!file_exists($uploadDirJuegos)) {
    mkdir($uploadDirJuegos, 0777, true);
}
if (!file_exists($uploadDirLogos)) {
    mkdir($uploadDirLogos, 0777, true);
}

// Procesar el archivo ZIP del juego
if (isset($_FILES['juegoFile']) && $_FILES['juegoFile']['error'] === UPLOAD_ERR_OK) {
    $originalName = basename($_FILES['juegoFile']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($ext !== 'zip') {
        echo json_encode(["success" => false, "error" => "El archivo del juego debe ser un ZIP."]);
        exit;
    }
    // Generar un nombre único para el ZIP
    $zipFilename = uniqid() . "_" . $originalName;
    $tempZipPath = $uploadDirJuegos . $zipFilename;

    if (move_uploaded_file($_FILES['juegoFile']['tmp_name'], $tempZipPath)) {
        // Abrir y extraer el ZIP usando ZipArchive
        $zip = new ZipArchive();
        if ($zip->open($tempZipPath) === TRUE) {
            // Crear una carpeta única para extraer el contenido
            $extractionFolderName = uniqid("game_") . "/";
            $extractionFolderPath = $uploadDirJuegos . $extractionFolderName;
            if (!file_exists($extractionFolderPath)) {
                mkdir($extractionFolderPath, 0777, true);
            }
            $zip->extractTo($extractionFolderPath);
            $zip->close();
            // Eliminar el ZIP ya extraído
            unlink($tempZipPath);
            // Ruta relativa para guardar en la BBDD
            $ruta_archivos = 'uploads/juegos/' . $extractionFolderName;
        } else {
            echo json_encode(["success" => false, "error" => "Error al abrir el archivo ZIP."]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "error" => "Error al subir el archivo ZIP del juego."]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "error" => "No se ha subido el archivo del juego."]);
    exit;
}

// Procesar la imagen/logo del juego (opcional)
if (isset($_FILES['logoFile']) && $_FILES['logoFile']['error'] === UPLOAD_ERR_OK) {
    $filenameLogo = uniqid() . "_" . basename($_FILES['logoFile']['name']);
    $targetPathLogo = $uploadDirLogos . $filenameLogo;
    if (move_uploaded_file($_FILES['logoFile']['tmp_name'], $targetPathLogo)) {
        $logo = 'uploads/logos/' . $filenameLogo;
    } else {
        $logo = null;
    }
} else {
    $logo = null;
}

try {
    // Inserción en la BBDD: se almacena la ruta de la carpeta extraída en 'ruta_archivos'
    $stmt = $pdo->prepare("INSERT INTO juegos (usuario_id, categoria_id, titulo, descripcion, ruta_archivos, logo, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $categoria_id, $titulo, $descripcion, $ruta_archivos, $logo, $estado]);
    echo json_encode(["success" => true, "message" => "Juego creado correctamente"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
