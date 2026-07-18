
function actualizarTitulo() {
    const tituloElemento = document.getElementById('titulo-seccion');
    const nombreArchivo = window.location.pathname.split('/').pop();
    
    const titulos = {
        'productos.html': 'Gestión de Productos',
        'usuarios.html': 'Gestión de Usuarios',
        'pedidos.html': 'Historial de Pedidos',
        'categorias.html': 'Gestión de Categorías',
    };

    const nuevoTitulo = titulos[nombreArchivo] || 'Panel de Administración';
    
    if (tituloElemento) {
        tituloElemento.textContent = nuevoTitulo;
    }
    document.title = nuevoTitulo + ' | Panel Admin';
}


fetch('admin_header.html')
    .then(response => response.text())
    .then(headerData => {
        document.getElementById('header-placeholder').innerHTML = headerData;
        return fetch('admin_menu.html');
    })
    .then(response => response.text())
    .then(menuData => {
        document.getElementById('menu-placeholder').innerHTML = menuData;
        actualizarTitulo();
    })
    .catch(error => {
        console.error('Error cargando los componentes:', error);
    });


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


document.addEventListener('click', function(e) {
    const btnAdd = e.target.closest('.btn-add');
    const btnEdit = e.target.closest('.btn-edit');
    const btnDelete = e.target.closest('.btn-delete');
    const closeBtn = e.target.closest('.close');

  
    if (btnAdd) {
        const modalId = btnAdd.getAttribute('data-modal');
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    }

    
    if (btnEdit) {
        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        if (!checkbox) {
            alert("Por favor, selecciona un elemento para modificar.");
            return; 
        }

        const fila = checkbox.closest('tr');
        const modalId = btnEdit.getAttribute('data-modal');

        // Si estás en la sección de Categorías
        if (document.getElementById('editCatId')) {
            document.getElementById('editCatId').value = fila.cells[1].innerText;
            document.getElementById('editCatNombre').value = fila.cells[2].innerText;
            document.getElementById('editCatDescripcion').value = fila.cells[3].innerText;
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

  
    if (btnDelete) {
        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        if (!checkbox) {
            alert("Por favor, selecciona el elemento que deseas eliminar.");
            return; 
        }
        
        const fila = checkbox.closest('tr');
        const id = fila.cells[1].innerText;

        // Asignar ID según el formulario activo
        const deleteCatInput = document.getElementById('deleteCatId');
        if (deleteCatInput) deleteCatInput.value = id;

        const modalId = btnDelete.getAttribute('data-modal');
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    }

  
    if (e.target.id === 'btn-confirmar-eliminar') {
        mostrarAvisoExito();
    }

   
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


window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});


document.addEventListener("DOMContentLoaded", () => {
    const menuExtra = document.getElementById("menu-complemento-placeholder");
    if (menuExtra) {
        fetch("admin_menu_extra.html")
            .then(response => response.text())
            .then(data => { menuExtra.innerHTML = data; });
    }
});

document.addEventListener('submit', function(e) {
    e.preventDefault(); 
    mostrarAvisoExito();
    
    const formAddCat = document.getElementById('formAddCategoria');
    const formEditCat = document.getElementById('formEditCategoria');
    const formAddProd = document.getElementById('formAdd');
    const formEditProd = document.getElementById('formEdit');

    if (formAddCat) formAddCat.reset();
    if (formEditCat) formEditCat.reset();
    if (formAddProd) formAddProd.reset();
    if (formEditProd) formEditProd.reset();
});


document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const textoBusqueda = this.value.toLowerCase();
            const filas = document.querySelectorAll('main.main_container table tr');

            filas.forEach((fila, indice) => {
                if (indice === 0) return; // Cabecera

                const columna2 = fila.cells[2] ? fila.cells[2].innerText.toLowerCase() : '';
                const columna3 = fila.cells[3] ? fila.cells[3].innerText.toLowerCase() : '';

                if (columna2.includes(textoBusqueda) || columna3.includes(textoBusqueda)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    }
});