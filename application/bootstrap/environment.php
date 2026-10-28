<?php
// Carga KEY=value sin interpolar variables ni ejecutar contenido del archivo.
return static function ($path) {
    if (!is_file($path) || !is_readable($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES) as $line) {
        $line = trim($line, "\xEF\xBB\xBF \t\r\n");
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (!preg_match('/^(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/', $line, $match)) {
            continue;
        }
        $name = $match[1];
        // Las variables del servidor y SetEnv tienen prioridad sobre .env.
        if (getenv($name) !== FALSE) {
            continue;
        }
        $value = $_SERVER[$name] ?? $_SERVER['REDIRECT_'.$name] ?? NULL;
        if ($value === NULL) {
            $value = trim($match[2]);
            $length = strlen($value);
            if ($length >= 2 && ($value[0] === '"' || $value[0] === "'") && $value[$length - 1] === $value[0]) {
                $value = substr($value, 1, -1);
            }
        }
        putenv($name.'='.$value);
    }
};
