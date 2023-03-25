<?php
require_once "./setup/curl_setup.php";

$dom = curlConnect("http://eatandmeet.sk/tyzdenne-menu");

$parseNodes = ["day-1", "day-2", "day-3", "day-4", "day-5", "day-6", "day-7"];

$name_eat = "Eat & Meet";

$foods = [
    ["date" => date('d.m.Y', strtotime('monday this week')), "day" => "Pondelok", "menu" => [], "name" => $name],
    ["date" => date('d.m.Y', strtotime('tuesday this week')), "day" => "Utorok", "menu" => [], "name" => $name],
    ["date" => date('d.m.Y', strtotime('wednesday this week')), "day" => "Streda", "menu" => [], "name" => $name],
    ["date" => date('d.m.Y', strtotime('thursday this week')), "day" => "Štvrtok", "menu" => [], "name" => $name],
    ["date" => date('d.m.Y', strtotime('friday this week')), "day" => "Piatok", "menu" => [], "name" => $name],
];

foreach ($parseNodes as $index => $nodeId) {
    $node = $dom->getElementById($nodeId);

    foreach ($node->childNodes as $menuItem) {
        if ($menuItem && $menuItem->childNodes->item(1) && $menuItem->childNodes->item(1)->childNodes->item(3)) {
            $title = trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(1)->childNodes->item(1)->nodeValue);
            $price = trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(1)->childNodes->item(3)->nodeValue);
            $description_raw = trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(3)->nodeValue);

            $description = preg_replace('!\s+!', ' ', $description_raw);

            $foods[$index]["menu"][] = "$title ($description): $price";
        }
    }
}

$foods_eat = $foods;