// js/interfaz.js

// 1. Control del Header Fijo
let ubicacionPrincipal = window.scrollY; // Corrección: de pageYOffset a scrollY
const header = document.getElementById("main-header");

window.addEventListener("scroll", function() {
    let desplazamientoActual = window.scrollY;
    
    if (ubicacionPrincipal < desplazamientoActual && desplazamientoActual > 50) {
        header.style.top = "-100px";
    } else {
        header.style.top = "0";
    }
    ubicacionPrincipal = desplazamientoActual;
});

// 2. Control del Carrusel de Promociones
const carousel = document.getElementById('promos-carousel');
const btnPrev = document.getElementById('btn-prev');
const btnNext = document.getElementById('btn-next');

if (carousel && btnPrev && btnNext) {
    // Calcula el ancho de una tarjeta + el gap para saber cuánto desplazar
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
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-agregar')) {
        const tarjeta = event.target.closest('.box');
        const nombreProducto = tarjeta.querySelector('h3').textContent;
        const precioProducto = tarjeta.querySelector('.precio').textContent;
        
        console.log(`Producto añadido: ${nombreProducto} por ${precioProducto}`);
      
    }
});

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