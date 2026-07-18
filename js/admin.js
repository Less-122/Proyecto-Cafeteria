
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
    //Traer el header y la barra lateral
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


      /*document.getElementById('test').onclick=function(){
                alert("Hola mundo")
      }*/

//Abrir modales añadir
document.addEventListener('click', function(e) {
  const btnAdd = e.target.closest('.btn-add');
  if (btnAdd) {
    const modalId = btnAdd.getAttribute('data-modal');
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.style.display = 'block';
    } else {
      console.warn('Modal con ID "' + modalId + '" no encontrado.');
    }
  }
});


const closeButtons = document.querySelectorAll('.close');
closeButtons.forEach(function(btn) {
  btn.addEventListener('click', function() {
    const modalId = this.getAttribute('data-modal');
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.style.display = 'none';
    }
  });
});

//Cerrar modales al hacer clic fuera 
window.addEventListener('click', function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.style.display = 'none';
  }
});
document.addEventListener("DOMContentLoaded", () => {
    // Llamada al menú principal
    cargarComponente("menu-placeholder", "admin_menu.html");
    
    // Llamada al segundo HTML que completa la interfaz
    cargarComponente("menu-complemento-placeholder", "admin_menu_extra.html");
});
document.addEventListener('click', function(e) {
    
    // --- LÓGICA PARA ABRIR MODALES ---
    const btnAdd = e.target.closest('.btn-add');
    const btnEdit = e.target.closest('.btn-edit');

    if (btnAdd) {
        const modalId = btnAdd.getAttribute('data-modal');
        document.getElementById(modalId).style.display = 'block';
    }

    if (btnEdit) {
        // 1. Buscamos el checkbox seleccionado
        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        
        if (!checkbox) {
            alert("Por favor, selecciona una categoría para modificar.");
            return;
        }

   
        const fila = checkbox.closest('tr');
        
       
        const id = fila.cells[1].innerText;
        const nombre = fila.cells[2].innerText;
        const descripcion = fila.cells[3].innerText;

    
        document.getElementById('editCatId').value = id;
        document.getElementById('editCatNombre').value = nombre;
        document.getElementById('editCatDescripcion').value = descripcion;

       
        const modalId = btnEdit.getAttribute('data-modal');
        document.getElementById(modalId).style.display = 'block';
    }

    if (e.target.id === 'btn-accept-confirm') {
        document.getElementById('confirmation-overlay').style.display = 'none';
   
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
    }
});


document.addEventListener('submit', function(e) {
    e.preventDefault(); 

    document.getElementById('confirmation-overlay').style.display = 'flex';
});

// nuevo eliminar 
document.addEventListener('click', function(e) {
    
    const btnAdd = e.target.closest('.btn-add');
    const btnEdit = e.target.closest('.btn-edit');
    const btnDelete = e.target.closest('.btn-delete'); 
    // 

    if (btnAdd) {
        const modalId = btnAdd.getAttribute('data-modal');
        document.getElementById(modalId).style.display = 'block';
    }

    if (btnEdit) {
        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        if (!checkbox) {
            alert("Por favor, selecciona una categoría para modificar.");
            return;
        }
        const fila = checkbox.closest('tr');
        
        document.getElementById('editCatId').value = fila.cells[1].innerText;
        document.getElementById('editCatNombre').value = fila.cells[2].innerText;
        document.getElementById('editCatDescripcion').value = fila.cells[3].innerText;

        const modalId = btnEdit.getAttribute('data-modal');
        document.getElementById(modalId).style.display = 'block';
    }
    if (btnDelete) {
        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        if (!checkbox) {
            alert("Por favor, selecciona la categoría que deseas eliminar.");
            return;
        }
        const fila = checkbox.closest('tr');
        const id = fila.cells[1].innerText;
        document.getElementById('deleteCatId').value = id;
        const modalId = btnDelete.getAttribute('data-modal');
        document.getElementById(modalId).style.display = 'block';
    }
    if (e.target.id === 'btn-confirmar-eliminar') {
        const idAEliminar = document.getElementById('deleteCatId').value;
        console.log('Enviando solicitud para eliminar el ID:', idAEliminar);
      document.getElementById('modalDeleteCategoria').style.display = 'none';
       
        document.getElementById('confirmation-overlay').style.display = 'flex';
    }
    if (e.target.id === 'btn-accept-confirm') {
        document.getElementById('confirmation-overlay').style.display = 'none';
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
    }
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

    // añadir
    if (btnAdd) {
        const modalId = btnAdd.getAttribute('data-modal');
        document.getElementById(modalId).style.display = 'block';
    }
    //modificar
    if (btnEdit) {
        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        if (!checkbox) { alert("Por favor, selecciona una categoría para modificar."); return; }
        const fila = checkbox.closest('tr');
        document.getElementById('editCatId').value = fila.cells[1].innerText;
        document.getElementById('editCatNombre').value = fila.cells[2].innerText;
        document.getElementById('editCatDescripcion').value = fila.cells[3].innerText;

        const modalId = btnEdit.getAttribute('data-modal');
        document.getElementById(modalId).style.display = 'block';
    }
    // btn eliminar
    if (btnDelete) {
        const checkbox = document.querySelector('input[name="seleccion"]:checked');
        if (!checkbox) { alert("Por favor, selecciona la categoría que deseas eliminar."); return; }
        
        const fila = checkbox.closest('tr');
        document.getElementById('deleteCatId').value = fila.cells[1].innerText;

        const modalId = btnDelete.getAttribute('data-modal');
        document.getElementById(modalId).style.display = 'block';
    }
    //btn de confirmacion 
    if (e.target.id === 'btn-confirmar-eliminar') {
        const idAEliminar = document.getElementById('deleteCatId').value;
        console.log('Eliminando ID:', idAEliminar);

        mostrarAvisoExito();
    }
});

 
document.addEventListener('submit', function(e) {
    e.preventDefault(); 
    
    // vinculo php
    
    mostrarAvisoExito();
    
    // clear
    const formAdd = document.getElementById('formAddCategoria');
    const formEdit = document.getElementById('formEditCategoria');
    if (formAdd) formAdd.reset();
    if (formEdit) formEdit.reset();
});
// 
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const textoBusqueda = this.value.toLowerCase();
            const filas = document.querySelectorAll('main.main_container table tr');

            filas.forEach((fila, indice) => {
                if (indice === 0) return;

                const nombreCategoria = fila.cells[2] ? fila.cells[2].innerText.toLowerCase() : '';
                const descripcionCategoria = fila.cells[3] ? fila.cells[3].innerText.toLowerCase() : '';

                if (nombreCategoria.includes(textoBusqueda) || descripcionCategoria.includes(textoBusqueda)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    }
});