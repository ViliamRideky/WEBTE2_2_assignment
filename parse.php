<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once "config.php";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $sql = "SELECT * FROM source ORDER BY id DESC LIMIT 1";
    $stmt = $db->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $id = $result["id"];
        $url = $result["url"];
        $name = $result["name"];
        $html = $result["html"];
        $dom = new DOMDocument();
        $dom->loadHTML($result['html']);

        if ($name == "Delikanti - PriF UK") {
            $tables = $dom->getElementsByTagName('table');
            $rows = $tables->item(0)->getElementsByTagName('tr');
            $index = 0;
            $dayCount = 0;
            $foods = [];
            $foodCount = $rows->item(0)->getElementsByTagName('th')->item(0)->getAttribute('rowspan');

            foreach ($rows as $row) {
                if ($row->getElementsByTagName('th')->item(0)) {
                    $foodCount = $row->getElementsByTagName('th')->item(0)->getAttribute('rowspan');

                    $day = trim($rows->item($index)->getElementsByTagName('th')->item(0)->getElementsByTagName('strong')->item(0)->nodeValue);

                    $th = $rows->item($index)->getElementsByTagName('th')->item(0);

                    foreach ($th->childNodes as $node) {
                        if (!($node instanceof \DomText)) {
                            $node->parentNode->removeChild($node);
                        }
                    }

                    $date = trim($rows->item($index)->getElementsByTagName('th')->item(0)->nodeValue);
                    $name = "Delikanti - PriF UK";

                    $foods[] = ["date" => $date, "day" => $day, "menu" => [], "name" => $name];

                    for ($i = $index; $i < $index + intval($foodCount); $i++) {
                        if ($foods[$dayCount]) {
                            $menu_item = trim($rows->item($i)->getElementsByTagName('td')->item(1)->nodeValue);
                            $foods[$dayCount]["menu"][] = preg_replace('!\s+!', ' ', $menu_item);
                        }
                    }

                    $index += intval($foodCount);
                    $dayCount++;
                }
            }
            $foods_delikanti = $foods;

            for ($a = 0; $a < 5; $a++) {
                for ($b = 0; $b < 6; $b++) {
                    //$restaurant = $name;
                    $source_id = $id;
                    $item = $foods_delikanti[$a]['menu'][$b];
                    $price = 4.90;
                    $date = date_create_from_format('j.n.Y', $foods_delikanti[$a]["date"]);
                    $formattedDate = $date->format('Y-m-d');

                    var_dump($restaurant);
                    var_dump($date);
                    var_dump($source_id);
                    var_dump($name);

                    $foodName = $item;

                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql = "SELECT * FROM food WHERE name=:name";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(":name", $foodName, PDO::PARAM_STR);
                    $stmt->execute();
                    $foodAlreadyInDB = $stmt->fetchAll();

                    if (!$foodAlreadyInDB) {
                        $sql = "INSERT INTO food (restaurant, date, source_id, name,price) VALUES (:restaurant, :date, :source_id, :name, :price)";
                        $stmt = $db->prepare($sql);
                        $sucess = $stmt->execute(
                            array(
                                ':restaurant' => $name,
                                ':date' => $formattedDate,
                                ':source_id' => $source_id,
                                ':name' => $foodName,
                                ':price' => $price

                            )
                        );
                    }
                }
            }

        } else if ($name == "Eat & Meet") {
            $dom->loadHTML($html);
            $parseNodes = ["day-1", "day-2", "day-3", "day-4", "day-5", "day-6", "day-7"];

            $foods = [
                ["date" => date('d.m.Y', strtotime('monday this week')), "day" => "Pondelok", "menu" => [], "name" => $name],
                ["date" => date('d.m.Y', strtotime('tuesday this week')), "day" => "Utorok", "menu" => [], "name" => $name],
                ["date" => date('d.m.Y', strtotime('wednesday this week')), "day" => "Streda", "menu" => [], "name" => $name],
                ["date" => date('d.m.Y', strtotime('thursday this week')), "day" => "Štvrtok", "menu" => [], "name" => $name],
                ["date" => date('d.m.Y', strtotime('friday this week')), "day" => "Piatok", "menu" => [], "name" => $name],
                ["date" => date('d.m.Y', strtotime('saturday this week')), "day" => "Sobota", "menu" => [], "name" => $name],
                ["date" => date('d.m.Y', strtotime('sunday this week')), "day" => "Nedeľa", "menu" => [], "name" => $name],
            ];

            foreach ($parseNodes as $index => $nodeId) {
                $node = $dom->getElementById($nodeId);

                foreach ($node->childNodes as $menuItem) {
                    if ($menuItem && $menuItem->childNodes->item(1) && $menuItem->childNodes->item(1)->childNodes->item(3)) {
                        $title = trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(1)->childNodes->item(1)->nodeValue);
                        $price = str_replace(',', '.', trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(1)->childNodes->item(3)->nodeValue));
                        $description_raw = trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(3)->nodeValue);

                        $description = preg_replace('!\s+!', ' ', $description_raw);

                        $foods[$index]["menu"][] = [
                            "title" => $title,
                            "description" => $description,
                            "price" => $price,
                        ];
                    }
                }
            }
            $foods_eat = $foods;

            for ($i = 0; $i < sizeof($foods_eat); $i++) {
                foreach ($foods_eat[$i]["menu"] as $item) {

                    $foodName = $item["description"];

                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql = "SELECT * FROM food WHERE name=:name";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(":name", $foodName, PDO::PARAM_STR);
                    $stmt->execute();
                    $foodAlreadyInDB = $stmt->fetchAll();


                    if (!$foodAlreadyInDB) {
                        $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $sql = "INSERT INTO food (source_id, restaurant, name, price, date) VALUES (?,?,?,?,?)";
                        $stmt = $db->prepare($sql);
                        $date = date_create_from_format('j.n.Y', $foods_eat[$i]["date"]);
                        $formattedDate = $date->format('Y-m-d');
                        $success = $stmt->execute(array($id, $name, $foodName, floatval($item["price"]), $formattedDate));
                    }
                }
            }
        }
    }


} catch (PDOException $e) {
    echo $e->getMessage();
}

?>