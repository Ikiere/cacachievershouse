<?php
$_POST['title'] = 'Test';
$_POST['start_date'] = '2024-01-01';
// do not call session_start() because save_event.php does it.
// Wait, we need to mock $_SESSION so we must start it!
session_start();
$_SESSION['admin_id'] = 1;
// we will capture output using ob_start to see if there are any errors.
ob_start();
require 'admin/save_event.php';
$output = ob_get_clean();
echo "OUTPUT WAS:\n" . $output;
