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
    nome INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE inquilinos (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    usuario_id INT NOT NULL,

    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE propriedades (
    id INT NOT NULL AUTO_INCREMENT,
    proprietario_id NOT NULL,
    inquilino_id NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    tipo INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    aluguel FLOAT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (proprietario_id) REFERENCES proprietarios(id),
    FOREIGN KEY (inquilino_id) REFERENCES inquilinos(id)
);

CREATE TABLE gastos (
    id INT NOT NULL AUTO_INCREMENT,
    valor FLOAT NOT NULL,
    data DATE NOT NULL,
    total FLOAT NOT NULL,
    propriedade_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (propriedade_id) REFERENCES propriedades(id),
);

CREATE TABLE arquivos (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);