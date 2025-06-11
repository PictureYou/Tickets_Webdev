<?php

include("dbconn.php");
$error = array('username'=>'', 'password'=>'');
$username = $password = '';
// validation
if(isset($_POST['submit'])){

    if(empty($_POST['username'])){
        $error['username'] = "Enter a username";
    }else{
        $username = $_POST["username"];
    }

    if(empty($_POST['password'])){
        $error['password'] = "Enter a password";
    }else{
        $password = $_POST["password"];
    }
    // Check username and password from db
    if(!array_filter($error)){
        $sql = "SELECT username, password, id FROM users WHERE username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)){
            $error['username'] = "Cannot find user";
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
        }
        
        mysqli_close($conn);    
        
        if($user === NULL){
            $error["username"] = "Username not found";
        }elseif (password_verify($password, $user['password'])){
            session_start();
            $_SESSION['id'] = $user['id'];
            header("location: booking.php");
        }else{
            $error['password'] = "password incorrect!";
        }
    }
}
ob_start();
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="login">
    <h4>Login</h4>
    <div class="labelinput">
        <div class="labelerror"><label for="username">Username:</label><span class="error"><?php echo $error['username'];?></span></div>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username)?>">
    </div>
    <div class="labelinput">
        <div class="labelerror"><label for="password">Password:</label><span class="error"><?php echo $error['password'];?></span></div>
        <input type="password" name="password">
    </div>
    <div class="labelinput">
        <input type="submit" name="submit" value="Login">
    </div>

    <p>Dont have an account? <a href="register.php">Register</a>.</p>

</form>
<?php
$content = ob_get_clean();
$title = "Login";
include 'templates/home_header.php';
?>