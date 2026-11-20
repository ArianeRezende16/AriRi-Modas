
-- Banco de dados da AriRi Modas
-- Estrutura para instalação do projeto

CREATE DATABASE IF NOT EXISTS ariri_modas
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE ariri_modas;

-- Tabela de usuários

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de produtos

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255),
    categoria VARCHAR(100),
    estoque INT NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de pedidos

CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    forma_pagamento ENUM('pix', 'boleto', 'cartao') NOT NULL,
    status ENUM('pendente', 'confirmado', 'cancelado')
        NOT NULL DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de itens dos pedidos

CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NULL,
    nome_produto VARCHAR(150) NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Produtos iniciais de demonstração
-- Execute a carga em um banco novo para evitar duplicações.

INSERT INTO produtos
(nome, descricao, preco, imagem, categoria, estoque)
VALUES
('Blusa Elegance', 'Blusa feminina elegante.', 89.90, 'blusa.png', 'Blusas', 15),
('Blusa Serena', 'Blusa feminina delicada.', 79.90, 'serena.png', 'Blusas', 12),
('Blusa Bella', 'Blusa feminina versátil.', 69.90, 'bella.png', 'Blusas', 20),
('Calça Alfaiataria', 'Calça feminina de alfaiataria.', 129.90, 'calca.png', 'Calças', 10),
('Calça Aurora', 'Calça feminina elegante.', 119.90, 'aurora.png', 'Calças', 15),
('Calça Linho', 'Calça feminina de linho.', 139.90, 'linho.png', 'Calças', 8),
('Vestido Florença', 'Vestido feminino elegante.', 149.90, 'vestido.png', 'Vestidos', 12),
('Vestido Amélia', 'Vestido feminino delicado.', 159.90, 'amelia.png', 'Vestidos', 10),
('Vestido Liz', 'Vestido feminino versátil.', 139.90, 'liz.png', 'Vestidos', 15),
('Saia Serena', 'Saia feminina elegante.', 99.90, 'saia-serena.png', 'Saias', 12),
('Saia Maya', 'Saia feminina delicada.', 109.90, 'maya.png', 'Saias', 10),
('Saia Liz', 'Saia feminina versátil.', 89.90, 'saia-liz.png', 'Saias', 18);
