<?php
date_default_timezone_set('Africa/Cairo');
session_start();

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Dashboard</title>
    <link rel="icon" href="./material/favicon.ico">
    <style>
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

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px; /* Maximum width of the container */
            width: 80%; /* Adjust as needed */
            margin: 0 auto; /* Center align the container */
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .data-table {
            overflow-x: auto;
            max-width: 100%; /* Ensure table does not overflow */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
            text-transform: uppercase;
        }

        tr.group-header {
            font-weight: bold;
        }

        tr.group-header.pending {
            background-color: #e0e0e0; /* Grey */
        }

        tr.group-header.ready {
            background-color: #d4edda; /* Light green */
        }

        tr.group-header.served {
            background-color: #d4edda; /* Light green */
        }

        tr.group-header td {
            text-align: center;
            font-size: 18px;
            padding: 12px 0;
        }

        tr.group-header button {
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .pending button {
            background-color: #ff6347; /* Red */
            color: #fff;
        }

        .ready button,
        .served button {
            background-color: #28a745; /* Dark green */
            color: #fff;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Kitchen Dashboard</h1>
    <div style="text-align: right; padding: 10px;">
        <a href="?action=logout" class="logout-btn">Logout</a>
    </div>
    <div class="data-table">
        <table>
            <thead>
            <tr>
                <th>Table number</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Include database connection
            include 'db_connection.php';

            // Fetch distinct order IDs from the order_items table
            $sql = "SELECT DISTINCT order_id, status FROM order_items ORDER BY order_id";
            $result = $conn->query($sql);

            // Check if there are any records
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    // Fetch order status for this order ID
                    $order_id = $row["order_id"];
                    $order_status = $row["status"];

                    // Determine group header class based on order status
                    $status_class = strtolower($order_status);

                    // Output group header with order ID and status
                    echo "<tr class='group-header " . $status_class . "'>";
                    echo "<td colspan='4'>Order ID: " . $order_id . " - Status: " . $order_status . "</td>"; // Group header
                    echo "<td>";
                    // Output button with dynamic class and text based on order status
                    if ($order_status == 'Pending') {
                        echo "<button class='pending' onclick='markOrderReady(" . $order_id . ")'>Mark as ready</button>";
                    } else {
                        echo "<button class='" . $status_class . "' disabled>Order " . $order_status . "</button>";
                    }
                    echo "</td>"; // Button
                    echo "</tr>";

                    // Fetch data for this order ID
                    $order_sql = "SELECT table_number, item_name, quantity, order_date FROM order_items WHERE order_id = $order_id";
                    $order_result = $conn->query($order_sql);

                    // Check if there are any records for this order ID
                    if ($order_result->num_rows > 0) {
                        // Output data of each row for this order ID
                        while ($order_row = $order_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $order_row["table_number"] . "</td>";
                            echo "<td>" . $order_row["item_name"] . "</td>";
                            echo "<td>" . $order_row["quantity"] . "</td>";
                            echo "<td>" . $order_row["order_date"] . "</td>";
                            echo "<td></td>"; // Empty cell for spacing
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No items found for this order</td></tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='5'>No orders found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function markOrderReady(orderId) {
        // Send AJAX request to mark the order as ready
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            location.reload();
            if (this.readyState == 4 && this.status == 200) {
                // Change button color to green
                var button = document.querySelector("button[data-order-id='" + orderId + "']");
                button.style.backgroundColor = "#4caf50"; // Green
                button.disabled = true; // Disable the button after clicking
            }
        };
        xhttp.open("GET", "mark_order_ready.php?order_id=" + orderId, true);
        xhttp.send();
    }

    function reloadPage() {
        location.reload();
    }

    // Call the reloadPage function after 5 seconds (5000 milliseconds)
    setTimeout(reloadPage, 5000);
</script>
</body>
</html>
