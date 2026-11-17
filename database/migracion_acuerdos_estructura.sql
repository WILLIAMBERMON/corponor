-- Ejecutar SOLO sobre govconor_resoluciones, tras respaldo. MariaDB 10.6.
CREATE TABLE IF NOT EXISTS grupos_acuerdos (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 consecutivo INT UNSIGNED NOT NULL UNIQUE,
 descripcion VARCHAR(255) NOT NULL,
 creado_por BIGINT UNSIGNED NOT NULL,
 creado_en DATETIME NOT NULL,
 actualizado_en DATETIME NULL,
 CONSTRAINT fk_ga_usuario FOREIGN KEY(creado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS acuerdos (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 grupo_id BIGINT UNSIGNED NOT NULL,
 titulo VARCHAR(255) NOT NULL,
 fecha_acuerdo DATE NOT NULL,
 archivo VARCHAR(500) NULL,
 url VARCHAR(1000) NULL,
 imagen VARCHAR(500) NULL,
 activo TINYINT(1) NOT NULL DEFAULT 0,
 creado_por BIGINT UNSIGNED NOT NULL,
 creado_en DATETIME NOT NULL,
 actualizado_en DATETIME NULL,
 token_publico CHAR(64) NULL,
 origen_post_id BIGINT NULL,
 UNIQUE KEY uq_acuerdo_token(token_publico),
 UNIQUE KEY uq_acuerdo_origen(origen_post_id),
 KEY idx_acuerdos_grupo(grupo_id),
 KEY idx_acuerdos_fecha(fecha_acuerdo),
 KEY idx_acuerdos_titulo(titulo),
 CONSTRAINT fk_acuerdo_grupo FOREIGN KEY(grupo_id) REFERENCES grupos_acuerdos(id),
 CONSTRAINT fk_acuerdo_usuario FOREIGN KEY(creado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS usuario_grupo_acuerdo (
 usuario_id BIGINT UNSIGNED NOT NULL,
 grupo_id BIGINT UNSIGNED NOT NULL,
 PRIMARY KEY(usuario_id,grupo_id),
 CONSTRAINT fk_uga_usuario FOREIGN KEY(usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
 CONSTRAINT fk_uga_grupo FOREIGN KEY(grupo_id) REFERENCES grupos_acuerdos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS usuario_permiso_acuerdo (
 usuario_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
 administrar_grupos TINYINT(1) NOT NULL DEFAULT 0,
 gestionar_acuerdos TINYINT(1) NOT NULL DEFAULT 0,
 CONSTRAINT fk_upa_usuario FOREIGN KEY(usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS mig_acuerdos_auditoria (
 post_id BIGINT NOT NULL,
 titulo TEXT NOT NULL,
 url TEXT NOT NULL,
 anio SMALLINT UNSIGNED NULL,
 fecha_acuerdo DATE NULL,
 estado VARCHAR(40) NOT NULL DEFAULT 'REVISAR',
 PRIMARY KEY(post_id),
 KEY idx_mig_acuerdos_estado(estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
