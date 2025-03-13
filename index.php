<script src="./show_hide.js"></script>

<?php
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    libxml_use_internal_errors(true);

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

    foreach ($xpath -> query("//script[@src='https://synergia.librus.pl/js/showHideRows.js']") as $node) {
        $node -> parentNode -> removeChild($node);
    }
    
    foreach ($xpath -> query("//div[@class='legend left stretch'][2]") as $node) {
        $node -> parentNode -> removeChild($node);
    }

    $marks_rows = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 1]/tr[position() != last() and position() != last() - 1]");
    $marks_first_term = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 1]/tr[position() != last() and position() != last() - 1]/td[position() = 3]");
    $marks_second_term = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 1]/tr[position() != last() and position() != last() - 1]/td[position() = 7]");

    $points_rows = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 2]/tbody/tr[position() != last() and position() != last() - 1]");
    $points_first_term = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 2]/tbody/tr[position() != last() and position() != last() - 1]/td[position() = 3]");
    $points_second_term = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 2]/tbody/tr[position() != last() and position() != last() - 1]/td[position() = 6]");

    $avgs_first_term = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 1]/tr[position() != last() and position() != last() - 1]/td[position() = 4]");
    $avgs_second_term = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 1]/tr[position() != last() and position() != last() - 1]/td[position() = 8]");
    $avgs_year = $xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 1]/tr[position() != last() and position() != last() - 1]/td[position() = 10]");

    foreach ($marks_rows as $i => $marks_row) {
        if ($i % 2 == 0) {
            if ($marks_first_term -> item($i / 2) -> textContent == "Brak ocen" && $marks_second_term -> item($i / 2) -> textContent == "Brak ocen") {
                $marks_first_term -> item($i / 2) -> parentNode -> replaceChild($points_first_term -> item($i / 2), $marks_first_term -> item($i / 2));
                $marks_second_term -> item($i / 2) -> parentNode -> replaceChild($points_second_term -> item($i / 2), $marks_second_term -> item($i / 2));

                $marks_rows -> item($i + 1) -> parentNode -> replaceChild($points_rows -> item($i + 1), $marks_rows -> item($i + 1));
                $desc_id = $marks_rows -> item($i + 1) -> getAttribute("id");
                $desc_id = str_replace("_OP", "", $desc_id);
                $points_rows -> item($i + 1) -> setAttribute("id", $desc_id);

                $first_term_avg = $doc -> createElement("td");
                $first_term_numerator = 0;
                $first_term_denominator = 0;

                $first_term_marks = $points_first_term -> item($i / 2) -> textContent;
                if ($first_term_marks == "Brak ocen") {
                    $first_term_avg -> textContent = "-";
                } else {
                    $marks = preg_split("/\s+/", trim($first_term_marks));
                    foreach ($marks as $mark) {
                        $mark = explode("/", $mark);

                        $first_term_numerator += floatval($mark[0]);
                        $first_term_denominator += floatval($mark[1]);
                    }
                    $first_term_avg -> textContent = strval(round($first_term_numerator / $first_term_denominator, 2) * 100) . "%";
                }
                $avgs_first_term -> item($i / 2) -> parentNode -> replaceChild($first_term_avg, $avgs_first_term -> item($i / 2));

                $second_term_avg = $doc -> createElement("td");
                $second_term_numerator = 0;
                $second_term_denominator = 0;

                $second_term_marks = $points_second_term -> item($i / 2) -> textContent;
                if ($second_term_marks == "Brak ocen") {
                    $second_term_avg -> textContent = "-";
                } else {
                    $marks = preg_split("/\s+/", trim($second_term_marks));
                    foreach ($marks as $mark) {
                        $mark = explode("/", $mark);

                        $second_term_numerator += floatval($mark[0]);
                        $second_term_denominator += floatval($mark[1]);
                    }
                    $second_term_avg -> textContent = strval(round($second_term_numerator / $second_term_denominator, 2) * 100) . "%";
                }
                $avgs_second_term -> item($i / 2) -> parentNode -> replaceChild($second_term_avg, $avgs_second_term -> item($i / 2));

                $year_avg = $doc -> createElement("td");
                $year_numerator = $first_term_numerator + $second_term_numerator;
                $year_denominator = $first_term_denominator + $second_term_denominator;

                if ($first_term_marks == "Brak ocen" && $second_term_marks == "Brak ocen") {
                    $year_avg -> textContent = "-";
                } else {
                    $year_avg -> textContent = strval(round($year_numerator / $year_denominator, 2) * 100) . "%";
                }
                $avgs_year -> item($i / 2) -> parentNode -> replaceChild($year_avg, $avgs_year -> item($i / 2));
            } else {
                $first_term_avg = $doc -> createElement("td");
                $first_term_count = 0;
                $first_term_sum = 0;

                $first_term_marks = $marks_first_term -> item($i / 2) -> textContent;
                if ($first_term_marks == "Brak ocen") {
                    $first_term_avg -> textContent = "-";
                } else {
                    $marks = preg_split("/\s+/", trim($first_term_marks));
                    foreach ($marks as $mark) {
                        if ($mark == "np") {
                            continue;
                        }
                        $mark = str_replace("+", ".5", $mark);
                        $first_term_count += 1;
                        $first_term_sum += floatval($mark);
                    }
                    $first_term_avg -> textContent = strval(round($first_term_sum / $first_term_count, 2));
                }
                $avgs_first_term -> item($i / 2) -> parentNode -> replaceChild($first_term_avg, $avgs_first_term -> item($i / 2));

                $second_term_avg = $doc -> createElement("td");
                $second_term_count = 0;
                $second_term_sum = 0;

                $second_term_marks = $marks_second_term -> item($i / 2) -> textContent;
                if ($second_term_marks == "Brak ocen") {
                    $second_term_avg -> textContent = "-";
                } else {
                    $marks = preg_split("/\s+/", trim($second_term_marks));
                    foreach ($marks as $mark) {
                        if ($mark == "np") {
                            continue;
                        }
                        $mark = str_replace("+", ".5", $mark);
                        $second_term_count += 1;
                        $second_term_sum += floatval($mark);
                    }
                    $second_term_avg -> textContent = strval(round($second_term_sum / $second_term_count, 2));
                }
                $avgs_second_term -> item($i / 2) -> parentNode -> replaceChild($second_term_avg, $avgs_second_term -> item($i / 2));

                $year_avg = $doc -> createElement("td");
                $year_count = $first_term_count + $second_term_count;
                $year_sum = $first_term_sum + $second_term_sum;

                if ($year_count == 0) {
                    $year_avg -> textContent = "-";
                } else {
                    $year_avg -> textContent = strval(round($year_sum / $year_count, 2));
                }
                $avgs_year -> item($i / 2) -> parentNode -> replaceChild($year_avg, $avgs_year -> item($i / 2));
            }
        }
    }

    foreach ($xpath -> query("//div[@class='container-background']//table[@class='decorated stretch' and position() = 2]") as $node) {
        $node -> parentNode -> removeChild($node);
    }

    echo $doc -> saveHTML();

?>

