<?php

$conn = mysqli_connect("localhost","root","","ticketsdb");

if(!$conn){
    echo "connection error" . mysqli_connect_error();
}
?>