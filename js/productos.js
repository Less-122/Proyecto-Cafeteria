document.addEventListener("DOMContentLoaded", () => {
    const btnModificar = document.getElementById("btnModificar");
    const modalEdit = document.getElementById("modalEdit");

    if (!btnModificar || !modalEdit) {
        return;
    }

    btnModificar.addEventListener("click", () => {
        const seleccionados = document.querySelectorAll(
            ".producto-check:checked"
        );

        if (seleccionados.length === 0) {
            alert("Selecciona un producto para modificar.");
            return;
        }

        if (seleccionados.length > 1) {
            alert("Selecciona solamente un producto.");
            return;
        }

        const producto = seleccionados[0];

        document.getElementById("editIdProducto").value =
            producto.value;

        document.getElementById("editNombre").value =
            producto.dataset.nombre;

        document.getElementById("editDescripcion").value =
            producto.dataset.descripcion;

        document.getElementById("editCategoria").value =
            producto.dataset.categoria;

        document.getElementById("editStock").value =
            producto.dataset.stock;

        document.getElementById("editPrecio").value =
            producto.dataset.precio;

        document.getElementById("editPromocion").checked =
            producto.dataset.promocion === "1";

        modalEdit.style.display = "block";
    });
});