<?php
// Deshabilitar la caché en la respuesta
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Clave de la API de YouTube
define('YOUTUBE_API_KEY', 'AIzaSyCwd8YtT5_BOLjjA0r4cYxNkZ-mWOR_9GY');
define('YOUTUBE_SEARCH_URL', 'https://www.googleapis.com/youtube/v3/search');

// Requerir archivos de datos
require __DIR__ . '/data/items.php';
require __DIR__ . '/inc/functions.php';

// Definir la acción solicitada
$action = $_GET['action'] ?? '';


// Obtener video de gameplay para un juego
if ($action === 'get_gameplay') {
    $id = intval($_GET['id'] ?? 0);
    $item = get_item_by_id($items, $id);

    if (!$item) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Juego no encontrado']);
        exit;
    }

    // Construir la consulta de búsqueda
    $query = urlencode($item['titulo'] . ' gameplay trailer');
    
    // Parámetros de la API de YouTube
    $params = [
        'part'       => 'snippet',
        'q'          => $query,
        'key'        => YOUTUBE_API_KEY,
        'type'       => 'video', 
        'maxResults' => 1,       
    ];

    $apiUrl = YOUTUBE_SEARCH_URL . '?' . http_build_query($params);

    // Hacer la petición a la API de YouTube
    $response = @file_get_contents($apiUrl);

    if ($response === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al conectar con la API de YouTube.']);
        exit;
    }

    $data = json_decode($response, true);
    $videoId = null;
    $videoTitle = null;

    if (isset($data['items'][0]['id']['videoId'])) {
        $videoId = $data['items'][0]['id']['videoId'];
        $videoTitle = $data['items'][0]['snippet']['title'];
    }

    if ($videoId) {
        echo json_encode([
            'success' => true,
            'videoId' => $videoId,
            'videoTitle' => $videoTitle
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron gameplays para este juego.'
        ]);
    }

} else {
    // Si no se especifica una acción válida
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}