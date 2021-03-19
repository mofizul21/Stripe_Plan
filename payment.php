<?php
// Include configuration file  
require_once 'config.php';
// Include libraries
require_once 'vendor/autoload.php';

// Get user ID from current SESSION 
$userID = isset($_SESSION['loggedInUserID']) ? $_SESSION['loggedInUserID'] : 1;

$payment_id = $statusMsg = $api_error = '';
$ordStatus = 'error';

// Check whether stripe token is not empty 
if (!empty($_POST['subscr_plan']) && !empty($_POST['stripeToken'])) {

    // Retrieve stripe token and user info from the submitted form data 
    $token  = $_POST['stripeToken'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Plan info 
    $planID = $_POST['subscr_plan'];
    $planInfo = $plans[$planID];
    $planName = $planInfo['name'];
    $planPrice = $planInfo['price'];
    $planInterval = $planInfo['interval'];

    // Set Stripe API key
    \Stripe\Stripe::setApiKey(DB::STRIPE_API_KEY);

    // Add customer to stripe 
    try {
        $customer = \Stripe\Customer::create(array(
            'email' => $email,
            'source'  => $token
        ));
        // echo "<pre>";
        // print_r($customer);
        // echo "</pre>";        
    } catch (Exception $e) {
        $api_error = $e->getMessage();
    }

    if (empty($api_error) && $customer) {

        // Convert price to cents 
        $priceCents = round($planPrice * 100);

        // Create a plan 
        try {
            $plan = \Stripe\Plan::create(array(
                "product" => [
                    "name" => $planName
                ],
                "amount" => $priceCents,
                "currency" => $currency,
                "interval" => $planInterval,
                "interval_count" => 1
            ));
            // echo "<pre>";
            // print_r($plan);
            // echo "</pre>";
        } catch (Exception $e) {
            $api_error = $e->getMessage();
        }

        if (empty($api_error) && $plan) {
            // Creates a new subscription 
            try {
                $subscription = \Stripe\Subscription::create(array(
                    "customer" => $customer->id,
                    "items"     => array(
                        array(
                            "plan" => $plan->id,
                        ),
                    ),
                    //'cancel_at' =>  $planInterval
                ));
                // echo "<pre>";
                // print_r($subscription);
                // echo "</pre>";                
            } catch (Exception $e) {
                $api_error = $e->getMessage();
            }
            

            if (empty($api_error) && $subscription) {
                // Retrieve subscription data 
                $subsData = $subscription->jsonSerialize();

                // Check whether the subscription activation is successful 
                if ($subsData['status'] == 'active') {
                    // Subscription info 
                    $subscrID = $subsData['id'];
                    $custID = $subsData['customer'];
                    $planID = $subsData['plan']['id'];
                    $planAmount = ($subsData['plan']['amount'] / 100);
                    $planCurrency = $subsData['plan']['currency'];
                    $planinterval = $subsData['plan']['interval'];
                    $planIntervalCount = $subsData['plan']['interval_count'];
                    $created = date("Y-m-d H:i:s", $subsData['created']);
                    $current_period_start = date("Y-m-d H:i:s", $subsData['current_period_start']);
                    $current_period_end = date("Y-m-d H:i:s", $subsData['current_period_end']);
                    $status = $subsData['status'];

                    // Insert transaction data into the database 
                    $dataInsert = $connection->insert('user_subscriptions', [
                        'user_id' => $userID,
                        'stripe_subscription_id' => $subscrID,
                        'stripe_customer_id' => $custID,
                        'stripe_plan_id' => $planID,
                        'plan_amount' => $planAmount,
                        'plan_amount_currency' => $planCurrency,
                        'plan_interval' => $planinterval,
                        'plan_interval_count' => $planIntervalCount,
                        'payer_email' => $email,
                        'created' => $created,
                        'plan_period_start' => $current_period_start,
                        'plan_period_end' => $current_period_end,
                        'status' => $status,
                    ]);

                    // Update subscription id in the users table  
                    if ($dataInsert && !empty($userID)) {
                        $subscription_id = $connection->lastInsertId();

                        $update_user = $connection->update('users', ['subscription_id' => $subscription_id], ['id' => $userID]);
                        // 
                        $get_user = $connection->select('user_subscriptions', '*');
                        $get_user->execute();
                        $data = $get_user->fetch();
                        $_SESSION['plan_interval'] = $data['plan_interval'];
                    }

                    $ordStatus = 'success';
                    $statusMsg = 'Your Subscription Payment has been Successful!';
                } else {
                    $statusMsg = "Subscription activation failed!";
                }
            } else {
                $statusMsg = "Subscription creation failed! " . $api_error;
            }
        } else {
            $statusMsg = "Plan creation failed! " . $api_error;
        }
    } else {
        $statusMsg = "Invalid card details! $api_error";
    }

    // Send email with attachment to the user by using SwiftMailer and mPDF
    // Generate  PDF
    $mpdf = new \Mpdf\Mpdf();
    ob_start();
    echo "<p>Hi {$name},</p>";
    echo "<p>Thanks for being member in our community.</p>";
    echo "<b>Your plan details</b>";
    echo "<p><b>Transaction ID:</b> {$subscrID}</p>";
    echo "<p><b>Plan name:</b> {$planName}</p>";
    echo "<p><b>Amount:</b> {$planAmount} {$planCurrency}</p>";
    echo "<p><b>Plan interval:</b> {$planInterval}</p>";
    echo "<p><b>Plan start:</b> {$current_period_start}</p>";
    echo "<p><b>Plan end:</b> {$current_period_end}</p>";
    echo "<p><b>Plan status:</b> {$status}</p>";
    echo "<p>Thanks.</p>";
    $html = ob_get_contents();
    ob_end_clean();
    // Here convert the encode for UTF-8, if you prefer the ISO-8859-1 just change for $mpdf->WriteHTML($html);
    $mpdf->WriteHTML(utf8_encode($html));
    $content = $mpdf->Output('', 'S');
    // Create instance of Swift_Attachment with our PDF file
    $attachment = new Swift_Attachment($content, 'subscription.pdf', 'application/pdf');


    // Create the email Transport
    $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 2525))
        ->setUsername('3a8cbc3e9c2e53')
        ->setPassword('ea6eac7ed290c8');

    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

    // Create a message
    $message = (new Swift_Message('Wonderful Subject'))
        ->setFrom(['no-reply@mofizul.com' => 'Mofizuls Plan'])
        ->setTo([$email => $name])
        ->setSubject('Thanks from Mofizuls Plan')
        ->setBody("Hi {$name}, you have successfully purchased our plan. Thanks for being a member in our community. See the attached PDF details about your plan. By- Mofizuls Plan")
        ->attach($attachment);

    // Send the message
    $result = $mailer->send($message);
} else {
    $statusMsg = "Error on form submission, please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="status">
                    <h1>This page will redirect within <span id="countdowntimer">10</span> seconds. Check email for details. <progress value="0" max="10" id="progressBar"></progress></h1>
                    
                    <h3 class="<?php echo $ordStatus; ?>"><?php echo $statusMsg; ?></h3>
                    <?php if (!empty($subscrID)) { ?>
                        <h4>Payment Information</h4>
                        <p><b>Reference Number:</b> <?php echo $subscription_id; ?></p>
                        <p><b>Transaction ID:</b> <?php echo $subscrID; ?></p>
                        <p><b>Amount:</b> <?php echo $planAmount . ' ' . $planCurrency; ?></p>

                        <h4>Subscription Information</h4>
                        <p><b>Plan Name:</b> <?php echo $planName; ?></p>
                        <p><b>Amount:</b> <?php echo $planPrice . ' ' . $currency; ?></p>
                        <p><b>Plan Interval:</b> <?php echo $planInterval; ?></p>
                        <p><b>Period Start:</b> <?php echo $current_period_start; ?></p>
                        <p><b>Period End:</b> <?php echo $current_period_end; ?></p>
                        <p><b>Status:</b> <?php echo $status; ?></p>
                    <?php } ?>
                </div>
                <a href="index.php" class="btn-link">Back to Subscription Page</a>

                <script>
                    // countdown
                    var timeleftCount = 10;
                    var downloadTimerCount = setInterval(function() {
                        timeleftCount--;
                        document.getElementById("countdowntimer").textContent = timeleftCount;
                        if (timeleftCount <= 0)
                            clearInterval(downloadTimerCount);
                    }, 1000);

                    // progressbar
                    var timeleft = 10;
                    var downloadTimer = setInterval(function() {
                        if (timeleft <= 0) {
                            clearInterval(downloadTimer);
                        }
                        document.getElementById("progressBar").value = 10 - timeleft;
                        timeleft -= 1;
                    }, 1000);

                    // redirect
                    window.setTimeout(function() {
                        window.location.href = 'content.php';
                    }, 10000);
                </script>
            </div>
        </div>
    </div>
</body>

</html>