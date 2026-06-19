CREATE DATABASE IF NOT EXISTS pequenos_proprietarios;

USE pequenos_proprietarios;

DROP TABLE IF EXISTS arquivos;
DROP TABLE IF EXISTS gastos;
DROP TABLE IF EXISTS propriedades;
DROP TABLE IF EXISTS inquilinos;
DROP TABLE IF EXISTS proprietarios;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(11) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE proprietarios (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    usuario_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE inquilinos (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    usuario_id INT NOT NULL,

    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE propriedades (
    id INT NOT NULL AUTO_INCREMENT,
    proprietario_id INT NOT NULL,
    inquilino_id INT NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    tipo INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    aluguel FLOAT NOT NULL,
    foto_path VARCHAR(255) NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (proprietario_id) REFERENCES proprietarios(id),
    FOREIGN KEY (inquilino_id) REFERENCES inquilinos(id)
);

CREATE TABLE gastos (
    id INT NOT NULL AUTO_INCREMENT,
    valor FLOAT NOT NULL DEFAULT 0,
    data DATE NOT NULL,
    total FLOAT NOT NULL DEFAULT 0,
    propriedade_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL DEFAULT '',
    inquilino VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (id),
    FOREIGN KEY (propriedade_id) REFERENCES propriedades(id)
);

CREATE TABLE arquivos (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    propriedade_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (propriedade_id) REFERENCES propriedades(id)
);

-- Dados iniciais para testes
-- Proprietário: CPF 12345678901 / Senha: 123
-- Inquilino:    CPF 98765432100 / Senha: 123
INSERT INTO usuarios (senha, cpf) VALUES ('123', '12345678901'), ('123', '98765432100');
INSERT INTO proprietarios (nome, usuario_id) VALUES ('João Silva', 1);
INSERT INTO inquilinos (nome, email, usuario_id) VALUES ('Maria Santos', 'maria@email.com', 2);
INSERT INTO propriedades (proprietario_id, inquilino_id, endereco, tipo, descricao, aluguel) VALUES
(1, 1, 'Rua das Flores, 100', 1, 'Apartamento Centro', 1500),
(1, 1, 'Av. Principal, 250', 2, 'Casa Jardim', 2200),
(1, 1, 'Rua do Comércio, 45', 1, 'Sala Comercial', 900),
(1, 1, 'Alameda Verde, 12', 2, 'Casa de Campo', 1800);
INSERT INTO gastos (valor, data, total, propriedade_id, descricao, inquilino) VALUES
(0, '2025-01-15', 0, 1, 'Pintura da sala', 'Maria Santos'),
(0, '2025-02-20', 0, 1, 'Troca de fechadura', 'Maria Santos');
INSERT INTO arquivos (nome, path, propriedade_id) VALUES
('Contrato de locação', '/docs/contrato-apto1.pdf', 1);