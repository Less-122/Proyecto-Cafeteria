document.addEventListener("DOMContentLoaded", () => {
    const cartItemsSection = document.querySelector(".cart-items-section");
    const btnConfirmar = document.querySelector(".btn-confirm-order");

    let carrito = JSON.parse(localStorage.getItem("carritoCompras")) || [];

    function renderizarCarrito() {
        if (!cartItemsSection) return;

        cartItemsSection.innerHTML = "";

        if (carrito.length === 0) {
            cartItemsSection.innerHTML = "<p class='carrito-vacio' style='text-align:center; padding:20px; font-weight: bold;'>Tu pedido está vacío. ¡Explora nuestro menú!</p>";
            actualizarTotales();
            return;
        }

        carrito.forEach((producto, indice) => {
            const itemHTML = `
                <div class="cart-item" data-indice="${indice}">
                    <img src="${producto.imagen}" alt="${producto.nombre}">
                    <div class="item-details">
                        <h3>${producto.nombre}</h3>
                        <p class="item-category">Producto Seleccionado</p>
                        <span class="item-price">$${producto.precio.toFixed(2)}</span>
                    </div>
                    <div class="item-quantity">
                        <button class="qty-btn btn-restar">-</button>
                        <input type="number" value="${producto.cantidad}" min="1" class="qty-input" readonly>
                        <button class="qty-btn btn-sumar">+</button>
                    </div>
                    <button class="delete-item-btn" title="Eliminar producto">×</button>
                </div>
            `;
            cartItemsSection.innerHTML += itemHTML;
        });

        actualizarTotales();
    }

    renderizarCarrito();


    // calculo
    function actualizarTotales() {
        let subtotal = 0;
        let totalProductos = 0;

        carrito.forEach(item => {
            subtotal += item.precio * item.cantidad;
            totalProductos += item.cantidad;
        });

        const subtotalLabel = document.querySelector(".summary-row:nth-of-type(1) span:first-child");
        const subtotalValue = document.querySelector(".summary-row:nth-of-type(1) span:last-child");
        const totalValue = document.querySelector(".summary-row.total span:last-child");

        if (subtotalLabel && subtotalValue && totalValue) {
            subtotalLabel.textContent = `Subtotal (${totalProductos} producto${totalProductos !== 1 ? 's' : ''})`;
            subtotalValue.textContent = `$${subtotal.toFixed(2)}`;
            totalValue.textContent = `$${subtotal.toFixed(2)}`; 
        }
    }

        //actualizar
    function guardarYActualizar() {
        localStorage.setItem("carritoCompras", JSON.stringify(carrito));
        renderizarCarrito();
    }

        //  (Sumar, Restar y Eliminar)
    if (cartItemsSection) {
        cartItemsSection.addEventListener("click", (e) => {
            const cartItem = e.target.closest(".cart-item");
            if (!cartItem) return;
            
            const indice = parseInt(cartItem.getAttribute("data-indice"));

            // Botón de Sumar (+)
            if (e.target.classList.contains("btn-sumar") || e.target.textContent === "+") {
                carrito[indice].cantidad++;
                guardarYActualizar();
            }
            // Botón de Restar (-)
            if (e.target.classList.contains("btn-restar") || e.target.textContent === "-") {
                if (carrito[indice].cantidad > 1) {
                    carrito[indice].cantidad--;
                    guardarYActualizar();
                }
            }
            // Botón Eliminar Producto (×)
            if (e.target.classList.contains("delete-item-btn")) {
                carrito.splice(indice, 1);
                guardarYActualizar();
            }
        });
    }   

    //confirmacion
    if (btnConfirmar) {
        btnConfirmar.addEventListener("click", () => {
            if (carrito.length === 0) {
                alert("Tu carrito está vacío. Agrega productos antes de confirmar.");
                return;
            }

            const totalText = document.querySelector(".summary-row.total span:last-child").textContent;
            alert(`Pedido confirmado. Total a pagar en sucursal: ${totalText}`);
            
            localStorage.removeItem("carritoCompras");
            carrito = [];
            renderizarCarrito();
        });
    }

}); 

// Listener de clics global para agregar productos al carrito
document.addEventListener('click', (event) => {
    const boton = event.target.closest('.btn-agCarrito');
    if (!boton) return;

    // Verifica que el usuario haya iniciado sesión
    if (!window.usuarioLogueado) {
        window.location.href = 'login.php';
        return;
    }

    // si el usuario ya inicio sesión 
    const tarjeta = boton.closest('.box, .product-item');
    if (!tarjeta) return;

    const nombreProducto = tarjeta.querySelector('h3')?.textContent.trim() || 'Producto';
    
    // Obtiene el precio promocional si existe; si no, el normal
    const precioElem = tarjeta.querySelector('.precio-promo') || tarjeta.querySelector('.precio');
    const precioTexto = precioElem ? precioElem.textContent : '$0';
    
    const imagenProducto = tarjeta.querySelector('img')?.src || '';

    const producto = {
        id: nombreProducto.toLowerCase().replace(/\s+/g, '-'),
        nombre: nombreProducto,
        precio: parseFloat(precioTexto.replace('$', '').replace(',', '').trim()) || 0,
        imagen: imagenProducto,
        cantidad: 1
    };

    agregarAlLocalStorage(producto);
    mostrarNotificacion(`${nombreProducto} ha sido agregado al carrito`);
});

function agregarAlLocalStorage(nuevoProducto) {
    let carrito = JSON.parse(localStorage.getItem("carritoCompras")) || [];

    const existe = carrito.find(item => item.id === nuevoProducto.id);

    if (existe) {
        existe.cantidad++; 
    } else {
        carrito.push(nuevoProducto); 
    }

    localStorage.setItem("carritoCompras", JSON.stringify(carrito));
    // Ya no redirige automáticamente a carrito.php para dejar seguir comprando
}

// Función para mostrar alerta emergente elegante sin bloquear la navegación
function mostrarNotificacion(mensaje) {
    let toast = document.getElementById("toast-notificacion");

    if (!toast) {
        toast = document.createElement("div");
        toast.id = "toast-notificacion";
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-size: 0.95rem;
            z-index: 1000;
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        `;
        document.body.appendChild(toast);
    }

    toast.textContent = mensaje;
    toast.style.opacity = "1";
    toast.style.transform = "translateY(0)";

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateY(20px)";
    }, 2500);
}
