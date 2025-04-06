let cart = [];

function addToCart(name, price, id) {
    let productIndex = cart.findIndex(product => product.id === id);

    if (productIndex > -1) {
        cart[productIndex].quantity += 1;
    } else {

        cart.push({
            id: id,
            name: name,
            price: price,
            quantity: 1
        });
    }

    // Cập nhật giỏ hàng và tổng tiền
    updateCart();
    // Hiển thị thông báo đã thêm sản phẩm vào giỏ
    showAddToCartNotification();
}

// Hàm hiển thị thông báo khi thêm sản phẩm vào giỏ
function showAddToCartNotification() {
    let notification = document.getElementById('cart-notification');

    // Đảm bảo thông báo luôn được reset về trạng thái ẩn trước khi hiển thị lại
    notification.classList.remove('show', 'hide');
    notification.style.display = 'block'; // Hiển thị thông báo
    setTimeout(() => {
        notification.classList.add('show'); // Kích hoạt hiển thị với hiệu ứng
    }, 10); // Chỉ cần delay rất ngắn để kích hoạt animation

    // Sau 3 giây, ẩn thông báo
    setTimeout(() => {
        notification.classList.remove('show');
        notification.classList.add('hide'); // Thêm class 'hide' để ẩn thông báo
    }, 3000);
}




// Hàm cập nhật giỏ hàng và tính tổng tiền
function updateCart() {
    let cartList = document.getElementById("cart-list");
    let totalPrice = 0;
    cartList.innerHTML = ""; // Làm sạch giỏ hàng cũ

    cart.forEach(product => {
        let li = document.createElement("li");
        li.innerHTML = `${product.name} - ${product.price * product.quantity} VND <button onclick="removeFromCart(${product.id})">Xóa</button>`;
        cartList.appendChild(li);
        totalPrice += product.price * product.quantity;
    });

    // Cập nhật tổng tiền
    document.getElementById("total-price").innerText = totalPrice;
}

// Hàm xóa sản phẩm khỏi giỏ hàng
function removeFromCart(id) {
    cart = cart.filter(product => product.id !== id);
    updateCart();
}

// Thêm sự kiện cho các nút "Thêm vào giỏ"
document.querySelectorAll(".add-to-cart").forEach(button => {
    button.addEventListener("click", (e) => {
        let productElement = e.target.parentElement;
        let id = parseInt(productElement.getAttribute("data-id"));
        let name = productElement.getAttribute("data-name");
        let price = parseInt(productElement.getAttribute("data-price"));

        addToCart(name, price, id);
    });
    document.getElementById("search-input").addEventListener("input", function () {
        let filter = this.value.toLowerCase();
        let products = document.querySelectorAll(".product");

        products.forEach(product => {
            let name = product.getAttribute("data-name").toLowerCase();
            if (name.includes(filter)) {
                product.style.display = "block";
            } else {
                product.style.display = "none";
            }
        });
    });
    document.getElementById('search-input').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            searchProducts(); // Gọi hàm tìm kiếm khi nhấn Enter
        }
    });
    document.querySelector('.search-btn').addEventListener('click', function () {
        searchProducts(); // Gọi hàm tìm kiếm khi nhấn nút
    });
    function searchProducts() {
        let input = document.getElementById('search-input').value.toLowerCase();
        let products = document.querySelectorAll('.product');
        let searchResults = document.getElementById('search-results');
        let resultsFound = false;

        // Clear previous search results
        searchResults.innerHTML = '';

        // Loop through each product and check if it matches the search input
        products.forEach(product => {
            let productName = product.getAttribute('data-name').toLowerCase();
            if (productName.includes(input)) {
                product.style.display = 'block'; // Show product if it matches
                resultsFound = true;
            } else {
                product.style.display = 'none'; // Hide product if it doesn't match
            }
        });

        // If no results found, show a message
        if (!resultsFound && input.trim() !== '') {
            searchResults.style.display = 'block'; // Ensure the message is displayed
            searchResults.innerHTML = '<p>No results found.</p>'; // Show no result message
        } else {
            searchResults.style.display = 'none'; // Hide the search results message if there are results
        }
    }
    document.getElementById("logout-btn").addEventListener("click", function () {
        localStorage.removeItem("isLoggedIn"); // Xóa trạng thái đăng nhập
        localStorage.removeItem("currentUser"); // Xóa user đang đăng nhập (nếu có)
        window.location.href = "../login.html"; // Chuyển về trang đăng nhập

    });
    document.addEventListener("DOMContentLoaded", function () {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        function updateCartCount() {
            document.getElementById("cart-count").innerText = cart.length;
        }

        function addToCart(event) {
            let product = event.target.closest('.product');
            let productName = product.getAttribute('data-name');
            let productPrice = parseInt(product.getAttribute('data-price'));
            let productImg = product.querySelector('img').src;

            let item = cart.find(i => i.name === productName);
            if (item) {
                item.quantity++;
            } else {
                cart.push({ img: productImg, name: productName, price: productPrice, quantity: 1 });
            }

            localStorage.setItem("cart", JSON.stringify(cart));
            updateCartCount();
        }

        document.querySelectorAll(".add-to-cart").forEach(button => {
            button.addEventListener("click", addToCart);
        });

        updateCartCount();
    });
    function showToast(message) {
        let toast = document.getElementById("toast");
        toast.textContent = message;
        toast.classList.add("show");
    
        setTimeout(() => {
            toast.classList.remove("show");
        }, 2000); // 3 giây sau tự biến mất
    }
    
    document.querySelectorAll(".add-to-cart").forEach((button) => {
        button.addEventListener("click", function () {
            showToast("✅ Product has been added to cart!");
        });
    });
    



});    