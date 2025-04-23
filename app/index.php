<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link rel="shortcut icon" href="./img/icon.ico" type="image/x-icon">
    <title>VideoinVideo</title>
</head>
<body>
        <img src="./img/VID.png" width="200" height="200" alt="">
       
    <h2>Listar todos os vídeos</h2>
    <form method="get" action="">
        <input type="hidden" name="acao" value="listar">
        <button type="submit">Listar Vídeos</button>
    </form>
    <div id="resultados">
        <?php
        if (isset($_GET['acao']) && $_GET['acao'] === 'listar') {
            $apiUrl = 'http://localhost/VIDEOinVIDEO/api-video-in-video/api/api.php'; // Substitua pelo caminho correto
            $response = file_get_contents($apiUrl);
            if ($response !== false) {
                $data = json_decode($response, true);
                if ($data && is_array($data)) {
                    foreach ($data as $video) {
                        echo '<div class="video-item">';
                        echo '<strong>ID:</strong> ' . htmlspecialchars($video['id_video']) . '<br>';
                        echo '<strong>Título:</strong> ' . htmlspecialchars($video['titulo']) . '<br>';
                        echo '<strong>Descrição:</strong> ' . htmlspecialchars($video['descricao']) . '<br>';
                        echo '</div>';
                    }
                } elseif (empty($data)) {
                    echo '<p>Nenhum vídeo encontrado.</p>';
                } else {
                    echo '<p class="erro">Erro ao decodificar a resposta da API.</p>';
                }
            } else {
                echo '<p class="erro">Erro ao acessar a API.</p>';
            }
        }
        ?>
    </div>

    <h2>Buscar vídeo por ID</h2>
    <form method="get" action="">
        <label for="buscar_id">ID do Vídeo:</label>
        <input type="number" id="buscar_id" name="id" required>
        <input type="hidden" name="acao" value="buscar">
        <button type="submit">Buscar</button>
    </form>
    <div id="resultado-busca">
        <?php
        if (isset($_GET['acao']) && $_GET['acao'] === 'buscar' && isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if ($id !== false && $id > 0) {
                $apiUrl = 'http://localhost/frudru/api-video-in-video/api/api.php?id=' . $id; // Substitua pelo caminho correto
                $response = file_get_contents($apiUrl);
                if ($response !== false) {
                    $data = json_decode($response, true);
                    if ($data && isset($data['id_video'])) {
                        echo '<p><strong>ID:</strong> ' . htmlspecialchars($data['id_video']) . '<br>';
                        echo '<strong>Título:</strong> ' . htmlspecialchars($data['titulo']) . '<br>';
                        echo '<strong>Descrição:</strong> ' . htmlspecialchars($data['descricao']) . '</p>';
                    } elseif (isset($data['message'])) {
                        echo '<p class="erro">' . htmlspecialchars($data['message']) . '</p>';
                    } else {
                        echo '<p>Nenhum resultado encontrado.</p>';
                    }
                } else {
                    echo '<p class="erro">Erro ao acessar a API.</p>';
                }
            } else {
                echo '<p class="erro">ID inválido.</p>';
            }
        }
        ?>
    </div>

    <h2>Adicionar novo vídeo</h2>
    <form method="post" action="">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required>
        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required></textarea>
        <input type="hidden" name="acao" value="adicionar">
        <button type="submit">Adicionar</button>
    </form>
    <div id="resultado-adicionar">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
            $apiUrl = 'http://localhost/frudru/api-video-in-video/api/api.php'; // Substitua pelo caminho correto
            $titulo = filter_input(INPUT_POST, 'titulo');
            $descricao = filter_input(INPUT_POST, 'descricao');
            $data = json_encode(['titulo' => $titulo, 'descricao' => $descricao]);

            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/json',
                    'content' => $data,
                ],
            ];
            $context = stream_context_create($options);
            $response = file_get_contents($apiUrl, false, $context);

            if ($response !== false) {
                $result = json_decode($response, true);
                if (isset($result['message'])) {
                    echo '<p class="sucesso">' . htmlspecialchars($result['message']);
                    if (isset($result['id'])) {
                        echo ' ID: ' . htmlspecialchars($result['id']);
                    }
                    echo '</p>';
                } elseif (isset($result['erro'])) {
                    echo '<p class="erro">' . htmlspecialchars($result['erro']) . '</p>';
                } else {
                    echo '<p class="erro">Resposta inesperada da API.</p>';
                }
            } else {
                echo '<p class="erro">Erro ao enviar dados para a API.</p>';
            }
        }
        ?>
    </div>

    <h2>Atualizar vídeo existente</h2>
    <form method="post" action="">
        <label for="atualizar_id">ID do Vídeo a Atualizar:</label>
        <input type="number" id="atualizar_id" name="id" required>
        <label for="atualizar_titulo">Novo Título:</label>
        <input type="text" id="atualizar_titulo" name="titulo" required>
        <label for="atualizar_descricao">Nova Descrição:</label>
        <textarea id="atualizar_descricao" name="descricao" required></textarea>
        <input type="hidden" name="acao" value="atualizar">
        <input type="hidden" name="_method" value="PUT"> <button type="submit">Atualizar</button>
    </form>
    <div id="resultado-atualizar">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar' && isset($_POST['id'])) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $titulo = filter_input(INPUT_POST, 'titulo');
            $descricao = filter_input(INPUT_POST, 'descricao');
            $apiUrl = 'http://localhost/frudru/api-video-in-video/api/api.php?id=' . $id; // Substitua pelo caminho correto
            $data = json_encode(['titulo' => $titulo, 'descricao' => $descricao]);

            $options = [
                'http' => [
                    'method' => 'PUT',
                    'header' => 'Content-type: application/json',
                    'content' => $data,
                ],
            ];
            $context = stream_context_create($options);
            $response = file_get_contents($apiUrl, false, $context);

            if ($response !== false) {
                $result = json_decode($response, true);
                if (isset($result['message'])) {
                    echo '<p class="sucesso">' . htmlspecialchars($result['message']) . '</p>';
                } elseif (isset($result['erro'])) {
                    echo '<p class="erro">' . htmlspecialchars($result['erro']) . '</p>';
                } else {
                    echo '<p class="erro">Resposta inesperada da API.</p>';
                }
            } else {
                echo '<p class="erro">Erro ao enviar dados para a API.</p>';
            }
        }
        ?>
    </div>

    <h2>Deletar vídeo</h2>
    <form method="post" action="">
        <label for="deletar_id">ID do Vídeo a Deletar:</label>
        <input type="number" id="deletar_id" name="id" required>
        <input type="hidden" name="acao" value="deletar">
        <input type="hidden" name="_method" value="DELETE"> <button type="submit">Deletar</button>
    </form>
    <div id="resultado-deletar">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'deletar' && isset($_POST['id'])) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $apiUrl = 'http://localhost/frudru/api-video-in-video/api/api.php?id=' . $id; // Substitua pelo caminho correto

            $options = [
                'http' => [
                    'method' => 'DELETE',
                ],
            ];
            $context = stream_context_create($options);
            $response = file_get_contents($apiUrl, false, $context);

            if ($response !== false) {
                $result = json_decode($response, true);
                if (isset($result['message'])) {
                    echo '<p class="sucesso">' . htmlspecialchars($result['message']) . '</p>';
                } elseif (isset($result['erro'])) {
                    echo '<p class="erro">' . htmlspecialchars($result['erro']) . '</p>';
                } else {
                    echo '<p class="erro">Resposta inesperada da API.</p>';
                }
            } else {
                echo '<p class="erro">Erro ao enviar a requisição para a API.</p>';
            }
        }
        ?>
    </div>

    <footer>
  <p>&copy; 2025 Todos os direitos reservados a VIDEOinVIDEO.</p>
  <p>É proibida a reprodução total ou parcial deste conteúdo sem autorização prévia.</p>
</footer>

</body>
</html>