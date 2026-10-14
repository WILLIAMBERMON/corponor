-- Ejecutar una sola vez sobre la base existente antes de desplegar el código.
ALTER TABLE resoluciones ADD COLUMN token_publico CHAR(64) NULL,
 ADD UNIQUE KEY uq_resolucion_token_publico (token_publico);
