<?php
date_default_timezone_set('Africa/Cairo');
// Include database connection
include 'db_connection.php';

// Check if order ID is provided in the GET parameters
if(isset($_GET['order_id'])) {
    // Sanitize the input to prevent SQL injection attacks
    $orderId = mysqli_real_escape_string($conn, $_GET['order_id']);

    // Get the current time
    $servedTime = date('Y-m-d H:i:s');

    // Update the database to mark the order as served and store the served time
    $sql = "UPDATE order_items SET status = 'Served', served_time = '$servedTime' WHERE order_id = '$orderId' AND status = 'Ready'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Order marked as served successfully at: " . $servedTime;
    } else {
        echo "Error marking order as served: " . $conn->error;
    }
} else {
    echo "Order ID is missing!";
}

// Close the database connection
$conn->close();
?>
