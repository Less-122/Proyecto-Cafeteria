document.addEventListener("DOMContentLoaded", function () {

    // validaciones
    function limpiarErrores(formulario) {
        const errores = formulario.querySelectorAll('.error-message');
        errores.forEach(el => el.remove());
        const inputs = formulario.querySelectorAll('.input-error');
        inputs.forEach(el => el.classList.remove('input-error'));
        // Resetear estilos
        inputs.forEach(el => {
            el.style.borderColor = '';
            el.style.boxShadow = '';
        });
    }

    function mostrarError(input, mensaje) {
        input.classList.add('input-error');
        input.style.borderColor = '#cc0c39';
        input.style.boxShadow = '0 0 0 1px #cc0c39 inset';

        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#c40000';
        errorDiv.style.fontSize = '12px';
        errorDiv.style.marginTop = '4px';
        errorDiv.style.marginBottom = '8px';
        errorDiv.textContent = '⚠️ ' + mensaje;

        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }

    function validarCategoria(nombre, descripcion) {
        let errores = [];

        // Validar nombre
        if (!nombre || nombre.trim().length < 3) {
            errores.push({ campo: 'nombre', mensaje: 'El nombre debe tener al menos 3 caracteres.' });
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
            errores.push({ campo: 'nombre', mensaje: 'El nombre solo puede contener letras y espacios.' });
        }

        // Validar descripción
        if (descripcion && descripcion.trim().length > 0) {
            if (descripcion.trim().length < 10) {
                errores.push({ campo: 'descripcion', mensaje: 'La descripción debe tener al menos 10 caracteres.' });
            } else if (/\d/.test(descripcion)) {
                errores.push({ campo: 'descripcion', mensaje: 'La descripción no puede contener números.' });
            }
        }

        return errores;
    }

    // Formulario para Añadir
    const formAdd = document.getElementById('formAddCategoria');
    if (formAdd) {
        formAdd.addEventListener('submit', function (e) {
            e.preventDefault();
            limpiarErrores(this);

            const nombre = document.getElementById('catNombre').value;
            const descripcion = document.getElementById('catDescripcion').value;

            const errores = validarCategoria(nombre, descripcion);

            if (errores.length > 0) {
                errores.forEach(err => {
                    let input;
                    if (err.campo === 'nombre') input = document.getElementById('catNombre');
                    else if (err.campo === 'descripcion') input = document.getElementById('catDescripcion');
                    if (input) mostrarError(input, err.mensaje);
                });
                return;
            }

            const formData = new FormData(this);
            formData.append('operacion', 'agregar');

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
            .catch(err => console.error('Error en fetch:', err));
        });
    }

    // Formulario de editar
    const formEdit = document.getElementById('formEditCategoria');
    if (formEdit) {
        formEdit.addEventListener('submit', function (e) {
            e.preventDefault();
            limpiarErrores(this);

            const nombre = document.getElementById('editCatNombre').value;
            const descripcion = document.getElementById('editCatDescripcion').value;

            const errores = validarCategoria(nombre, descripcion);

            if (errores.length > 0) {
                errores.forEach(err => {
                    let input;
                    if (err.campo === 'nombre') input = document.getElementById('editCatNombre');
                    else if (err.campo === 'descripcion') input = document.getElementById('editCatDescripcion');
                    if (input) mostrarError(input, err.mensaje);
                });
                return;
            }

            const formData = new FormData(this);
            formData.append('operacion', 'editar');

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
            .catch(err => console.error('Error en fetch:', err));
        });
    }

    // eliminar
    const btnEliminar = document.getElementById('btn-confirmar-eliminar-categoria');
    if (btnEliminar) {
        btnEliminar.addEventListener('click', function () {
            const id = document.getElementById('deleteCatId')?.value;
            if (!id) {
                alert("No se ha seleccionado ninguna categoría para eliminar.");
                return;
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('operacion', 'eliminar');

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
        });
    }
});