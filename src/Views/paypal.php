<?php


require_once __DIR__ . '/../../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post = $_POST;
    $total = $post['total'];
}


$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        'AUppIT-CdFXD5q0nbUlmYonqu-zd1Ey7pc__BBqhgBPfxyJyr782j2xe1RogmBAZxy4yqj7luZF7uq0b',     // ClientID
        'EFpOb7EsHrchoDGdColg6sy1TLOhGhvD3vFRx82zonAv1d_zQuqIQQRVR-_aCfhZ3v-IOcQtUAG4aa-p' 
        )
    );
    
$payer = new \PayPal\Api\Payer();
$payer->setPaymentMethod('paypal');

$amount = new \PayPal\Api\Amount();
$amount->setTotal($total);
$amount->setCurrency('USD');

$transaction = new \PayPal\Api\Transaction();
$transaction->setAmount($amount);

$base_url = "http://localhost:9000/"; 
$success = $base_url . 'src/Views/success.php';
$failure = $base_url . 'src/Views/failure.php';
$redirectUrls = new \PayPal\Api\RedirectUrls();
$redirectUrls->setReturnUrl($success)
    ->setCancelUrl($failure);

$payment = new \PayPal\Api\Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions(array($transaction))
    ->setRedirectUrls($redirectUrls);

try {
    $payment->create($apiContext);

    header("Location: index.php");
} catch (\PayPal\Exception\PayPalConnectionException $ex) {
    echo "An error occurred: " . $ex->getData();
} catch (Exception $ex) {
    echo "An unexpected error occurred: " . $ex->getMessage();
}