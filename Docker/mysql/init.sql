-- Inicialización de la base de datos para Tala Trivia
CREATE DATABASE IF NOT EXISTS tala_trivia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario si no existe
CREATE USER IF NOT EXISTS 'tala_trivia'@'%' IDENTIFIED BY 'tala_trivia';

-- Otorgar permisos
GRANT ALL PRIVILEGES ON tala_trivia.* TO 'tala_trivia'@'%';

-- Crear base de datos de testing
CREATE DATABASE IF NOT EXISTS tala_trivia_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON tala_trivia_testing.* TO 'tala_trivia'@'%';

FLUSH PRIVILEGES;
