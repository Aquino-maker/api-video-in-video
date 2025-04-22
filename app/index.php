<?php
$apiUrl = 'http://localhost/teste/api/api.php'; // Substitua pelo caminho correto da sua API

$dados = null;
$erro = null;
$mensagem = null;

// Função genérica para fazer requisições HTTP
function fazerRequisicao($url, $method = 'GET', $data = null) {
    $options = [
        'http' => [
            'method' => $method,
            'header' => 'Content-type: application/json',
            'content' => $data ? json_encode($data) : false
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === false) {
        $error = error_get_last();
        throw new Exception("Erro na requisição HTTP: " . $error['message']);
    }
    return $result;
}

// Lógica para buscar todos os dados
if (isset($_POST['buscar_todos'])) {
    try {
        $dados = json_decode(fazerRequisicao($apiUrl), true);
        if (empty($dados)) {
            $mensagem = "Nenhum dado encontrado.";
        }
    } catch (Exception $e) {
        $erro = "Erro ao buscar os dados: " . $e->getMessage();
    }
}

// Lógica para buscar por ID
if (isset($_POST['buscar_por_id']) && !empty($_POST['id_buscar'])) {
    $id = filter_input(INPUT_POST, 'id_buscar', FILTER_SANITIZE_NUMBER_INT);
    if ($id !== false && $id > 0) {
        try {
            $resultado = fazerRequisicao("$apiUrl?id=$id");
            $dados = json_decode($resultado, true);
            if (empty($dados)) {
                $mensagem = "Registro com ID $id não encontrado.";
            }
        } catch (Exception $e) {
            $erro = "Erro ao buscar o ID $id: " . $e->getMessage();
        }
    } else {
        $erro = "ID inválido para busca.";
    }
}

// Lógica para adicionar novo dado
if (isset($_POST['adicionar']) && !empty($_POST['novo_titulo']) && !empty($_POST['nova_descricao'])) {
    $novoTitulo = filter_input(INPUT_POST, 'novo_titulo', FILTER_SANITIZE_STRING);
    $novaDescricao = filter_input(INPUT_POST, 'nova_descricao', FILTER_SANITIZE_STRING);
    $novoDado = ['titulo' => $novoTitulo, 'descricao' => $novaDescricao];
    try {
        $resultado = fazerRequisicao($apiUrl, 'POST', $novoDado);
        $resposta = json_decode($resultado, true);
        if (isset($resposta['message'])) {
            $mensagem = $resposta['message'];
        } else {
            $mensagem = "Dado adicionado com sucesso!";
        }
    } catch (Exception $e) {
        $erro = "Erro ao adicionar o dado: " . $e->getMessage();
    }
}

// Lógica para atualizar dado
if (isset($_POST['atualizar']) && !empty($_POST['id_atualizar']) && ( !empty($_POST['atualizar_titulo']) || !empty($_POST['atualizar_descricao']) )) {
    $idAtualizar = filter_input(INPUT_POST, 'id_atualizar', FILTER_SANITIZE_NUMBER_INT);
    $atualizarTitulo = filter_input(INPUT_POST, 'atualizar_titulo', FILTER_SANITIZE_STRING);
    $atualizarDescricao = filter_input(INPUT_POST, 'atualizar_descricao', FILTER_SANITIZE_STRING);

    if ($idAtualizar !== false && $idAtualizar > 0) {
        $dadosAtualizar = [];
        if (!empty($atualizarTitulo)) $dadosAtualizar['titulo'] = $atualizarTitulo;
        if (!empty($atualizarDescricao)) $dadosAtualizar['descricao'] = $atualizarDescricao;

        try {
            $resultado = fazerRequisicao("$apiUrl?id=$idAtualizar", 'PUT', $dadosAtualizar);
            $resposta = json_decode($resultado, true);
            if (isset($resposta['message'])) {
                $mensagem = $resposta['message'];
            } else {
                $mensagem = "Dado atualizado com sucesso!";
            }
        } catch (Exception $e) {
            $erro = "Erro ao atualizar o dado: " . $e->getMessage();
        }
    } else {
        $erro = "ID inválido para atualização.";
    }
}

// Lógica para deletar dado
if (isset($_POST['deletar']) && !empty($_POST['id_deletar'])) {
    $idDeletar = filter_input(INPUT_POST, 'id_deletar', FILTER_SANITIZE_NUMBER_INT);
    if ($idDeletar !== false && $idDeletar > 0) {
        try {
            $resultado = fazerRequisicao("$apiUrl?id=$idDeletar", 'DELETE');
            $resposta = json_decode($resultado, true);
            if (isset($resposta['message'])) {
                $mensagem = $resposta['message'];
            } else {
                $mensagem = "Dado deletado com sucesso!";
            }
        } catch (Exception $e) {
            $erro = "Erro ao deletar o dado: " . $e->getMessage();
        }
    } else {
        $erro = "ID inválido para exclusão.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumindo a API de Vídeos (PHP)</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }
        h2 {
            margin-top: 20px;
        }
        #dados {
            margin-top: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            white-space: pre-wrap; /* Para preservar a formatação JSON */
        }
        .form-container {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], textarea, button {
            width: calc(100% - 12px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-top: 5px;
        }
        .success {
            color: green;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Consumindo a API de Vídeos (PHP)</h1>

    <?php if ($erro): ?>
        <div class="error"><?php echo $erro; ?></div>
    <?php endif; ?>

    <?php if ($mensagem): ?>
        <div class="success"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <section class="form-container">
        <h2>Buscar Todos os Dados</h2>
        <form method="post">
            <button type="submit" name="buscar_todos">Buscar Dados</button>
        </form>
    </section>

    <section class="form-container">
        <h2>Buscar Dado por ID</h2>
        <form method="post">
            <label for="id_buscar">ID do Vídeo:</label>
            <input type="text" id="id_buscar" name="id_buscar" placeholder="Digite o ID">
            <button type="submit" name="buscar_por_id">Buscar</button>
        </form>
    </section>

    <section class="form-container">
        <h2>Adicionar Novo Dado</h2>
        <form method="post">
            <label for="novo_titulo">Título:</label>
            <input type="text" id="novo_titulo" name="novo_titulo" placeholder="Título do vídeo" required>
            <label for="nova_descricao">Descrição:</label>
            <textarea id="nova_descricao" name="nova_descricao" placeholder="Descrição do vídeo" required></textarea>
            <button type="submit" name="adicionar">Adicionar</button>
        </form>
    </section>

    <section class="form-container">
        <h2>Atualizar Dado por ID</h2>
        <form method="post">
            <label for="id_atualizar">ID do Vídeo a Atualizar:</label>
            <input type="text" id="id_atualizar" name="id_atualizar" placeholder="Digite o ID" required>
            <label for="atualizar_titulo">Novo Título:</label>
            <input type="text" id="atualizar_titulo" name="atualizar_titulo" placeholder="Novo título (opcional)">
            <label for="atualizar_descricao">Nova Descrição:</label>
            <textarea id="atualizar_descricao" name="atualizar_descricao" placeholder="Nova descrição (opcional)"></textarea>
            <button type="submit" name="atualizar">Atualizar</button>
        </form>
    </section>

    <section class="form-container">
        <h2>Deletar Dado por ID</h2>
        <form method="post">
            <label for="id_deletar">ID do Vídeo a Deletar:</label>
            <input type="text" id="id_deletar" name="id_deletar" placeholder="Digite o ID" required>
            <button type="submit" name="deletar">Deletar</button>
        </form>
    </section>

    <h2>Dados da API:</h2>
    <div id="dados">
        <?php if ($dados !== null): ?>
            <pre><?php echo json_encode($dados, JSON_PRETTY_PRINT); ?></pre>
        <?php elseif ($mensagem && !strpos($mensagem, 'sucesso')): ?>
            <p><?php echo $mensagem; ?></p>
        <?php else: ?>
            <p>Nenhum dado carregado ainda.</p>
        <?php endif; ?>
    </div>

</body>
</html>