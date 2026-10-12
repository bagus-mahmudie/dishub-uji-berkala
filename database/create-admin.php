<?php
// Local setup only. Never expose account provisioning through HTTP.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/config.php';

function promptValue(string $label): string
{
    fwrite(STDERR, $label . ': ');
    $value = fgets(STDIN);
    if ($value === false) {
        throw new RuntimeException('Input tidak lengkap.');
    }
    return rtrim($value, "\r\n");
}

try {
    $identity = trim(promptValue('Username'));
    $password = promptValue('Password baru (minimal 12 karakter; input terlihat di terminal)');
    $confirmation = promptValue('Ulangi password');
    if ($identity === '' || strlen($identity) > 100) {
        throw new RuntimeException('Identitas akun kosong atau terlalu panjang.');
    }
    if (strlen($password) < 12 || strlen($password) > 72 || $password !== $confirmation) {
        throw new RuntimeException('Gunakan password 12–72 karakter dengan konfirmasi yang sama.');
    }
    
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM admins WHERE username = ?');
    $stmt->execute([$identity]);
    if ((int) $stmt->fetchColumn() > 0) { throw new RuntimeException('Username sudah digunakan.'); }
    $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
    $stmt->execute([$identity, password_hash($password, PASSWORD_DEFAULT)]);
    unset($password, $confirmation, $hash);
    fwrite(STDOUT, "Akun lokal berhasil dibuat.\n");
} catch (Throwable $error) {
    fwrite(STDERR, "Akun belum dibuat. Periksa input, koneksi database, dan kemungkinan akun sudah ada.\n");
    exit(1);
}
