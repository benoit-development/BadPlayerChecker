<?php

/*
 * Search and add a player to session if found
 */

require_once __DIR__ . '/../utils/utils.php';
clog('Add Player');


if (isset($_REQUEST['search']) && isset($_REQUEST['week'])) {
    if (addPlayer($_REQUEST['search'], $_REQUEST['week'])) {
        clog('Player found');
        echo 'ok';
    } else {
        clog('Player not found');
        http_response_code(404);
        echo 'ko';
    }
} else {
    http_response_code(404);
    echo 'ko';
}

