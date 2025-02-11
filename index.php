<?php
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    libxml_use_internal_errors(true);

    // header("Content-Type: text/plain");

    include("passwd.php");

    $cookie_file_path = "";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file_path);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

    function get($url) {
        global $curl;
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        return $res;
    };

    function post($url, $body) {
        global $curl;
        $POSTFIELDS = http_build_query($body);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $POSTFIELDS);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        return $res;
    };

    get("https://synergia.librus.pl/loguj/portalRodzina?v=1738664710");

    $auth_data = array(
        "action" => "login",
        "login" => $login,
        "pass" => $pass,
    );
    post("https://api.librus.pl/OAuth/Authorization?client_id=46", $auth_data);

    $mysterious_data = array(
        "command" => "open_synergia_window",
        "commandPayload" => array(
            "url" => "https:\/\/synergia.librus.pl\/uczen\/index"
        ),
    );
    post("https://api.librus.pl/OAuth/Authorization/2FA?client_id=46", $mysterious_data);

    $page_string = get("https://synergia.librus.pl/przegladaj_oceny/uczen");

    $page_string = preg_replace('/(src|href)=\"(.*?)\"/m', '$1="https://synergia.librus.pl$2"', $page_string);

    $doc = new DOMDocument();
    $doc -> loadHTML($page_string);
    $xpath = new DOMXPath($doc);

    foreach ($xpath -> query("//div[@id='header']") as $node) {
        $node -> parentNode -> removeChild($node);
    }

    foreach ($xpath -> query("//div[@class='right screen-only']") as $node) {
        $node -> parentNode -> removeChild($node);
    }

    foreach ($xpath -> query("//div[@class='container-icon']") as $node) {
        $node -> parentNode -> removeChild($node);
    }

    foreach ($xpath -> query("//h2[@class='inside']") as $node) {
        $node -> parentNode -> removeChild($node);
    }

    foreach ($xpath -> query("//span[@class='fold']") as $node) {
        $node -> parentNode -> removeChild($node);
    }

    foreach ($xpath -> query("//div[@id='footer']") as $node) {
        $node -> parentNode -> removeChild($node);
    }

    echo $doc -> saveHTML();
?>

<script src="./combine_tables.js" defer></script>
