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

switch ($method) {
    case 'GET':
        // Lógica para lidar com requisições GET
        // Verifica se um ID foi passado pela URL
        if (isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if ($id !== false && $id > 0) {
                // Consulta para buscar um registro específico pelo ID
                $stmt = $pdo->prepare("SELECT * FROM title WHERE id_video = :id");
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
            $stmt = $pdo->query("SELECT * FROM title");
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
        // Lógica para lidar com requisições POST - Adicionar uma nova tarefa
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === "application/json") {
            // Recebe o corpo da requisição JSON
            $jsonPayload = file_get_contents('php://input');

            // Decodifica o JSON para um array associativo
            $data = json_decode($jsonPayload, true);

            // Verifica se a decodificação foi bem-sucedida e se os campos necessários existem
            if (json_last_error() === JSON_ERROR_NONE && isset($data['titulo'], $data['descricao'])) {
                $titulo = filter_var($data['titulo']);
                $descricao = filter_var($data['descricao']);

                // Validação básica dos dados (opcional, mas recomendado)
                if (empty($titulo) || empty($descricao)) {
                    http_response_code(400);
                    echo json_encode(['erro' => 'Os campos "titulo" e "descricao" são obrigatórios.']);
                    exit;
                }

                try {
                    // Prepara a consulta SQL para inserir um novo registro
                    $stmt = $pdo->prepare("INSERT INTO title (titulo, descricao) VALUES (:titulo, :descricao)");
                    $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
                    $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);

                    // Executa a consulta
                    if ($stmt->execute()) {
                        // Retorna o ID do novo registro criado
                        $lastId = $pdo->lastInsertId();
                        http_response_code(201); // Created
                        echo json_encode(['message' => 'Tarefa adicionada com sucesso!', 'id' => $lastId]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['erro' => 'Erro ao adicionar a tarefa.']);
                    }
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
                }
            } else {
                // Se o JSON for inválido ou os campos necessários não estiverem presentes
                http_response_code(400);
                echo json_encode(['erro' => 'Requisição JSON inválida ou campos ausentes.']);
            }
        } else {
            // Se o Content-Type não for application/json
            http_response_code(415); // Unsupported Media Type
            echo json_encode(['erro' => 'Content-Type nao suportado. Use application/json.']);
        }
        break;
    case 'PUT':
        // Lógica para lidar com requisições PUT - Atualizar uma tarefa existente
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === "application/json") {
            // Recebe o corpo da requisição JSON
            $jsonPayload = file_get_contents('php://input');
            $data = json_decode($jsonPayload, true);

            // Verifica se a decodificação foi bem-sucedida e se os campos necessários existem
            if (json_last_error() === JSON_ERROR_NONE && isset($data['titulo'], $data['descricao'])) {
                // Obtém o ID da tarefa da URL
                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

                if ($id !== false && $id > 0) {
                    $titulo = filter_var($data['titulo']);
                    $descricao = filter_var($data['descricao']);

                    // Validação básica dos dados (opcional, mas recomendado)
                    if (empty($titulo) || empty($descricao)) {
                        http_response_code(400);
                        echo json_encode(['erro' => 'Os campos "titulo" e "descricao" são obrigatórios para atualizar.']);
                        exit;
                    }

                    try {
                        // Prepara a consulta SQL para atualizar o registro
                        $stmt = $pdo->prepare("UPDATE title SET titulo = :titulo, descricao = :descricao WHERE id_video = :id");
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
                        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);

                        // Executa a consulta
                        if ($stmt->execute()) {
                            // Verifica se alguma linha foi afetada
                            if ($stmt->rowCount() > 0) {
                                http_response_code(200); // OK
                                echo json_encode(['message' => 'Tarefa atualizada com sucesso!']);
                            } else {
                                http_response_code(404); // Not Found - se o ID não existir
                                echo json_encode(['message' => 'Tarefa não encontrada.']);
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode(['erro' => 'Erro ao atualizar a tarefa.']);
                        }
                    } catch (PDOException $e) {
                        http_response_code(500);
                        echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
                    }
                } else {
                    // Se o ID não for válido
                    http_response_code(400);
                    echo json_encode(['message' => 'ID da tarefa inválido para atualização.']);
                }
            } else {
                // Se o JSON for inválido ou os campos necessários não estiverem presentes
                http_response_code(400);
                echo json_encode(['erro' => 'Requisição JSON inválida ou campos ausentes para atualização (titulo e descricao).']);
            }
        } else {
            // Se o Content-Type não for application/json
            http_response_code(415); // Unsupported Media Type
            echo json_encode(['erro' => 'Content-Type nao suportado. Use application/json para atualizar.']);
        }
        break;
    case 'DELETE':
        // Lógica para lidar com requisições DELETE - Deletar uma tarefa existente
        // Verifica se um ID foi passado pela URL
        if (isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

            if ($id !== false && $id > 0) {
                try {
                    // Prepara a consulta SQL para deletar o registro
                    $stmt = $pdo->prepare("DELETE FROM title WHERE id_video = :id");
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                    // Executa a consulta
                    if ($stmt->execute()) {
                        // Verifica se alguma linha foi afetada
                        if ($stmt->rowCount() > 0) {
                            http_response_code(200); // OK
                            echo json_encode(['message' => 'Tarefa deletada com sucesso!']);
                        } else {
                            http_response_code(404); // Not Found - se o ID não existir
                            echo json_encode(['message' => 'Tarefa não encontrada.']);
                        }
                    } else {
                        http_response_code(500);
                        echo json_encode(['erro' => 'Erro ao deletar a tarefa.']);
                    }
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
                }
            } else {
                // Se o ID não for válido
                http_response_code(400);
                echo json_encode(['message' => 'ID da tarefa inválido para deletar.']);
            }
        } else {
            // Se nenhum ID for fornecido para DELETE
            http_response_code(400);
            echo json_encode(['message' => 'ID da tarefa é necessário para deletar.']);
        }
        break;
    default:
        http_response_code(405); // Método não permitido
        echo json_encode(['message' => 'Metodo nao permitido']);
        break;
}