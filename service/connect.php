<?php 
//session_start();
error_reporting(E_ALL); 
date_default_timezone_set('Asia/Bangkok');

$conn= mysqli_connect("apps-odpc7.ddc.moph.go.th","chatchawan","Ch@tch@w@nPWD","medical_revenue");
mysqli_query($conn, "SET NAMES 'utf8' ");
