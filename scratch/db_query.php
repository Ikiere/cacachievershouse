<?php
require_once dirname(__DIR__) . '/admin/config.php';
echo "GALLERY IMAGES:\n";
$res = $conn->query("SELECT * FROM gallery");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}
echo "\nMINISTRIES:\n";
$res2 = $conn->query("SELECT * FROM ministries");
while ($r = $res2->fetch_assoc()) {
    print_r($r);
}
