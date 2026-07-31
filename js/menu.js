// Menu control
// Redirección al inicio de sesión
const btnLogin = document.getElementById("boton-login");
if (btnLogin) {
    btnLogin.addEventListener("click", () => {
        window.location.href = "login.php";
    });
}

// Redirección al carrito desde el botón del header
const btnCarrito = document.getElementById("boton-carrito");
if (btnCarrito) {
    btnCarrito.addEventListener("click", () => {
        window.location.href = "carrito.php";
    });
}

// Carrusel de fotos hero (presentación)
const imagenesHero = [
    "img/productos/Postres/producto_6a658017028190.67328164.jpeg",
    "img/productos/Promociones/Combo-dulce.jpeg",
    "img/productos/Promociones/Desayuno-Amanecer.jpeg",
    "img/productos/Promociones/Doble-felicidad.jpeg"
];
let indiceHero = 0;
const heroImage = document.getElementById("PromoImagen");
if (heroImage) {
    setInterval(() => {
        indiceHero++;
        if (indiceHero >= imagenesHero.length) {
            indiceHero = 0;
        }
        heroImage.src = imagenesHero[indiceHero];
    }, 1000);
}
// Mostrar y ocultar productos adicionales (Ver más)
function configurarBotonVerMas(boton) {
    if (!boton) {
        return;
    }

    const idSeccion = boton.dataset.target;
    const seccion = document.getElementById(idSeccion);

    if (!seccion) {
        return;
    }

    const productosExtra = seccion.querySelectorAll(".product-item-extra");

    if (productosExtra.length === 0) {
        boton.style.display = "none";
        return;
    }

    let productosVisibles = false;

    boton.addEventListener("click", () => {
        productosVisibles = !productosVisibles;

        productosExtra.forEach((producto) => {
            producto.style.display = productosVisibles ? "flex" : "none";
        });

        boton.textContent = productosVisibles
            ? "Ver menos"
            : "Ver más productos";
    });
}

document.querySelectorAll(".btn-ver-mas").forEach(configurarBotonVerMas);

// Scroll suave en la página
function animarHaciaSeccion(seccion) {
    const posicionInicial = window.scrollY;

    const posicionFinal =
        seccion.getBoundingClientRect().top +
        window.scrollY -
        100;

    const distancia = posicionFinal - posicionInicial;
    const duracion = 1500;
    let tiempoInicial = null;

    function animarScroll(tiempoActual) {
        if (tiempoInicial === null) {
            tiempoInicial = tiempoActual;
        }

        const tiempoTranscurrido = tiempoActual - tiempoInicial;

        const progreso = Math.min(
            tiempoTranscurrido / duracion,
            1
        );

        const suavizado =
            progreso < 0.5
                ? 2 * progreso * progreso
                : 1 - Math.pow(-2 * progreso + 2, 2) / 2;

        window.scrollTo(
            0,
            posicionInicial + distancia * suavizado
        );

        if (progreso < 1) {
            requestAnimationFrame(animarScroll);
        }
    }

    requestAnimationFrame(animarScroll);
}

const enlacesMenu = document.querySelectorAll(".header-navbar a");

enlacesMenu.forEach((enlace) => {
    enlace.addEventListener("click", (evento) => {
        const destino = enlace.getAttribute("href");

        if (!destino || !destino.includes("#")) {
            return;
        }

        const partes = destino.split("#");
        const idSeccion = partes[1];

        if (!idSeccion) {
            return;
        }

        const seccion = document.getElementById(idSeccion);

        if (seccion) {
            evento.preventDefault();
            animarHaciaSeccion(seccion);
        }
    });
});

// NOTA: Se eliminó el listener de "load" que forzaba scrollTo(0,0) y luego
// animaba hacia la sección del hash al cargar la página. Ese bloque competía
// con el scroll del usuario durante ~1.5s después de cada carga (por eso se
// sentía "trabado" al volver de login), y era redundante: el navegador ya
// posiciona correctamente la sección gracias a "scroll-margin-top: 100px"
// definido en menu.css, sin necesidad de JavaScript.

// Control del Header Fijo
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

// Control del Carrusel de Promociones
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
document.querySelector('.carrusel-container').addEventListener('wheel', function(e) {
    if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
        return; // deja que el scroll vertical de la página funcione normal
    }
}, { passive: true });