<?php

function makeConnection() {

    $DB_HostName = "localhost"; //$host;
    $DB_Name     = "remita"; //$database;
    $DB_User     = "root"; //$username;
    $DB_Pass     = ""; //$password;

    $_con = mysqli_connect($DB_HostName, $DB_User, $DB_Pass, $DB_Name);
    if (mysqli_connect_errno()) {
        error_log("Connect failed. Error:" . mysqli_connect_error() . " Code: " . mysqli_connect_errno());
    }

    return $_con;
}

function autoCommit($con, $value) {
    return mysqli_autocommit($con, $value);
}

function commit($con) {
    return mysqli_commit($con);
}

function rollback($con) {
    return mysqli_rollback($con);
}

function disconnectConnection($con) {
    mysqli_close($con);
}

?>