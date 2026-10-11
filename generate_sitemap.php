<?php
declare(strict_types=1);
error_reporting(E_ALL & ~E_NOTICE);

function generate_sitemap_rfid(string $relativeDir = 'qr/v1/rfid'): array {
  $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
  $BASE = rtrim(str_replace('\\','/',$scriptName), '/');
  if ($BASE === '/' || $BASE === '\\') { $BASE = ''; }
  $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $BASE_URL = $scheme . '://' . $host . $BASE;

  $targetDir = __DIR__ . '/' . $relativeDir;
  $entries = [];

  if (is_dir($targetDir)) {
    foreach (new DirectoryIterator($targetDir) as $fi) {
      if ($fi->isDot() || !$fi->isFile()) continue;
      $name = $fi->getFilename();
      if (!preg_match('~\.html?$~i', $name)) continue;

      $loc = $BASE_URL . '/' . $relativeDir . '/' . $name;
      $entries[] = [
        'loc'     => $loc,
        'lastmod' => date('c', $fi->getMTime() ?: time()),
      ];
    }
  }

  usort($entries, fn($a,$b) => strcmp($b['lastmod'], $a['lastmod']));

  $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $xml .= '<?xml-stylesheet type="text/xsl" href="sitemap.xsl"?>' . "\n"; 
  $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
  foreach ($entries as $e) {
    $xml .= "  <url>\n";
    $xml .= "    <loc>".htmlspecialchars($e['loc'], ENT_QUOTES, 'UTF-8')."</loc>\n";
    $xml .= "    <lastmod>{$e['lastmod']}</lastmod>\n";
    $xml .= "  </url>\n";
  }
  $xml .= "</urlset>\n";

  $finalPath = __DIR__ . '/sitemap.xml';
  $tmpPath   = $finalPath . '.tmp';
  $existing  = is_file($finalPath) ? file_get_contents($finalPath) : '';

  if ($existing !== $xml) {
    file_put_contents($tmpPath, $xml);
    @rename($tmpPath, $finalPath); 
    @unlink($tmpPath);
  }

  return ['count'=>count($entries), 'path'=>$finalPath];
}

if (php_sapi_name() === 'cli' || realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
  $res = generate_sitemap_rfid();
  header('Content-Type: text/plain; charset=UTF-8');
  echo "OK - sitemap diperbarui\nPath: {$res['path']}\nJumlah URL: {$res['count']}\n";
}
