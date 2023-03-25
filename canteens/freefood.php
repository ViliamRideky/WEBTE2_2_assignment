<?php
require_once "./setup/curl_setup.php";

$dom = curlConnect("http://www.freefood.sk/menu/#fiit-food");

$div = $dom->getElementById('fiit-food');

$rows = $div->getElementsByTagName('li');

$name_free = "FreeFood - FIITFOOD";

$index = 1;
$dayCount = 0;

$foodCount = 4;

$foods_freefood = [
    ["date" => date('d.m.Y', strtotime('monday this week')), "day" => "Pondelok", "menu" => []],
    ["date" => date('d.m.Y', strtotime('tuesday this week')), "day" => "Utorok", "menu" => []],
    ["date" => date('d.m.Y', strtotime('wednesday this week')), "day" => "Streda", "menu" => []],
    ["date" => date('d.m.Y', strtotime('thursday this week')), "day" => "Štvrtok", "menu" => []],
    ["date" => date('d.m.Y', strtotime('friday this week')), "day" => "Piatok", "menu" => []],
];

for ($i = 0; $i < 5; $i++) {

    array_push($foods_freefood[$dayCount]["menu"], $rows->item($index)->nodeValue);
    array_push($foods_freefood[$dayCount]["menu"], $rows->item($index + 1)->nodeValue);
    array_push($foods_freefood[$dayCount]["menu"], $rows->item($index + 2)->nodeValue);
    array_push($foods_freefood[$dayCount]["menu"], $rows->item($index + 3)->nodeValue);

    $index += $foodCount + 1;
    $dayCount++;
}