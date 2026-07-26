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

// Carga dinámica de componentes (Header y Menú)
document.addEventListener("DOMContentLoaded", () => {
    const rutaHeader = 'admin_header.php'; 
    const rutaMenu = 'admin_menu.php'; 

    fetch(rutaHeader)
        .then(response => {
            if (!response.ok) throw new Error(`Header 404: No se encontró en ${rutaHeader}`);
            return response.text();
        })
        .then(headerData => {
            document.getElementById('header-placeholder').innerHTML = headerData;
            return fetch(rutaMenu);
        })
        .then(response => {
            if (!response.ok) throw new Error(`Menú 404: No se encontró en ${rutaMenu}`);
            return response.text();
        })
        .then(menuData => {
            document.getElementById('menu-placeholder').innerHTML = menuData;
            actualizarTitulo();
        })
        .catch(error => {
            console.error('Fallo en la arquitectura de la interfaz:', error);
            document.getElementById('header-placeholder').innerHTML = `<div style="background:red; color:white; padding:10px;">Error crítico de carga. Revisa consola.</div>`;
        });
});

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
            document.getElementById('editUserTelefono').value = fila.cells[4].innerText;
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

    // Abrir Modal Eliminar y pasar ID
    if (btnDelete) {
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

        fetch('../usuario_panel/eliminar_usuario.php', {
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
                // Si la respuesta no es un JSON válido, mostramos en pantalla el contenido que respondió Apache/PHP
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

        // Si tu editar_usuario.php está en la raíz, cambia esta ruta a 'editar_usuario.php'
        fetch('../usuario_panel/editar_usuario.php', {
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

    // Formulario: Agregar Usuario (se mantiene)
    if (e.target === formAddUser) {
        e.preventDefault();
        const formData = new FormData(formAddUser);

        fetch('agregar_usuario.php', {
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

// Motor de Búsqueda
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const textoBusqueda = this.value.toLowerCase();
            const filas = document.querySelectorAll('main.main_container table tr');

            filas.forEach((fila, indice) => {
                if (indice === 0) return; 

                const columnaID_Pedidos = fila.cells[0] ? fila.cells[0].innerText.toLowerCase() : '';
                const columnaID_Productos = fila.cells[1] ? fila.cells[1].innerText.toLowerCase() : '';
                const columnaNombre_Pedidos = fila.cells[1] ? fila.cells[1].innerText.toLowerCase() : '';
                const columnaNombre_Productos = fila.cells[2] ? fila.cells[2].innerText.toLowerCase() : '';

                if (
                    columnaID_Pedidos.includes(textoBusqueda) || 
                    columnaID_Productos.includes(textoBusqueda) || 
                    columnaNombre_Pedidos.includes(textoBusqueda) || 
                    columnaNombre_Productos.includes(textoBusqueda)
                ) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    }
});

// Filtro por Categorías
document.addEventListener('DOMContentLoaded', function() {
    const selectorCategoria = document.querySelector('#selector');
    const tabla = document.querySelector('table');

    if (!selectorCategoria || !tabla) return; 

    const mapaCategorias = {
        'Bebidas calientes': 'Bebidas calientes',
        'Bebidas frias': 'Bebidas frías',
        'Postres': 'Postres'
    };

    function filtrarPorCategoria() {
        const valorSeleccionado = selectorCategoria.value;
        const categoriaSeleccionada = mapaCategorias[valorSeleccionado] || '';
        const filas = tabla.querySelectorAll('tr');

        for (let i = 1; i < filas.length; i++) {
            const fila = filas[i];
            const celdas = fila.querySelectorAll('td');
            if (celdas.length === 0) continue;
            const celdaCategoria = celdas[4];
            if (!celdaCategoria) continue; 
            const textoCategoria = celdaCategoria.textContent.trim();

            if (categoriaSeleccionada === '' || textoCategoria === categoriaSeleccionada) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        }

        
    }

    selectorCategoria.addEventListener('change', filtrarPorCategoria);
    filtrarPorCategoria();
});