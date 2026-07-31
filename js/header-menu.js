document.addEventListener("DOMContentLoaded", () => {
    const botonMenu =
        document.getElementById("header-menu-toggle");

    const navbar =
        document.getElementById("header-navbar");

    const iconoMenu =
        botonMenu?.querySelector(".header-menu-icon");

    if (!botonMenu || !navbar || !iconoMenu) {
        return;
    }

    function abrirMenu() {
        navbar.classList.add("menu-abierto");
        botonMenu.classList.add("mostrar-cerrar");

        botonMenu.setAttribute(
            "aria-expanded",
            "true"
        );

        botonMenu.setAttribute(
            "aria-label",
            "Cerrar menú"
        );

        iconoMenu.style.display = "none";
    }

    function cerrarMenu() {
        navbar.classList.remove("menu-abierto");
        botonMenu.classList.remove("mostrar-cerrar");

        botonMenu.setAttribute(
            "aria-expanded",
            "false"
        );

        botonMenu.setAttribute(
            "aria-label",
            "Abrir menú"
        );

        iconoMenu.style.display = "block";
    }

    botonMenu.addEventListener("click", () => {
        const menuEstaAbierto =
            navbar.classList.contains("menu-abierto");

        if (menuEstaAbierto) {
            cerrarMenu();
        } else {
            abrirMenu();
        }
    });

    const enlacesMenu =
        navbar.querySelectorAll("a");

    enlacesMenu.forEach((enlace) => {
        enlace.addEventListener("click", () => {
            cerrarMenu();
        });
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth > 800) {
            cerrarMenu();
        }
    });
});