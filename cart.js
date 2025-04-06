document.addEventListener("DOMContentLoaded", function () {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let cartContainer = document.getElementById("cart-items");
    let totalPriceElement = document.getElementById("totalPrice");

    function renderCart() {
        cartContainer.innerHTML = "";
        let totalPrice = 0;

        if (cart.length === 0) {
            cartContainer.innerHTML = "<p>Cart is empty</p>";
            totalPriceElement.innerText = "0";
            localStorage.removeItem("cart"); // Clear localStorage if cart is empty
            return;
        }

        cart.forEach((item, index) => {
            totalPrice += item.price * item.quantity;

            let cartItem = document.createElement("div");
            cartItem.classList.add("cart-item");
            cartItem.innerHTML = `
                <div class="item-details">
                    <img src="${item.img}" alt="${item.name}">
                    <span class="item-name">${item.name}</span>
                </div>
                <div class="quantity-controls">
                    <button class="decrease-qty" data-index="${index}">-</button>
                    <span class="item-quantity">${item.quantity}</span>
                    <button class="increase-qty" data-index="${index}">+</button>
                </div>
                <div class="item-price">${(item.price * item.quantity).toLocaleString()}đ</div>
                <button class="delete-btn" data-index="${index}">Delete</button>
            `;

            cartContainer.appendChild(cartItem);
        });

        totalPriceElement.innerText = totalPrice.toLocaleString();
        localStorage.setItem("cart", JSON.stringify(cart));

        attachEventListeners(); // Add event listener after render
    }

    function attachEventListeners() {
        document.querySelectorAll(".increase-qty").forEach(button => {
            button.addEventListener("click", function () {
                let index = this.getAttribute("data-index");
                cart[index].quantity++;
                renderCart();
            });
        });

        document.querySelectorAll(".decrease-qty").forEach(button => {
            button.addEventListener("click", function () {
                let index = this.getAttribute("data-index");
                if (cart[index].quantity > 1) {
                    cart[index].quantity--;
                } else {
                    if (confirm("Do you want to delete this product?")) {
                        cart.splice(index, 1);
                    }
                }
                renderCart();
            });
        });

        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function () {
                let index = this.getAttribute("data-index");
                if (confirm("Are you sure you want to delete this product?")) {
                    cart.splice(index, 1);
                    renderCart();
                }
            });
        });
    }

    document.getElementById("checkout-all").addEventListener("click", function () {
        if (cart.length === 0) {
            alert("Your cart is empty!");
            return;
        }

        if (confirm("Are you sure you want to pay for all products?")) {
            alert("Payment successful!");
            cart = [];
            localStorage.removeItem("cart");
            renderCart();
        }
    });

    renderCart();
});
function goBack(){
    window.location.href = "http://localhost/SDLC/ASM1/Index.html";
}