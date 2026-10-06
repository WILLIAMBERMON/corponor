CREATE DATABASE IF NOT EXISTS corponor_resoluciones CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE corponor_resoluciones;

CREATE TABLE usuarios (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nombres VARCHAR(150) NOT NULL,
 documento VARCHAR(30) NOT NULL UNIQUE,
 clave VARCHAR(255) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 es_admin TINYINT(1) NOT NULL DEFAULT 0,
 activo TINYINT(1) NOT NULL DEFAULT 1,
 creado_en DATETIME NOT NULL,
 actualizado_en DATETIME NULL,
 INDEX idx_usuario_activo (activo)
) ENGINE=InnoDB;

CREATE TABLE grupos_resoluciones (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 consecutivo INT UNSIGNED NOT NULL UNIQUE,
 descripcion VARCHAR(255) NOT NULL,
 creado_por BIGINT UNSIGNED NOT NULL,
 creado_en DATETIME NOT NULL,
 actualizado_en DATETIME NULL,
 CONSTRAINT fk_grupo_usuario FOREIGN KEY (creado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE resoluciones (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 grupo_id BIGINT UNSIGNED NOT NULL,
 titulo VARCHAR(255) NOT NULL,
 fecha_resolucion DATE NOT NULL,
 archivo VARCHAR(500) NULL,
 url VARCHAR(1000) NULL,
 imagen VARCHAR(500) NULL,
 activo TINYINT(1) NOT NULL DEFAULT 1,
 creado_por BIGINT UNSIGNED NOT NULL,
 creado_en DATETIME NOT NULL,
 actualizado_en DATETIME NULL,
 CONSTRAINT fk_resolucion_grupo FOREIGN KEY (grupo_id) REFERENCES grupos_resoluciones(id),
 CONSTRAINT fk_resolucion_usuario FOREIGN KEY (creado_por) REFERENCES usuarios(id),
 INDEX idx_res_titulo (titulo), INDEX idx_res_fecha (fecha_resolucion), INDEX idx_res_grupo (grupo_id), INDEX idx_res_activo (activo),
 CONSTRAINT chk_res_origen CHECK (archivo IS NOT NULL OR url IS NOT NULL)
) ENGINE=InnoDB;

CREATE TABLE password_reset_tokens (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 usuario_id BIGINT UNSIGNED NOT NULL,
 token_hash CHAR(64) NOT NULL UNIQUE,
 expira_en DATETIME NOT NULL,
 usado_en DATETIME NULL,
 creado_en DATETIME NOT NULL,
 CONSTRAINT fk_token_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
 INDEX idx_token_expira (expira_en)
) ENGINE=InnoDB;

CREATE TABLE usuario_grupo (
 usuario_id BIGINT UNSIGNED NOT NULL,
 grupo_id BIGINT UNSIGNED NOT NULL,
 PRIMARY KEY (usuario_id, grupo_id),
 CONSTRAINT fk_ug_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
 CONSTRAINT fk_ug_grupo FOREIGN KEY (grupo_id) REFERENCES grupos_resoluciones(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE estadisticas_eventos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 tipo VARCHAR(20) NOT NULL,
 resolucion_id BIGINT UNSIGNED NULL,
 grupo_id BIGINT UNSIGNED NULL,
 ip VARCHAR(45) NULL,
 user_agent VARCHAR(500) NULL,
 creado_en DATETIME NOT NULL,
 CONSTRAINT fk_evento_resolucion FOREIGN KEY (resolucion_id) REFERENCES resoluciones(id) ON DELETE SET NULL,
 CONSTRAINT fk_evento_grupo FOREIGN KEY (grupo_id) REFERENCES grupos_resoluciones(id) ON DELETE SET NULL,
 INDEX idx_evento_tipo_fecha (tipo, creado_en), INDEX idx_evento_resolucion (resolucion_id), INDEX idx_evento_grupo (grupo_id)
) ENGINE=InnoDB;

INSERT INTO usuarios (nombres,documento,clave,email,es_admin,activo,creado_en)
VALUES ('Administrador CORPONOR','1000000000','$2y$12$uO8AJxnU2WXf/nmXrtns0eGeGi8L3jY.Dya0nlvZTUP1G3zQojwWi','admin@corponor.gov.co',1,1,NOW());

-- Roles: 0 usuario, 1 administrador, 2 superadministrador.
UPDATE usuarios SET es_admin=2 WHERE documento='1000000000';
