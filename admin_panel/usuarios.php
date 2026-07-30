<?php
include '../config/conexion.php';
include 'seguridad_admin.php';

// Consulta para obtener todos los usuarios registrados (incluyendo la fecha de registro)
$stmt = $conexion->prepare("SELECT id_usuario, nombre, apellido, correo, password, fecha_registro, rol FROM usuarios");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Admin</title>
    <!-- Ruta absoluta corregida -->
    <link rel="stylesheet" href="/Proyecto-Cafeteria/css/admin.css">
</head>
<body>
    
    <!-- Inserción directa con PHP, eliminando los placeholders de JS -->
    <?php include 'admin_header.php'; ?>
    <?php include 'admin_menu.php'; ?>

    <main class="main_container">
        <h1 class="titulo" id="titulo-seccion">Gestión de Usuarios</h1>
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
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Contraseña</th>
                    <th>Fecha de Registro</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No hay usuarios registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td><input type="radio" name="seleccion" value="<?= htmlspecialchars($u['id_usuario']) ?>"></td>
                        <td><?= htmlspecialchars($u['id_usuario']) ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['apellido']) ?></td>
                        <td><?= htmlspecialchars($u['correo']) ?></td>
                        <td>••••••••</td>
                        <td>
                            <?= !empty($u['fecha_registro']) ? date('d/m/Y H:i', strtotime($u['fecha_registro'])) : 'N/A' ?>
                        </td>
                        <td><?= htmlspecialchars($u['rol']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- Modal modificar usuario -->
    <div id="modalEditUsuario" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="modalEditUsuario">&times;</span>
            <h2>Modificar Usuario</h2>
            <form id="formEditUsuario">
                <input type="hidden" id="editUserId" name="id_usuario">

                <label for="editUserNombre">Nombre:</label>
                <input type="text" id="editUserNombre" name="nombre" required>

                <label for="editUserApellido">Apellido:</label>
                <input type="text" id="editUserApellido" name="apellido" required>

                <label for="editUserCorreo">Correo:</label>
                <input type="text" id="editUserCorreo" name="correo" required>

                <label for="editUserContra">Contraseña:</label>
                <input type="password" id="editUserContra" name="password" placeholder="Nueva contraseña (opcional)">

                <label for="editUserRol">Rol: </label>
                <select id="editUserRol" name="rol" required>
                    <option value="1"></option>
                    <option value="cliente">Cliente</option>
                    <option value="admin">Admin</option>
                </select>

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

    <!-- Overlay de confirmación -->
    <div id="confirmation-overlay" class="overlay" style="display:none;">
        <div class="modal-confirm">
            <h3>LISTO</h3>
            <p><br>Los cambios se han guardado correctamente.</p>
        </div>
    </div>

    <!-- Rutas absolutas para los scripts -->
    <script src="/Proyecto-Cafeteria/js/admin.js"></script>
    <script src="/Proyecto-Cafeteria/js/usuarios.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</body>
</html>