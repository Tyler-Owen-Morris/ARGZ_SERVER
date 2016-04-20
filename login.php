<?php
session_start();
include("db_connect.php");

if(isset($_POST['email'])) {
    if(isset($_POST['password'])) {
        $email = protect($_POST['email']);
        $password = protect($_POST['password']);
        
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $login_check = mysql_query("SELECT id FROM user_sheet WHERE email = '$email' AND password = '".md5($password)."'") or die(mysql_error);
            if (mysql_num_rows($login_check) == 0) {
                echo "Incorrect email or password";
            } else {
                $get_id = mysql_fetch_assoc($login_check);
                $_SESSION['uid'] = $get_id['id'];
                echo "successfully logged in";
            }
        } else {
            echo "email not valid";
        }
    }else {
        echo "no password sent";
    }
} else {
    echo "No email submitted";
}

?>