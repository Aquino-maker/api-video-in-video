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
        // Verifica se um ID foi passado pela URL
        if (isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if ($id !== false && $id > 0) {
                // Consulta para buscar um registro específico pelo ID
                $stmt = $pdo->prepare("SELECT * FROM video WHERE id_video = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verifica e retorna o código de status apropriado
                // Se o registro for encontrado, retorna os dados
                if ($data) {
                    http_response_code(200);
                    echo json_encode($data);
                } else {
                    // Se o registro não for encontrado, retorna 404 Not Found
                    http_response_code(404);
                    echo json_encode(['message' => 'Registro não encontrado']);
                }
            } else {
                // Se o ID não for válido, retorna 400 Bad Request
                http_response_code(400);
                echo json_encode(['message' => 'ID inválido']);
            }
        } else {
            // Consulta para buscar todos os registros da tabela
            $stmt = $pdo->query("SELECT * FROM video");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($data) {
                http_response_code(200);
                echo json_encode($data);
            } else {
                http_response_code(204); // No Content - se não houver dados
                echo json_encode([]);
            }
        }
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
?>