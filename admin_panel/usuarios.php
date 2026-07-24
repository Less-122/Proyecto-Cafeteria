<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    
    <div id="header-placeholder" class="header-placeholder"></div>

    <div id="menu-placeholder" class="menu-placeholder"></div>
    <main class="main_container">
        <h1 class="titulo">Gestión de Usuarios</h1>
    <section>
        <div class="filtros_container">
            <div class="search_box">
                <ion-icon name="search-outline" class="icono_filtro"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar usuarios">
            </div>
        </div>
       
        <div>
            <button data-modal="modalEditUsuario" class="btn-edit">Modificar</button>
            <button data-modal="modalDeleteUsuario" class="btn-delete">Borrar</button>
        </div>
    </section>
        <table>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Contraseña</th>
                <th>Fecha registro</th>
            </tr>
            <tr>
                <td><input type="checkbox" name="seleccion" value="1"></td>
                <td>100</td>
                <td>Juan</td>
                <td>Dominguez</td>
                <td>juan123@gmail.com</td>
                <td>contra123</td>
                <td>08-07-2026</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="seleccion" value="1"></td>
                <td>101</td>
                <td>Pepe</td>
                <td>Morales</td>
                <td>pepe123@gmail.com</td>
                <td>contra123</td>
                <td>08-07-2026</td>
            </tr>
        </table>
    </main>
    <!-- Modal modificar usuario -->
    <div id="modalEditUsuario" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="modalEditUsuario">&times;</span>
            <h2>Modificar Usuario</h2>
            <form id="formEditUsuario" action="#" method="POST">
                <input type="hidden" id="editUserId">
                <label for="editUserNombre">Nombre:</label>
                <input type="text" id="editUserNombre" name="nombre" required>

                <label for="editUserApellido">Apellido:</label>
                <input type="text" id="editUserApellido" name="apellido" required>

                <label for="editUserCorreo">Correo:</label>
                <input type="email" id="editUserCorreo" name="correo" required>

                <label for="editUserContra">Contraseña:</label>
                <input type="text" id="editUserContra" name="contra" required>

                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>

    <!-- Modal eliminar usuario -->
    <div id="modalDeleteUsuario" class="modal">
        <div class="modal-content text-center">
            <span class="close" data-modal="modalDeleteUsuario">&times;</span>
            <h2>¿Eliminar Usuario?</h2>
            <br>
            <p>Esta acción no se puede deshacer.<br><br>¿Estás seguro de que deseas eliminar al usuario seleccionado?<br></p>
            <input type="hidden" id="deleteUserId">
            
            <div class="modal-buttons">
                <button type="button" class="btn-cancelar" onclick="document.getElementById('modalDeleteUsuario').style.display='none'">Cancelar</button>
                <button type="button" id="btn-confirmar-eliminar" class="btn-danger">Confirmar Eliminar</button>
            </div>
        </div>
    </div>

    <!-- Overlay de confirmación si no existe en este HTML -->
    <div id="confirmation-overlay" class="overlay" style="display:none;">
        <div class="modal-confirm">
            <h3>LISTO</h3>
            <p><br>Los cambios se han guardado correctamente.</br></p>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</body>
</html>