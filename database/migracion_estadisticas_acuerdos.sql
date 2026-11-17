-- Ejecutar solo en una copia de govconor_resoluciones; no altera estadisticas_eventos.
CREATE TABLE IF NOT EXISTS estadisticas_acuerdos_eventos (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 tipo VARCHAR(20) NOT NULL,
 acuerdo_id BIGINT UNSIGNED NULL,
 grupo_id BIGINT UNSIGNED NULL,
 ip VARCHAR(45) NULL,
 user_agent VARCHAR(500) NULL,
 creado_en DATETIME NOT NULL,
 KEY idx_eae_tipo_fecha (tipo,creado_en),
 KEY idx_eae_acuerdo (acuerdo_id),
 KEY idx_eae_grupo (grupo_id),
 CONSTRAINT fk_eae_acuerdo FOREIGN KEY (acuerdo_id) REFERENCES acuerdos(id) ON DELETE SET NULL,
 CONSTRAINT fk_eae_grupo FOREIGN KEY (grupo_id) REFERENCES grupos_acuerdos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
