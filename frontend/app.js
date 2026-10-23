let allMenus = [];
let cart = JSON.parse(localStorage.getItem("cart")) || [];

// UPDATE COUNTER NAV
function updateCartCount() {
    let el = document.getElementById("cart-count");
    if (el) {
        let totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        el.innerText = totalItems;
    }
}

// SIMPAN & TAMBAH ITEM
function addToCart(menu) {
    let item = cart.find(i => i.id === menu.id);
    if (item) {
        item.qty++;
    } else {
        cart.push({
            id: menu.id,
            nama: menu.nama,
            harga: menu.harga,
            qty: 1
        });
    }
    saveCart();
    alert(`${menu.nama} dimasukkan ke keranjang.`);
}

function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();
}

// FETCH DATA DARI data.json
function loadMenu(callback) {
    fetch("data.json?v=" + new Date().getTime()) // prevent cache
        .then(res => res.json())
        .then(data => {
            allMenus = data.menus || [];
            if (callback) callback();
        })
        .catch(err => console.error("Gagal membaca data.json:", err));
}

// RENDER GRID MENU
function renderMenu(category) {
    let container = document.getElementById("menu-container");
    if (!container) return;
    container.innerHTML = "";

    let filtered = allMenus.filter(m => m.kategori === category);
    if(filtered.length === 0) {
        container.innerHTML = "<p style='grid-column: 1/-1; text-align:center;'>Menu belum tersedia untuk kategori ini.</p>";
        return;
    }

    filtered.forEach(menu => {
        container.innerHTML += `
            <div class="menu-item">
                <img src="uploads/${menu.gambar}" alt="${menu.nama}">
                <div class="menu-item-info">
                    <h3>${menu.nama}</h3>
                    <p class="desc">${menu.deskripsi || ''}</p>
                    <p class="price">Rp ${parseInt(menu.harga).toLocaleString('id-ID')}</p>
                    <button onclick='addToCart(${JSON.stringify(menu)})'>+ Tambah ke Cart</button>
                </div>
            </div>
        `;
    });
}

// Inisialisasi Counter Global
updateCartCount();