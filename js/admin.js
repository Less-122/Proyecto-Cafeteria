// Actualizar título dinámicamente
function actualizarTitulo() {
    const tituloElemento = document.getElementById('titulo-seccion');
    const nombreArchivo = window.location.pathname.split('/').pop();

    const titulos = {
        'productos.php': 'Gestión de Productos',
        'usuarios.php': 'Gestión de Usuarios',
        'pedidos.php': 'Historial de Pedidos',
        'categorias.php': 'Gestión de Categorías',
        'panel_barista.php': 'Panel de Barista'
    };
    const nuevoTitulo = titulos[nombreArchivo] || 'Panel de Administración';

    if (tituloElemento) {
        tituloElemento.textContent = nuevoTitulo;
    }
    document.title = nuevoTitulo + ' | Panel Admin';
}

// Funciones de UI y Modales
function mostrarAvisoExito() {
    const overlay = document.getElementById('confirmation-overlay');
    if (overlay) {
        overlay.style.display = 'flex';
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
        setTimeout(() => {
            overlay.style.display = 'none';
        }, 2000);
    }
}

// Eventos global para clics en la interfaz (Abrir y cerrar modales, botones)
document.addEventListener('click', function(e) {
    const btnAdd = e.target.closest('.btn-add');
    const btnEdit = e.target.closest('.btn-edit');
    const btnDelete = e.target.closest('.btn-delete');
    const closeBtn = e.target.closest('.close');

    // Abrir Modal Añadir
    if (btnAdd) {
        const modalId = btnAdd.getAttribute('data-modal');
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    }

    // Abrir Modal Modificar y cargar datos a los inputs
    if (btnEdit) {
        const isProductsPage = !!document.getElementById('editIdProducto') && !!document.getElementById('modalEdit');
        if (isProductsPage) {
            return;
        }

        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        if (!checkbox) {
            alert("Por favor, selecciona un elemento para modificar.");
            return; 
        }
        const fila = checkbox.closest('tr');
        const modalId = btnEdit.getAttribute('data-modal');

        if (document.getElementById('editCatId')) {
            document.getElementById('editCatId').value = fila.cells[1].innerText;
            document.getElementById('editCatNombre').value = fila.cells[2].innerText;
            document.getElementById('editCatDescripcion').value = fila.cells[3].innerText;
        } 
        else if (document.getElementById('editUserId')) {
            document.getElementById('editUserId').value = fila.cells[1].innerText;
            document.getElementById('editUserNombre').value = fila.cells[2].innerText;
            document.getElementById('editUserApellido').value = fila.cells[3].innerText;
            document.getElementById('editUserCorreo').value = fila.cells[4].innerText;
            const inputContra = document.getElementById('editUserContra') || document.getElementById('editUserPassword');
            if (inputContra) inputContra.value = ''; // Limpiar campo contraseña por seguridad
        }
        else if (document.getElementById('editNombre')) {
            document.getElementById('editNombre').value = fila.cells[2].innerText;
            document.getElementById('editDescripcion').value = fila.cells[3].innerText;
            document.getElementById('editCategoria').value = fila.cells[4].innerText;
            document.getElementById('editPrecio').value = fila.cells[5].innerText;
            document.getElementById('editImg-url').value = fila.cells[6].innerText;
            
            const promoCheckbox = fila.cells[7].querySelector('input[type="checkbox"]');
            document.getElementById('editPromocion').checked = promoCheckbox ? promoCheckbox.checked : false;
        }

        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    }

    // Abrir Modal Eliminar y pasar ID USUARIOS
    if (btnDelete) {
        const isProductsPage = !!document.getElementById('deleteProdId') && !!document.getElementById('modalDeleteProducto');
        if (isProductsPage) {
            return;
        }

        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        if (!checkbox) {
            alert("Por favor, selecciona el elemento que deseas eliminar.");
            return; 
        } 
        
        const fila = checkbox.closest('tr');
        const id = fila.cells[1].innerText.trim();

        const deleteCatInput = document.getElementById('deleteCatId');
        if (deleteCatInput) deleteCatInput.value = id;

        const deleteUserInput = document.getElementById('deleteUserId');
        if (deleteUserInput) deleteUserInput.value = id;

        const modalId = btnDelete.getAttribute('data-modal');
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    }

    // Confirmar eliminación de usuario vía Fetch
    if (e.target.id === 'btn-confirmar-eliminar') {
        const deleteUserId = document.getElementById('deleteUserId')?.value;
        if (!deleteUserId) {
            alert("No se ha seleccionado ningún usuario para eliminar.");
            return;
        }

        const formData = new FormData();
        formData.append('id_usuario', deleteUserId);

        fetch('/Proyecto-Cafeteria/usuario_panel/eliminar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(async res => {
            const textoRespuesta = await res.text();
            
            // Si la respuesta es HTTP 404
            if (res.status === 404) {
                throw new Error("No se encontró el archivo 'eliminar_usuario.php'. Verifica su ubicación en la estructura de tu proyecto.");
            }

            try {
                return JSON.parse(textoRespuesta);
            } catch (err) {
                throw new Error("El servidor no devolvió JSON válido. Respuesta recibida:\n\n" + textoRespuesta);
            }
        })
        .then(data => {
            if (data.success) {
                mostrarAvisoExito();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error al eliminar: ' + data.message);
            }
        })
        .catch(err => {
            console.error('Error en la petición de eliminación:', err);
            alert(err.message);
        });
    }




    // Cerrar Modales
    if (closeBtn) {
        const modalId = closeBtn.getAttribute('data-modal');
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'none';
    }

    if (e.target.id === 'btn-accept-confirm') {
        const overlay = document.getElementById('confirmation-overlay');
        if (overlay) overlay.style.display = 'none';
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
    }
});

// Cerrar modal al hacer clic en el fondo gris fuera del contenido
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

// Manejo de Envíos de Formulario (Submit)
document.addEventListener('submit', function(e) {
    const formEditUser = document.getElementById('formEditUsuario');
    const formAddUser  = document.getElementById('formAgregarUsuario');

    // Formulario: Modificar Usuario
    if (e.target === formEditUser) {
        e.preventDefault();
        const formData = new FormData(formEditUser);

        fetch('/Proyecto-Cafeteria/usuario_panel/editar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(async res => {
            const textoRespuesta = await res.text();

            if (res.status === 404) {
                throw new Error("No se encontró el archivo 'editar_usuario.php'. Verifica su ubicación.");
            }

            try {
                return JSON.parse(textoRespuesta);
            } catch (err) {
                throw new Error("El servidor no devolvió un JSON válido. Respuesta recibida:\n\n" + textoRespuesta);
            }
        })
        .then(data => {
            if (data.success) {
                mostrarAvisoExito();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert("Error al actualizar: " + data.message);
            }
        })
        .catch(err => {
            console.error("Error al editar usuario:", err);
            alert(err.message);
        });
    }

    // Formulario: Agregar Usuario
    if (e.target === formAddUser) {
        e.preventDefault();
        const formData = new FormData(formAddUser);

        fetch('/Proyecto-Cafeteria/usuario_panel/agregar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarAvisoExito();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert("Error al agregar: " + data.message);
            }
        })
        .catch(err => console.error("Error al agregar usuario:", err));
    }
});

// Filtro por búsqueda y categoría en la tabla de productos
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const selectorCategoria = document.querySelector('#selector');
    const tabla = document.querySelector('main.main_container table tbody');

    if (!tabla) return;

    const filas = Array.from(tabla.querySelectorAll('tr'));

    function aplicarFiltros() {
        const textoBusqueda = (searchInput?.value || '').toLowerCase().trim();
        const categoriaSeleccionada = (selectorCategoria?.value || '').trim().toLowerCase();

        filas.forEach((fila) => {
            const textoFila = (fila.textContent || '').toLowerCase();
            const nombreFila = (fila.cells[2]?.textContent || '').toLowerCase();
            const categoriaFila = (fila.dataset.categoriaNombre || '').toLowerCase();

            const coincideBusqueda = textoBusqueda === '' ||
                textoFila.includes(textoBusqueda) ||
                nombreFila.includes(textoBusqueda);
            const coincideCategoria = categoriaSeleccionada === '' ||
                categoriaFila === categoriaSeleccionada;

            fila.style.display = coincideBusqueda && coincideCategoria ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', aplicarFiltros);
    }

    if (selectorCategoria) {
        selectorCategoria.addEventListener('change', aplicarFiltros);
    }

    aplicarFiltros();
});


// Ejecutar la actualización del título en cuanto cargue la página
document.addEventListener("DOMContentLoaded", () => {
    // Verificamos que el elemento exista antes de intentar cambiarlo
    if (document.getElementById('titulo-seccion')) {
        actualizarTitulo();
    }
});