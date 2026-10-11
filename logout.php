<?php
session_start();
session_unset();
session_destroy();

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$BASE = rtrim(str_replace('\\', '/', $scriptName), '/');
if ($BASE === '/' || $BASE === '\\') { $BASE = ''; }

// arahkan ke root (case '' di router)
header('Location: ' . $BASE . '/');
exit;
