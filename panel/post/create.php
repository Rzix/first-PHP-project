<?php
require_once '../../functions/helpers.php';
require_once '../../functions/pdo_connection.php';
require_once '../../functions/check-login.php';

if (
    isset($_POST['title']) && $_POST['title'] !== '' &&
    isset($_POST['cat_id']) && $_POST['cat_id'] !== '' &&
    isset($_POST['body']) && $_POST['body'] !== '' &&
    isset($_FILES['image']) && $_FILES['image']['name'] !== ''
) {

    global $pdo;

    $query = 'SELECT *  FROM php_project.categories WHERE id = ?';
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_POST['cat_id']]);
    $category = $stmt->fetch();

    $allowedMimes = ['png', 'jpeg', 'jpg', 'gif']; //Allowed suffix values
    $imageMine = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    // Check if the file extension is valid
    if (!in_array($imageMine, $allowedMimes)) {
        redirect('panel/post');
    }

    $basePath = dirname(dirname(__DIR__));
    $image = '/assets/images/posts/' . date("Y_m_d_H_i_s") . '.' . $imageMine;
    $image_upload = move_uploaded_file($_FILES['image']['tmp_name'], $basePath . $image);

    if ($image_upload !== false && $category !== false) {

        $query = 'INSERT INTO php_project.posts SET title = ? , cat_id=? , body=? , image=? , created_at = NOW() ;';
        $stmt = $pdo->prepare($query);
        $stmt->execute([$_POST['title'], $_POST['cat_id'], $_POST['body'], $image]);
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

                    <form action="<?= url('panel/post/create.php') ?>" method="post" enctype="multipart/form-data">
                        <section class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="title ...">
                        </section>
                        <section class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" name="image" id="image">
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
                                    <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                <?php } ?>
                            </select>
                        </section>
                        <section class="form-group">
                            <label for="body">Body</label>
                            <textarea class="form-control" name="body" id="body" rows="5" placeholder="body ..."></textarea>
                        </section>
                        <section class="form-group">
                            <button type="submit" class="btn btn-primary">Create</button>
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