<?php
date_default_timezone_set('Africa/Cairo');
// Database connection parameters
$servername = "localhost";
$username = "admin"; // Your MySQL username
$password = "admin123"; // Your MySQL password
$dbname = "orders"; // Your database name

// Receive data sent by JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// Extract form data
$totalPrice = $data['total_price'];
$orderItems = $data['order_items'];
$customerName = $data['customerName'];
$tableNumber = $data['tableNumber']; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the SQL statement to insert the order
$sql = "INSERT INTO checkout (total_price, customer_name, table_number) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dss", $totalPrice, $customerName, $tableNumber);
$stmt->execute();

// Get the ID of the inserted order
$orderId = $conn->insert_id;

// Insert order items into order_items table
foreach ($orderItems as $item) {
    $itemId = $item['id'];
    $itemName = $item['name'];
    $itemPrice = $item['price'];
    $quantity = $item['quantity'];

    $sql = "INSERT INTO order_items (order_id, item_id, item_name, item_price, quantity, total_price, customer_name, table_number) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisdiiss", $orderId, $itemId, $itemName, $itemPrice, $quantity, $totalPrice, $customerName, $tableNumber);
    $stmt->execute();
}

// Close the database connection
$stmt->close();
$conn->close();

// Return the newly generated order ID
echo $orderId;
?>
