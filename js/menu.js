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
    "img/productos/Postres/sueño-chocolate.jpeg",
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
    }, 4000);
}

// Listener de clics global para agregar productos al carrito
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-agCarrito')) {
        const tarjeta = event.target.closest('.box, .product-item');
        if (!tarjeta) return;

        // Búsqueda flexible de elementos dentro de la tarjeta
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

// Mostrar y ocultar productos adicionales (Ver más)
function configurarBotonVerMas(idBoton, idSeccion) {
    const boton = document.getElementById(idBoton);
    const productosExtra = document.querySelectorAll(
        `${idSeccion} .product-item-extra`
    );

    if (!boton || productosExtra.length === 0) {
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

configurarBotonVerMas("btn-masBcalientes", "#calientes");
configurarBotonVerMas("btn-masBFrias", "#frias");
configurarBotonVerMas("btn-masPostres", "#postres");

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

window.addEventListener("load", () => {
    const idDestino = window.location.hash;

    if (!idDestino) {
        return;
    }

    const seccion = document.querySelector(idDestino);

    if (!seccion) {
        return;
    }

    window.scrollTo(0, 0);

    setTimeout(() => {
        animarHaciaSeccion(seccion);
    }, 150);
});

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