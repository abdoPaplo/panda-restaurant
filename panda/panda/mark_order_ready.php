<?php
date_default_timezone_set('Africa/Cairo');
// Include database connection
include 'db_connection.php';

// Check if order_id is provided in the URL
if(isset($_GET['order_id']) && !empty($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Get the current time
    $ready_time = date('Y-m-d H:i:s');

    // Update the database to mark the order as ready and store the ready time
    $update_sql = "UPDATE order_items SET status = 'Ready', ready_time = ? WHERE order_id = ? AND status = 'Pending'";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $ready_time, $order_id);
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
        echo "Order marked as ready at: " . $ready_time;
    } else {
        echo "Failed to mark order as ready. It may already be marked as ready or not in pending state.";
    }

    // Close prepared statement
    $stmt->close();
} else {
    echo "Order ID not provided.";
}
?>
