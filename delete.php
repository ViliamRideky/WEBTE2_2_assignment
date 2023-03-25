<?php
require_once "./config.php";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the source table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM `source`");
    $rowCount = $stmt->fetchColumn();

    if ($rowCount > 0) {
        // Delete all rows from the source table
        $stmt = $db->prepare("DELETE FROM `source`");
        $stmt->execute();

        echo "success";
    } else {
        // The source table is empty, return an error message or do nothing
        echo "Tabuľka je prázdna nedá sa z nej nič vymazať";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>