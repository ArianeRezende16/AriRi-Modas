
# AriRi Modas 🛍️

A AriRi Modas é uma loja virtual de roupas femininas desenvolvida como projeto final do 4º bimestre.

O objetivo é demonstrar a integração entre Front-end, Back-end e banco de dados, simulando o funcionamento de um e-commerce.

## Tecnologias utilizadas

- HTML5 e CSS3
- Bootstrap
- JavaScript
- PHP
- MySQL/MariaDB
- PDO
- Apache/XAMPP
- Git e GitHub

## Funcionalidades

### Área do cliente
- Visualização e pesquisa de produtos
- Página individual dos produtos
- Cadastro e login
- Carrinho de compras com controle de quantidades
- Finalização de pedidos
- Simulação de pagamento por PIX, boleto ou cartão
- Histórico e detalhes das compras

### Área administrativa
- Login de administrador
- Cadastro de produtos com imagens
- Listagem de produtos
- Edição e exclusão de produtos
- Visualização dos pedidos recebidos

### Banco de dados
- Cadastro de usuários com senhas protegidas
- Relacionamento entre produtos e categorias
- Registro dos pedidos e seus itens
- Atualização automática do estoque após a compra

## Como executar o projeto

### 1. Instalar o ambiente

É necessário ter o XAMPP instalado, com Apache, PHP e MySQL/MariaDB.

Coloque a pasta do projeto dentro de:

C:\xampp\htdocs\AriRi Modas

Inicie o Apache e o MySQL pelo painel do XAMPP.

### 2. Configurar o banco de dados

Acesse:

http://localhost/phpmyadmin/

Importe o arquivo:

database/schema.sql

O script cria o banco ariri_modas, suas cinco tabelas, as categorias e os produtos iniciais de demonstração.

### 3. Configurar o arquivo .env

Na pasta principal do projeto, crie uma cópia do arquivo .env.example e renomeie para .env.

Preencha as configurações de acordo com o banco de dados local.

Exemplo para uma instalação padrão do XAMPP:

DB_HOST=localhost
DB_NAME=ariri_modas
DB_USER=root
DB_PASS=

O arquivo .env contém as configurações locais e não deve ser enviado ao GitHub.

### 4. Criar o primeiro administrador

Abra o terminal na pasta principal do projeto e execute:

& "C:\xampp\php\php.exe" "scripts\criar_admin.php"

Informe o nome e o e-mail solicitados.

O sistema criará uma conta administrativa e apresentará uma senha gerada automaticamente.

Guarde a senha para acessar o painel.

O comando deve ser executado em um banco que ainda não possua administrador.

### 5. Acessar a loja

Página inicial:

http://localhost/AriRi%20Modas/

Cadastro de clientes:

http://localhost/AriRi%20Modas/pages/cadastro.php

Área administrativa:

http://localhost/AriRi%20Modas/admin/login.php


## Segurança

O projeto utiliza:

- Consultas preparadas com PDO
- password_hash() e password_verify()
- Sessões para autenticação e controle de acesso
- Tokens de proteção em formulários
- Arquivo .env ignorado pelo Git