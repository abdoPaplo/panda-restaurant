<?php
session_start();
date_default_timezone_set('Africa/Cairo');
// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if logout action is requested
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page after logout
    header("Location: login.php");
    exit;
}

// Include database connection
include 'db_connection.php';

// Fetch all months with orders from the database
$sql = "SELECT DISTINCT MONTH(order_date) AS month FROM order_items ORDER BY month";
$result = $conn->query($sql);
$months = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $months[] = [
            'number' => $row['month'],
            'name' => date("F", mktime(0, 0, 0, $row['month'], 1))
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="./material/favicon.ico">
    <header>
    <div style="text-align: right; padding: 10px;">
        <a href="?action=logout" class="logout-btn">Logout</a>
    </div>
    </header>

    <style>
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .data-table table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #ddd;
        }

        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .data-table th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .data-table tr:hover {
            background-color: #f1f1f1;
        }

        .edit-button, .generate-button, .clear-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 2px 10px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 4px;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .clear-button {
            background-color: #f44336; /* Red color */
        }

        .edit-button:hover, .generate-button:hover, .clear-button:hover {
            background-color: #45a049; /* Darker green */
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .data-table {
            overflow-x: auto;
        }

        .group-header {
            font-size: 18px;
        }

        .group-header .highlight {
            color: #fff;
            background-color: #ADD8E6;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .select-month {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <a href="generate_expenses.php?type=current_month" class="generate-button">Generate Current Month Expenses</a>
        <a href="generate_expenses.php?type=current_year" class="generate-button">Generate Current Year Expenses</a>
        <a href="generate_expenses.php?type=last_year" class="generate-button">Generate Last Year Expenses</a>

        <!-- Dropdown menu to select month -->
        <form action="#" method="GET" class="select-month">
            <label for="select-month">Select Month:</label>
            <select id="select-month" name="month">
                <option value="">-- Select Month --</option>
                <?php foreach ($months as $month): ?>
                    <option value="<?php echo $month['number']; ?>"><?php echo $month['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Show Orders" class="generate-button">
        </form>

        <button onclick="clearOrders()" class="clear-button">Clear All Orders</button>

        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Item Price</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Date</th>
                        <th>Ready Time</th>
                        <th>Served Time</th>
                        <th>Status</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                <?php
                // Fetch data based on selected month or show all if no specific month is selected
                $sql = "SELECT * FROM order_items";
                if (isset($_GET['month']) && $_GET['month'] !== '') {
                    $selectedMonth = $_GET['month'];
                    $sql .= " WHERE MONTH(order_date) = '$selectedMonth'";
                }
                $sql .= " ORDER BY order_id";

                $result = $conn->query($sql);

                // Check if there are any records
                if ($result->num_rows > 0) {
                    // Initialize current order ID and table number variables
                    $currentOrderId = null;
                    $currentTableNumber = null;
                    $currentCustomerName = null;
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        // If current order ID is different from previous row, add group header
                        if ($currentOrderId !== $row["order_id"]) {
                            // Fetch and store the customer name for the group header
                            $customerNameSql = "SELECT customer_name FROM order_items WHERE order_id = " . $row["order_id"] . " LIMIT 1";
                            $customerNameResult = $conn->query($customerNameSql);
                            if ($customerNameResult->num_rows > 0) {
                                $customerNameRow = $customerNameResult->fetch_assoc();
                                $currentCustomerName = $customerNameRow["customer_name"];
                            }
                            // Output the group header with order ID, table number, and customer name
                            echo "<tr class='group-header'><td class='highlight' colspan='9'>Order ID: " . $row["order_id"] . " - Table Number: " . $row["table_number"] . " - Customer Name: " . $currentCustomerName . "</td></tr>"; // Group header
                            $currentOrderId = $row["order_id"];
                            $currentTableNumber = $row["table_number"];
                        }
                            // Output row data
                            echo "<tr>";
                            echo "<td>" . $row["item_name"] . "</td>";
                            echo "<td>" . $row["item_price"] . "</td>";
                            echo "<td>" . $row["quantity"] . "</td>";
                            echo "<td>" . $row["total_price"] . "</td>";
                            echo "<td>" . $row["order_date"] . "</td>"; 
                            echo "<td>" . $row["ready_time"] . "</td>"; // Display Ready Time
                            echo "<td>" . $row["served_time"] . "</td>"; // Display Served Time
                            echo "<td>" . $row["status"] . "</td>"; // Status column
                            // Add an edit button for each row
                            echo "<td><button class='edit-button' onclick='window.location.href=\"edit_order.php?item_id=" . $row["item_id"] . "\"'>Edit</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    function clearOrders() {
        if (confirm("Are you sure you want to clear all orders?")) {
            // Redirect to the clear_orders.php script
            window.location.href = "clear_orders.php";
        }
    }
    </script>
</body>
</html>
