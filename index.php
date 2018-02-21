<?php

/**
 * Index file displaying main page
 */
require_once __DIR__ . '/utils/utils.php';

clog('Index');

// search for players if any posted
if (isset($_POST['searches'])) {
    
}

// calculate weeks from a defined date
// 18 : 2017-01-04
$i = 18;
$time = strtotime('2018-01-04');
$dateList = [];
while ($time <= time()) {
    $dateList[$i++] = date("d/m/Y", $time);
    $time = strtotime("+7 day", $time);
}
$dateList = array_reverse($dateList);

include './vue.php';
