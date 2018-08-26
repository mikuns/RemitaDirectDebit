<?php


require_once('connect.php');
require_once('functions.php');
require_once('commands.php');

if(isset($_GET['action']) && $_GET['action'] == 'success') {

    $con = makeConnection();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        # Get the JSON body of the incoming POST request
        $callbackBody = file_get_contents('php://input');
        # Load the JSON body into an associative array
        $callbackBodyJson = json_decode(removeJSONP($callbackBody));
        # If the notification indicates a PAYMENT_UPDATED event...
        if (property_exists($callbackBodyJson, 'notificationType') and $callbackBodyJson->notificationType == 'ACTIVATION') {
            $responseItems = $callbackBodyJson->lineItems;
            $run = runSimpleUpdateQuery($con, "mandate_tbl", ['activationStatus'], [1], ['mandateId', 'requestId'], ['=','='], ["'".$responseItems['mandateId']."'", "'".$responseItems['requestId']."'"]);

        }elseif(property_exists($callbackBodyJson, 'notificationType') and $callbackBodyJson->notificationType == 'DEBIT'){
            $newStatus = 'Debited';
            $responseItems = $callbackBodyJson->lineItems;
            $run = runSimpleUpdateQuery($con, "transactions_tbl", ['status'], ["'".$newStatus."'"], ['mandateId', 'requestId'], ['=','='], ["'".$responseItems['mandateId']."'", "'".$responseItems['requestId']."'"]);
        }
    } else {
        error_log("Received a non-POST request");
    }
}


?>


<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
<link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" />

</head>
<body>

<div class="container-fluid">
    <div class="row" style="height:100%;">

        <div class="col-lg-6" style="background-color:#282828; text-align:center;">
            <h1 class="text-white" style="margin-top: 45%;">NOTIFICATIONS</h1>
        </div>
        <div class="col-lg-6">
            <div class="text-center" style="margin-top: 40%;">
                <i class="fa fa-thumbs-up fa-5x" style="text-color:#282828;"></i>
            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.8.0/parsley.min.js"></script>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/parsley.min.js"></script>
<script src="assets/js/toastr.js" type="text/javascript"></script>
<script src="assets/js/script.js" type="text/javascript"></script>

</body>
</html>