<?php 

$live = false;

if($live == true){
//    Live Mode

//    $GLOBALS['merchantID'] = '';
//    $GLOBALS['ServiceTypeID'] = '';
//    $GLOBALS['fundingAccount'] = '';
//    $GLOBALS['fundingBankCode'] = '';
//
//    $GLOBALS['apiKey'] = '';
//    $GLOBALS['apiToken'] = '';
//
//    $GLOBALS['apiMandateSetupURL'] = '';
//
//    $GLOBALS['baseRemitaMandateStatusURL'] = '';
//    $GLOBALS['responseURL'] = '';
//    $GLOBALS['baseRemitaURL'] = '';
//    $GLOBALS['debitDirectProcessURL'] = '';
//    $GLOBALS['DirectDebitStatusURL'] = '';
//    $GLOBALS['MandateTransactionHistoryURL'] = '';
//    $GLOBALS['baseMandateViewURL'] = '';
//    $GLOBALS['cancelDebitDirectURL'] = '';
//    $GLOBALS['terminateMandateURL'] = '';
//    $GLOBALS['OTPMandateSetupActivation'] = '';
//    $GLOBALS['OTPMandateSetupValidation'] = '';
    //$GLOBALS['bankMandateURL'] = '';
}else{
    //Test Mode
    $GLOBALS['merchantID'] = 27768931;
    $GLOBALS['ServiceTypeID'] = 35126630;
    $GLOBALS['fundingAccount'] = 32883773244;
    $GLOBALS['fundingBankCode'] = 057;

    $GLOBALS['apiKey'] = 'Q1dHREVNTzEyMzR8Q1dHREVNTw==';
    $GLOBALS['apiToken'] = 'SGlQekNzMEdMbjhlRUZsUzJCWk5saDB6SU14Zk15djR4WmkxaUpDTll6bGIxRCs4UkVvaGhnPT0=';

    $GLOBALS['baseRemitaMandateStatusURL'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/';
    $GLOBALS['responseURL'] = 'http://localhost/remita/callbackURL.php';
    $GLOBALS['baseRemitaURL'] = 'http://www.remitademo.net/remita/ecomm/';
    $GLOBALS['debitDirectProcessURL'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/send';
    $GLOBALS['DirectDebitStatusURL'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/status';
    $GLOBALS['MandateTransactionHistoryURL'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/history';
    $GLOBALS['baseMandateViewURL'] = 'http://www.remitademo.net/remita/ecomm/mandate/form/';
    $GLOBALS['cancelDebitDirectURL'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/stop';
    $GLOBALS['terminateMandateURL'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/stop';
    $GLOBALS['apiMandateSetupURL'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/setup';
    $GLOBALS['OTPMandateSetupActivation'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/requestAuthorization';
    $GLOBALS['OTPMandateSetupValidation'] = 'http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/validateAuthorization';
    //$GLOBALS['bankMandateURL'] = 'http://www.remitademo.net/remita/ecomm/mandate/form/{merchantId}/{hash}/{mandateId}/{requestId}/rest.reg';
}

    function callRemitaOTPApi($endPoint, $postData, $requestTS) {
    $api_key = $GLOBALS['apiKey'];
    $api_token = $GLOBALS['apiToken'];
    $merchantId = $GLOBALS['merchantID'];
    $api_Hash = hash('sha512',  $api_key. time(). $api_token);
//        base64_encode(hash('sha512', $enteredPassword, true));
//    $requestTS = date("Y-m-d\TH:i:s\Z", time());
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'API_DETAILS_HASH: '.$api_Hash,
        'API_KEY: '.$api_key,
        'Cache-Control: no-cache',
        'Content-Type: application/json',
        'MERCHANT_ID: '.$merchantId,
        'REQUEST_ID: '.time(),
        'REQUEST_TS: '.$requestTS)
    );
    $output = curl_exec($ch);
    return $output;
}

//Remita POST API via Curl -- Use This
function callRemitaApiPost($endPoint, $postData) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($postData)))
    );
    $output = curl_exec($ch);
    return $output;
}
//Remita POST API via Curl -- Use This
function callRemitaApiGet($endPoint) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Cache-Control: no-cache',
            'Content-Type: application/json')
    );
    $output = curl_exec($ch);
    return $output;
}

//Remove JSONP
function removeJSONP($data) {
    if (substr($data, 0, 5) == 'jsonp') {
        $firstPass = substr($data, 5);
        return trim($firstPass, ') (');
    }
    return $data;
}

//Remove JSONP
function removeJSONP2($data) {
    $data1 = implode($data);  
    if (substr($data1, 0, 5) == 'jsonp') {
        $firstPass = substr($data1, 5);
        return trim($firstPass, ') (');
    }
    // $data2 = explode($data1);
    return $data1;
}

//Setup Direct Debit Mandate per each User
function setupDirectDebitMandate($payerName, $payerEmail, $payerPhone, $payerBankCode, $payerAccount, $requestId, $amount, $startDate, $endDate, $mandateType, $maxNoOfDebits ) {

    $hash = hash('sha512',  $GLOBALS['merchantID']. $GLOBALS['ServiceTypeID']. $requestId. $amount. $GLOBALS['apiKey']);

    $mandateData = array(

        'merchantId' => $GLOBALS['merchantID'],
        'serviceTypeId' => $GLOBALS['ServiceTypeID'],
        'hash' => $hash,
        'payerName' => $payerName,
        'payerEmail' => $payerEmail,
        'payerPhone' => $payerPhone,
        'payerBankCode' => $payerBankCode,
        'payerAccount' => $payerAccount,
        'requestId' => $requestId,
        'amount' => $amount,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'mandateType' => $mandateType,
        'maxNoOfDebits' => $maxNoOfDebits);
    //echo json_encode($mandateData);
    $response = callRemitaApiPost($GLOBALS['apiMandateSetupURL'], $mandateData);
    return json_decode(removeJSONP($response), TRUE);
}

// Request OTP for Mandate Activation
function RequestOTPforMandateActivation($mandateId, $requestId, $requestTS) {
    $mandateOTPData = array(
        'mandateId' => $mandateId,
        'requestId' => $requestId);
    //echo json_encode($mandateOTPData);
    $response = callRemitaOTPApi($GLOBALS['OTPMandateSetupActivation'], $mandateOTPData, $requestTS);
    return json_decode(removeJSONP($response), TRUE);
}
// Activate OTP for Mandate Activation
function ActivateOTPforMandateActivation($remitaTransRef, $card, $otp, $requestTS) {
    $mandateOTPData = array(
        'remitaTransRef' => $remitaTransRef,
        'authParams' => array(
            '0' => Array(
                'param1' => 'OTP',
                'value' => $otp),
            '1' => Array(
                'param2' => 'CARD',
                'value' => $card)
        )
    );
    //echo json_encode($mandateOTPData);
    $response = callRemitaOTPApi($GLOBALS['OTPMandateSetupValidation'], $mandateOTPData, $requestTS);
    return json_decode(removeJSONP($response), TRUE);
}

//Do Direct Debit
function doDirectDebit($requestId, $mandateId, $amount) {

    //HASH: SHA512(merchantId+serviceTypeId+requestId+api_key)
    $hash = hash('sha512', $GLOBALS['merchantID'] . $GLOBALS['ServiceTypeID'] . $requestId. $amount . $GLOBALS['apiKey']);
    $postData = array(
        'merchantId' => $GLOBALS['merchantID'],
        'serviceTypeId' => $GLOBALS['ServiceTypeID'],
        'hash' => $hash,
        'requestId' => $requestId,
        'totalAmount' => $amount,
        'mandateId' => $mandateId,
        'fundingAccount' => $GLOBALS['fundingAccount'],
        'fundingBankCode' => $GLOBALS['fundingBankCode']
    );
    // print_r(json_encode($postData));
    $response = callRemitaApiPost($GLOBALS['debitDirectProcessURL'], $postData);
    return json_decode(removeJSONP($response), TRUE);
}

//Direct Debit Status
function directDebitStatus($requestId, $mandateId) {
    //
    $hash = hash('sha512', $mandateId . $GLOBALS['merchantID']. $requestId . $GLOBALS['apiKey']);

     $mandateData = array(
        'merchantId' => $GLOBALS['merchantID'],
        'mandateId' => $mandateId,
        'hash' => $hash,
        'requestId' => $requestId);
        
    //echo json_encode($mandateData);
    $response = callRemitaApiPost($GLOBALS['DirectDebitStatusURL'], $mandateData);
    return json_decode(removeJSONP($response), TRUE);   
}

//Cancel Direct Debits
function cancelDirectDebit($requestId, $mandateId, $transaction_ref) {

    $hash = hash('sha512', $transaction_ref . $GLOBALS['merchantID'] . $requestId . $GLOBALS['apiKey']);
    $postData = array(
        'merchantId' => $GLOBALS['merchantID'],
        'mandateId' => $mandateId,
        'hash' => $hash,
        'transactionRef' => $transaction_ref,
        'requestId' => $requestId
    );

    $response = callRemitaApiPost($GLOBALS['cancelDebitDirectURL'], $postData);
    return json_decode(removeJSONP($response), TRUE);
}

//Mandate Transaction History
function mandateTransactionHistory($mandateId, $requestId) {

    $hash = hash('sha512', $mandateId . $GLOBALS['merchantID']. $requestId . $GLOBALS['apiKey']);

    $mandateData = array(
        'merchantId' => $GLOBALS['merchantID'],
        'mandateId' => $mandateId,
        'hash' => $hash,
        'requestId' => $requestId);

    //echo json_encode($mandateData);
    $response = callRemitaApiPost($GLOBALS['MandateTransactionHistoryURL'], $mandateData);
    return json_decode(removeJSONP($response), TRUE);
}

//Stop Mandate
function stopMandate($mandateId, $requestId) {

    $hash = hash('sha512', $mandateId . $GLOBALS['merchantID'] . $requestId . $GLOBALS['apiKey']);

    $postData = array(
        'merchantId' => $GLOBALS['merchantID'],
        'hash' => $hash,
        'mandateId' => $mandateId,
        'requestId' => $requestId
    );

    $response = callRemitaApiPost($GLOBALS['terminateMandateURL'], $postData);
    return json_decode(removeJSONP($response), TRUE);
}

//Get Mandate Status
function getMandateStatus($requestId) {

    $hash = hash('sha512', $requestId . $GLOBALS['apiKey'] . $GLOBALS['merchantID']);
    $url = $GLOBALS['baseRemitaURL'].'mandate/' . $GLOBALS['merchantID'] . "/" . $requestId . "/" . $hash . "/status.reg";
    $response = callRemitaApiGet($url);
    return json_decode(removeJSONP($response), TRUE);
}
