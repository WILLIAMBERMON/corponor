-- Ejecutar una sola vez antes de desplegar. Roles: 0 usuario, 1 administrador, 2 superadministrador.
UPDATE usuarios SET es_admin=2 WHERE documento='1000000000';
