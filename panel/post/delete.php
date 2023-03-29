<?php
require_once '../../functions/helpers.php';
require_once '../../functions/pdo_connection.php';
require_once '../../functions/check-login.php';

global $pdo;

if (isset($_GET['post_id']) &&  $_GET['post_id'] !== '') {

    $query = 'SELECT * FROM php_project.posts WHERE id = ?';
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_GET['post_id']]);
    $post = $stmt->fetch();

    $basePath=dirname(dirname(__DIR__));

    if(file_exists($basePath . $post -> image)){
        unlink($basePath . $post->image);
    }
    $query = "DELETE FROM php_project.posts WHERE id=?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_GET['post_id']]);
 
}

redirect('panel/post');
