<?php

require_once __DIR__ . '/../utils/utils.php';
clog('Get Players List');
/**
 * Get all players from session
 */


echo json_encode(getPlayers());