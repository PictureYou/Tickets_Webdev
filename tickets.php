<?php
session_start();
include("dbconn.php");

$userid = $_SESSION['id'];
$sql = "SELECT * FROM bookings WHERE userid=? ORDER BY time_created DESC";
$stmt = mysqli_stmt_init($conn);
// Get User tickets from db
if (!mysqli_stmt_prepare($stmt, $sql)){
    echo "SQL statement failed";
} else {
    mysqli_stmt_bind_param($stmt, "s", $userid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);  
    $bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
}
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sqlDelete = "DELETE FROM bookings WHERE id=?";
    $stmtDelete = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmtDelete, $sqlDelete)) {
        echo "Deletion Error";
    } else {
        mysqli_stmt_bind_param($stmtDelete, "i", $delete_id);
        mysqli_stmt_execute($stmtDelete);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
mysqli_close($conn);
ob_start();
?>
<h4>My Tickets:</h4>
<div class="containertickets">
    <?php foreach($bookings as $booking): ?>
        <div class="ticket">
            <div class="ticketside1">
                <h6><?php echo htmlspecialchars($booking['destination']);?></h6>
                <h6><?php echo htmlspecialchars($booking['class']) . " Class "?> ticket</h6>
                <div class="desc">Flight Time: <?php $booking['time'] = date("h:i A", strtotime($booking['time'])); 
                                    echo htmlspecialchars($booking['date']) . " | " . htmlspecialchars($booking['time']);?>
                </div>
                <div>Passengers:</div>
                <div class="groupform2">
                    <div class="groupforms2"><?php echo "Adults: " . htmlspecialchars($booking['adults']);?></div>
                    <div class="groupforms2"><?php echo "Children: " . htmlspecialchars($booking['children']);?></div>
                    <div class="groupforms2"><?php echo "Infants: " . htmlspecialchars($booking['infants']);?></div>
                </div>
                <div class="desc price"><?php echo "₱" . number_format($booking['price'])?></div>
            </div>

            <div class="ticketside2" style="<?php if($booking['status'] == 'Pending...'){ echo 'backgound-color: background-color: orange;';} else echo 'background-color: green;'; ?>">
                <div class="ticketstatus"><?php echo htmlspecialchars($booking['status']);?></div>
                <?php if($booking['status'] == 'Pending...'){ ?>
                <a  href="?delete_id=<?php echo htmlspecialchars($booking['id']); ?>" 
                    onclick="return confirm('Are you sure you want to delete this ticket?');">Cancel</a>
                <?php }?>
            </div>
        </div>
    <?php endforeach; ?>   
</div>
<?php
$content = ob_get_clean();
$title = "Register";
include 'templates/home_header.php';
?>