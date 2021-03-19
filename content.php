<?php
include_once 'partials/header.php';
require_once 'vendor/autoload.php';

// Create connection
$conn = mysqli_connect('localhost', 'root', '', 'stripe_plan');

use \DrewM\MailChimp\MailChimp;

// $MailChimp = new MailChimp('fd481d7cbcce56ba88de1bace90910c7-us9');
// $result = $MailChimp->get('lists');

echo "<pre>";
//print_r($result);
echo "</pre>";

// Set Stripe API key 
$stripe = new Stripe\StripeClient(DB::STRIPE_API_KEY);
// $subscriptions = $stripe->subscriptions->all();

echo "<pre>";
//print_r($subscriptions);
echo "</pre>";

// foreach ($subscriptions as $data) {
//     echo $data->id . "<br>";
// }

$userID = '';
// Get the logged in user ID
if (isset($_SESSION['loggedInUserID'])) {
    $userID = $_SESSION['loggedInUserID'];
}

// Cancel subscription
if (isset($_POST['cancel_subs'])) {
    $subscription_id = trim($_POST['subscription_id']);
    $subsTableId = trim($_POST['subsTableId']);
    $stripe->subscriptions->cancel($subscription_id);
    // update the users from our database
    $connection->update('users', ['subscription_id' => 0], ['id' => $userID]);
    // delete the subscription from user_subscriptions table
    $connection->delete('user_subscriptions', ['id' => $subsTableId]);

    echo "Your subscription has been canceled";
}

// Create customer and subscription, send email and auto billing with cron job
$token = $stripe->tokens->create([
    'card' => [
        'number' => '4242424242424242',
        'exp_month' => 3,
        'exp_year' => 2022,
        'cvc' => '314',
    ],
]);
$customer = $stripe->customers->create([
    'email' =>  'mofizul21@gmail.com',
    'source'  => $token->id
]);

$priceCents = round(1500 * 100);
$plan = $stripe->plans->create([
    'amount' => $priceCents,
    'currency' => 'usd',
    'interval' => 'day',
    'product' => [
        'name'  =>  'Test product by Mofi'
    ],
]);
//printor($plan);die;
$today = time();
$cancelAt = strtotime('+30 day', $today);
$subscription = $stripe->subscriptions->create([
    'customer' => $customer->id,
    'cancel_at' => $cancelAt,
    //'cancel_at_period_end' => true,
    // 'collection_method' => 'send_invoice',
    // 'days_until_due'    =>  30,
    'items' => [
        ['plan' => $plan->id],
    ],

]);
//printor($subscription); die;
// $product = $stripe->products->create([
//     'name' => 'Gold Special',
// ]);
// $price = $stripe->prices->create([
//   'unit_amount' => $priceCents,
//   'currency' => 'usd',
//   'recurring' => ['interval' => 'month'],
//   'product' => $product->id,
// ]);
// $inv = $stripe->invoiceItems->create([
//     'price' => $price->id,
//     'customer' => $customer->id,
// ]);
// $invoice = $stripe->invoices->create([
//     'customer' => $customer->id,
//     'collection_method' => 'send_invoice',
//     'days_until_due' => 30,
// ]);
// //printor($invoice); die;
// $invoice->sendInvoice();
?>

<div class="container">
    <div class="row mt-3">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h2>Plan details</h2>
                    <?php
                    $sql = "SELECT  users.id,  users.first_name, 
                    user_subscriptions.id as subsTableId, 
                    user_subscriptions.payment_method, 
                    user_subscriptions.stripe_subscription_id, 
                    user_subscriptions.plan_amount, 
                    user_subscriptions.plan_interval, 
                    user_subscriptions.payer_email 
                    FROM users 
                    INNER JOIN user_subscriptions 
                    ON users.subscription_id = user_subscriptions.user_id 
                    WHERE users.id = '$userID'";

                    $result = mysqli_query($conn, $sql);
                    
                    $row = mysqli_fetch_assoc($result);
                    if ($row) {
                        echo "Logged in user ID: " . $row['id'] . "<br>";
                        echo "Logged in subscription table ID: " . $row['subsTableId'] . "<br>";
                        echo "Logged in user name: " . $row['first_name'] . "<br>";
                        echo "Payment method: " . $row['payment_method'] . "<br>";
                        echo "Subscription ID: " . $row['stripe_subscription_id'] . "<br>";
                        echo "Plan amount: " . $row['plan_amount'] . "<br>";
                        echo "Plan interval: " . $row['plan_interval'] . "<br>";
                        echo "Payer email: " . $row['payer_email'] . "<br>";
                    }else{
                        echo "<p>Your haven't any purchased plan. Please <a href='index.php'>purchase a plan</a>.</p>";
                    }
                    
                    ?>

                    <?php if($row): ?>
                    <h4>Do you cancel your subscription?</h4>
                    <form action="" method="post">
                        <input type="hidden" name="subscription_id" value="<?= $row['stripe_subscription_id'] ?>">
                        <input type="hidden" name="subsTableId" value="<?= $row['subsTableId'] ?>">
                        <button type="submit" class="btn btn-info" name="cancel_subs">Cancel Subscription</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h3>Recurring Payment</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2>Free Content</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h2>Weekly Content</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h2>Monthly Content</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h2>Yearly Content</h2>
                </div>
            </div>
        </div>

    </div>

    <?php include_once 'partials/footer.php'; ?>