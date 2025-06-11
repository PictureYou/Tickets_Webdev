<?php
include("dbconn.php");
// get flight of user
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM flights where id=?";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "Error Fetching Data";
    } else {
        mysqli_stmt_bind_param($stmt,"s", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $flight_info = mysqli_fetch_assoc($result);
    }
}

if(isset($_POST["submit"])){
    $id = $_POST["hidden"];
    $destination = mysqli_real_escape_string($conn, $_POST["destination"]);
    $date = mysqli_real_escape_string($conn, $_POST["date"]);
    $time = mysqli_real_escape_string($conn, $_POST["time"]);
    $economy_seats= mysqli_real_escape_string($conn, $_POST["economy"]);
    $business_seats = mysqli_real_escape_string($conn, $_POST["business"]);
    $first_class_seats = mysqli_real_escape_string($conn, $_POST["first"]);
    $id = mysqli_real_escape_string($conn, $id);

    $sql = "UPDATE flights SET destination=?,date=?,time=?,economy_seats=?,business_seats=?,first_class_seats=? WHERE id=?";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL error";
    } else {
        mysqli_stmt_bind_param($stmt,"sssiiii", $destination, $date, $time, $economy_seats, $business_seats, $first_class_seats, $id);
        mysqli_stmt_execute($stmt);
        header("Location: admin_view_flights.php?flight_id=" . urlencode($id));
        exit();
    }
}
ob_start();
?>
<form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . urlencode($id)?>" method="POST">
    <div class="labelinput">
        <label for="destination">Destination Country:</label>
        <select id="destination" name="destination">
            <option value="" disabled <?php if(!isset($flight_info['destination'])) echo 'selected'; ?>>Select a destination</option>
            <?php
            $countries = ['Japan - Osaka','South korea - Seoul','USA - New York','Canada - Ottawa','Australia - Canberra','Germany - Berlin','France - Paris','United Kingdom - London','Italy - Rome','Thailand - Bankok'];
            sort($countries);
            foreach ($countries as $country) {
                $selected = (isset($flight_info['destination']) && $flight_info['destination'] == $country) ? 'selected' : '';
                echo "<option value='$country' $selected>" . ucwords(str_replace('_', ' ', $country)) . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="labelinput">
        <label for="date">Departure Date:</label>
        <input type="date" id="date" name="date" min="<?php echo $min_date; ?>" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($flight_info['date']))); ?>">
    </div>
    <div class="labelinput">
        <label for="time">Departure Time:</label>
        <input type="time" id="time" name="time" value="<?php echo htmlspecialchars(date('H:i', strtotime($flight_info['time']))); ?>">
    </div>
    <label>Seats:</label><br><br>
    <div class="groupform">
        <div class="labelinput groupforms">
            <label for="economy">Economy:</label>
            <input type="number" name="economy" max="100" min="0" value="<?php echo htmlspecialchars($flight_info['economy_seats'])?>">
        </div>
        <div class="labelinput groupforms">
            <label for="business">Business:</label>
            <input type="number" name="business" max="30" min="0" value="<?php echo htmlspecialchars($flight_info['business_seats']) ?>">
        </div>
        <div class="labelinput groupforms">
            <label for="first">First Class:</label>
            <input type="number" name="first" max="10" min="0" value="<?php echo htmlspecialchars($flight_info['first_class_seats']) ?>">
        </div>
    </div>
        <div class="labelinput">
            <input type="hidden" name="hidden" value="<?php echo $_GET['id'];?>">
            <input type="submit" name="submit" value="Update">
        </div>
</form>
<?php
$content = ob_get_clean();
$title = "Edit Flights";
include 'templates/adminheader.php';
?>