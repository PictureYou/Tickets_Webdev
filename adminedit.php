<?php 
include("dbconn.php");
$book_update = '';
//edit user
if (!isset($_GET['id'])) {
    header("Location: adminhome.php");
    exit();
}
// get user info
if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $sql = "SELECT * FROM users WHERE id=?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)){
     echo "SQL statement failed";
    } else {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt); 
        $infos = mysqli_fetch_assoc($result);

        mysqli_free_result($result); 
        mysqli_stmt_close($stmt);
    }
//delete booking
    if (isset($_GET['delete_id'])){
        $book_id = (int) $_GET['delete_id'];

        $sqlBook = "SELECT flight_id, class, passengers FROM bookings WHERE id=?";
        $stmtBook = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmtBook, $sqlBook)) {
            echo "SQL error";
        } else 
        mysqli_stmt_bind_param($stmtBook,"i", $book_id);
        mysqli_stmt_execute($stmtBook);
        $result = mysqli_stmt_get_result($stmtBook);
        $book_info = mysqli_fetch_assoc($result);
        
        $seat = '';
        if($book_info['class'] == 'economy'){
            $seat = 'economy_seats';
        } elseif ($book_info['class'] == 'business'){
            $seat = 'business_seats';
        } elseif ($book_info['class'] == 'first'){
            $seat = 'first_class_seats';
        }
        //delete book and update flight seats
        if($seat !== ''){
            $sql = "UPDATE flights SET $seat = $seat + ? WHERE id=?";
            $stmtEdit = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmtEdit, $sql)){
                echo "SQL error";
            } else {
                 mysqli_stmt_bind_param($stmtEdit, "ii", $book_info['passengers'], $book_info['flight_id']);
                mysqli_stmt_execute($stmtEdit);
            }            
            $sqlDelete = "DELETE FROM bookings WHERE id = ?";
            $stmtDelete = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmtDelete, $sqlDelete)) {
                mysqli_stmt_bind_param($stmtDelete, "i", $book_id);
                mysqli_stmt_execute($stmtDelete);
                mysqli_stmt_close($stmtDelete);
            }
            header("Location: " . $_SERVER['PHP_SELF'] . '?id=' . urlencode($id));
            exit(); 
        }else{
           echo "Deletion Error";
        }
    }
    // get booking info of user
    function getBookingsByUserId($conn, $userId) {
    $sql = "SELECT * FROM bookings WHERE userid = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL statement failed";
        return [];
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);

    return $bookings;
    }
    $bookings = getBookingsByUserId($conn, $id);
    // get all book info
    if(isset($_POST['book_id'])){
        $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);
        
        $sql = "SELECT flight_id,destination,date,time,class,adults,children,infants,price,status FROM bookings WHERE id=?";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "Book not found";
        }else{
            mysqli_stmt_bind_param($stmt, "i", $book_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $book_info = mysqli_fetch_assoc($result);
        }
        // update if infos are not the same
        $book_info['time'] = substr($book_info['time'], 0, 5);
        $temp_post = $_POST;
        unset($temp_post['book_id'], $temp_post['submit']);

        if($temp_post != $book_info){
            $destination = mysqli_real_escape_string($conn, $_POST['destination']);
            $date = mysqli_real_escape_string($conn, $_POST['date']);
            $time = mysqli_real_escape_string($conn, $_POST['time']);
            $class = mysqli_real_escape_string($conn, $_POST['class']);
            $adults = mysqli_real_escape_string($conn, $_POST['adults']);
            $children = mysqli_real_escape_string($conn, $_POST['children']);
            $infants = mysqli_real_escape_string($conn, $_POST['infants']);
            $price = mysqli_real_escape_string($conn, $_POST['price']);
            $status = mysqli_real_escape_string($conn, $_POST['stats']);

            $sql = "UPDATE bookings SET destination=?, date=? ,time=? ,class=?,
                                        adults=?, children=?, infants=?, price=?, status=? WHERE id=?";
            $stmt = mysqli_stmt_init($conn);
            
            if (!mysqli_stmt_prepare($stmt, $sql)){ 
                $book_update = "Update Failed";
            } else {
                mysqli_stmt_bind_param($stmt,"ssssiiiisi", $destination, $date, $time, $class, $adults, $children, $infants, $price, $status, $book_id);
                mysqli_stmt_execute($stmt);
                $bookings = getBookingsByUserId($conn, $id);
                // Redirect with updated_id
                header("Location: " . $_SERVER['PHP_SELF'] . '?id=' . urlencode($id) . '&updated_id=' . urlencode($book_id));
                exit();
            }
        }else{
            echo "Infos are the same.";
        }
    }
}
ob_start();
?>
<h3>User Information:</h3>
<div>
    <table>
        <th>ID</th><th>Username</th><th>First</th><th>Last</th><th>Birthday</th><th>Sex</th><th>Phonenumber</th><th>Time Created</th>
        <tr>
            <td><?php echo htmlspecialchars($infos['id'])?></td>
            <td><?php echo htmlspecialchars($infos['username'])?></td>
            <td><?php echo htmlspecialchars($infos['fname'])?></td>
            <td><?php echo htmlspecialchars($infos['lname'])?></td>
            <td><?php echo htmlspecialchars($infos['birthday'])?></td>
            <td><?php echo htmlspecialchars($infos['sex'])?></td>
            <td><?php echo htmlspecialchars($infos['phonenumber'])?></td>
            <td><?php echo htmlspecialchars($infos['time_created'])?></td>
        </tr>
    </table>

<h3>Users Bookings:</h3>

<table>
    <th>Flight ID</th><th>Time Booked</th><th>Destination</th><th>Departure<br>Schedule</th><th>Class</th><th>Passengers</th><th>Price</th><th>Status</th><th>Action</th>
    <?php foreach($bookings as $booking){ ?>
        <tr>
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . urlencode($id);?>" method="POST">
                <td>
                    <?php echo htmlspecialchars($booking['flight_id'])?>
                </td>
                <td>
                    <div style="color: black;"><?php $booking['time_created'] = substr($booking['time_created'], 0, 10);
                    echo htmlspecialchars($booking['time_created'])?></div>
                </td>
                <td>
                    <select id="destination" name="destination" class="table">
                        <option value="japan" <?php if($booking['destination'] == 'japan') echo 'selected';?>>Japan</option>
                        <option value="south_korea" <?php if($booking['destination'] == 'south_korea') echo 'selected';?>>South Korea</option>
                        <option value="usa" <?php if($booking['destination'] == 'usa') echo 'selected';?>>United States</option>
                        <option value="canada" <?php if($booking['destination'] == 'canada') echo 'selected';?>>Canada</option>
                        <option value="australia" <?php if($booking['destination'] == 'australia') echo 'selected';?>>Australia</option>
                        <option value="germany" <?php if($booking['destination'] == 'germany') echo 'selected';?>>Germany</option>
                        <option value="france" <?php if($booking['destination'] == 'france') echo 'selected';?>>France</option>
                        <option value="uk" <?php if($booking['destination'] == 'uk') echo 'selected';?>>United Kingdom</option>
                        <option value="italy" <?php if($booking['destination'] == 'italy') echo 'selected';?>>Italy</option>
                        <option value="thailand" <?php if($booking['destination'] == 'thailand') echo 'selected';?>>Thailand</option>
                    </select>
                </td>
                <td>
                    <input style="border: 1px, solid, black;" type="date" id="date" name="date" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['date']))); ?>" class="table"><br>
                    <input style="border: 1px, solid, black;" type="time" id="time" name="time" value="<?php echo htmlspecialchars(date('H:i', strtotime($booking['time']))); ?>" class="table">
                </td>
                <td>
                    <select id="class" name="class" class="table">
                        <option value="economy" <?php if($booking['class'] == 'economy') echo 'selected';?>>Economy</option>
                        <option value="business" <?php if($booking['class'] == 'business') echo 'selected';?>>Business</option>
                        <option value="first" <?php if($booking['class'] == 'first') echo 'selected';?>>First Class</option>    
                    </select>
                </td>
                <td>
                    <div class="passengertable">
                        <label class="table" for="adults">Adults (12+):</label>
                        <input type="number" id="adults" name="adults" min="1" value="<?php echo htmlspecialchars($booking['adults']);?>" class="table">
                    </div>
                    <div class="passengertable">
                        <label class="table" for="children">Children (2–11):</label>
                        <input type="number" id="children" name="children" min="0" value="<?php echo htmlspecialchars($booking['children']);?>" class="table">
                    </div>
                    <div class="passengertable">
                        <label class="table" for="infants">Infants (under 2):</label>
                        <input type="number" id="infants" name="infants" min="0" value="<?php echo htmlspecialchars($booking['infants']);?>" class="table">
                    </div>
                </td>
                <td>
                    <label class="table" for="price">Price:</label>
                    <input type="number" style="width: 100px;" id="price" name="price" min="0" value="<?php echo htmlspecialchars($booking['price']);?>" class="table">
                </td>
                <td>
                    <select name="stats" class="table">
                        <option value="Pending..." <?php if($booking['status'] == 'Pending...') echo 'selected';?>>Pending...</option>
                        <option value="Confirmed" <?php if($booking['status'] == 'Confirmed') echo 'selected';?>>Confirmed</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($booking['id'])?>">
                    <input type="submit" name="submit" value="Submit" class="table"> |
                    <a class="delete" href="?id=<?php echo urlencode($id); ?>&delete_id=<?php echo htmlspecialchars($booking['id']); ?>" 
                    onclick="return confirm('Are you sure you want to delete this flight?');">Delete</a><br>
                    <?php if (isset($_GET['updated_id']) && $_GET['updated_id'] == $booking['id']): ?>
                        <span style="color: green; font-weight: bold;">Updated</span>
                    <?php endif; ?>
                </td>
            </form>
        </tr>   
    <?php } ?>
</table>
<?php
$content = ob_get_clean();
$title = "Edit Users";
include 'templates/adminheader.php';
?>