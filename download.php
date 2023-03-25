<?php
require_once "./config.php";
require_once "./setup/curl_setup.php";


try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $delikanti_name = "Delikanti - FEI STU";
    $delikanti_url = "https://www.delikanti.sk/prevadzky/1-jedalen-fei-stu/";

    $eat_name = "Eat & Meet";
    $eat_url = "http://eatandmeet.sk/tyzdenne-menu";

    $freefood_name = "FreeFood - FIITFOOD";
    $freefood_url = "http://www.freefood.sk/menu/#fiit-food";

    $delikanti_DOM = curlConnect($delikanti_url);
    $eat_DOM = curlConnect($eat_url);
    $freefood_DOM = curlConnect($freefood_url);

    $delikanti_page = $delikanti_DOM->saveHTML();
    $eat_page = $eat_DOM->saveHTML();
    $freefood_page = $freefood_DOM->saveHTML();

    // zachovanie diaktritiky
    $delikanti_text = html_entity_decode($delikanti_page);
    $eat_text = html_entity_decode($eat_page);
    $freefood_text = html_entity_decode($freefood_page);

    $sql = "INSERT INTO source (url,name,html) VALUES (?,?,?),(?,?,?),(?,?,?)";
    $stmt = $db->prepare($sql);
    $success = $stmt->execute(
        array(
            $delikanti_url,
            $delikanti_name,
            $delikanti_text,
            $eat_url,
            $eat_name,
            $eat_text,
            $freefood_url,
            $freefood_name,
            $freefood_text
        )
    );

} catch (PDOException $e) {
    echo $e->getMessage();
}

?>