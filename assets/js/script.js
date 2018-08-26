var outputData = new Array();

function currentDate() {
  var d = new Date();
  var n = d.getTime();
  return n;
}

//Mandate Setup and OTP Validation
function setupMandate() {

    $('#mandateProcess').html('Setting Up Mandate <i class="fa fa-spinner fa-pulse fa-fw"></i> ');

    $.ajax({
        url: "commands.php",
        data: {
            command: '1',
            timeStamp: requestTS()
        },
        type: "get",
        async: false,
        success: function (data) {
            toastr.options = {"timeOut": 0,  "extendedTImeout": 0, "progressBar": false, "closeButton": true}
            outputData = JSON.parse(data);
            if (outputData['status'] === 1) {

                // var tRef = document.getElementById('transRef');
                var tRef = outputData['result'];
                var mID = outputData['mandateId'];
                var rID = outputData['requestId'];
                if (outputData['status']['err'] === 1) {
                    toastr.error(outputData['error']);
                }else{
                    $('#myModal').modal('show');
                    $('body').on('submit', '#OTPForm', (function (event) {
                        event.preventDefault();
                        OTPForm(mID, rID, tRef);
                    }));
                }
                // toastr.success('OTP Request Sent');
            }else if(outputData['status'] === 0) {
                toastr.error(outputData['error']);
            }else if(outputData['status'] === -1) {
                toastr.error(remitaResponseMSG(outputData['error']));
            }else if(outputData['status'] === -2) {
                toastr.error(outputData['error']);
            } else {
                toastr.error('Error with your code');
            }
            $('#mandateProcess').html(' Setup Mandate <i class="fa fa-check "></i>');
        },
        error: function () {
            toastr.error('Error processing the request');
        }
    });
}

//OTP Validation
function OTPForm(mID, rID, transRef) {
    var cardNum = document.getElementById('cardNum').value;
    var otp = document.getElementById('otp').value;

    $.ajax({
        url: "commands.php",
        data: {
            command: '2',
            mandateId: mID,
            requestId: rID,
            transRef: transRef,
            timeStamp: requestTS(),
            card: cardNum,
            otp: otp
        },
        type: "get",
        async: false,
        success: function (data) {
            toastr.options = {"timeOut": 0,  "extendedTImeout": 0, "progressBar": false, "closeButton": true}
            outputData = JSON.parse(data);
            if (outputData['status'] === 1) {
                $('#myModal').modal('hide');
                $('#info_alert').append('<div class="alert alert-info"><p class="text-warning">MANDATE SETUP AND ACTIVATION COMPLETE</p></div>');
                toastr.success('Mandate Activated Successfully');
            }else if (outputData['status'] === 0) {

                $('#myModal').modal('hide');
                toastr.success('Mandate Activated But Unable To Update Database');
            }else {
                toastr.error(remitaResponseMSG(outputData['result']));
            }
        },
        error: function () {
            toastr.error('Error processing the request');
        }
    });
}

//Mandate Status Section
function mandateStatus(){
    $('#mandateStatus').html('Checking Mandate <i class="fa fa-spinner fa-pulse fa-fw"></i> ');

    $.ajax({
        url: "commands.php",
        data: {
            command: '3'
        },
        type: "get",
        async: false,
        success: function (data) {
            toastr.options = {"timeOut": 0,  "extendedTimeout": 0, "progressBar": false, "closeButton": true}

            outputData = JSON.parse(data);

            if (outputData['status'] === 1) {

                // var tRef = document.getElementById('transRef');
                var requestId = outputData['result']['requestId'];
                var mandateId = outputData['result']['mandateId'];
                var registrationDate = outputData['result']['registrationDate'];
                var activationDate = outputData['result']['activationDate'];
                var startDate = outputData['result']['startDate'];
                var endDate = outputData['result']['endDate'];
                var isActive = outputData['result']['isActive'];

                $('#mStatModal').html('<div class="row">' +
                    '<div class="col-lg-3 "> Mandate Id: <span class="btn btn-danger">'+ mandateId + '</span><br> Request Id: <span class="btn btn-danger">' + requestId +'</span></div> ' +
                    '<div class="col-lg-3"> Registration Date: <span class="btn btn-danger">'+ registrationDate + '</span><br> Activation Date: <span class="btn btn-danger">' + activationDate +'</span></div>' +
                    '<div class="col-lg-3"> Start Date: <span class="btn btn-danger">'+ startDate + '</span><br> End Date: <span class="btn btn-danger">' + endDate +'</span></div>' +
                    '<div class="col-lg-3"> Mandate Status: <span class="btn btn-danger">' + isActive +'</span></div>' +
                    ' </div>');
                $('#mStatusModal').modal('show');

            }else if(outputData['status'] === 0) {
                toastr.error('No Mandate Found');
            }else if(outputData['status'] === -1) {
                toastr.error('Mandate Not Yet Created on DB');
            } else {
                toastr.error('Unknown Error');
            }
            $('#mandateStatus').html(' Mandate Status <i class="fa fa-check"></i>');
        },
        error: function () {
            $('#mandateStatus').html(' Mandate Status ');
            toastr.error('Error processing the request');
        }
    });
}

//Mandate Payment History Section
function mandatePayments(){
    $('#mandatePayments').html('Checking Mandate <i class="fa fa-spinner fa-pulse fa-fw"></i> ');

    $.ajax({
        url: "commands.php",
        data: {
            command: '7'
        },
        type: "get",
        async: false,
        success: function (data) {
            toastr.options = {"timeOut": 0,  "extendedTImeout": 0, "progressBar": false, "closeButton": true}

            outputData = JSON.parse(data);

            if (outputData['status'] === 1) {

                if(outputData['result']['statuscode'] === '00'){
                    toastr.success(JSON.stringify(outputData['result']));
                    if(typeof(outputData['result']) !== "undefined" && outputData['result'] !== null) {
                        for (var j = 0; j< outputData['result']['data']['data']['paymentDetails'].length; j++){
                            $('#mStatusData').append('<tr id="row_'+ outputData["result"]['data']['data']['paymentDetails'][j]["id"] +'">' +
                                '<th scope="row">'+ (j + 1) +'</th>' +
                                '<th>'+ outputData["result"]["mandateId"] +'</th>' +
                                '<td>'+ outputData["result"]["requestId"] +'</td>' +
                                '<td>'+ outputData["result"]['data']['data']['paymentDetails'][j]["amount"] +'</td>' +
                                '<td>'+ outputData["result"]['data']['data']['paymentDetails'][j]["status"] +'</td>' +
                                '<td>'+ outputData["result"]['data']['data']['paymentDetails'][j]["lastStatusUpdateTime"] +'</td>' +
                                '</tr>');
                        }
                    }else {
                        $('#mStatusData').append('<tr>' +
                            '<th scope="row">Nil</th>' +
                            '<td>Nil</td>' +
                            '<td>Nil</td>' +
                            '<td>Nil</td>' +
                            '<td>Nil</td>' +
                            '<td>Nil</td>' +
                            '</tr>');
                    }

                    $('#mStatusModal').modal('show');
                }else{
                    toastr.error(remitaResponseMSG(outputData['result']['statuscode']));
                }
            }else if(outputData['status'] === 0) {
                toastr.error('No Mandate Found');
            }else if(outputData['status'] === -1) {
                toastr.error('Mandate Not Yet Created on DB');
            } else {
                toastr.error('Unknown Error');
            }
            $('#mandatePayments').html(' Mandate Payments <i class="fa fa-check"></i>');
        },
        error: function () {
            toastr.error('Error processing the request');
        }
    });
}

//Direct Debit Section
    $('body').on('submit', '#debitForm', (function (event) {
        event.preventDefault();
        $('#directDebit').html('Initializing Payment <i class="fa fa-spinner fa-pulse fa-fw"></i> ');
        var debitAmt = document.getElementById('debitAmt').value;

        $.ajax({
            url: "commands.php",
            data: {
                command: '4',
                debitAmt: debitAmt
            },
            type: "get",
            async: false,
            success: function (data) {
                toastr.options = {"timeOut": 0,  "extendedTImeout": 0, "progressBar": false, "closeButton": true}
                outputData = JSON.parse(data);
                if (outputData['status'] === 1) {

                    if(typeof(outputData['error']) !== "undefined" && outputData['error'] !== null) {
                        toastr.error(remitaResponseMSG(outputData['error']));
                    }
                    if(outputData['result']['statuscode'] === '069'){
                        toastr.success(JSON.stringify(outputData['result']));
                    }else{
                        toastr.error(remitaResponseMSG(outputData['result']['statuscode']));
                    }
                    $('#debitModal').modal('hide');

                }else if(outputData['status'] === 0) {
                    toastr.error(remitaResponseMSG(outputData['error']));
                }else if(outputData['status'] === -1) {
                    toastr.error(outputData['error']);
                }else {
                    toastr.error('Unknown Error');
                }
                $('#directDebit').html(' Direct Debit <i class="fa fa-check"></i>');
            },
            error: function () {
                toastr.error('Error processing the request');
            }
        });
    }));

function debitStatus(){
    $('#dStatus').html('Checking Status <i class="fa fa-spinner fa-pulse fa-fw"></i> ');

    $.ajax({
        url: "commands.php",
        data: {
            command: '5'
        },
        type: "get",
        async: false,
        cache: false,
        success: function (data) {
            toastr.options = {"timeOut": 0,  "extendedTImeout": 0, "progressBar": false, "closeButton": true}

            outputData = JSON.parse(data);
            if (outputData['status'] === 1) {
                if(typeof(outputData['error']) !== "undefined" && outputData['error'] !== null) {
                    for (var i = 0; i< outputData['error'].length; i++){
                        toastr.error('Error '+ (i+1) + ': ' + remitaResponseMSG(outputData['error'][i]));
                    }
                }
                // toastr.success(JSON.stringify(outputData['result']));

                if(typeof(outputData['result']) !== "undefined" && outputData['result'] !== null) {
                    for (var j = 0; j< outputData['result'].length; j++){
                        $('#dStatusData').append('<tr id="row_'+ outputData["result"][j]["id"] +'">' +
                            '<th scope="row">'+ (j + 1) +'</th>' +
                            '<th scope="row">'+ outputData["result"][j]["mandateId"] +'</th>' +
                            '<td>'+ outputData["result"][j]["requestId"] +'</td>' +
                            '<td>'+ outputData["result"][j]["amount"] +'</td>' +
                            '<td>'+ outputData["result"][j]["status"] +'</td>' +
                            '<td>'+ outputData["result"][j]["debitDate"] +'</td>' +
                            '</tr>');
                    }
                }else {
                    $('#dStatusData').append('<tr>' +
                        '<th scope="row">Nil</th>' +
                        '<td>Nil</td>' +
                        '<td>Nil</td>' +
                        '<td>Nil</td>' +
                        '<td>Nil</td>' +
                        '<td>Nil</td>' +
                        '</tr>');
                }

                $('#debitStatusModal').modal('show');
            }else if(outputData['status'] === 0) {
                toastr.error(outputData['error']);
            }else {
                toastr.error('Unexpected Error, Check code');
            }
            $('#dStatus').html(' Debit Status <i class="fa fa-check"></i>');
        },
        error: function () {
            toastr.error('Error processing the request');
        }
    });
}

function cancelMD(){
    $('#cStatus').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> ');

    $.ajax({
        url: "commands.php",
        data: {
            command: '8'
        },
        type: "get",
        async: false,
        cache: false,
        success: function (data) {
            toastr.options = {"timeOut": 0,  "extendedTImeout": 0, "progressBar": false, "closeButton": true}
            outputData = JSON.parse(data);
            if (outputData['status'] === 1) {
                toastr.success(outputData['result']);
            }else if(outputData['status'] === 0) {
                toastr.error(remitaResponseMSG(outputData['statuscode']));
            }else {
                toastr.error('Unexpected Error, Check code');
            }
            $('#confirm-delete').modal('hide');
            $('#cStatus').html(' Cancel Mandate <i class="fa fa-check"></i>');
        },
        error: function () {
            toastr.error('Error processing the request');
        }
    });
}

// Remita Errors
function remitaResponseMSG(responsecode){
    if(responsecode === '00'){
        return 'Transaction Completed Successfully';
    }else if(responsecode === '01'){
        return 'Transaction Approved';
    }else if(responsecode === '02'){
        return 'Transaction Failed';
    }else if(responsecode === '012'){
        return 'User Aborted Transaction';
    }else if(responsecode === '020'){
        return 'Invalid User Authentication';
    }else if(responsecode === '021'){
        return 'Transaction Pending';
    }else if(responsecode === '022'){
        return 'Invalid Request';
    }else if(responsecode === '023'){
        return 'Service Type or Merchant Does not Exist';
    }else if(responsecode === '025'){
        return 'Payment Reference Generated';
    }else if(responsecode === '029'){
        return 'Invalid Bank Code';
    }else if(responsecode === '030'){
        return ' Insufficient Balance';
    }else if(responsecode === '031'){
        return 'No Funding Account';
    }else if(responsecode === '032'){
        return 'Invalid Date Format';
    }else if(responsecode === '034'){
        return 'Invalid Funding Source';
    }else if(responsecode === '035'){
        return 'Payment Limit Exceeded';
    }else if(responsecode === '036'){
        return 'Duplicate Unique Reference';
    }else if(responsecode === '040'){
        return 'Initial Request OK';
    }else if(responsecode === '062'){
        return 'Mandate Not Due';
    }else if(responsecode === '075'){
        return 'Mandate is Already Active';
    }else if(responsecode === '074'){
        return 'No Available Record';
    }else if(responsecode === '076'){
        return 'Mandate Maximum Number of Times Exceeded';
    }else if(responsecode === '999'){
        return 'Unknown Error';
    }

    //Other Errors
    else if(responsecode === '1001'){
        return 'Table Query Error';
    }else {
        return 'Error should not exist';
    }
}
function requestTS() {
    var d = new Date();
    var dd = d.getDate();
    var mm = d.getMonth() + 1; //January is 0!
    var yyyy = d.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    var hours = d.getUTCHours();
    var minutes = d.getUTCMinutes();
    var seconds = d.getUTCSeconds();
    return (yyyy + '-' + mm + '-' + dd + 'T' + hours + ':' + minutes + ':' + seconds + '+000000');
}