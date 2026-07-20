

let ubicacionPrincipal = window.scrollY;
const header = document.getElementById("main-header");

window.addEventListener("scroll", function() {
    let desplazamientoActual = window.scrollY;
    
    if (header) { 
        if (ubicacionPrincipal < desplazamientoActual && desplazamientoActual > 50) {
            header.style.top = "-100px";
        } else {
            header.style.top = "0";
        }
    }
    ubicacionPrincipal = desplazamientoActual;
});


const carousel = document.getElementById('promos-carousel');
const btnPrev = document.getElementById('btn-prev');
const btnNext = document.getElementById('btn-next');

if (carousel && btnPrev && btnNext) {
    const getScrollAmount = () => {
        const box = carousel.querySelector('.box');
        return box ? box.offsetWidth + 20 : 300; 
    };

    btnNext.addEventListener('click', () => {
        carousel.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
    });

    btnPrev.addEventListener('click', () => {
        carousel.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
    });
}
//se agrega a carrito
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-agregar')) {
        const tarjeta = event.target.closest('.box');
        if (!tarjeta) return;

        const nombreProducto = tarjeta.querySelector('h3').textContent;
        const precioProducto = tarjeta.querySelector('.precio').textContent;
        const imagenProducto = tarjeta.querySelector('img').src;
        
        const producto = {
            id: nombreProducto.trim().toLowerCase().replace(/\s+/g, '-'),
            nombre: nombreProducto,
            precio: parseFloat(precioProducto.replace('$', '')),
            imagen: imagenProducto,
            cantidad: 1
        };

        agregarAlLocalStorage(producto);
        console.log(`Producto añadido: ${nombreProducto} por ${precioProducto}`);
    }
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
    alert(`¡${nuevoProducto.nombre} se agregó al carrito con éxito!`);


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


document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('botonLogin').addEventListener('click', function(evento) {
        evento.preventDefault();
        window.location.href = 'login.html';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('botonCarrito').addEventListener('click', function(evento) {
        evento.preventDefault();
        window.location.href = 'carrito.html';
    });
});
    