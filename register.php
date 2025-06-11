<?php
    include("dbconn.php");
    mysqli_report(MYSQLI_REPORT_OFF);

    $errors = array('fname'=>'', 'lname'=>'', 'email'=>'', 'username'=>'',
    'password'=>'','cpassword'=>'', 'bday'=>'', 'sex'=>'', 'phonenumber'=>'');
    $fname = $lname = $email = $username = $password = $bday = $sex = $phonenumber = '';
    // Register Validation
    if(isset($_POST['submit'])){
        if(empty($_POST['fname'])){
            $errors['fname'] = 'Required';
        } else {
            $fname = $_POST['fname'];
            if(!preg_match('/^[a-zA-Z ]{1,20}$/', $fname)){
                $errors['fname'] = "Numbers and special characters are not allowed.";
            }
        }

        if(empty($_POST['lname'])){
            $errors['lname'] = 'Required';
        } else {
            $lname = $_POST['lname'];
            if(!preg_match('/^[a-zA-Z]{1,20}$/', $lname)){
                $errors['lname'] = "Numbers and special characters are not allowed.";
            }
        }

        if(empty($_POST['email'])){
            $errors['email'] = 'Required';
        } else {
            $email = $_POST['email'];
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors['email'] = "Enter a be a valid email";
            }
        }
        
        if(empty($_POST['username'])){
            $errors['username'] = 'Required';
        } else {
            $username = $_POST['username'];
            if(!preg_match('/^[^\s]{5,20}$/', $username)){
                $errors['username'] = "Username must be 5-20 characters long and cannot contain spaces.";
            }
        }

        if(empty($_POST['password'])){
            $errors['password'] = 'Required';
        } else {
                $password = $_POST['password'];
                if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[^\s]{8,20}$/', $password)){
                    $errors['password'] = "Password must be 8-20 characters long has one capital letter, lowercase letter, number and special character.";
                }
        }

        if($_POST['password'] != $_POST['cpassword']){
                $errors['cpassword'] = "Passwords did not match";
            }

        if (empty($_POST['bday'])) {
            $errors['bday'] = 'Required';
        }else{
            $inputDate = new DateTime($bday = $_POST['bday']);
            $today = new DateTime();
            $ageLimit = $today->modify('-18 years');

            if ($inputDate <= $ageLimit) {
                $bday = $_POST['bday'];
            } else {
                $errors['bday'] = "Age must be 18+.";
            }
        }

        if(!empty($_POST['sex'])){
            $sex = $_POST['sex'];
        }else{
            $sex = 'NULL';
        }
        
        if(empty($_POST['phonenumber'])){
            $errors['phonenumber'] = 'Required';
        } else {
            $phonenumber = $_POST['phonenumber'];
            if(!preg_match('/^[0-9+]{10,15}$/', $phonenumber)){
                $errors['phonenumber'] = "Phone number must be 10-15 numbers long.";
            }
        }
        // Insert register into db
        if(array_filter($errors)){
            // Do nothing
        }else{
            $fname = mysqli_real_escape_string($conn, $_POST["fname"]);
            $lname = mysqli_real_escape_string($conn, $_POST["lname"]);
            $email = mysqli_real_escape_string($conn, $_POST["email"]);
            $username= mysqli_real_escape_string($conn, $_POST["username"]);
            $password = $_POST["password"];
            $hashedpass = password_hash($password, PASSWORD_DEFAULT);
            $bday = mysqli_real_escape_string($conn, $_POST["bday"]);
            $sex = mysqli_real_escape_string($conn, $_POST["sex"]);
            $phonenumber = mysqli_real_escape_string($conn, $_POST["phonenumber"]);

            $sql = "INSERT INTO users(fname,lname,email,username,password,birthday,sex,phonenumber) 
            VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)){ 
                echo "Insert Failed";
                
            } else {
                mysqli_stmt_bind_param($stmt,"ssssssss", $fname, $lname, $email, $username, $hashedpass, $bday, $sex, $phonenumber);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: login.php");
                } else {
                    if (mysqli_errno($conn) == 1062) {
                        $error_message = mysqli_error($conn);
                        if (strpos($error_message, 'email') !== false) {
                            $errors['email'] = "Email already exists.";
                        } elseif (strpos($error_message, 'username') !== false) {
                            $errors['username'] = "Username already exists.";
                        } else {
                            echo "Duplicate entry: " . $error_message;
                        }
                    }
                }
            }
        }
}
ob_start();
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="register">
    <h4>Register</h4>
    <div class="section">
        <div class="sections">
            <div class="labelinput">
                <div class="labelerror"><label for="fname">First Name</label><span class="error"><?php echo $errors['fname'];?></span></div>
                <input type="text" name="fname" value="<?php echo htmlspecialchars($fname)?>">
            </div>
            <div class="labelinput">
                <div class="labelerror"><label for="lname">Last Name</label><span class="error"><?php echo $errors['lname'];?></span></div>
                <input type="text" name="lname" value="<?php echo htmlspecialchars($lname)?>">
            </div>
            <div class="labelinput">
                <div class="labelerror"><label for="bday">Birthdate</label><span class="error"><?php echo $errors['bday'];?></span></div>
                <input type="date" name="bday" value="<?php echo htmlspecialchars($bday)?>">
            </div>
            <div class="labelinput">
                <div class="labelerror"><label for="phonenumber">Phone Number</label><span class="error"><?php echo $errors['phonenumber'];?></span></div>
                <input type="text" name="phonenumber" value="<?php echo htmlspecialchars($phonenumber)?>">
            </div>
            <div class="labelinput">
                <label>Sex (optional):</label>
                <div class="radio">
                    <div class="radios">
                        <input type="radio" id="male" name="sex" value="male">
                        <label for="male">Male</label>
                    </div>
                    <div class="radios">
                        <input type="radio" id="female" name="sex" value="female">
                        <label for="female">Female</label>
                    </div>
                    <div class="radios">
                        <input type="radio" id="other" name="sex" value="other">
                        <label for="other">Others</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="sections">
            <div class="labelinput">
                <div class="labelerror"><label for="Email">Email</label><span class="error"><?php echo $errors['email'];?></span></div>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email)?>">
            </div>
            <div class="labelinput">
                <div class="labelerror"><label for="username">Username:</label><span class="error"><?php echo $errors['username'];?></span></div>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username)?>">
            </div>
            <div class="labelinput">
                <div class="labelerror"><label for="password">Password:</label><span class="error"><?php echo $errors['password'];?></span></div>
                <input type="password" name="password">
            </div>
            <div class="labelinput">
                <div class="labelerror"><label for="cpassword">Re-enter Password:</label><span class="error"><?php echo $errors['cpassword'];?></span></div>
                <input type="password" name="cpassword">
            </div>
        </div>
    </div>
    <div class="labelinput">
        <input type="submit" name="submit" value="Submit">
    </div>
</form>
<?php
$content = ob_get_clean();
$title = "Register";
include 'templates/home_header.php';
?>