<?php
header("Content-Type: application/json");
require_once '../../config/config.php';

try {

    $stmt = $pdo->prepare("SELECT juegos.id, juegos.titulo, juegos.descripcion, juegos.ruta_archivos, juegos.fecha_subida, 
                                  usuarios.username AS autor, categorias.nombre AS categoria
                           FROM juegos
                           JOIN usuarios ON juegos.usuario_id = usuarios.id
                           JOIN categorias ON juegos.categoria_id = categorias.id
                           WHERE juegos.estado = 'publicado'
                           ORDER BY juegos.fecha_subida DESC");
    $stmt->execute();
    $juegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $juegos]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
