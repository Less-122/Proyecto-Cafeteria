document.addEventListener("DOMContentLoaded", function () {
    const formAdd = document.getElementById('formAddCategoria');
    const formEdit = document.getElementById('formEditCategoria');

    function manejarEnvio(formulario, operacion) {
        formulario.addEventListener('submit', function (e) {
            e.preventDefault(); // Evita el envío tradicional

            const formData = new FormData(this);
            formData.append('operacion', operacion); 

            fetch(this.action, {
                method: this.method,
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (typeof mostrarAvisoExito === 'function') mostrarAvisoExito();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => console.error(err));
        });
    }

    if (formAdd) manejarEnvio(formAdd, 'agregar');
    if (formEdit) manejarEnvio(formEdit, 'editar');
});


// ---- ELIMINAR CATEGORÍA ----
document.addEventListener('click', function(e) {
    // Solo actuar si el clic es en el botón de confirmar eliminar
    if (e.target.id === 'btn-confirmar-eliminar') {
        // Verificar que el modal de categorías esté visible
        const modalCategoria = document.getElementById('modalDeleteCategoria');
        if (!modalCategoria || modalCategoria.style.display !== 'block') {
            return; 
        }

        const id = document.getElementById('deleteCatId')?.value;
        if (!id) {
            alert("No se ha seleccionado ninguna categoría para eliminar.");
            return;
        }

        // Preparar datos
        const formData = new FormData();
        formData.append('id', id);
        formData.append('operacion', 'eliminar');

        // Enviar al controlador
        fetch('../controlador/categorias_controlador.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof mostrarAvisoExito === 'function') mostrarAvisoExito();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => console.error('Error al eliminar categoría:', err));
    }
});