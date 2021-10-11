<?php 

// konekcija sa bazom
$con = mysqli_connect(hostname:'localhost', username:'root', password:'', database:'drustvena_mreza');

function escape($string) {
    global $con;
    return mysqli_real_escape_string($con, $string);
}

function query($query) {
    global $con;
    return mysqli_query($con, $query);
}

function confim($result) {
    global $con;
    if(!$result) {
        die("QUERY FAILED" . mysqli_error($con));
    }
}