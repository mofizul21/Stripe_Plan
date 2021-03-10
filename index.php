<?php include_once 'partials/header.php'; ?>

<div class="container mt-3">
    <div class="row">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>

        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <?php if (isset($_SESSION['loggedInUserID'])) { ?>
                        <form action="payment.php" method="POST" id="paymentFrm">
                            <div class="card-header mb-3">
                                <h3 class="card-title text-center">Plan Subscription with Stripe</h3>

                                <!-- Plan Info -->
                                <p>
                                    <b>Select Plan:</b>
                                    <select name="subscr_plan" id="subscr_plan" class="form-control">
                                        <?php foreach ($plans as $id => $plan) { ?>
                                            <option value="<?php echo $id; ?>"><?php echo $plan['name'] . ' [$' . $plan['price'] . '/' . $plan['interval'] . ']'; ?></option>
                                        <?php } ?>
                                    </select>
                                </p>
                            </div>

                            <!-- Display errors returned by createToken -->
                            <div id="paymentResponse"></div>

                            <!-- Payment form -->
                            <div class="form-group mb-3">
                                <label>NAME</label>
                                <input type="text" name="name" id="name" class="field form-control" value="<?= $_SESSION['name'] ?>" required="">
                            </div>
                            <div class="form-group mb-3">
                                <label>EMAIL</label>
                                <input type="email" name="email" id="email" class="field form-control" value="<?= $_SESSION['email'] ?>" required="">
                            </div>
                            <div class="form-group mb-3">
                                <label>CARD NUMBER</label>
                                <div id="card_number" class="field form-control"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>EXPIRY DATE</label>
                                        <div id="card_expiry" class="field form-control"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>CVC CODE</label>
                                        <div id="card_cvc" class="field form-control"></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-block" id="payBtn">Submit Payment</button>
                        </form>
                    <?php } else { ?>
                        <div class="alert alert-warning" role="alert">
                            <h3>Please <a href="login.php">login</a> or <a href="register.php">register</a> to purchase our plan</h3>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!--end .col-md-5 -->
        <div class="col-md-7">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Brand</th>
                        <th>CVC</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>4242424242424242</td>
                        <td>Visa</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>4000056655665556</td>
                        <td>Visa (debit)</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>5555555555554444</td>
                        <td>Mastercard</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>2223003122003222</td>
                        <td>Mastercard (2-series)</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>5200828282828210</td>
                        <td>Mastercard (debit)</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>5105105105105100</td>
                        <td>Mastercard (prepaid)</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>378282246310005</td>
                        <td>American Express</td>
                        <td>Any 4 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>371449635398431</td>
                        <td>American Express</td>
                        <td>Any 4 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>6011111111111117</td>
                        <td>Discover</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>6011000990139424</td>
                        <td>Discover</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>3056930009020004</td>
                        <td>Diners Club</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>36227206271667</td>
                        <td>Diners Club (14 digit card)</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>3566002020360505</td>
                        <td>JCB</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                    <tr>
                        <td>6200000000000005</td>
                        <td>UnionPay</td>
                        <td>Any 3 digits</td>
                        <td>Any future date</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>