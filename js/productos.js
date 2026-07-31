document.addEventListener("DOMContentLoaded", () => {
    const btnModificar = document.getElementById("btnModificar");
    const modalEdit = document.getElementById("modalEdit");
    const promocionCheckbox = document.getElementById("promocion");
    const promoFieldsAdd = document.getElementById("promoFieldsAdd");
    const editPromocionCheckbox = document.getElementById("editPromocion");
    const promoFieldsEdit = document.getElementById("promoFieldsEdit");

    function mostrarCamposPromocion(checkbox, contenedor) {
        if (!checkbox || !contenedor) {
            return;
        }

        contenedor.style.display = checkbox.checked ? "block" : "none";
    }

    if (promocionCheckbox && promoFieldsAdd) {
        promocionCheckbox.addEventListener("change", () => {
            mostrarCamposPromocion(promocionCheckbox, promoFieldsAdd);
        });
    }

    if (editPromocionCheckbox && promoFieldsEdit) {
        editPromocionCheckbox.addEventListener("change", () => {
            mostrarCamposPromocion(editPromocionCheckbox, promoFieldsEdit);
        });
    }

    if (promocionCheckbox && promoFieldsAdd) {
        mostrarCamposPromocion(promocionCheckbox, promoFieldsAdd);
    }

    if (editPromocionCheckbox && promoFieldsEdit) {
        mostrarCamposPromocion(editPromocionCheckbox, promoFieldsEdit);
    }

    if (!btnModificar || !modalEdit) {
        return;
    }

    btnModificar.addEventListener("click", () => {
        const seleccionados = document.querySelectorAll(".producto-check:checked");

        if (seleccionados.length === 0) {
            alert("Selecciona un producto para modificar.");
            return;
        }

        if (seleccionados.length > 1) {
            alert("Selecciona solamente un producto.");
            return;
        }

        const producto = seleccionados[0];

        document.getElementById("editIdProducto").value = producto.value;
        document.getElementById("editNombre").value = producto.dataset.nombre || "";
        document.getElementById("editDescripcion").value = producto.dataset.descripcion || "";
        document.getElementById("editCategoria").value = producto.dataset.categoria || "";
        document.getElementById("editPrecio").value = producto.dataset.precio || "";
        const checkPromo = producto.dataset.promocion === "1";
        document.getElementById("editPromocion").checked = checkPromo;
        document.getElementById("editEtiquetaPromo").value = producto.dataset.etiquetaPromo || "";
        document.getElementById("editPrecioDescuento").value = producto.dataset.precioDescuento || "";

        mostrarCamposPromocion(document.getElementById("editPromocion"), promoFieldsEdit);
        modalEdit.style.display = "block";
    });
});

// Eliminar producto
document.addEventListener("DOMContentLoaded", () => {
    const btnEliminarProducto = document.getElementById("btnEliminarProducto");
    const modalDeleteProducto = document.getElementById("modalDeleteProducto");
    const deleteProdId = document.getElementById("deleteProdId");

    if (btnEliminarProducto && modalDeleteProducto && deleteProdId) {
        btnEliminarProducto.addEventListener("click", () => {
            const seleccionados = document.querySelectorAll(".producto-check:checked");

            if (seleccionados.length === 0) {
                alert("Selecciona un producto para eliminar.");
                return;
            }

            if (seleccionados.length > 1) {
                alert("Selecciona solamente un producto.");
                return;
            }

            deleteProdId.value = seleccionados[0].value;
            modalDeleteProducto.style.display = "block";
        });
    }
});

