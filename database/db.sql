CREATE DATABASE IF NOT EXISTS `pedidos`;
USE `pedidos`;
CREATE TABLE `pedidos`.`clientes`
(
    `id`       INT          NOT NULL AUTO_INCREMENT,
    `cpf`      VARCHAR(255) NOT NULL,
    `nome`     VARCHAR(255) NOT NULL,
    `email`    VARCHAR(255) NOT NULL,
    `telefone` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);
CREATE TABLE `pedidos`.`pedidos`
(
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `numero_pedido` INT          NOT NULL,
    `produto`       VARCHAR(255) NOT NULL,
    `valor`         DOUBLE          NOT NULL,
    `data_pedido`   DATETIME     NOT NULL,
    `cliente_id`    INT          NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
);
