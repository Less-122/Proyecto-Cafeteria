

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

    //actualizar
    function guardarYActualizar() {
        localStorage.setItem("carritoCompras", JSON.stringify(carrito));
        renderizarCarrito();
    }

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
        // --- EL NUEVO CÓDIGO VA AQUÍ (DENTRO DEL DOMContentLoaded) ---
    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("btn-agregar")) {
            const contenedorProducto = e.target.closest(".box");
            
            if (contenedorProducto) {
                const nombre = contenedorProducto.querySelector("h3").textContent;
                const precioTexto = contenedorProducto.querySelector(".precio").textContent;
                const precio = parseFloat(precioTexto.replace(/[^0-9.]/g, ""));
                const imagen = contenedorProducto.querySelector("img").getAttribute("src");

                const productoExistente = carrito.find(item => item.nombre === nombre);

                if (productoExistente) {
                    productoExistente.cantidad++;
                } else {
                    const nuevoProducto = {
                        nombre: nombre,
                        precio: precio,
                        imagen: imagen,
                        cantidad: 1
                    };
                    carrito.push(nuevoProducto);
                }

                guardarYActualizar();
                alert(`¡${nombre} agregado al carrito!`);
            }
        }
    });
}); 
    
