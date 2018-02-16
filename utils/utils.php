<?php

session_start();

/**
 * Search for player details :
 * <ul>
 * <li>license</li>
 * <li>name</li>
 * <li>age</li>
 * <li>rankings : s/d/m : points/ranking</li>
 * </ul>
 * 
 * @param string $search player name or license
 * @return array player details in array format
 */
function getPlayerDetailsFromVerybad($search) {

    clog("Searching : " . $search);

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
        return $result['status'] = 'ko';
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

        // âge
        $patternName = "/<h4 class=\"text-info\">.*\n(.*)\n.*<\/h4>/";
        preg_match($patternName, $output, $matches);
        $result['age'] = trim($matches[1]);

        // classements
        $patternName = "/<span class=\"label label-warning row-stat-badge\">(.*)<\/span>/";
        preg_match_all($patternName, $output, $matches);
        $result['rankings']['s']['ranking'] = $matches[1][0];
        $result['rankings']['d']['ranking'] = $matches[1][1];
        $result['rankings']['m']['ranking'] = $matches[1][2];

        // points
        $patternName = "/<h3 class=\"row-stat-value\">(.*)<\/h3>/";
        preg_match_all($patternName, $output, $matches);
        $result['rankings']['s']['points'] = $matches[1][0];
        $result['rankings']['d']['points'] = $matches[1][1];
        $result['rankings']['m']['points'] = $matches[1][2];


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

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        // CURLOPT_NOBODY => true,
        CURLOPT_FRESH_CONNECT => true,
        CURLOPT_FOLLOWLOCATION => true,
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
        CURLOPT_PROXYTYPE => 'HTTP',
        CURLOPT_PROXYAUTH => true,
        CURLOPT_PROXY => '138.21.89.90',
        CURLOPT_PROXYPORT => 3128,
        CURLOPT_PROXYUSERPWD => 'p089509:Prout345',
        CURLOPT_POST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_ENCODING => "gzip",
    ));

    $output = curl_exec($ch);

    curl_close($ch);

    return $output;
}

/**
 * If found, this method will search then add player details to the current session
 * 
 * @param string $search player license or name
 */
function addPlayer($search) {
    clog('addPlayer requested : ' . $search);

    $details = getPlayerDetailsFromVerybad($search);
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
 * @return string newly created CSV file
 */
function exportToCsvFile($category = 's') {
    $filePath = __DIR__ . '/../data/export/' . $category . '-' . time() . '.csv';
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
    ]);
    foreach ($_SESSION['players'] as $player) {
        fputcsv($f, [
            $player['license'],
            $player['name'],
            $player['age'],
            $player['rankings']['s']['ranking'],
            $player['rankings']['s']['points'],
            $player['rankings']['d']['ranking'],
            $player['rankings']['d']['points'],
            $player['rankings']['m']['ranking'],
            $player['rankings']['m']['points']
        ]);
    }
    fclose($f);
    return $filePath;
}
