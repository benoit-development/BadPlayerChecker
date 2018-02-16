<?php

require_once __DIR__ . '/../utils/utils.php';
clog('Delete Player');
/**
 * Delete player from list
 */

if (isset($_REQUEST['id'])) {
    clog('id : ' . $_REQUEST['id']);
    deletePlayer($_REQUEST['id']);
} else {
    clog("no parameter sent");
}
