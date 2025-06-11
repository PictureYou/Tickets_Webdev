<?php
// Start sesion if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("dbconn.php");
// Define variables
$errors = array('destination'=> '', 'date'=>'', 'time'=> '', 'class'=> '',
                'adults'=> '', 'children'=> '', 'infants'=> '', 'passengers'=> '');
$min_date = date('Y-m-d', strtotime('+1 days'));
$countries = ['Japan - Osaka','South korea - Seoul','USA - New York','Canada - Ottawa','Australia - Canberra','Germany - Berlin','France - Paris','United Kingdom - London','Italy - Rome','Thailand - Bankok'];
sort($countries);
$default_times_by_country = [];

foreach ($countries as $i => $country) {
    $base_hour = 7 + $i;
    $am = str_pad($base_hour, 2, '0', STR_PAD_LEFT) . ':00:00';
    $pm = str_pad(($base_hour + 12) % 24, 2, '0', STR_PAD_LEFT) . ':00:00'; // 12 hours later

    $default_times_by_country[$country] = [$am, $pm];
}

// Handle booking submission
if (isset($_POST['submit'])) {

    if (!isset($_SESSION['id'])) {
        header("location: login.php");
    } else {   
        // Validation
    if (empty($_POST['destination'])) $errors['destination'] = "Choose a destination.";
    else $destination = $_POST["destination"];

    if (empty($_POST['date'])) $errors['date'] = "Choose a flight date.";
    else $date = $_POST["date"];

    if (empty($_POST['time'])) $errors['time'] = "Choose the time of flight.";
    else $time = $_POST["time"];
    
    if (empty($_POST['class'])) $errors['class'] = "Choose Flight Class.";
    else $class = $_POST["class"];

    if (empty($_POST["adults"])){
        $errors["adults"] = "At least 1 Adult per ticket.";
    } else {
        $adults = $_POST["adults"];
        $children = $_POST["children"];
        $infants = $_POST["infants"];
        $num_of_passengers = (int)$adults + (int)$children + (int)$infants;
    
        if ($num_of_passengers > 10){
            $errors['passengers'] = "Passengers must not exceed 10";
        }
        if ($infants > $adults+1){
            $errors['infants'] = "Infants can only exceed Adults by 1";
        }
    }
    

    if (array_filter($errors)) {
        echo "errors exists";
    } else {
         $seat_column_map = [
            'economy' => 'economy_seats',
            'business' => 'business_seats',
            'first' => 'first_class_seats'
        ];
        // Fetch user info
        $userid = mysqli_real_escape_string($conn, $_SESSION["id"]);
        $destination = mysqli_real_escape_string($conn, $_POST["destination"]);
        $date = mysqli_real_escape_string($conn, $_POST["date"]);
        $time = mysqli_real_escape_string($conn, $_POST["time"]);
        $class = mysqli_real_escape_string($conn, $_POST["class"]);
        $total_passengers = (int)$adults + (int)$children + (int)$infants;

        // Check if flight exists
        $flight_query = "SELECT * FROM flights WHERE destination = ? AND date = ? AND time = ?";
        $stmt = mysqli_stmt_init($conn);
        mysqli_stmt_prepare($stmt, $flight_query);
        mysqli_stmt_bind_param($stmt, "sss", $destination, $date, $time);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // After checking if flight exists
        if (mysqli_num_rows($result) > 0) {
            $flight = mysqli_fetch_assoc($result);
            $flight_id = $flight['id'];
            $available_seats = (int)$flight[$seat_column_map[$class]];

            if ($total_passengers > $available_seats) {
                $errors['passengers'] = "Not enough seats available in $class class. Only $available_seats left.";
            } else {
                // Update seats
                $new_seats = $available_seats - $total_passengers;
                $seat_column = $seat_column_map[$class];
                $update_query = "UPDATE flights SET `$seat_column` = ? WHERE id = ?";
                $update_stmt = mysqli_stmt_init($conn);
                mysqli_stmt_prepare($update_stmt, $update_query);
                mysqli_stmt_bind_param($update_stmt, "ii", $new_seats, $flight_id);
                mysqli_stmt_execute($update_stmt);
            }
        } else {
            // Flight doesn't exist, insert with default seats
            $default_seats = [
                'first' => 10,
                'business' => 30,
                'economy' => 100
            ];
            $new_seats = $default_seats[$class] - $total_passengers;
            if ($new_seats < 0) {
                $errors['passengers'] = "Not enough default seats available in $class class.";
            } else {
                $insert_flight_query = "INSERT INTO flights (destination, date, time, economy_seats, business_seats, first_class_seats)
                                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_flight = mysqli_stmt_init($conn);
                mysqli_stmt_prepare($stmt_flight, $insert_flight_query);

                $first = $class === 'first' ? $new_seats : $default_seats['first'];
                $business = $class === 'business' ? $new_seats : $default_seats['business'];
                $economy = $class === 'economy' ? $new_seats : $default_seats['economy'];

                mysqli_stmt_bind_param($stmt_flight, "sssiii", $destination, $date, $time, $economy, $business, $first);
                mysqli_stmt_execute($stmt_flight);
                $flight_id = mysqli_insert_id($conn);
            }
        }
        // Calculate price
        $base_prices = [
            'japan' => 3000,
            'south_korea' => 2500,
            'usa' => 7000,
            'canada' => 6500,
            'australia' => 5500,
            'germany' => 6000,
            'france' => 6200,
            'uk' => 6300,
            'italy' => 6100,
            'thailand' => 2000
        ];
        $base_price = $base_prices[$destination] ?? 300;
        $class_multiplier = ($class === 'business') ? 2 : (($class === 'first') ? 5 : 1);
        $price = $class_multiplier * (// Apply discounts
            (int)$adults * $base_price +
            (int)$children * $base_price * 0.5 +
            (int)$infants * $base_price * 0.1
        );
        // Insert booking info    
        if (array_filter($errors)) {
            echo "errors exist";
        } else {
            $status = "Pending...";
            $sql = "INSERT INTO bookings (userid, flight_id, destination, date, time, class, passengers, adults, children, infants, price, status) 
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
            $stmt = mysqli_stmt_init($conn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt,"isssssiiiiis",$userid, $flight_id, $destination, $date, $time, $class, $total_passengers, $adults, $children, $infants, $price, $status);
            mysqli_stmt_execute($stmt);
            header("Location: tickets.php");
            exit();
        }
    }
}
}

if (isset($_POST['ajax_date']) && isset($_POST['destination'])) {
    $date = $_POST['ajax_date'];
    $destination = $_POST['destination'];
    // Normalize Time Hour:time
    $times_map = []; 
    if (isset($default_times_by_country[$destination])) {
        foreach ($default_times_by_country[$destination] as $default_time) {
            $normalized = substr($default_time, 0, 5);
            if (!isset($times_map[$normalized])) {
                $times_map[$normalized] = $default_time;
            }
        }
    }

    // Fetch flight times from DB
    $sql = "SELECT time FROM flights WHERE date = ? AND destination = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $date, $destination);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $normalized = substr($row['time'], 0, 5);
            if (!isset($times_map[$normalized])) {
                $times_map[$normalized] = $row['time'];
            }
        }
    }
    // Extract unique times and sort
    $times = array_values($times_map);
    sort($times);

    echo json_encode($times);
    exit();
}
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking</title>
    <link rel="stylesheet" href="css/home_style.css">
</head>
<body>
    <form action="booking.php" method="POST" class="booking">
    <h4>Booking</h4>
    <div class="section">
        <div class="sections">
            <div class="labelinput">
                <label for="destination">Destination Country:</label><span class="error"><?php echo $errors['destination'] ?></span>
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
               <label for="date">Choose a date:</label><span class="error"><?php echo $errors['date'] ?></span>
               <input type="date" id="date" name="date" min="<?php echo $min_date; ?>" value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>">
            </div>
        
            <div class="labelinput">
                <label for="time">Choose a time:</label><span class="error"><?php echo $errors['time'] ?></span>
                <select id="time" name="time">
                    <option value="">Select a time</option>
                    <?php if (isset($_POST['time'])) {
                        $selected_time = $_POST['time'];
                        echo "<option value='$selected_time' selected>$selected_time</option>";
                    } ?>
                </select>
            </div>
        </div>
        
        <div class="sections passengersec">
            <div class="labelinput">
                <label for="class">Flight Class:</label><span class="error"><?php echo $errors['class'] ?></span>
                <select id="class" name="class">
                    <option value="" disabled <?php if(!isset($_POST['class'])) echo 'selected'; ?>>Select a class</option>
                    <option value="economy" <?php if(isset($_POST['class']) && $_POST['class'] == 'Economy') echo 'selected'; ?>>Economy</option>
                    <option value="business" <?php if(isset($_POST['class']) && $_POST['class'] == 'Business') echo 'selected'; ?>>Business</option>
                    <option value="first" <?php if(isset($_POST['class']) && $_POST['class'] == 'First') echo 'selected'; ?>>First Class</option>
                </select>
            </div>
            <div class="filler"> </div>
            <label>Passengers:</label><span class="error"><?php echo htmlspecialchars($errors['passengers']);?></span>
            <div class="groupform">
                <div class="labelinput groupforms">
                    <label for="adults">Adults:</label>
                    <input type="number" id="adults" name="adults" min="1" max="10" value="<?php echo htmlspecialchars($_POST['adults'] ?? '1'); ?>">
                </div>
                <div class="labelinput groupforms">
                    <label for="children">Children:</label>
                    <input type="number" id="children" name="children" min="0"max="9" value="<?php echo htmlspecialchars($_POST['children'] ?? '0'); ?>">
                </div>
                <div class="labelinput groupforms">
                    <label for="infants">Infants:</label><span class="error"><?php echo htmlspecialchars($errors['infants']);?></span>
                    <input type="number" id="infants" name="infants" min="0"max="5" value="<?php echo htmlspecialchars($_POST['infants'] ?? '0'); ?>">
                </div>
            </div>
        </div>
    </div>
    <p id="priceDisplay" class="price"></p>
    <div class="labelinput">
        <input type="submit" name="submit" value="Submit">
    </div>
</form>
<script>
    // fetch time and Calculate, display functions
function fetchTimes() {
    const date = document.getElementById('date').value;
    const destination = document.getElementById('destination').value;
    const timeSelect = document.getElementById('time');

    if (!date || !destination) return;

    timeSelect.innerHTML = '<option value="">Loading...</option>';

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "booking.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status == 200) {
            const times = JSON.parse(xhr.responseText);
            timeSelect.innerHTML = '<option value="">Select a time</option>';
            times.forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = formatTime12Hour(time);
                if (time === "<?php echo $_POST['time'] ?? ''; ?>") {
                    option.selected = true;
                }
                timeSelect.appendChild(option);
            });
        }
    };
    xhr.send("ajax_date=" + encodeURIComponent(date) + "&destination=" + encodeURIComponent(destination));
}

const basePrices = {
    japan: 3000,
    south_korea: 2500,
    usa: 7000,
    canada: 6500,
    australia: 5500,
    germany: 6000,
    france: 6200,
    uk: 6300,
    italy: 6100,
    thailand: 2000
};

function calculatePrice() {
    const destination = document.getElementById('destination').value;
    const travelClass = document.getElementById('class').value;
    const adults = parseInt(document.getElementById('adults').value || 0);
    const children = parseInt(document.getElementById('children').value || 0);
    const infants = parseInt(document.getElementById('infants').value || 0);

    if (!destination || !travelClass) {
        document.getElementById('priceDisplay').textContent = '';
        return;
    }

    let basePrice = basePrices[destination] || 300;
    let multiplier = 1;
    if (travelClass === 'business') multiplier = 2;
    else if (travelClass === 'first') multiplier = 5;

    const adultTotal = adults * basePrice;
    const childTotal = children * basePrice * 0.5;
    const infantTotal = infants * basePrice * 0.1;

    const total = (adultTotal + childTotal + infantTotal) * multiplier;

    document.getElementById('priceDisplay').textContent = `Estimated Total Price: ₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

document.getElementById('date').addEventListener('change', fetchTimes);
document.getElementById('destination').addEventListener('change', fetchTimes);

function formatTime12Hour(timeStr) {
    const [hour, minute] = timeStr.split(':');
    const hourNum = parseInt(hour, 10);
    const ampm = hourNum >= 12 ? 'PM' : 'AM';
    const hour12 = hourNum % 12 || 12;
    return `${hour12}:${minute} ${ampm}`;
}

window.addEventListener('load', function () {
    const destination = document.getElementById('destination').value;
    const date = document.getElementById('date').value;

    if (destination && date) {
        fetchTimes();
    }

['destination', 'class', 'adults', 'children', 'infants'].forEach(id => {
    document.getElementById(id).addEventListener('change', calculatePrice);
});

window.addEventListener('load', calculatePrice);
});
</script>
<?php
$content = ob_get_clean();
$title = "Booking";
include 'templates/home_header.php';
?>
