<?php
require_once '../../functions/helpers.php';
require_once '../../functions/pdo_connection.php';
require_once '../../functions/check-login.php';
global $pdo;

if (isset($_GET['post_id']) &&  $_GET['post_id']!=='') {

    $query = 'SELECT * FROM php_project.posts WHERE id = ?';
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_GET['post_id']]);
    $post = $stmt->fetch();

    if ($post !== false) {
        $status= ($post-> status == 10) ? 0 :10 ;

        $query = 'UPDATE php_project.posts SET status=? WHERE id = ? ;';
        $stmt = $pdo->prepare($query);
        $stmt->execute([$status, $_GET['post_id'] ]);
    }
}

redirect('panel/post');