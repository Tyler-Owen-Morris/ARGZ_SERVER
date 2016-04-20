<?php 
//RegisterNewAccount.php
    include ("db_connect.php");

?>
<?php
if(isset($_POST['email'])) {
    $email = protect($_POST['email']);
    $password = protect($_POST['password']);
    
    //first use PHP tools to validate email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //then verify that the email isn't already registered
        $register1 = mysql_query("SELECT id FROM user_sheet WHERE email='$email'")or die(mysql_error());
        if (mysql_num_rows($register1) > 0) {
            die ("email address is already registered");
        }else{
            $insert = mysql_query("INSERT INTO user_sheet (email, password) VALUES ('$email', '".md5($password)."')")or die(mysql_error);
            echo "account successfully created";
        }
    
    } else {
        die("email not valid");
    }
}


// REMEMBER to turn OFF the HTML below here before testing with the Unity code

//this is where I'm storing the HTML form code while messing with the Unity form code.

    //</br></br></br>
    //<form action="register.php" method="POST"/></br>
    //email: <input type="text" name="email"/></br>
    //password: <input type="password" name="password"/></br>
    //<input type="submit" name="register" value="register"/>
    //</form>
?>

