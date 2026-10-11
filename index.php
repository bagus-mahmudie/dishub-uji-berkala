<?php
session_start();
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$BASE = rtrim(str_replace('\\', '/', $scriptName), '/');
if ($BASE === '/' || $BASE === '\\') {
    $BASE = '';
}

$reqPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path    = preg_replace('#^' . preg_quote($BASE, '#') . '#', '', $reqPath);
$path    = trim($path, '/');

switch ($path) {
  case '':
  case 'qr':
  case 'qr/v1':
    if (!empty($_SESSION['admin_id'])) {
        require __DIR__ . '/dashboard.php';
        break;
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <title>Hasil Uji Berkala Kendaraan Bermotor</title>
      <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet"/>
      <link rel="icon" type="image/x-icon" href="/favicon.ico">
      <link rel="icon" type="image/png" href="/favicon.png">

      <style>
        .header {display: flex;align-items: center;justify-content: center;gap: 10px;margin-bottom: 20px;}
        .header h3 {margin: 0;font-size: 20px;font-weight: bold;color: #222;}
        .logo {width: 50px;height: auto;}

         body {margin: 0;padding: 0;font-family: Arial, sans-serif;background: linear-gradient(to bottom, #0A0F47 0%, #0A1A58 60%, #0A2267 100%);display: flex;justify-content: center;align-items: center;height: 100vh;}
        .card {background: white;padding: 40px;border-radius: 8px;text-align: center;width: 1290px !important; max-width: 100%;box-shadow: 0 0 20px rgba(0,0,0,0.2);box-sizing: border-box;}
        .card h2 {margin: 20px 0;font-size: 48px;color: #444;font-family: 'Quicksand', 'Segoe UI', Arial, sans-serif;font-weight:400;}
        .card h3 {font-size: 18.5px;color: #333;}
        .card p {font-size: 18px;color: #333;font-family: Arial, sans-serif;}
        .card a {color: #0a1149;text-decoration: none;font-weight: bold;}
        .card a:hover {text-decoration: underline;}
        .logo {width: 50px;height: auto;}

        @media (max-width: 640px){
        .card{ padding: 40px 20px; border-radius:0px }}
        .card h2{line-height:3rem;}
        .card p{line-height:1.6rem;}
      </style>
    </head>
    <body>
      <div class="card">
      <div class="header">
        <img src="<?= $BASE ?>/favicon.png" alt="Logo" class="logo">
        <h3>KEMENTERIAN PERHUBUNGAN</h3>
      </div>

        <h2>Data Pengujian Bermasalah</h2>
        <p>Maaf, kami menemukan kendala data pengujian yang telah disinkronkan oleh daerah pengujian Anda. Mohon untuk menghubungi <a href="<?= $BASE ?>/login">pihak terkait</a>.
        </p>
      </div>
    </body>
    </html>
    <?php
    break;

  case 'login':
    require __DIR__ . '/login.php';
    break;

  case 'logout':
    session_unset();
    session_destroy();
    header('Location: ' . $BASE . '/login');
    exit;

  default:
    $candidate = __DIR__ . '/' . $path . '.php';
    if (is_file($candidate)) { 
        require $candidate; 
        break; 
    }
    http_response_code(404);
    echo '<h1>404 Not Found</h1>';
}
