<?php
$filename = isset($_GET['filename']) ? $_GET['filename'] : die();
unlink($filename);

