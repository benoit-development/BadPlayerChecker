<?php

require_once __DIR__ . '/../utils/utils.php';
clog('Update Players Order List');
/**
 * Update players list order
 */

if (isset($_REQUEST['idList']) && is_array($_REQUEST['idList'])) {
    clog($_REQUEST['idList']);
    updatePlayersListOrder($_REQUEST['idList']);
} else {
    clog("no parameter sent");
}
