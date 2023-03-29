<?php
require_once '../../functions/helpers.php';
require_once '../../functions/pdo_connection.php';
require_once '../../functions/check-login.php';
global $pdo;

if (!isset($_GET['post_id'])) {
    redirect('panel/post');
}

$query = 'SELECT * FROM php_project.posts WHERE id = ?';
$stmt = $pdo->prepare($query);
$stmt->execute([$_GET['post_id']]);
$post = $stmt->fetch();

if ($post === false) {
    redirect('panel/post');
}


if (
    isset($_POST['title']) && $_POST['title'] !== '' &&
    isset($_POST['cat_id']) && $_POST['cat_id'] !== '' &&
    isset($_POST['body']) && $_POST['body'] !== ''
) {

    $query = 'SELECT * FROM php_project.categories WHERE id = ?';
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_POST['cat_id']]);
    $category = $stmt->fetch();


    if (isset($_FILES['image']) && $_FILES['image']['name'] !== '') {

        $allowedMimes = ['png', 'jpeg', 'jpg', 'gif']; //Allowed extension values
        $imageMine = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        // Check if the file extension is valid
        if (!in_array($imageMine, $allowedMimes)) {
            redirect('panel/post');
        }
        $basePath = dirname(dirname(__DIR__));
        if (file_exists($basePath . $post->image)) {

            unlink($basePath . $post->image);
        }


        $image = '/assets/images/posts/' . date("Y_m_d_H_i_s") . '.' . $imageMine;
        $image_upload = move_uploaded_file($_FILES['image']['tmp_name'], $basePath . $image);

        if ($image_upload !== false && $category !== false) {

            $query = 'UPDATE php_project.posts SET title = ? , cat_id=? , body = ? , image=? , updated_at = NOW() WHERE id = ? ;';
            $stmt = $pdo->prepare($query);
            $stmt->execute([$_POST['title'], $_POST['cat_id'], $_POST['body'], $image, $_GET['post_id']]);
        }
    }
    // Removed the "other" keyword in the following if statement
    else {

        if ($category !== false) {

            $query = 'UPDATE php_project.posts SET title = ? , cat_id=? , body = ?  , updated_at = NOW() WHERE id = ? ;';
            $stmt = $pdo->prepare($query);
            $stmt->execute([$_POST['title'], $_POST['cat_id'], $_POST['body'],  $_GET['post_id']]);
        }
    }
    redirect('panel/post');
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP panel</title>
    <link rel="stylesheet" href="<?= asset('assets/css/bootstrap.min.css') ?>" media="all" type="text/css">
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>" media="all" type="text/css">
</head>

<body>
    <section id="app">
        <?php require_once '../layouts/top-nav.php'; ?>

        <section class="container-fluid">
            <section class="row">
                <section class="col-md-2 p-0">
                    <!-- sidebar-->
                    <?php require_once '../layouts/sidebar.php'; ?>
                </section>
                <section class="col-md-10 pt-3">

                    <form action="<?= url('panel/post/edit.php?post_id=' . $_GET['post_id']) ?>" method="post" enctype="multipart/form-data">
                        <section class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title" value="<?= $post->title ?>">
                        </section>
                        <section class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" name="image" id="image">
                            <img src="<?= asset($post->image) ?>" alt="" class="mt-2" width=150rem height="150rem">
                        </section>
                        <section class="form-group">
                            <label for="cat_id">Category</label>
                            <select class="form-control" name="cat_id" id="cat_id">
                                <?php
                                global $pdo;

                                // ساخت query و استفاده از prepared statement
                                $query = "SELECT * FROM php_project.categories";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();

                                $categoris = $stmt->fetchAll();

                                foreach ($categoris as $category) {
                                ?>
                                    <option value="<?= $category->id ?>" <?php if ($category->id == $post->cat_id) echo 'selected'; ?>><?= $category->name ?></option>
                                <?php } ?>
                            </select>
                        </section>
                        <section class="form-group">
                            <label for="body">Body</label>
                            <textarea class="form-control" name="body" id="body" rows="5"><?= $post->body ?></textarea>
                        </section>
                        <section class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </section>
                    </form>

                </section>
            </section>
        </section>

    </section>

    <script src="<?= asset('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= asset('assets/js/bootstrap.min.js') ?>"></script>
</body>

</html>