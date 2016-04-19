<?php

session_start();

require_once '../api/include/DbHandlerDriver.php';

$db = new DbHandlerDriver();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comments = $_POST['comments'];
    $ref = $_POST['ref'];
    $id = $_POST['id'];
    $rating = $_POST['rating'];
    
    $res = $db->setQualify($id, $ref, $rating, $comments);
 
    header("Location: success.html");
    die();
}