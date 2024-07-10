<?php
date_default_timezone_set('Africa/Cairo');
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'db_connection.php';

// Check if item_id is set
if (!isset($_GET['item_id'])) {
    echo "No item ID specified.";
    exit;
}

// Get item_id from URL
$item_id = $_GET['item_id'];

// Fetch item details from the database
$sql = "SELECT * FROM order_items WHERE item_id = $item_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
} else {
    echo "Item not found.";
    exit;
}

// Update item details in the database when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];

    $update_sql = "UPDATE order_items SET 
        item_name = '$item_name', 
        item_price = '$item_price', 
        quantity = '$quantity', 
        status = '$status' 
        WHERE item_id = $item_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "Item updated successfully.";
        header("Location: admin.php");
        exit;
    } else {
        echo "Error updating item: " . $conn->error;
    }
}

// Close database connection
$conn->close();
?>
<?php
// Fetch products and order details from the database
$products = []; // Fetch from database
$orderDetails = []; // Fetch from database
?>

<script>
let products = <?php echo json_encode($products); ?>;
let orderItems = <?php echo json_encode($orderDetails); ?>;
</script>

<form id="editOrderForm" action="update_order.php" method="POST">
    <?php foreach ($orderDetails as $key => $item): ?>
        <div>
            <select name="items[<?php echo $key; ?>][product_id]" onchange="updateProduct(<?php echo $key; ?>, this.value)">
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>" <?php echo ($product['id'] == $item['product_id']) ? 'selected' : ''; ?>>
                        <?php echo $product['name'] . ' - ' . $product['price'] . ' EGP'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="items[<?php echo $key; ?>][quantity]" value="<?php echo $item['quantity']; ?>" />
            <input type="hidden" name="items[<?php echo $key; ?>][price]" value="<?php echo $item['price']; ?>" />
            <span class="price-<?php echo $key; ?>"><?php echo $item['price'] . ' EGP'; ?></span>
            <button type="button" onclick="changeQuantity(<?php echo $key; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
            <span class="quantity-<?php echo $key; ?>"><?php echo $item['quantity']; ?></span>
            <button type="button" onclick="changeQuantity(<?php echo $key; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
        </div>
    <?php endforeach; ?>
    <button type="submit">Update Order</button>
</form>

<script>
function updateProduct(key, productId) {
    let selectedProduct = products.find(product => product.id == productId);
    if (selectedProduct) {
        orderItems[key].product_id = selectedProduct.id;
        orderItems[key].price = selectedProduct.price * orderItems[key].quantity;
        document.querySelector(`input[name="items[${key}][price]"]`).value = orderItems[key].price;
        reloadOrderForm();
    }
}

function changeQuantity(key, quantity) {
    if (quantity <= 0) {
        delete orderItems[key];
    } else {
        orderItems[key].quantity = quantity;
        let selectedProduct = products.find(product => product.id == orderItems[key].product_id);
        orderItems[key].price = quantity * selectedProduct.price;
    }
    reloadOrderForm();
}

function reloadOrderForm() {
    orderItems.forEach((item, key) => {
        document.querySelector(`input[name="items[${key}][quantity]"]`).value = item.quantity;
        document.querySelector(`input[name="items[${key}][price]"]`).value = item.price;
        document.querySelector(`select[name="items[${key}][product_id]"]`).value = item.product_id;
        document.querySelector(`span.price-${key}`).innerText = item.price + ' EGP';
        document.querySelector(`span.quantity-${key}`).innerText = item.quantity;
    });
}
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="number"], select {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .submit-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Item</h1>
        <form method="POST">
            <label for="item_name">Item Name:</label>
            <input type="text" id="item_name" name="item_name" value="<?php echo $item['item_name']; ?>" required>

            <label for="item_price">Item Price:</label>
            <input type="number" id="item_price" name="item_price" value="<?php echo $item['item_price']; ?>" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo $item['quantity']; ?>" required>
            <button type="submit" class="submit-button">Update Item</button>
        </form>
    </div>
</body>
</html>
