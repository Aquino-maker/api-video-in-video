# 📹 VideoinVideo

Uma aplicação simples em PHP para gerenciar vídeos via uma API RESTful. Permite listar, buscar, adicionar, atualizar e deletar vídeos através de um cliente HTML/PHP.

## 🚀 Funcionalidades

- ✅ Listar todos os vídeos cadastrados
- 🔍 Buscar vídeo por ID
- ➕ Adicionar novo vídeo
- ✏️ Atualizar vídeo existente
- ❌ Deletar vídeo por ID

## 📂 Estrutura do Projeto

videoinvideo/ ├── api-video-in-video/ │ └── api/ │ └── api.php # API REST em PHP ├── cliente/ │ ├── index.php # Interface HTML/PHP │ ├── style.css # Estilos CSS │ └── img/ │ ├── icon.ico │ └── VID.png └── README.md

================================================================================================================================================================================================================================================================================================================


## ⚙️ Requisitos

- PHP 7.4 ou superior
- Servidor local (como XAMPP, WAMP ou Laragon)
- Navegador atualizado

## 🛠️ Como Rodar

1)  Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/videoinvideo.git
   

   2) Coloque o projeto dentro da pasta htdocs (XAMPP) ou www (WAMP).

===================================================================================================================================================================================================================================================================================================================

3) Inicie seu servidor local e acesse:
http://localhost/videoinvideo/cliente/index.php

4) Certifique-se que o arquivo api.php esteja acessível em:
http://localhost/videoinvideo/api-video-in-video/api/api.php


Requisições com application/json
Para utilizar os métodos POST e PUT, é obrigatório enviar o cabeçalho HTTP:
Content-Type: application/json

🔧 Exemplo de corpo da requisição:
{
  "titulo": "Meu Vídeo Legal",
  "descricao": "Esse vídeo é sobre algo muito interessante!"
}


📌 Importante:
Se o cabeçalho não for enviado corretamente, a API responderá com:
{
  "erro": "Content-Type nao suportado. Use application/json."
}

A API também validará se os campos titulo e descricao estão presentes e não vazios.

===================================================================================================================================================================================================================================================================================================================

📡 Rotas da API

Método | Rota | Descrição
GET | /api.php | Lista todos os vídeos
GET | /api.php?id=ID | Busca vídeo por ID
POST | /api.php | Adiciona novo vídeo
PUT | /api.php?id=ID | Atualiza vídeo existente
DELETE | /api.php?id=ID | Deleta vídeo por ID

As requisições PUT e DELETE são simuladas via POST + _method no corpo da requisição.


===================================================================================================================================================================================================================================================================================================================

✅ Exemplo de JSON esperado

Adição e atualização de vídeos:
{
  "titulo": "Exemplo de Título",
  "descricao": "Descrição do vídeo"
}




Respostas de sucesso:
{
  "message": "Vídeo adicionado com sucesso!",
  "id": 1
}




Erro de ID inexistente:
{
  "message": "Vídeo não encontrado."
}


===================================================================================================================================================================================================================================================================================================================

🧱 Tecnologias Utilizadas

. PHP

. HTML5 + CSS3

. JSON

. Servidor local (Apache)


