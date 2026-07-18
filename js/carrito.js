// js/carrito.js
const loadMoreBtn = document.querySelector("#load-more");
let currentItem = 4;

if (loadMoreBtn) {
    loadMoreBtn.onclick = () => {
        let boxes = [...document.querySelectorAll(".box-container .box")];
        for (let i = currentItem; i < currentItem + 4; i++) {
            if (boxes[i]) boxes[i].style.display = "inline-block";
        }
        currentItem += 4;
        if (currentItem >= boxes.length) {
            loadMoreBtn.style.display = "none";
        }
    };
}

const carrito = document.getElementById("carrito");
const elementos1 = document.getElementById("lista-1");
const lista = document.querySelector("#lista-carrito tbody"); // Corregido
const vaciarCarritoBtn = document.getElementById("vaciar-carrito");

if (elementos1 && carrito) {
    cargarEventListeners();
}

function cargarEventListeners() {
    elementos1.addEventListener('click', comprarElemento);
    carrito.addEventListener('click', eliminarElemento);
    vaciarCarritoBtn.addEventListener('click', vaciarCarrito);
}

function comprarElemento(e) {
    e.preventDefault();
    if (e.target.classList.contains('agregar-carrito')) {
        const elemento = e.target.parentElement.parentElement;
        leerDatosElemento(elemento);
    }
}

function leerDatosElemento(elemento) {
    const infoElemento = {
        imagen: elemento.querySelector('img').src,
        titulo: elemento.querySelector('h3').textContent,
        precio: elemento.querySelector('.precio').textContent,
        id: elemento.querySelector('a').getAttribute('data-id')
    }
    insertarCarrito(infoElemento);
}

function insertarCarrito(elemento) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <img src="${elemento.imagen}" width="100px" style="border-radius:10px;">
        </td>
        <td>${elemento.titulo}</td>
        <td>${elemento.precio}</td>
        <td>
            <a href="#" class="borrar-producto" data-id="${elemento.id}">X</a>
        </td>
    `;
    lista.appendChild(row);
}

function eliminarElemento(e) {
    e.preventDefault();
    if (e.target.classList.contains('borrar-producto')) {
        e.target.parentElement.parentElement.remove();
    }
}

function vaciarCarrito(e) {
    e.preventDefault();
    while (lista.firstChild) {
        lista.removeChild(lista.firstChild);
    }
}
document.addEventListener("DOMContentLoaded", () => {
    
    const cartItemsSection = document.querySelector(".cart-items-section");
    
    const btnConfirmar = document.querySelector(".btn-confirm-order");

    if (cartItemsSection) {
       // borrar, sumar y restar productos
        cartItemsSection.addEventListener("click", (e) => {
            
           
            if (e.target.classList.contains("qty-btn") && e.target.textContent === "+") {
                const input = e.target.parentElement.querySelector(".qty-input");
                input.value = parseInt(input.value) + 1;
                actualizarTotales();
            }

           
            if (e.target.classList.contains("qty-btn") && e.target.textContent === "-") {
                const input = e.target.parentElement.querySelector(".qty-input");
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    actualizarTotales();
                }
            }

            // elimina
            if (e.target.classList.contains("delete-item-btn")) {
                const cartItem = e.target.closest(".cart-item");
                cartItem.remove();
                actualizarTotales();
            }
        });

        //detecta cambios 
        cartItemsSection.addEventListener("change", (e) => {
            if (e.target.classList.contains("qty-input")) {
                if (e.target.value < 1) e.target.value = 1;
                actualizarTotales();
            }
        });
    }

    // pedido
    if (btnConfirmar) {
        btnConfirmar.addEventListener("click", () => {
            const totalText = document.querySelector(".summary-row.total span:last-child").textContent;
            
            // Verificacion

            const items = document.querySelectorAll(".cart-item");
            if(items.length === 0) {
                alert("Tu carrito está vacío. Agrega productos antes de confirmar.");
                return;
            }

            alert(`¡Pedido confirmado con éxito! Total a pagar en sucursal: ${totalText}`);
           
        });
    }

    // CALCULO 
    function actualizarTotales() {
        const cartItems = document.querySelectorAll(".cart-item");
        let subtotal = 0;
        let totalProductos = 0;

        cartItems.forEach(item => {
            
            const precioTexto = item.querySelector(".item-price").textContent;
            const precio = parseFloat(precioTexto.replace("$", ""));
            
           
            const cantidad = parseInt(item.querySelector(".qty-input").value);

            subtotal += precio * cantidad;
            totalProductos += cantidad;
        });

        // Actualizacion 
        const subtotalLabel = document.querySelector(".summary-row:nth-of-type(1) span:first-child");
        const subtotalValue = document.querySelector(".summary-row:nth-of-type(1) span:last-child");
        const totalValue = document.querySelector(".summary-row.total span:last-child");

        if (subtotalLabel && subtotalValue && totalValue) {
            subtotalLabel.textContent = `Subtotal (${totalProductos} producto${totalProductos !== 1 ? 's' : ''})`;
            subtotalValue.textContent = `$${subtotal.toFixed(2)}`;
            totalValue.textContent = `$${subtotal.toFixed(2)}`; //Poner el descuento
        }
    }
});