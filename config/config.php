<?php
/**
 * @author Bruno Pelatieri Goulart
 * @version 1.0.0
 * @date 2025-11-01
 * @license MIT
 * @package Config
 * 
 * Configurações do Supabase
 * 
 * IMPORTANTE: Este arquivo contém credenciais sensíveis.
 * Nunca comite este arquivo em repositórios públicos.
 * Use .gitignore para protegê-lo.
 */

// Configurações do Supabase
//define('SUPABASE_URL', 'https://seu-projeto.supabase.co');
//define('SUPABASE_KEY', 'sua-chave-anon-key-aqui');
define('SUPABASE_URL', 'https://pdvumgsluxvqqiivurkc.supabase.co');
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InBkdnVtZ3NsdXh2cXFpaXZ1cmtjIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMzMjA0OTcsImV4cCI6MjA2ODg5NjQ5N30.tMr0myyknLID0AxPS9Mwnv68E_wqq0TLuWmP_idfwak');

// Configurações de segurança
define('ALLOWED_ORIGINS', [
    'https://seudominio.com.br',
    'http://localhost',
    'http://localhost:3000'
]);

// Configurações de erro
define('ENVIRONMENT', 'production'); // production ou development

// Configurações de log
define('ENABLE_LOGS', false);
define('LOG_FILE', __DIR__ . '/logs/api.log');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, ALLOWED_ORIGINS)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    http_response_code(200);
    exit();
}

// Exibição de erros baseada no ambiente
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}
