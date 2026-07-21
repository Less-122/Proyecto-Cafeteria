//Animacion para cambiar entre inicio de sesion y crear cuenta

const signIn = document.getElementById('sign-in')
const signUp = document.getElementById('sign-up')
const form = document.getElementById('form')

signIn.addEventListener('click',()=>{
    form.classList.remove('toggle')
})
signUp.addEventListener('click',()=>{
    form.classList.add('toggle')
})

//
const botonIS = document.querySelectorAll(".btniniciarSesion");

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
