<?php
include("dbconn.php");

// delete flight
if (isset($_GET['delete_id'])) {
    $flight_id = (int) $_GET['delete_id'];

    $sqlDelete = "DELETE FROM flights WHERE id = ?";
    $stmtDelete = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmtDelete, $sqlDelete)) {
        mysqli_stmt_bind_param($stmtDelete, "i", $flight_id);
        mysqli_stmt_execute($stmtDelete);
        mysqli_stmt_close($stmtDelete);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['flight_id'])) {
    $_SESSION['update'] = $_GET['flight_id'] . "#ID has been updated";
    header("Location: admin_view_flights.php?updated=1");
    exit();
}

if (isset($_GET['updated']) && isset($_SESSION['update'])) {
    $update = $_SESSION['update'];
    unset($_SESSION['update']); 
}

// Sorting logic
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$orderBy = "id ASC"; // default

if ($sort === "country") {
    $orderBy = "destination ASC";
} elseif ($sort === "date_oldest") {
    $orderBy = "date ASC";
} elseif ($sort === "date_newest") {
    $orderBy = "date DESC";
}

// get flight information from db
$sql = "SELECT * FROM flights ORDER BY $orderBy";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)){
    echo "Could not fetch flights";
} else {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $flights = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($conn);
}
ob_start();
?>
<form method="get" class="noclass">
<div class="sorting">
    <h4>Flights:</h4>
        <label for="sort" style="color: white;">Sort by:</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
            <option value="">Default</option>
            <option value="country" <?php if($sort==='country') echo 'selected'; ?>>Country</option>
            <option value="date_oldest" <?php if($sort==='date_oldest') echo 'selected'; ?>>Flight Date (Oldest)</option>
            <option value="date_newest" <?php if($sort==='date_newest') echo 'selected'; ?>>Flight Date (Newest)</option>
        </select>
        <noscript><input type="submit" value="Sort"></noscript>
    </div>
</form>
<table>
    <th>ID</th><th>Destination</th><th>Date</th><th>Time</th><th>Economy Seats</th><th>Business Seats</th><th>First Class Seats</th><th>Action</th>
    <?php foreach ($flights as $flight): ?>
        <tr>    
            <td><?php echo htmlspecialchars($flight['id'])?></td>   
            <td><?php echo htmlspecialchars($flight['destination'])?></td>
            <td><?php echo htmlspecialchars($flight['date'])?></td>
            <td><?php echo htmlspecialchars($flight['time'])?></td>
            <td><?php echo htmlspecialchars($flight['economy_seats'])?></td>
            <td><?php echo htmlspecialchars($flight['business_seats'])?></td>
            <td><?php echo htmlspecialchars($flight['first_class_seats'])?></td>
            <td><a class="edit" href="admin_edit_flights.php?id=<?php echo htmlspecialchars($flight['id']) ?>">Edit</a> | 
                <a class="delete" href="?delete_id=<?php echo htmlspecialchars($flight['id']) ?>" 
                onclick="return confirm('Are you sure you want to delete this flight?');">Delete</a>
            </td>
        </tr>
    <?php endforeach ?>
</table><br>
<?php
$content = ob_get_clean();
$title = "View Flights";
include 'templates/adminheader.php';
?>
