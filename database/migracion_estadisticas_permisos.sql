-- Ejecutar sobre la base de datos existente.
-- No elimina datos actuales.
CREATE TABLE IF NOT EXISTS usuario_grupo (
 usuario_id BIGINT UNSIGNED NOT NULL,
 grupo_id BIGINT UNSIGNED NOT NULL,
 PRIMARY KEY (usuario_id, grupo_id),
 CONSTRAINT fk_ug_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
 CONSTRAINT fk_ug_grupo FOREIGN KEY (grupo_id) REFERENCES grupos_resoluciones(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS estadisticas_eventos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 tipo VARCHAR(20) NOT NULL,
 resolucion_id BIGINT UNSIGNED NULL,
 grupo_id BIGINT UNSIGNED NULL,
 ip VARCHAR(45) NULL,
 user_agent VARCHAR(500) NULL,
 creado_en DATETIME NOT NULL,
 CONSTRAINT fk_evento_resolucion FOREIGN KEY (resolucion_id) REFERENCES resoluciones(id) ON DELETE SET NULL,
 CONSTRAINT fk_evento_grupo FOREIGN KEY (grupo_id) REFERENCES grupos_resoluciones(id) ON DELETE SET NULL,
 INDEX idx_evento_tipo_fecha (tipo, creado_en),
 INDEX idx_evento_resolucion (resolucion_id),
 INDEX idx_evento_grupo (grupo_id)
) ENGINE=InnoDB;
