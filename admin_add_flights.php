<?php
include("dbconn.php");

$min_date = date('Y-m-d', strtotime('+1 days'));
$errors = array('destination'=>'','date'=>'','time'=>'');
// forms flight validation
if(isset($_POST['submit'])){
    if(empty($_POST['destination'])){
    $errors['destination'] = "Select a destination";
    }
    if(empty($_POST["date"])){
        $errors["date"] = "Set a date";
    }
    if(empty($_POST["time"])){
        $errors["time"] = "Set a time";
    }
    // Check if there are any errors
    if(!array_filter($errors)){
        // add flights to db table
        $destination = mysqli_real_escape_string($conn, $_POST['destination']);
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $cleantime = date("H:i:s", strtotime($_POST['time']));
        $time = mysqli_real_escape_string($conn, $cleantime);
        $economy = 100;
        $business = 30;
        $first = 10;
    
        $sql = "INSERT INTO flights(destination, date, time, economy_seats, business_seats, first_class_seats) VALUES (?,?,?,?,?,?)";
        $stmt = mysqli_stmt_init($conn);
    
        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "SQL error";
        } else {
            mysqli_stmt_bind_param($stmt,"sssiii", $destination, $date, $time, $economy, $business, $first);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: admin_view_flights.php?added=1");
            exit();
        }
    }
}
ob_start();
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<div class="labelinput">
    <div class="labelerror"><label for="destination">Destination Country:</label><span class="error"><?php echo $errors['destination'] ?></span></div>
    <select id="destination" name="destination">
        <option value="" disabled <?php if(!isset($_POST['destination'])) echo 'selected'; ?>>Select a destination</option>
        <?php
        $countries = ['Japan - Osaka','South korea - Seoul','USA - New York','Canada - Ottawa','Australia - Canberra','Germany - Berlin','France - Paris','United Kingdom - London','Italy - Rome','Thailand - Bankok'];
        sort($countries);
        foreach ($countries as $country) {
            $selected = (isset($_POST['destination']) && $_POST['destination'] == $country) ? 'selected' : '';
            echo "<option value='$country' $selected>" . ucwords(str_replace('_', ' ', $country)) . "</option>";
        }
        ?>
    </select>
</div>
<div class="labelinput">
    <div class="labelerror"><label for="date">Departure Date:</label><span class="error"><?php echo $errors['date'] ?></span></div>
    <input type="date" id="date" name="date" min="<?php echo $min_date; ?>" value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>">
</div>
<div class="labelinput">
    <div class="labelerror"><label for="time">Departure Time:</label><span class="error"><?php echo $errors['time'] ?></span></div>
    <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($_POST['time'] ?? ''); ?>">
</div>
<div class="labelinput">
    <input type="submit" value="Add Flight" name="submit">
</div>
</form>
<?php
$content = ob_get_clean();
$title = "Add Flights";
include 'templates/adminheader.php';
?>