//Menu control

//Redireccíon a login
// Redirección al inicio de sesión
const btnLogin = document.getElementById("boton-login");
if (btnLogin) {
    btnLogin.addEventListener("click", () => {
        window.location.href = "login.php";
    });
}

// Redirección al carrito
const btnCarrito = document.getElementById("boton-carrito");

if (btnCarrito) {
    btnCarrito.addEventListener("click", () => {
        window.location.href = "carrito.php";
    });
}
//Carrusel de fotos para una mejor presentacion en la pagina web
const imagenesHero =[
    "img/productos/Postres/sueño-chocolate.jpeg",
    "img/productos/Promociones/Combo-dulce.jpeg",
    "img/productos/Promociones/Desayuno-Amanecer.jpeg",
    "img/productos/Promociones/Doble-felicidad.jpeg"
];
let indiceHero=0;
const heroImage=document.getElementById("PromoImagen");
if(heroImage){
    setInterval(()=>{
        indiceHero++;
        if(indiceHero >= imagenesHero.length){
            indiceHero =0;
        }
    heroImage.src=imagenesHero[indiceHero]
    },4000);
}



//Agregar al carrito
const botonesComprar = document.querySelectorAll(".btn-agCarrito");

let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

botonesComprar.forEach((boton) => {
    boton.addEventListener("click", () => {
        const tarjeta = boton.closest(".product-item, .box");

        if (!tarjeta) {
            return;
        }

        const nombre = tarjeta.querySelector("h3")?.textContent.trim();
        const descripcion = tarjeta.querySelector("p")?.textContent.trim();
        const imagen = tarjeta.querySelector("img")?.getAttribute("src");
        const precioTexto = tarjeta.querySelector(".precio")?.textContent;

        const precio = Number(
            precioTexto
                ?.replace("$", "")
                .replace(",", "")
                .trim()
        );

        if (!nombre || Number.isNaN(precio)) {
            console.error("No se pudo obtener la información del producto.");
            return;
        }

        const productoExistente = carrito.find(
            (producto) => producto.nombre === nombre
        );

        if (productoExistente) {
            productoExistente.cantidad += 1;
        } else {
            carrito.push({
                nombre,
                descripcion,
                imagen,
                precio,
                cantidad: 1
            });
        }

        localStorage.setItem("carrito", JSON.stringify(carrito));

        alert(`${nombre} se agregó al carrito`);
    });
});

// Para el botón de ver mas

// Mostrar y ocultar productos adicionales
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

/*Para el scroll en la pagina*/
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

        const tiempoTranscurrido =
            tiempoActual - tiempoInicial;

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

        /*
        Si la sección existe, significa que estamos en index.php.
        Entonces evitamos el salto normal y usamos la animación.
        */
        if (seccion) {
            evento.preventDefault();
            animarHaciaSeccion(seccion);
        }

        /*
        Si no existe, por ejemplo desde login.php,
        el navegador irá normalmente a index.php#seccion.
        */
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

    /*
    El navegador intenta saltar automáticamente a la sección.
    Lo regresamos arriba y después iniciamos la animación.
    */
    window.scrollTo(0, 0);

    setTimeout(() => {
        animarHaciaSeccion(seccion);
    }, 150);
});


























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