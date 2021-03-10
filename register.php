<?php 
include_once 'partials/header.php'; 

if (isset($_POST['register'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone_number = trim($_POST['phone_number']);

    $result = $connection->insert('users', [
        'subscription_id'       =>  1,
        'first_name'            =>  $first_name,
        'last_name'             =>  $last_name,
        'email'                 =>  $email,
        'password'              =>  password_hash($password, PASSWORD_BCRYPT),
        'phone'                 =>  $phone_number,
        'status'                =>  '1'
    ]);
    // Display database error: mysqli_query($conn, $query) or die(mysqli_error($conn)); 
    if ($result) {
        echo "Data inserted";
    }
}

?>

<main class="form-signin text-center">
    <form action="" method="POST">
        <img class="mb-4" src="https://getbootstrap.com/docs/5.0/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
        <h1 class="h3 mb-3 fw-normal">Please register</h1>

        <label for="first_name" class="visually-hidden">First Name</label>
        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="First Name" required>

        <label for="last_name" class="visually-hidden">Last Name</label>
        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Last Name" required>

        <label for="inputEmail" class="visually-hidden">Email address</label>
        <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required>

        <label for="inputPassword" class="visually-hidden">Password</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>

        <label for="phone" class="visually-hidden">Phone Number</label>
        <input type="number" id="phone" name="phone_number" class="form-control" placeholder="Phone Number" required>

        <br>
        
        <button class="w-100 btn btn-lg btn-primary" type="submit" name="register">Register</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2017–2021</p>
    </form>
</main>

<?php include_once 'partials/footer.php'; ?>