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
    <title>Waiter Dashboard</title>
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
            max-width: 1200px;
            width: 80%;
            margin: 0 auto;
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
            background-color: #e0e0e0;
            font-weight: bold;
        }

        tr.group-header td {
            text-align: center;
            font-size: 18px;
        }

        tr.group-header span {
            font-weight: bold;
        }

        tr.group-header button {
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        tr.group-header button.pending {
            background-color: grey;
            color: white;
        }

        tr.group-header button.ready {
            background-color: red;
            color: white;
        }

        tr.group-header button.served {
            background-color: green;
            color: white;
        }

        tr:not(.group-header):hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Waiter Dashboard</h1>
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
                <th>Ready Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Include database connection
            include 'db_connection.php';

            // Fetch data from the orders table
            $sql = "SELECT order_id, table_number, item_name, quantity, order_date, ready_time, status FROM order_items ORDER BY order_id";
            $result = $conn->query($sql);

            // Check if there are any records
            if ($result->num_rows > 0) {
                // Initialize current order ID variable
                $currentOrderId = null;
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    // If current order ID is different from previous row, add group header
                    if ($currentOrderId !== $row["order_id"]) {
                        // Close the previous group header row if it exists
                        if ($currentOrderId !== null) {
                            echo "</tr>";
                        }
                        echo "<tr class='group-header' data-order-id='" . $row["order_id"] . "'>";
                        echo "<td colspan='5'>Order ID: " . $row["order_id"] . " ";
                        $status_class = strtolower($row['status']);
                        echo "<span class='status " . $status_class . "' style='color: ";
                        if ($status_class == "pending") {
                            echo "grey"; // Pending status color
                        } elseif ($status_class == "ready") {
                            echo "red"; // Ready status color
                        } elseif ($status_class == "served") {
                            echo "green"; // Served status color
                        }
                        echo "'>" . $row['status'] . "</span></td>"; // Group header

                        echo "<td colspan='2'>";
                        switch (strtolower($row['status'])) {
                            case 'pending':
                                echo "<button class='pending' disabled>Waiting</button>";
                                break;
                            case 'ready':
                                echo "<button class='ready' onclick='markOrderServed(" . $row["order_id"] . ")'>Mark Served</button>";
                                break;
                            case 'served':
                                echo "<button class='served' disabled>Served</button>";
                                break;
                        }
                        echo "</td>"; // Button
                        echo "</tr>";
                        $currentOrderId = $row["order_id"];
                    }
                    // Output row data
                    echo "<tr>";
                    echo "<td>" . $row["table_number"] . "</td>"; // Table number column
                    echo "<td>" . $row["item_name"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "<td>" . $row["order_date"] . "</td>"; // Date column
                    echo "<td>" . $row["ready_time"] . "</td>"; // Ready Time column
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td></td>"; // Empty cell for spacing
                    echo "</tr>";
                }
                // Close the last group header row
                echo "</tr>";
            } else {
                echo "<tr><td colspan='7'>No orders found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function markOrderServed(orderId) {
        // Send an AJAX request to mark the order as served
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            location.reload();
            if (this.readyState == 4 && this.status == 200) {
                // Change button color to green
                var button = document.querySelector("button[data-order-id='" + orderId + "']");
                button.classList.add('served'); // Add served class
                button.disabled = true; // Disable the button after clicking
                // Update the status text
                var statusSpan = document.querySelector("tr.group-header[data-order-id='" + orderId + "'] .status");
                if (statusSpan) {
                    statusSpan.innerHTML = "Served";
                    statusSpan.classList.add('served'); // Add served class
                }
            }
        };
        xhttp.open("GET", "mark_order_served.php?order_id=" + orderId, true);
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
