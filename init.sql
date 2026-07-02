-- Criação dos bancos de dados necessários para o sistema
CREATE DATABASE IF NOT EXISTS api90 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS apipp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criação dos usuários se não existirem
CREATE USER IF NOT EXISTS 'api90'@'%' IDENTIFIED BY '13211321';
CREATE USER IF NOT EXISTS 'apipp'@'%' IDENTIFIED BY '13211321';

-- Concede todas as permissões para os usuários do sistema
GRANT ALL PRIVILEGES ON api90.* TO 'api90'@'%';
GRANT ALL PRIVILEGES ON apipp.* TO 'apipp'@'%';
FLUSH PRIVILEGES;
