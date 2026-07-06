-- =========================================================
-- Rent a Car - Schema do Banco de Dados
-- Projeto acadêmico - Sistema de Aluguel de Carros
-- =========================================================

CREATE DATABASE IF NOT EXISTS rent_a_car CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rent_a_car;

-- ---------------------------------------------------------
-- Tabela: lojas (pontos de retirada/devolução)
-- ---------------------------------------------------------
CREATE TABLE lojas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(255) NOT NULL
);

INSERT INTO lojas (nome, endereco) VALUES
('Loja Centro', 'Rua Uruguaiana, 105 - Centro, Rio de Janeiro - RJ, 20050-091'),
('Loja Copacabana', 'Avenida Nossa Senhora de Copacabana, 500 - Copacabana, Rio de Janeiro - RJ, 22020-000'),
('Loja Ipanema', 'Rua Visconde de Pirajá, 250 - Ipanema, Rio de Janeiro - RJ, 22410-000'),
('Loja Barra da Tijuca', 'Avenida das Américas, 3500 - Barra da Tijuca, Rio de Janeiro - RJ, 22640-100'),
('Loja Aeroporto Santos Dumont', 'Praça Senador Salgado Filho, s/n - Aeroporto, Rio de Janeiro - RJ, 20021-340'),
('Loja Galeão', 'Avenida Vinte de Janeiro, s/n - Galeão, Rio de Janeiro - RJ, 21941-900'),
('Loja Botafogo', 'Praia de Botafogo, 400 - Botafogo, Rio de Janeiro - RJ, 22250-040'),
('Loja Recreio dos Bandeirantes', 'Avenida das Américas, 16500 - Recreio dos Bandeirantes, Rio de Janeiro - RJ, 22790-701'),
('Loja Lapa', 'Rua Riachuelo, 200 - Lapa, Rio de Janeiro - RJ, 20230-011'),
('Loja Tijuca', 'Rua Conde de Bonfim, 700 - Tijuca, Rio de Janeiro - RJ, 20530-002');

-- ---------------------------------------------------------
-- Tabela: usuarios
-- ---------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(150) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    data_nascimento DATE NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    cep VARCHAR(9),
    endereco VARCHAR(200),
    numero VARCHAR(10),
    bairro VARCHAR(100),
    pais VARCHAR(60) DEFAULT 'Brasil',
    estado VARCHAR(60),
    municipio VARCHAR(100),
    cnh VARCHAR(20),
    cnh_validade VARCHAR(7),
    cnh_categoria VARCHAR(5),
    foto_habilitacao VARCHAR(255),
    foto_perfil VARCHAR(255),
    loja_partida_id INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loja_partida_id) REFERENCES lojas(id)
);

-- ---------------------------------------------------------
-- Tabela: carros
-- ---------------------------------------------------------
CREATE TABLE carros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,           -- SUV, Intermediário, Econômico, Sedan, Compacto, Pickup
    ano_fabricacao INT NOT NULL,
    passageiros INT NOT NULL,
    capacidade_bagagem_litros INT NOT NULL,
    combustivel VARCHAR(50) NOT NULL,          -- Gasolina, Diesel, Etanol, Elétrico, Flex
    cambio VARCHAR(20) NOT NULL,               -- Manual, Automático
    potencia_cv INT NOT NULL,
    cor VARCHAR(30) NOT NULL,
    tem_ar_condicionado BOOLEAN DEFAULT TRUE,
    preco_diaria DECIMAL(10,2) NOT NULL,
    selo VARCHAR(30),                          -- ex: "Novo"
    imagem VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE
);

INSERT INTO carros (nome, categoria, ano_fabricacao, passageiros, capacidade_bagagem_litros, combustivel, cambio, potencia_cv, cor, preco_diaria, selo, imagem) VALUES
('Jeep Renegade', 'SUV', 2023, 5, 320, 'Flex (Gasolina e Etanol)', 'Automática de 6 velocidades', 185, 'Laranja', 328.07, 'Novo', 'assets/images/carros/jeep-renegade.png'),
('Ford Maverick', 'Pickup', 2023, 5, 380, 'Flex', 'Manual', 210, 'Verde', 203.40, NULL, 'assets/images/carros/ford-maverick.png'),
('Kwid Zen', 'Compacto', 2023, 5, 290, 'Flex', 'Manual', 70, 'Branco', 185.25, NULL, 'assets/images/carros/kwid-zen.png'),
('BMW X3', 'SUV', 2023, 5, 550, 'Flex', 'Automático', 249, 'Azul', 506.80, NULL, 'assets/images/carros/bmw-x3.png'),
('Hyundai HB20', 'Compacto', 2023, 5, 300, 'Flex', 'Automático', 120, 'Champagne', 201.07, NULL, 'assets/images/carros/hyundai-hb20.png'),
('BYD Dolphin', 'Compacto', 2023, 5, 345, 'Elétrico', 'Automático', 95, 'Cinza', 399.90, NULL, 'assets/images/carros/byd-dolphin.png');

-- ---------------------------------------------------------
-- Tabela: protecoes (Proteção Básica / Premium)
-- ---------------------------------------------------------
CREATE TABLE protecoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(255),
    preco_diaria DECIMAL(10,2) NOT NULL
);

INSERT INTO protecoes (nome, descricao, preco_diaria) VALUES
('Proteção Básica', 'Inclui cobertura contra danos ao veículo e proteção contra terceiros. Ideal para quem busca segurança com custo acessível.', 29.95),
('Proteção Premium', 'Cobertura completa, incluindo danos ao veículo, proteção contra terceiros, roubo, furto e assistência 24h. Para quem deseja mais tranquilidade em sua viagem.', 45.99);

-- ---------------------------------------------------------
-- Tabela: adicionais (opcionais da reserva)
-- ---------------------------------------------------------
CREATE TABLE adicionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    preco_diaria DECIMAL(10,2) NOT NULL,
    permite_quantidade BOOLEAN DEFAULT FALSE   -- ex: Cadeirinha Infantil / Locatário jovem podem ter faixa 0-3
);

INSERT INTO adicionais (nome, preco_diaria, permite_quantidade) VALUES
('Motorista', 108.99, FALSE),
('GPS', 28.99, FALSE),
('Cadeirinha Infantil', 19.98, TRUE),
('Locatário jovem', 21.98, TRUE);

-- ---------------------------------------------------------
-- Tabela: cupons
-- ---------------------------------------------------------
CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    valor_desconto DECIMAL(10,2) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE
);

INSERT INTO cupons (codigo, valor_desconto) VALUES
('ANDRE20VIP', 393.68),
('BISPO010', 32.19);

-- ---------------------------------------------------------
-- Tabela: reservas
-- ---------------------------------------------------------
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    carro_id INT NOT NULL,
    loja_retirada_id INT NOT NULL,
    loja_devolucao_id INT NOT NULL,
    data_hora_retirada DATETIME NOT NULL,
    data_hora_devolucao DATETIME NOT NULL,
    protecao_id INT,
    cupom_id INT,
    subtotal_diarias DECIMAL(10,2) NOT NULL,
    desconto_promocao DECIMAL(10,2) DEFAULT 0,
    subtotal_protecao DECIMAL(10,2) DEFAULT 0,
    subtotal_adicionais DECIMAL(10,2) DEFAULT 0,
    desconto_cupom DECIMAL(10,2) DEFAULT 0,
    taxa_locacao DECIMAL(10,2) DEFAULT 100.29,
    valor_total DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'confirmada',   -- confirmada, cancelada, finalizada
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (carro_id) REFERENCES carros(id),
    FOREIGN KEY (loja_retirada_id) REFERENCES lojas(id),
    FOREIGN KEY (loja_devolucao_id) REFERENCES lojas(id),
    FOREIGN KEY (protecao_id) REFERENCES protecoes(id),
    FOREIGN KEY (cupom_id) REFERENCES cupons(id)
);

-- ---------------------------------------------------------
-- Tabela: reserva_adicionais (N:N entre reservas e adicionais)
-- ---------------------------------------------------------
CREATE TABLE reserva_adicionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    adicional_id INT NOT NULL,
    quantidade INT DEFAULT 1,
    preco_diaria_aplicado DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
    FOREIGN KEY (adicional_id) REFERENCES adicionais(id)
);
