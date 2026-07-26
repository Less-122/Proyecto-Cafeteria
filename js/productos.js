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

//Eliminar producto
document.addEventListener("DOMContentLoaded", () => {

    const btnEliminarProducto =
        document.getElementById("btnEliminarProducto");

    const modalDeleteProducto =
        document.getElementById("modalDeleteProducto");

    const deleteProdId =
        document.getElementById("deleteProdId");


    if (
        btnEliminarProducto &&
        modalDeleteProducto &&
        deleteProdId
    ) {

        btnEliminarProducto.addEventListener("click", () => {

            const seleccionados =
                document.querySelectorAll(
                    ".producto-check:checked"
                );


            // No seleccionó ningún producto
            if (seleccionados.length === 0) {

                alert(
                    "Selecciona un producto para eliminar."
                );

                return;
            }


            // Seleccionó más de uno
            if (seleccionados.length > 1) {

                alert(
                    "Selecciona solamente un producto."
                );

                return;
            }


            // Tomamos el producto seleccionado
            const productoSeleccionado =
                seleccionados[0];


            // Guardamos el ID en el input oculto
            deleteProdId.value =
                productoSeleccionado.value;


            // Abrimos el modal
            modalDeleteProducto.style.display =
                "block";

        });

    }

});

