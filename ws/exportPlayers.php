<?php

/*
 * Send a csv file to download
 */

require_once __DIR__ . '/../utils/utils.php';
clog('CSV Export');

$fn = exportToCsvFile($_REQUEST['category']);

header('Location: ' . $baseURL . '/data/export/' . $fn);


