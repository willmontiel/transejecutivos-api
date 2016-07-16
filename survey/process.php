<?php

session_start();

require_once '../api/include/DbHandlerDriver.php';

$db = new DbHandlerDriver();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comments = $_POST['comments'];
    $id = $_POST['id'];
    $rating = $_POST['rating'];
    
    $res = $db->updateQualify($id, $rating, $comments);
 
    header("Location: success.html");
    die();
}