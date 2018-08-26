<?php
include_once('commands.php');
$merchantId = 27768931;
?>

<html>
<head>
<!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" />-->
<!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />-->
<!---->

    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" />
    <link href="assets/css/toastr.css" rel="stylesheet"/>
<!--     Material Design Bootstrap -->
    <link href="assets/css/mdb.min.css" rel="stylesheet">


</head>
<body>

<header>
    <!-- Navbar -->
    <nav class="navbar fixed-top navbar-expand-lg scrolling-navbar double-nav top-nav-collapse" style="background-color: #F5F5F5">

        <!--Navbar links-->
        <ul class="nav navbar-nav nav-flex-icons ml-auto">

            <li class="nav-item">
                <a type="button" class="btn btn-primary waves-effect waves-light" id="mandateStatus" onclick="mandateStatus()">Mandate Status</a>
            </li>
            <li class="nav-item">
                <a type="button" class="btn btn-primary waves-effect" data-toggle="modal" id="dStatus" data-target="#debitModal">Direct Debit</a>
            </li>
            <li class="nav-item">
                <a type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#debitStatusModal">Debit Status</a>
            </li>
            <li class="nav-item">
                <a type="button" class="btn btn-primary waves-effect"  data-toggle="modal" data-target="#mPaymentsModal">Mandate Payments</a>
            </li>
            <li class="nav-item">
                <a type="button" class="btn btn-danger waves-effect" id="cStatus" data-toggle="modal" data-target="#confirm-delete">Cancel Mandate</a>
            </li>
        </ul>
        <!--/Navbar links-->
    </nav>
    <!-- /.Navbar -->

</header>

<div class="main">
<div class="container-fluid">

    <div class="row" style="height:100%;">

        <div class="col-lg-6" style="background-color:#282828; text-align:center;">
            <h1 class="text-white" style="margin-top: 45%;">REMITA IMPLEMENTATION</h1>
            <h5 class="text-white">DIRECT DEBIT API TEST MODEL</h5>

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog cascading-modal" role="document">
                    <!--Content-->
                    <div class="modal-content">

                        <!--Header-->
                        <div class="modal-header light-blue darken-3 white-text">
                            <h4 class=""><i class="fa fa-newspaper-o"></i> Mandate Activation</h4>
                            <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--Body-->
                        <form id="OTPForm"  method="post">
                            <div class="modal-body mb-0">

                                <div class="md-form form-sm">
                                    <input id="cardNum" class="form-control form-control-sm" type="text" value="0441234567890" required>
                                    <label for="cardNum">Enter Last 4 Digits of your Card</label>
                                </div>

                                <div class="md-form form-sm">
                                    <input id="otp" class="form-control form-control-sm" type="password" value="1234" required>
                                    <label for="otp">Enter OTP</label>
                                </div>

                                <div class="text-center mt-1-half">
                                    <button class="btn btn-info mb-1 waves-effect waves-light">Activate Mandate <i class="fa fa-check ml-1"></i></button>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!--/.Content-->
                </div>
            </div><!-- /.modal -->

            <!-- Debit Modal -->
            <div class="modal fade" id="debitModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog cascading-modal" role="document">
                    <!--Content-->
                    <div class="modal-content">

                        <!--Header-->
                        <div class="modal-header light-blue darken-3 white-text">
                            <h4 class=""><i class="fa fa-newspaper-o"></i> Direct Debit</h4>
                            <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--Body-->
                        <form id="debitForm"  method="post">
                            <div class="modal-body mb-0">

                                <div class="md-form form-sm">
                                    <input id="debitAmt" class="form-control form-control-sm" type="text" value="1000" required>
                                    <label for="otp">Enter Amount</label>
                                </div>

                                <div class="text-center mt-1-half">
                                    <button class="btn btn-info mb-1 waves-effect waves-light">Debit Merchant <i class="fa fa-check ml-1"></i></button>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!--/.Content-->
                </div>
            </div><!-- /.modal -->

            <!-- Debit Modal -->
            <div class="modal fade" id="debitStatusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
                <div class="modal-dialog cascading-modal" role="document" style="max-width: 750px;">
                    <!--Content-->
                    <div class="modal-content">

                        <!--Header-->
                        <div class="modal-header light-blue darken-3 white-text">
                            <h4 class=""><i class="fa fa-newspaper-o"></i> Debit Status</h4>
                            <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--Body-->
                            <div class="modal-body mb-0">

                                <!--Table-->
                                <table class="table table-hover">

                                    <!--Table head-->
                                    <thead class="blue-grey lighten-4">
                                    <tr>
                                        <th>#</th>
                                        <th>Mandate Id</th>
                                        <th>Request Id</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Debit Date</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <!--Table head-->

                                    <!--Table body-->
                                    <tbody id="dStatusData">
                                    <?php
                                    $outputData = array();
                                    $outputData = ddStatus($merchantId);
                                    if($outputData['status'] === 1){
                                        $cnt = 1;
                                        for($j=0; $j<count($outputData['result']); $j++) {
                                            ?>
                                            <tr <?php echo 'id="row_'.$outputData["result"][$j]["id"].'"'; ?> >
                                            <th scope="row"><?php echo $cnt;  ?></th>
                                            <th> <?php echo $outputData["result"][$j]["mandateId"] ?></th>
                                            <td> <?php echo $outputData["result"][$j]["requestId"] ?> </td>
                                            <td> <?php echo $outputData["result"][$j]["amount"] ?> </td>
                                            <td> <?php echo $outputData["result"][$j]["status"] ?> </td>
                                            <td> <?php echo $outputData["result"][$j]["debitDate"] ?> </td>
                                            <td><button class="btn-danger" onclick="document.write('<?php echo cancelDD($outputData["result"][$j]["mandateId"], $outputData["result"][$j]["requestId"]); ?>');">Cancel Debit</button> </td>  </tr>'
                                    <?php
                                            $cnt++;
                                        }
                                    }else{
                                        ?>
                                        <tr>
                                            <td>Nil</td>
                                            <td>Nil</td>
                                            <td>Nil</td>
                                            <td>Nil</td>
                                            <td>Nil</td>
                                            <td>Nil</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>

                                    <!--Table body-->

                                </table>
                                <!--Table-->

                            </div>
                    </div>
                    <!--/.Content-->
                </div>
            </div><!-- /.modal -->

            <!-- Mandate History Modal -->
            <div class="modal fade" id="mPaymentsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
                <div class="modal-dialog cascading-modal" role="document" style="max-width: 900px;">
                    <!--Content-->
                    <div class="modal-content">

                        <!--Header-->
                        <div class="modal-header light-blue darken-3 white-text">
                            <h4 class=""><i class="fa fa-newspaper-o"></i> Mandate Transactions</h4>
                            <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--Body-->
                        <div class="modal-body mb-0">

                            <!--Table-->
                            <table class="table table-hover">

                                <!--Table head-->
                                <thead class="blue-grey lighten-4">
                                <tr>
                                    <th>#</th>
                                    <th>Mandate Id</th>
                                    <th>Request Id</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Debit Date</th>
                                </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody id="mStatusData">
                                <?php
                                $outputData2 = array();
                                $outputData2 = mandateTransactions($merchantId);
                                if($outputData2["status"] === 1 && isset($outputData2["result"]["data"])){
                                    $cnt = 1;
                                    for($j=0; $j<count($outputData2["result"]["data"]["data"]["paymentDetails"]); $j++) {
                                        ?>
                                        <tr >
                                            <th scope="row"><?php echo $cnt;  ?></th>
                                            <th> <?php echo $outputData2["result"]["mandateId"] ?></th>
                                            <td> <?php echo $outputData2["result"]["requestId"] ?> </td>
                                            <td> <?php echo $outputData2["result"]["data"]["data"]["paymentDetails"][$j]["amount"] ?> </td>
                                            <td> <?php echo $outputData2["result"]["data"]["data"]["paymentDetails"][$j]["status"] ?> </td>
                                            <td> <?php echo $outputData2["result"]["data"]["data"]["paymentDetails"][$j]["lastStatusUpdateTime"] ?> </td>                                         </tr>'
                                        <?php
                                        $cnt++;
                                    }
                                }else{
                                    ?>
                                    <tr>
                                        <td>Nil</td>
                                        <td>Nil</td>
                                        <td>Nil</td>
                                        <td>Nil</td>
                                        <td>Nil</td>
                                        <td>Nil</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>

                                <!--Table body-->

                            </table>
                            <!--Table-->

                        </div>
                    </div>
                    <!--/.Content-->
                </div>
            </div><!-- /.modal -->

            <!-- Mandate Status Modal -->
            <div class="modal fade" id="mStatusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
                <div class="modal-dialog cascading-modal" role="document" style="max-width: 900px;">
                    <!--Content-->
                    <div class="modal-content">

                        <!--Header-->
                        <div class="modal-header light-blue darken-3 white-text">
                            <h4 class=""><i class="fa fa-newspaper-o"></i> Mandate Status</h4>
                            <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--Body-->
                        <div class="modal-body mb-0" id="mStatModal">

                        </div>
                            <!--/.Card-->
                    </div>
                </div>
                    <!--/.Content-->
            </div>

            <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            CANCEL MANDATE
                        </div>
                        <div class="modal-body">
                            ARE YOU SURE
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">NO</button>
                            <a class="btn btn-danger btn-ok" onclick="cancelMD()">YES</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-6">
            <div class="text-center" style="margin-top: 43%;">
                <div id="mandateProcess" class="btn btn-info" onclick="setupMandate()">SETUP MANDATE</div>
                <div id="info_alert"></div>
            </div>
        </div>

    </div>
</div>
</div>


<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js" type="text/javascript" ></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/mdb.min.js" type="text/javascript" ></script>
<script src="assets/js/toastr.js" type="text/javascript"></script>
<script src="assets/js/script.js" type="text/javascript"></script>

<script>
    // $(document).ready(function () {
    //     $('#myModal').modal('hide');
    // });
    $('.btn').click(function() {
        $.ajax({
            url: "",
            context: document.body,
            success: function(s,x){

                $('html[manifest=saveappoffline.appcache]').attr('content', '');
                $(this).html(s);
            }
        });
    });
</script>

<div id="sidenav-overlay"></div>
</body>
</html>