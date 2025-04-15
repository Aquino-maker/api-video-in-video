<?php 
// definindo conexão com o banco de dados
$host = 'localhost';
$db = 'video_in_video_api';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
//tratamento de erros de conexão
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()]);
    exit;
}

//Roteamento de requisição
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Lógica para lidar com requisições GET
        break;
    case 'POST':
        // Lógica para lidar com requisições POST
        break;
    case 'PUT':
        // Lógica para lidar com requisições PUT
        break;
    case 'DELETE':
        // Lógica para lidar com requisições DELETE
        break;
    default:
        http_response_code(405); // Método não permitido
        echo json_encode(['message' => 'Método não permitido']);
        break;
}

?>