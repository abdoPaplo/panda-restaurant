<?php
date_default_timezone_set('Africa/Cairo');
// Include database connection parameters
include 'db_connection.php';

// SQL query to delete all orders
$sql = "DELETE FROM order_items";

if ($conn->query($sql) === TRUE) {
    echo "All orders have been successfully cleared.";
} else {
    echo "Error clearing orders: " . $conn->error;
}

// Close database connection
$conn->close();
?>
