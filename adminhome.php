<?php
include("dbconn.php");
$sort = "id";
$number = $number_of_tickets = $number_of_user = 0;
// Check sort
if (isset($_POST["submit_sort"])){
   $sort = $_POST['sort'];
}
// Validate sort input
$allowed_sort_columns = ['id', 'time_created DESC', 'username ASC'];
if (!in_array($sort, $allowed_sort_columns)) {
    $sort = 'id';
}
//prepare sort
$sql = "SELECT * FROM users ORDER BY $sort";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)){
    echo "SQL Statement Failed";
} else {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
}
// delete user
if (isset($_GET["delete_id"])){
    $delete_id = $_GET["delete_id"];
    $sqldelete = "DELETE FROM users WHERE id=?";
    $stmtdelete = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmtdelete, $sqldelete)){
        echo "Deletion Error";
    } else{
        mysqli_stmt_bind_param($stmtdelete,"i", $delete_id);
        mysqli_stmt_execute($stmtdelete);
        header("Location: " . $_SERVER['PHP_SELF'] . '?id=' . urlencode($id));
        exit(); 
    }
}

// Count total users
$user_count = 0;
$sql_count = "SELECT COUNT(*) as total FROM users";
$result_count = mysqli_query($conn, $sql_count);
if ($result_count) {
    $row = mysqli_fetch_assoc($result_count);
    $user_count = $row['total'];
    mysqli_free_result($result_count);
}

// Count total tickets
$ticket_count = 0;
$pending_total = 0;
$confirmed_total = 0;
$sql_ticket_count = "SELECT 
    COUNT(*) as total, 
    SUM(status='Pending...') as pending, 
    SUM(status='Confirmed') as confirmed 
    FROM bookings";
$result_ticket_count = mysqli_query($conn, $sql_ticket_count);
if ($result_ticket_count) {
    $row = mysqli_fetch_assoc($result_ticket_count);
    $ticket_count = $row['total'];
    $pending_total = $row['pending'] ?? 0;
    $confirmed_total = $row['confirmed'] ?? 0;
    mysqli_free_result($result_ticket_count);
}

ob_start();
?>
<h4>Overview:</h4>
<div class="admininfos">
    <p><strong>Total Users:</strong> <?php echo $user_count; ?></p>
</div>
<div class="admininfo">
    <div class="admininfos">
        <p><strong>Total Tickets:</strong> <?php echo $ticket_count; ?></p>
    </div>
    <div class="admininfos">
        <p><strong>Pending Tickets:</strong> <?php echo $pending_total; ?></p>
    </div>
    <div class="admininfos">
        <p><strong>Confirmed Tickets:</strong> <?php echo $confirmed_total; ?></p>
    </div>
</div>
</div>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" class="noclass">
    <div class="sorting">
        <h4>Users:</h4>
        <div>
            <label for="sort">Sort by:</label>
                <select id="sort" name="sort" >
                    <option value="id" <?php if ($sort == 'id') echo "selected"; ?>>User ID</option>
                    <option value="time_created DESC" <?php if ($sort == 'time_created DESC') echo "selected"; ?>>Most Recent</option>
                    <option value="username ASC" <?php if ($sort == 'username ASC') echo "selected"; ?>>Alphabethical</option>
                </select>
            <input type="submit" value="sort" name="submit_sort">
        </div>
    </div>
</form> 
<table>
    <th>ID</th>
    <th>Username</th>
    <th>No. of Tickets</th>
    <th>Pending</th>
    <th>Confirmed</th>
    <th>Actions</th>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id'];?></td>
            <td><?php echo $user['username'];?></td>
            <td>
                <?php
                    // Count all tickets for this user
                    $sql = "SELECT 
                                COUNT(*) as total, 
                                SUM(status='Pending...') as pending, 
                                SUM(status='Confirmed') as confirmed 
                            FROM bookings WHERE userid=?";
                    $stmt = mysqli_stmt_init($conn);

                    if (!mysqli_stmt_prepare($stmt, $sql)){
                        echo "Get user tickets failed";
                        $total = $pending = $confirmed = 0;
                    } else {
                        mysqli_stmt_bind_param($stmt,"i", $user['id']);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);
                        mysqli_free_result($result);
                        mysqli_stmt_close($stmt);

                        $total = $row['total'] ?? 0;
                        $pending = $row['pending'] ?? 0;
                        $confirmed = $row['confirmed'] ?? 0;

                        echo $total;
                    }
                ?>
            </td>
            <td>
                <?php
                    echo $pending;
                ?>
            </td>
            <td>
                <?php
                    echo $confirmed;
                ?>
            </td>
            <td>
                <a class="edit" href="adminedit.php?id=<?php echo $user['id'];?>">Edit</a> |
                <a class="delete" href="?delete_id=<?php echo urlencode($user['id']); ?>" 
                onclick="return confirm('Are you sure you want to delete this User?');" >Delete</a>
            </td>
        </tr>
    <?php endforeach ?>
</table>
<?php
$content = ob_get_clean();
$title = "Admin Dashboard";
include 'templates/adminheader.php';
?>