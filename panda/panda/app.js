let openShopping = document.querySelector('.shopping');
let closeShopping = document.querySelector('.closeShopping');
let list = document.querySelector('.list');
let listCard = document.querySelector('.listCard');
let body = document.querySelector('body');
let total = document.querySelector('.total');
let quantity = document.querySelector('.quantity');

openShopping.addEventListener('click', ()=>{
    body.classList.add('active');
})
closeShopping.addEventListener('click', ()=>{
    body.classList.remove('active');
})

let products = [
    { id: 1, name: 'Chicken wings', image: '1.jpg', price: 180 },
    { id: 2, name: 'Sushi', image: '2.jpg', price: 240 },
    { id: 3, name: 'Salad', image: '3.jpg', price: 120 },
    { id: 4, name: 'Egg sushi', image: '4.jpg', price: 160 },
    { id: 5, name: 'Chocolate tablet', image: '5.jpg', price: 180 },
    { id: 6, name: 'Chicken Strips', image: '6.jpg', price: 180 },
    { id: 7, name: 'french fries', image: '7.jpg', price: 100 },
    { id: 8, name: 'Cheese croissant', image: '8.jpg', price: 100 },
    { id: 9, name: 'Sausage bites', image: '9.jpg', price: 120 },
    { id: 10, name: 'Potato bites', image: '10.jpg', price: 120 },
    { id: 11, name: 'Sushi bites', image: '11.jpg', price: 220 },
    { id: 12, name: 'blueberry cheese', image: '12.jpg', price: 160 },
    { id: 13, name: 'Chocolate cake', image: '13.jpg', price: 60 },
    { id: 14, name: 'Chocolate peanuts', image: '14.jpg', price: 50 },
    { id: 15, name: 'Strawberry cake', image: '15.jpg', price: 60 },
    { id: 16, name: 'Molten cake', image: '16.jpg', price: 80 },
    { id: 17, name: 'Donuts', image: '17.jpg', price: 30 },
    { id: 18, name: 'Vailla caramel', image: '18.jpg', price: 100 },
    { id: 19, name: 'Green burger', image: '19.jpg', price: 50 },
    { id: 20, name: 'Cookies', image: '20.jpg', price: 120 },
    { id: 21, name: 'Pancake', image: '21.jpg', price: 60 },
    { id: 22, name: 'icecream oreo', image: '22.jpg', price: 120 },
    { id: 23, name: 'Berry cake', image: '23.jpg', price: 90 },
    { id: 24, name: 'Vanilla cake', image: '24.jpg', price: 130 },
    { id: 25, name: 'Coffee', image: '25.jpg', price: 50 },
    { id: 26, name: 'Blueberry juice', image: '26.jpg', price: 120 },
    { id: 27, name: 'Milk with cantaloupe', image: '27.jpg', price: 130 },
    { id: 28, name: 'Tea', image: '28.jpg', price: 40 },
    { id: 29, name: 'Pepsi', image: '29.jpg', price: 50 },
    { id: 30, name: 'cocoa milk', image: '30.jpg', price: 120 },
    { id: 31, name: 'Orange', image: '31.jpg', price: 100 },
    { id: 32, name: 'Strawberry', image: '32.jpg', price: 120 },
    { id: 33, name: 'Mint', image: '33.jpg', price: 100 },
    { id: 34, name: 'Lemon', image: '34.jpg', price: 120 },
    { id: 35, name: 'Cocacola', image: '35.jpg', price: 50 },
    { id: 36, name: 'Pineapple', image: '36.jpg', price: 120 },
    { id: 37, name: 'Pasta and meat', image: '37.jpg', price: 220 },
    { id: 38, name: 'Mushroom pizza', image: '38.jpg', price: 240 },
    { id: 39, name: 'Margerita Pizza', image: '39.jpg', price: 180 },
    { id: 40, name: 'Chicken Pizza', image: '40.jpg', price: 220 },
    { id: 41, name: 'Goulash with Cheese', image: '41.jpg', price: 200 },
    { id: 42, name: 'Meat Loaf', image: '42.jpg', price: 210 },
    { id: 43, name: 'Chicken Soup', image: '43.jpg', price: 140 },
    { id: 44, name: 'Roasted Chicken', image: '44.jpg', price: 200 },
    { id: 45, name: 'Grilled Chicken', image: '45.jpg', price: 230 },
    { id: 46, name: 'Pasta and cheese', image: '46.jpg', price: 140 },
    { id: 47, name: 'Chicken With Vegetables', image: '47.jpg', price: 160 },
    { id: 48, name: 'Chicken salad', image: '48.jpg', price: 150 },
    { id: 49, name: 'Luncheon', image: '49.jpg', price: 120 },
    { id: 50, name: 'Chicken sandwich', image: '50.jpg', price: 180 },
    { id: 51, name: 'Mushroom sandwich', image: '51.jpg', price: 140 },
    { id: 52, name: 'Pastarmi', image: '52.jpg', price: 130 },
    { id: 53, name: 'Pastarmi with Cheese', image: '53.jpg', price: 160 },
    { id: 54, name: 'Mixed grill', image: '54.jpg', price: 300 },
    { id: 55, name: 'Pasta with meat', image: '55.jpg', price: 200 },
    { id: 56, name: 'Pasta with chicken', image: '56.jpg', price: 180 },
    { id: 57, name: 'Kofta', image: '57.jpg', price: 150 },
    { id: 58, name: 'Rosto', image: '58.jpg', price: 190 },
    { id: 59, name: 'Beefburger', image: '59.jpg', price: 140 },
    { id: 60, name: 'Cheese burger', image: '60.jpg', price: 160 },
];


function reloadCard(){
    listCard.innerHTML = '';
    let count = 0;
    let totalPrice = 0;
    listCards.forEach((value, key)=>{
        totalPrice = totalPrice + value.price;
        count = count + value.quantity;
        if(value != null){
            let newDiv = document.createElement('li');
            newDiv.innerHTML = `
                <div><img src="material/menu/${value.image}"/></div>
                <div>${value.name}</div>
                <div>${value.price.toLocaleString()}</div>
                <div>
                    <button onclick="changeQuantity(${key}, ${value.quantity - 1})">-</button>
                    <div class="count">${value.quantity}</div>
                    <button onclick="changeQuantity(${key}, ${value.quantity + 1})">+</button>
                </div>`;
                listCard.appendChild(newDiv);
        }
    })
    //Total price in cart
    total.innerText = totalPrice.toLocaleString() + " egp \nOrder";
    quantity.innerText = count;
}

let listCards  = [];


//add empty space before each card
function initApp() {
    const productsPerPage = 12; // Products per section
    const sections = [
        { image: 'Appetizers.jpg', startIndex: 0, spacesBefore: 0 },
        { image: 'Dessert.jpg', startIndex: 12, spacesBefore: 2 },
        { image: 'Drinks.jpg', startIndex: 24, spacesBefore: 2 },
        { image: 'maindishes.jpg', startIndex: 36, spacesBefore: 2 },
        { image: 'Sandwiches.jpg', startIndex: 48, spacesBefore: 2 }
    ];
    
    sections.forEach((section, index) => {
        // Add empty placeholders before the section
        for (let i = 0; i < section.spacesBefore; i++) {
            const emptyPlaceholder = document.createElement('div');
            emptyPlaceholder.classList.add('item', 'empty'); // Add 'empty' class for styling
            emptyPlaceholder.style.opacity = '0'; // Make the placeholder invisible 
            list.appendChild(emptyPlaceholder);
        }
    
        // Add unique image before each section
        const sectionImage = document.createElement('img');
        sectionImage.src = `material/${section.image}`;
    
        // Add a class based on the index of the section
        sectionImage.classList.add(`section-image-${index}`);
    
        list.appendChild(sectionImage);
    
        // Add products for the section
        for (let i = section.startIndex; i < section.startIndex + productsPerPage; i++) {
            if (i >= products.length) break; // Stop if all products are displayed
            const product = products[i];
    
            const newDiv = document.createElement('div');
            newDiv.classList.add('item');
            newDiv.innerHTML = `
                <img src="material/menu/${product.image}">
                <div class="title">${product.name}</div>
                <div class="price">${product.price.toLocaleString()}</div>
                <button onclick="addToCard(${i})">Add To Cart</button>`;
    
            list.appendChild(newDiv);
        }
    });
    
    
}








initApp();
function addToCard(key){
    if(listCards[key] == null){
        listCards[key] = JSON.parse(JSON.stringify(products[key]));
        listCards[key].quantity = 1;
    }
    reloadCard();
}

function changeQuantity(key, quantity){
    if(quantity == 0){
        delete listCards[key];
    }else{
        listCards[key].quantity = quantity;
        listCards[key].price = quantity * products[key].price;
    }
    reloadCard();
}



//send order to the database
function sendOrderToServer( customerName, tableNumber, totalPrice) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "save_order.php", true); 
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let order_id = xhr.responseText; // Get the new order ID
                // Display the order ID in the popup
                confirmCheckout(order_id);
            } else {
                console.error("Error placing order:", xhr.responseText);
            }
        }
    };
    let data = {
        total_price: totalPrice,
        order_items: listCards.filter(item => item !== null),
        customerName: customerName,
        tableNumber: tableNumber
    };
    let jsonData = JSON.stringify(data);
    xhr.send(jsonData);
}



// Add event listener to the total price
document.getElementById('totalPrice').addEventListener('click', function() {
    let totalPrice = parseFloat(total.innerText.replace(',', '')); // Get total price
    let orderItems = listCards.filter(item => item !== null); // Filter out null items
    let overlay = document.getElementById('overlay');
    showForm(totalPrice, orderItems); // Pass orderItems to the showForm function
});

// Function to show the confirmation popup with order ID
function confirmCheckout(order_id) {
    // Create a styled popup
    let popup = document.createElement('div');
    popup.className = 'popup';
    popup.innerHTML = `<p>Your order (ID: ${order_id}) has been placed successfully!</p>`;

    // Create an OK button
    let okButton = document.createElement('button');
    okButton.textContent = 'OK';
    okButton.className = 'ok-button'; // Apply a class for styling
    okButton.addEventListener('click', function() {
        document.body.removeChild(popup); // Remove the popup when clicked
    });
    popup.appendChild(okButton); // Append the OK button to the popup

    document.body.appendChild(popup);
    // Center the popup
    popup.style.top = '50%';
    popup.style.left = '50%';
    popup.style.transform = 'translate(-50%, -50%)';
}

// Function to show the form
function showForm(totalPrice, orderItems) {
    // Create and append the form
    let form = document.createElement('form');
    form.id = 'checkoutForm';
    form.innerHTML = `
        <label for="customerName">Customer Name:</label>
        <input type="text" id="customerName" name="customerName" required>
        <label for="tableNumber">Table Number:</label>
        <input type="text" id="tableNumber" name="tableNumber" required>
        <button type="submit">Submit</button>
        <button type="button" id="cancelButton">Cancel</button>
    `;

    // Append form to body
    document.body.appendChild(form);

    // Prevent scrolling
    document.body.style.overflow = 'hidden';

    // Add event listener for form submission
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        let formData = new FormData(form);
        let customerName = formData.get('customerName');
        let tableNumber = formData.get('tableNumber');

        // Process the form data (e.g., send to server)
        console.log("Customer Name:", customerName);
        console.log("Table Number:", tableNumber);

        // Remove the form
        document.body.removeChild(form);
        document.body.style.overflow = 'auto'; // Restore scrolling

        // Send order to server using XMLHttpRequest (XHR)
        sendOrderToServer(customerName, tableNumber, totalPrice, products, orderItems);
    });

    // Add event listener for the cancel button
    form.querySelector('#cancelButton').addEventListener('click', function() {
        document.body.removeChild(form); // Remove the form when cancel is clicked
        document.body.style.overflow = 'auto'; // Restore scrolling
    });
}


// Event listener for the cancel button
document.getElementById('cancelButton').addEventListener('click', function() {
    document.getElementById('overlay').style.display = 'none'; // Hide the overlay
    // Do nothing or provide feedback to the user
});


