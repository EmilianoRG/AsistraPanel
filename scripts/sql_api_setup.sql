-- SQL: crear tabla de usuarios para API (login)
CREATE TABLE IF NOT EXISTS api_user (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50) DEFAULT 'admin',
  auth_key VARCHAR(64) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario por defecto (username: admin)
-- Contraseña ya encriptada (proporcionada): $2y$10$.7ebZUPuZB3JXt92ve6dLu61EmZbPwhYnf4gtgg0cqqV4I/dQveCe
INSERT INTO api_user (username, password_hash, role, auth_key)
VALUES ('admin', '$2y$10$.7ebZUPuZB3JXt92ve6dLu61EmZbPwhYnf4gtgg0cqqV4I/dQveCe', 'admin', LEFT(UUID(), 32))
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = VALUES(role), auth_key = VALUES(auth_key);

-- SQL: tabla para almacenar las instancias/proyectos Asistra a conectar
CREATE TABLE IF NOT EXISTS external_instances (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  base_url VARCHAR(255) NOT NULL,
  api_key VARCHAR(255) DEFAULT NULL,
  status TINYINT(1) DEFAULT 1,
  last_sync_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ejemplo de inserción de instancia
INSERT INTO external_instances (name, base_url, api_key)
VALUES ('Sucursal Ejemplo', 'https://asistra-remote.example.com', 'example-instance-api-key')
ON DUPLICATE KEY UPDATE base_url = VALUES(base_url), api_key = VALUES(api_key);

