<?php

require_once('connect.php');
require_once('functions.php');
require_once('remita_Commands.php');
session_start();

if (isset($_GET['command'])) {

  //Setup Direct Debit Mandate
  if (strcmp($_GET['command'], "1") == 0) {
//      echo setupDDMandate($_GET['merchantId']);
      if (isset($_GET['pre'])) {
          echo '<pre>' . json_encode(setupDDMandate($_GET['timeStamp']), JSON_PRETTY_PRINT) . '</pre>';
      } else {
          echo json_encode(setupDDMandate($_GET['timeStamp']), JSON_PRETTY_PRINT);
      }
  }
    //Activate OTP
  if (strcmp($_GET['command'], "2") == 0) {

      if (isset($_GET['pre'])) {
          echo '<pre>' . json_encode(ActivateOTP($_GET['mandateId'], $_GET['requestId'],  $_GET['transRef'], $_GET['card'], $_GET['otp'], $_GET['timeStamp']), JSON_PRETTY_PRINT) . '</pre>';
      } else {
          echo json_encode(ActivateOTP($_GET['mandateId'], $_GET['requestId'], $_GET['transRef'], $_GET['card'], $_GET['otp'], $_GET['timeStamp']), JSON_PRETTY_PRINT);
      }
  }
    //Mandate Status
    if (strcmp($_GET['command'], "3") == 0) {
        if (isset($_GET['pre'])) {
            echo '<pre>' . json_encode(mStatus(), JSON_PRETTY_PRINT) . '</pre>';
        } else {
            echo json_encode(mStatus(), JSON_PRETTY_PRINT);
        }
    }
    //Initiate Direct Debit
  if (strcmp($_GET['command'], "4") == 0) {
      if (isset($_GET['pre'])) {
          echo '<pre>' . json_encode(doDDebit($_GET['debitAmt']), JSON_PRETTY_PRINT) . '</pre>';
      } else {
          echo json_encode(doDDebit($_GET['debitAmt']), JSON_PRETTY_PRINT);
      }
  }
    //Direct Debit Status
  if (strcmp($_GET['command'], "5") == 0) {
      if (isset($_GET['pre'])) {
          echo '<pre>' . json_encode(ddStatus(), JSON_PRETTY_PRINT) . '</pre>';
      } else {
          echo json_encode(ddStatus(), JSON_PRETTY_PRINT);
      }

  }
    //Cancel Direct Debit
  if (strcmp($_GET['command'], "6") == 0) {
      if (isset($_GET['pre'])) {
          echo '<pre>' . json_encode(cancelDD($_GET['requestId'], $_GET['mandateId']), JSON_PRETTY_PRINT) . '</pre>';
      } else {
          echo json_encode(cancelDD($_GET['requestId'], $_GET['mandateId']), JSON_PRETTY_PRINT);
      }
  }
    // Check Mandate Transactions
  if (strcmp($_GET['command'], "7") == 0) {
      if (isset($_GET['pre'])) {
          echo '<pre>' . json_encode(mandateTransactions(), JSON_PRETTY_PRINT) . '</pre>';
      } else {
          echo json_encode(mandateTransactions(), JSON_PRETTY_PRINT);
      }
  }
    //Stop Mandate
  if (strcmp($_GET['command'], "8") == 0) {
      if (isset($_GET['pre'])) {
          echo '<pre>' . json_encode(stopM(), JSON_PRETTY_PRINT) . '</pre>';
      } else {
          echo json_encode(stopM(), JSON_PRETTY_PRINT);
      }
  }


}

//-------FUNCTIONS--------

//1
//Setup Direct Debit Mandate
function setupDDMandate($requestTS){

    $responseData = array();
    $con = makeConnection();

    $urll = runSimpleFetchQuery($con, ['*'], "customers_tbl", ['id'], ['='], [1], "", "", "");

    if (count($urll) > 0) {
        $payerName = trim($urll['result'][0]['payerName']);
        $payerEmail = trim($urll['result'][0]['payerEmail']);
        $payerPhone = trim($urll['result'][0]['payerPhone']);
        $payerBankCode = trim($urll['result'][0]['payerBankCode']);
        $payerAccount = trim($urll['result'][0]['payerAccount']);
        $requestId = time();
        $amount = trim($urll['result'][0]['amount']);
        $startDate = trim($urll['result'][0]['startDate']);
        $endDate = trim($urll['result'][0]['endDate']);
        $mandateType = trim($urll['result'][0]['mandateType']);
        $maxNoOfDebits = trim($urll['result'][0]['maxNoOfDebits']);

        $isMandateSetup = runSimpleFetchQuery($con, ['*'], "mandate_tbl", ['customerId'], ['='], [1], "", "", "");
        if (count($isMandateSetup['result']) > 0) {

            if ($isMandateSetup['result'][0]['activationStatus'] == 1){
                $responseData['status'] = -1;
                $responseData['error'] = '075';
            }elseif($isMandateSetup['result'][0]['activationStatus'] == 0){
                $mOTP = RequestOTPforMandateActivation($isMandateSetup['result'][0]['mandateId'], $isMandateSetup['result'][0]['requestId'], $requestTS);
                if($mOTP['statuscode'] === '00'){
                    $update = runSimpleUpdateQuery($con, "mandate_tbl", ['remitaTransRef', 'amount'], [$mOTP['remitaTransRef'], $amount], ['customerId', 'mandateId', 'requestId'], ['=', '='], [1, "'" . $isMandateSetup['result'][0]['mandateId'] . "'", "'" . $isMandateSetup['result'][0]['requestId'] . "'"]);
                    if (count($update) > 0) {
                        $responseData['status'] = 1;
                        $responseData['result'] = $mOTP['remitaTransRef'];
                        $responseData['mandateId'] = $isMandateSetup['result'][0]['mandateId'];
                        $responseData['requestId'] = $isMandateSetup['result'][0]['requestId'];
                        $responseData['status']['err'] = 0;
                    } else {
                        $responseData['status'] = 1;
                        $responseData['result'] = $mOTP['remitaTransRef'];
                        $responseData['mandateId'] = $isMandateSetup['result'][0]['mandateId'];
                        $responseData['requestId'] = $isMandateSetup['result'][0]['requestId'];
                        $responseData['status']['err'] = 1;
                        $responseData['error'] = 'Mandate Activated but Table Unable to Update.';
                    }
                }else{
                    $responseData['status'] = -1;
                    $responseData['error'] = $mOTP['statuscode'];
                }

            } else {
                $responseData['status'] = 0;
                $responseData['error'] = 'Activation Status Error.';
            }
        }else {

//            'Samuel Akinseinde','ayomikun.sam@gmail.com','07062680280','030','0117455473','1517915621','5000','21/05/2018','21/08/2018','DD','3'
            $mSetup = setupDirectDebitMandate('Samuel Ayomikun','sam.sam@gmail.com','07062000000','030','0100000000', '1517915622','5000','21/05/2018','21/08/2018','DD','3');

            return $mSetup;
//            if ($mSetup['statuscode'] === '040') {
//
//                $mInsert = runSimpleInsertQuery($con, "mandate_tbl", ['`customerId`', '`mandateId`', '`requestId`'], [1, "'" . $mSetup['mandateId'] . "'", "'" . $mSetup['requestId'] . "'"]);
//                if (count($mInsert) > 0) {
//                    $mOTP = RequestOTPforMandateActivation($mSetup['mandateId'], $mSetup['requestId'], $requestTS);
//
//                    $update = runSimpleUpdateQuery($con, "mandate_tbl", ['remitaTransRef', 'amount'], [$mOTP['remitaTransRef'], $amount], ['customerId', 'mandateId', 'requestId'], ['=', '='], [1, "'" . $mSetup['mandateId'] . "'", "'" . $mSetup['requestId'] . "'"]);
//                    if (count($update) > 0) {
//                        $responseData['status'] = 1;
//                        $responseData['result'] = $mOTP['remitaTransRef'];
//                        $responseData['mandateId'] = $mSetup['mandateId'];
//                        $responseData['requestId'] = $mSetup['requestId'];
//                    } else {
//                        $responseData['status'] = 0;
//                        $responseData['error'] = 'Mandate Table Unable to Update.';
//                    }
//                } else{
//                    $responseData['status'] = 0;
//                    $responseData['error'] = 'Error inserting to mandate table.';
//                }
//            } else {
//                $responseData['status'] = -1;
//                $responseData['error'] = $mSetup['statuscode'];
//            }
        }
    } else {
        $responseData['status'] = -2;
        $responseData['error'] = 'Merchant Does Not Exit';
    }
    //alert investors.
    return $responseData;
    disconnectConnection($con);
}

//2
//Activate OTP
function ActivateOTP($mandateId, $requestId, $transRef, $card, $pin, $requestTS){
    $con = makeConnection();
    $responseData = array();

    $mValidate = ActivateOTPforMandateActivation($transRef, $card, $pin, $requestTS);
    $responseData['result'] = $mValidate['statuscode'];
    if($mValidate['statuscode'] === '00'){
        $mStat = getMandateStatus($requestId);
        $update = runSimpleUpdateQuery($con, "mandate_tbl", ['customerId', 'activationStatus', 'activationDate', 'startDate', 'endDate'], [1, 1, $mStat['activationDate'], $mStat['startDate'], $mStat['endDate']], ['mandateId','requestId'], ['=', '='], ["'".$mandateId."'", "'".$requestId."'"]);
        if(count($update)>0){
            $responseData['status'] = 1;
        } else{
            $responseData['status'] = 0;
        }
    }else{
        $responseData['status'] = -1;
    }
    return $responseData;
    disconnectConnection($con);
}

//3
//Mandate Status
function mStatus(){
    $responseData = array();
    $con = makeConnection();

        $isMandate = runSimpleFetchQuery($con, ['*'], "mandate_tbl", ['customerId'], ['='], [1], "", "", "");
        if(isset($isMandate) && count($isMandate['result']) > 0) {
            $requestId = $isMandate['result'][0]['requestId'];
            $mStat = getMandateStatus($requestId);
            if(isset($mStat) && count($mStat) > 0){
                $responseData['status'] = 1;
                $responseData['result'] = $mStat;
            }else{
                $responseData['status'] = 0;
            }
        }else{
            $responseData['status'] = -1;
        }
        return $responseData;
    disconnectConnection($con);

}

//4
//Initiate Direct Debit
function doDDebit($debitAmt){

    $responseData = array();
    $con = makeConnection();

    $urll = runSimpleFetchQuery($con, ['*'], "customers_tbl", ['id'], ['='], [1], "", "", "");

    if (count($urll) > 0) {
        $requestId = time();

        $isMandateSetup = runSimpleFetchQuery($con, ['*'], "mandate_tbl", ['customerId'], ['='], [1], "", "", "");
        if (count($isMandateSetup['result']) > 0) {
            $mandateId = $isMandateSetup['result'][0]['mandateId'];
            $amount = $isMandateSetup['result'][0]['amount'];

                $ddDebit = doDirectDebit($requestId, $mandateId, $debitAmt);
                $dDate = date('d/m/Y H:i:s', time());
                if ($ddDebit['statuscode'] == '069') {
                    $debitInsert = runSimpleInsertQuery($con, "transactions_tbl", ['`customerId`', '`mandateId`', '`requestId`', '`amount`', '`RRR`', '`transactionRef`', '`status`', '`debitDate`'], [1, "'" . $ddDebit['mandateId'] . "'", "'" . $ddDebit['requestId'] . "'", "'" . $debitAmt . "'", "'" . $ddDebit['RRR'] . "'", "'" . $ddDebit['transactionRef'] . "'", "'" . $ddDebit['status'] . "'", "'". $dDate ."'" ]);
                    if (!(count($debitInsert) > 0)) {
                        $responseData['error'] = '1001';
                    }
                }
                $responseData['status'] = 1;
                $responseData['result'] = $ddDebit;

        }else{
                $responseData['status'] = -1;
                $responseData['error'] = 'Mandate not found on DB';
        }
    }else{
            $responseData['status'] = -1;
            $responseData['error'] = 'Merchant not found on DB';
        }
        return $responseData;

}

//5
//Direct Debit Status
function ddStatus(){
    $responseData = array();
    $con = makeConnection();

    $isMandateSetup = runSimpleFetchQuery($con, ['*'], "mandate_tbl", ['customerId', 'activationStatus'], ['=','='], [1, 1], "", "", "");
    if (count($isMandateSetup['result']) > 0) {
        $mandateId = $isMandateSetup['result'][0]['mandateId'];
        $amount = $isMandateSetup['result'][0]['amount'];
        $isTrans = runSimpleFetchQuery($con, ['*'], "transactions_tbl", ['customerId', 'mandateId'], ['=', '='], [1, "'" . $mandateId . "'"], "", "", "");
        if (count($isTrans['result']) > 0) {
            //Update Transactions
            $responseData['status'] = 1;
            for ($n = 0; $n < count($isTrans['result']); $n++) {
                $thisTrans = $isTrans['result'][$n];
                $requestId = $thisTrans['requestId'];
                $ddStat = directDebitStatus($requestId, $mandateId);
                if ($ddStat['statuscode'] == '072') {
                    $updateStat = runSimpleUpdateQuery($con, "transactions_tbl", ['status', 'debitDate'], ["'" . $ddStat['status'] . "'", "'" . $ddStat['lastStatusUpdateTime'] . "'"], ['customerId', 'mandateId', 'requestId'], ['=', '='], [1, "'" . $mandateId . "'", "'" . $requestId . "'"]);
                } else {
                    $responseData['error'][$n] = $ddStat['statuscode'];
                }
            }
            //Fetch The New Updates
            $isTrans = runSimpleFetchQuery($con, ['*'], "transactions_tbl", ['customerId', 'mandateId'], ['=', '='], [1, "'" . $mandateId . "'"], "", "", "");
            for ($n = 0; $n < count($isTrans['result']); $n++) {
                $responseData['result'][$n] = $isTrans['result'][$n];
            }
        }else{
            $responseData['status'] = 0;
            $responseData['error'] = 'Transactions Not Found On DB';
        }
    }else{
        $responseData['status'] = 0;
        $responseData['error'] = 'Mandate Not Found On DB';
    }
    return $responseData;
}

//6
//Cancel Direct Debit
function cancelDD($mandateId, $requestId){
    $responseData = array();
    $con = makeConnection();

        $isMandate = runSimpleFetchQuery($con, ['*'], "transactions_tbl", ['customerId', 'mandateId', 'requestId'], ['=', '='], [1, "'" . $mandateId . "'", "'" . $requestId . "'"], "", "", "");
        if (count($isMandate['result']) > 0) {
            $transactionRef = $isMandate['result'][0]['transactionRef'];

            $sMandate = cancelDirectDebit($requestId, $mandateId, $transactionRef);
            if ($sMandate['statuscode'] === '02') {
                $responseData['status'] = 1;
                $responseData['result'] = $sMandate['status'];
                echo $sMandate['status'];
            } else {
                $responseData['status'] = 0;
            }
        }
    return $responseData;
}

//7
// Check Mandate Transactions
function mandateTransactions(){

    $responseData = array();
    $con = makeConnection();

    $isMandate = runSimpleFetchQuery($con, ['*'], "mandate_tbl", ['customerId'], ['='], [1], "", "", "");
    if(count($isMandate['result']) > 0) {
        $requestId = $isMandate['result'][0]['requestId'];
        $mandateId = $isMandate['result'][0]['mandateId'];
        $mStat = mandateTransactionHistory($mandateId, $requestId);
        if(count($mStat) > 0){
                $responseData['status'] = 1;
                $responseData['result'] = $mStat;
        }else{
            $responseData['status'] = 0;
        }
    }else{
        $responseData['status'] = -1;
    }
    return $responseData;
    disconnectConnection($con);

    return mandateTransactionHistory($requestId, $mandateId);
}

//8
//Stop Mandate
function stopM(){
    $responseData = array();
    $con = makeConnection();

    $isMandate = runSimpleFetchQuery($con, ['*'], "mandate_tbl", ['customerId','activationStatus'], ['=','='], [1, 1], "", "", "");
    if(count($isMandate['result']) > 0) {
        $requestId = $isMandate['result'][0]['requestId'];
        $mandateId = $isMandate['result'][0]['mandateId'];

        $sMandate = stopMandate($mandateId, $requestId);
        if($sMandate['statuscode'] === '00'){
            $responseData['status'] = 1;
            $responseData['result'] = $sMandate['status'];
        }else{
            $responseData['status'] = 0;
        }
    }
    return $responseData;

}

//10
//
//print_r(setupDDMandate('2018-05-21T14:11:43+000000'));\