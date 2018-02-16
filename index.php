<?php
/**
 * Index file displaying main page
 */

require_once __DIR__ . '/utils/utils.php';

clog('Index');

// search for players if any posted
if (isset($_POST['searches'])) {
    
}

include './vue.html';