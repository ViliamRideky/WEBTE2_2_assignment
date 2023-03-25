<?php
function curlConnect($address): DOMDocument
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $address);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);

    curl_close($ch);

    $dom = new DOMDocument();
    $dom->loadHTML($output);
    $dom->preserveWhiteSpace = false;

    return $dom;
}