//Menu control

//Redireccíon a login
// Redirección al inicio de sesión
const btnLogin = document.getElementById("boton-login");
if (btnLogin) {
    btnLogin.addEventListener("click", () => {
        window.location.href = "login.html";
    });
}

// Redirección al carrito
const btnCarrito = document.getElementById("boton-carrito");

if (btnCarrito) {
    btnCarrito.addEventListener("click", () => {
        window.location.href = "carrito.html";
    });
}
//Carrusel de fotos para una mejor presentacion en la pagina web
const imagenesHero =[
    "img/productos/Promociones/Combo-dulce.jpg",
    "img/productos/Promociones/Desayuno-Amanecer.jpg",
    "img/productos/Promociones/Doble-felicidad.jpg"
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
    },400);
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