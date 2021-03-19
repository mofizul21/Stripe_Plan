<?php include_once 'partials/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <h3>Data insert using Firebase and PHP</h3>
            <?php
                if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
                    echo "<div class='alert alert-success'>{$_SESSION['status']}</div>";
                    unset($_SESSION['status']);
                }else{
                    echo "<div class='alert alert-warning'>{$_SESSION['status']}</div>";
                    unset($_SESSION['status']);
                }
            ?>
            <form action="firbasecodeprocess.php" method="post">
                <div class="form-group mb-2">
                    <input type="text" name="username" class="form-control" placeholder="Enter username">
                </div>
                <div class="form-group mb-2">
                    <input type="text" name="email" class="form-control" placeholder="Enter email">
                </div>
                <div class="form-group mb-2">
                    <input type="number" name="phoneno" class="form-control" placeholder="Enter phone number">
                </div>
                <div class="form-group">
                    <button type="submit" name="save_push_data" class="btn btn-primary">Save Data</button>
                </div>
            </form>
        </div>
    </div>
</div>