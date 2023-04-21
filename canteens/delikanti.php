<?php
require_once "./setup/curl_setup.php";

$dom = curlConnect("https://www.delikanti.sk/prevadzky/3-jedalen-prif-uk/");

$name_del = "Delikanti - PriF UK";

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
        $name = "Delikanti";

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