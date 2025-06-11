<?php
include("dbconn.php");
// Initialize variables
$error= array('username'=> '', 'password'=> '');
// Validate forms
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
        $sql = "SELECT username, password, id FROM admin WHERE username=?";
        $stmt = mysqli_stmt_init($conn);
        
        if(!mysqli_stmt_prepare($stmt, $sql)){
            $error["username"] = "Invalid input";
        }else{
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
        }
        mysqli_close($conn);    

        if($user['password'] == $password){
            session_start();
            header('location: adminhome.php');
        }else{
            $error['username'] = "Wrong credentials";
        }
    }
}
ob_start();
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
    <div class="labelinput">
        <div class="labelerror"><label for="username">Username</label><span class="error"><?php echo htmlspecialchars($error['username'])?></span></div>
        <input type="text" name="username"><br>
    </div>
    <div class="labelinput">
        <div class="labelerror"><label for="password">Password</label><span class="error"><?php echo htmlspecialchars($error['password'])?></span></div>
        <input type="password" name="password"><br>
    </div>
    <input type="submit" name="submit" value="Sumbit">
</form>
<?php
$content = ob_get_clean();
$title = "Admin Login"; 
include 'templates/adminheader.php';
?>