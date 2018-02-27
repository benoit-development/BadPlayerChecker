<?php

session_start();

$baseURL = 'http://localhost/ffbad/';

/**
 * Search for player details from poona & verybad to a certain week
 * <ul>
 * <li>license</li>
 * <li>name</li>
 * <li>age</li>
 * <li>rankings : s/d/m : points/ranking</li>
 * </ul>
 * 
 * @param string $search player name or license
 * @param string $week week number
 */
function getPlayerDetails($search, $week) {

    clog("Searching : " . $search . "/" . $week);

    // getting player informations from verybad
    $verybad = getPlayerDetailsFromVerybad($search);
    if ($verybad['status'] == 'ok') {
        $poona = getPlayerDetailsFromPoona($verybad['license'], $week);
        if ($poona['status'] == 'ok') {
            $result = array_merge($poona, $verybad);
        } else {
            $result['status'] = 'ko';
        }
    } else {
        $result['status'] = 'ko';
    }

    return $result;
}

/**
 * Search for player details from poona to a certain week
 * <ul>
 * <li>rankings : s/d/m : points/ranking</li>
 * </ul>
 * 
 * @param string $license player license
 * @param string $week week number
 */
function getPlayerDetailsFromPoona($license, $week) {

    clog("Searching poona : " . $license . "/" . $week);

    $url = "https://poona.ffbad.org/page.php?P=fo/menu/public/accueil/classement_hebdo";
    $params = [
        'Action' => 'consultation_joueur_rechercher',
        'requestForm' => 'formRechercher',
        'recherche_text_licence' => $license,
        'recherche_select_classementHebdo' => $week
    ];

    $output = loadUrl($url, $params);

    // rankings
    $patternClassements = "/\sCote FFBaD : (.*) \((.*)\)/";
    if (preg_match_all($patternClassements, $output, $matches)) {
        // print_r($matches);

        $result['rankings'] = [
            's' => [
                'points' => $matches[1][0],
                'ranking' => $matches[2][0]
            ],
            'd' => [
                'points' => $matches[1][1],
                'ranking' => $matches[2][1]
            ],
            'm' => [
                'points' => $matches[1][2],
                'ranking' => $matches[2][2]
            ]
        ];

        $result['status'] = 'ok';
    } else {
        $result['status'] = 'ko';
    }

    clog($result);

    return $result;
}

/**
 * Search for player details :
 * <ul>
 * <li>license</li>
 * <li>name</li>
 * <li>age</li>
 * </ul>
 * 
 * @param string $search player name or license
 * @return array player details in array format
 */
function getPlayerDetailsFromVerybad($search) {

    clog("Searching verybad : " . $search);

    if (is_numeric($search)) {
        // license number
        clog('search for license number');
        $url = 'http://verybad.fr/joueur/detail/' . $search;
        $params = [];
    } elseif ($search) {
        // name
        clog('search for a name');
        $url = 'http://verybad.fr/recherche';
        $params = [
            'search_input' => $search
        ];
    } else {
        clog('ko');
        $result['status'] = 'ko';
        return $result;
    }


    $output = loadUrl($url, $params);

    // parse result
    // nom
    $patternName = "/<h2 class=\"\">(.*)<\/h2>/";
    if (preg_match($patternName, $output, $matches)) {
        $result['name'] = $matches[1];

        // license
        $patternName = "/<h5 class=\"text-muted\">(.*)<\/h5>/";
        preg_match($patternName, $output, $matches);
        $result['license'] = $matches[1];

        // age
        $patternName = "/<h4 class=\"text-info\">.*\n(.*)\n.*<\/h4>/";
        preg_match($patternName, $output, $matches);
        $result['age'] = trim($matches[1]);

        // club
        $patternName = "/<h4>(.*)<\/h4>/";
        preg_match($patternName, $output, $matches);
        $result['club'] = trim($matches[1]);

//        // classements
//        $patternName = "/<span class=\"label label-warning row-stat-badge\">(.*)<\/span>/";
//        preg_match_all($patternName, $output, $matches);
//        $result['rankings']['s']['ranking'] = $matches[1][0];
//        $result['rankings']['d']['ranking'] = $matches[1][1];
//        $result['rankings']['m']['ranking'] = $matches[1][2];
//
//        // points
//        $patternName = "/<h3 class=\"row-stat-value\">(.*)<\/h3>/";
//        preg_match_all($patternName, $output, $matches);
//        $result['rankings']['s']['points'] = $matches[1][0];
//        $result['rankings']['d']['points'] = $matches[1][1];
//        $result['rankings']['m']['points'] = $matches[1][2];


        $result['status'] = 'ok';
    } else {
        $result['status'] = 'ko';
    }

    clog($result);

    return $result;
}

/**
 * Call an URL and return its content
 * 
 * @param string $url url to be called
 * @param array $params params posted
 * @return string call result
 */
function loadUrl($url, $params = []) {

    require __DIR__ . '/../conf/config.php';

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        // CURLOPT_NOBODY => true,
        CURLOPT_FRESH_CONNECT => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'X-Apple-Tz: 0',
            'X-Apple-Store-Front: 143444,12',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: en-US,en;q=0.5',
            'Cache-Control: no-cache',
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
            'X-MicrosoftAjax: Delta=true',
        ],
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13',
        CURLOPT_POST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_ENCODING => "gzip",
    ));

    if (isset($proxyConf[CURLOPT_PROXYTYPE]) && ($proxyConf[CURLOPT_PROXYTYPE])) {
        curl_setopt_array($ch, $proxyConf);
    }

    $output = curl_exec($ch);

    curl_close($ch);

    // possible redirection in verybad
    $patternName = "/<title>Redirecting to \/\joueur\/detail\/(.*)<\/title>/";
    if (preg_match($patternName, $output, $matches)) {
        $license = trim($matches[1]);
        return loadUrl('http://verybad.fr/joueur/detail/' . $license);
    } else {
        return $output;
    }
}

/**
 * If found, this method will search then add player details to the current session
 * 
 * @param string $search player license or name
 * @param string $week week number
 */
function addPlayer($search, $week) {
    clog('addPlayer requested : ' . $search . '/' . $week);

    $details = getPlayerDetails($search, $week);
    clog($details);
    if ($details['status'] == 'ok') {
        $_SESSION['players'][$details['license']] = $details;
        return true;
    } else {
        return false;
    }
}

/**
 * Delete a player from the session with the provided id
 * 
 * @param string $id player id
 */
function deletePlayer($id) {
    if (isset($_SESSION['players'][$id])) {
        unset($_SESSION['players'][$id]);
    }
}

/**
 * Retrieve player from session
 * 
 * @return array player list details
 */
function getPlayers() {
    if (!isset($_SESSION['players'])) {
        $_SESSION['players'] = [];
    }
    return $_SESSION['players'];
}

/**
 * log function
 * 
 * @param mixed $data data to be logged
 */
function clog($data) {
    $f = fopen(__DIR__ . '/../data/log.txt', 'a');

    fputs($f, date(DATE_RFC822) . ' : ' . json_encode($data, JSON_PRETTY_PRINT) . "\n");

    fclose($f);
}

/**
 * Update the order of players with ids array
 * 
 * @param array $newOrder array of ids
 */
function updatePlayersListOrder($newOrder = []) {
    $result = [];
    foreach ($newOrder as $id) {
        if (isset($_SESSION['players'][$id])) {
            $result[$id] = $_SESSION['players'][$id];
        }
    }
    $_SESSION['players'] = $result;

    clog("new order : ");
    clog($result);
}

/**
 * Export players in session to a CSV file
 * 
 * @param string $category s/d/m
 * @return string newly created CSV file name
 */
function exportToCsvFile($category = 's') {
    $fileName = $category . '-' . time() . '.csv';
    $filePath = __DIR__ . '/../data/export/' . $fileName;
    $f = fopen($filePath, 'w');
    fputcsv($f, [
        'license',
        'name',
        'age',
        'S',
        '',
        'D',
        '',
        'Mx',
        ''
            ], ';');
    $i = 0;
    foreach ($_SESSION['players'] as $player) {
        $row = [
            $player['license'],
            $player['name'],
            $player['age'],
            $player['rankings']['s']['ranking'],
            $player['rankings']['s']['points'],
            $player['rankings']['d']['ranking'],
            $player['rankings']['d']['points'],
            $player['rankings']['m']['ranking'],
            $player['rankings']['m']['points'],
            ''
        ];
        if ($category == 's') {
            $row[] = $player['rankings']['s']['points'];
        } elseif ($i % 2 == 0) {
            $p1 = $player['rankings'][$category]['points'];
        } elseif ($i % 2 == 1) {
            $p2 = $player['rankings'][$category]['points'];
            $row[] = ($p1 + $p2) / 2;
        }
        fputcsv($f, array_map('utf8_decode', $row), ';');

        $i++;
    }
    fclose($f);
    return $fileName;
}
