<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$BASE = rtrim(str_replace('\\', '/', $scriptName), '/');
if ($BASE === '/' || $BASE === '\\') { $BASE = ''; }
if (!empty($_SESSION['admin_id'])) {
  header('Location: ' . $BASE . '/');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = trim($_POST['password'] ?? '');

  $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = :u LIMIT 1');
  $stmt->execute([':u' => $u]);
  $row = $stmt->fetch();

  if ($row && password_verify($p, $row['password_hash'])) {
      $_SESSION['admin_id']   = $row['id'];
      $_SESSION['admin_name'] = $row['username']; 
      header('Location: ' . $BASE . '/'); 
      exit;
  } else {
      $error = 'Username atau password salah.';
  }

}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="<?= $BASE ?>/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="icon" type="image/png" href="/favicon.png">
</head>
<body>
<div class="container">
  <div class="login-wrapper">
    <div class="content">
      <div class="login-box">

        <?php if ($error): ?>
          <div class="alert alert-danger" style="margin-bottom:12px;">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form action="<?= $BASE ?>/login" method="POST" autocomplete="off" novalidate>
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan Username Anda" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan Kata sandi Anda" required>
          </div>
          <div class="forgot">
            <a href="#">Lupa Password?</a>
          </div>
      </div> <!-- end login-box -->
    </div> <!-- end content -->

    <div class="footer">
        <button type="submit" class="btn-login">
          <i class="fa-solid fa-right-to-bracket"></i> Masuk
        </button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
