<header id="main-header" class="site-header">

    <div class="header-container">

        <a href="index.php#inicio" class="header-brand">
            <img src="img/Logo/Isotipo.jpg" alt="Isotipo de Aroma a Café" class="header-isotipo">
            <span class="header-logo-text">Aroma a Café</span>
        </a>

        <nav class="header-navbar" aria-label="Menú principal">
            <ul class="header-menu">
                <li><a href="index.php#inicio">Inicio</a></li>
                <li><a href="index.php#promociones">Promociones</a></li>
                <li><a href="index.php#calientes">Bebidas calientes</a></li>
                <li><a href="index.php#frias">Bebidas frías</a></li>
                <li><a href="index.php#postres">Postres</a></li>
                <li><a href="index.php#nosotros">Ubicacion</a></li>
            </ul>
        </nav>

        <div class="header-actions">

            <?php if (isset($_SESSION['id_usuario'])): ?>
                
                <!-- 1. VISTA PARA USUARIOS LOGUEADOS -->
                <span class="nombre-usuario" style="margin-right: 15px; font-weight: 600;">
                    Hola, <?php echo $_SESSION['nombre']; ?>
                </span>
                
                <a href="logout.php" class="header-icon-btn" style="text-decoration: none; color: #cc0c39; font-weight: bold; margin-right: 15px;" aria-label="Cerrar sesión">
                    Salir
                </a>

            <?php else: ?>
                
                <!-- 2. VISTA PARA VISITANTES (Tu botón original) -->
                <button type="button" class="header-icon-btn" id="boton-login" aria-label="Iniciar sesión">
                    <img src="img/iconos/icon-usuario.png" alt="Iniciar sesión">
                </button>

            <?php endif; ?>

            <!-- 3. EL CARRITO (Siempre visible para ambos casos) -->
            <button type="button" class="header-icon-btn" id="boton-carrito" aria-label="Ver carrito">
                <img src="img/iconos/icon-carrito.png" alt="Ver carrito">
            </button>

        </div>

    </div>

</header>