DROP DATABASE IF EXISTS sylviaestruch;
CREATE DATABASE sylviaestruch CHARACTER SET utf8 COLLATE utf8_general_ci;
USE sylviaestruch;
DROP TABLE IF EXISTS categorias_pintura;
DROP TABLE IF EXISTS pinturas;
DROP TABLE IF EXISTS categorias_teatro;
DROP TABLE IF EXISTS teatros;
CREATE TABLE categorias_pintura (id INT AUTO_INCREMENT NOT NULL, title_ca VARCHAR(255) NOT NULL, title_es VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB;
CREATE TABLE pinturas (id INT AUTO_INCREMENT NOT NULL, title_ca VARCHAR(255) NOT NULL, title_es VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, categorias_pintura_id INT NOT NULL, INDEX IDX_catpintura (categorias_pintura_id), PRIMARY KEY(id)) ENGINE = InnoDB;
CREATE TABLE categorias_teatro (id INT AUTO_INCREMENT NOT NULL, title_ca VARCHAR(255) NOT NULL, title_es VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB;
CREATE TABLE teatros (id INT AUTO_INCREMENT NOT NULL, title_ca VARCHAR(255) NOT NULL, title_es VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, categorias_teatro_id INT NOT NULL, INDEX IDX_catteatro (categorias_teatro_id), PRIMARY KEY(id)) ENGINE = InnoDB;

