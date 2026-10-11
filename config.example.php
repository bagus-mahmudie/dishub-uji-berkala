<?php
function get_base_path() {
  $docroot = rtrim(str_replace('\\','/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
  $here    = rtrim(str_replace('\\','/', __DIR__), '/');      
  $base    = preg_replace('#^'.preg_quote($docroot,'#').'#', '', $here);
  if ($base === '' || $base === '/') return '';                       
  return '/'.trim($base,'/');                                     
}
function get_base_url() {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
  return $scheme.'://'.$host.get_base_path();                      
}
$BASE     = get_base_path();
$BASE_URL = get_base_url();

$DB_HOST = '127.0.0.1';
$DB_NAME = '';
$DB_USER = '';
$DB_PASS = '';

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
  http_response_code(500);
  exit('Database connection failed.');
}

session_start();
