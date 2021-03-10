<?php 
include_once 'partials/header.php'; 

if (isset($_POST['login'])) {    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $result = $connection->select('users', '*', ['email'=>$email]);
    $result->execute();

    if ($result->rowCount() === 1) {
        $data = $result->fetch();
        if (password_verify($password, $data['password'])) {
            $_SESSION['name'] = $data['first_name'].' '.$data['last_name'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['loggedInUserID'] = $data['id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['message'] = message('success', 'Login success');
            header('location: index.php');
        }else{
            $message = message('warning', 'Wrong password');
        }
    }else{
        $message = message('warning', 'User not found');
    }
}
?>

<main class="form-signin text-center">
    <?php 
    if (isset($message)) {
        echo $message;
    } 
    ?>

    <form method="POST" action="">
        <img class="mb-4" src="https://getbootstrap.com/docs/5.0/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
        <label for="inputEmail" class="visually-hidden">Email address</label>
        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required>
        <label for="inputPassword" class="visually-hidden">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit" name="login">Sign in</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2017–2021</p>
    </form>
</main>

<?php include_once 'partials/footer.php'; ?>