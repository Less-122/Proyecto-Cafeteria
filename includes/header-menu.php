<header id="main-header" class="site-header">

    <div class="header-container">

        <a href="index.php#inicio" class="header-brand">

            <img
                src="img/Logo/logotipo.jpeg"
                alt="logotipo de Aroma a Café"
                class="header-isotipo"
            >


        </a>

        <nav class="header-navbar" id="header-navbar" aria-label="Menú principal">
            <ul class="header-menu" id="header-menu">
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
                <span class="nombre-usuario" style="margin-right: 15px; font-weight: 600; color: white;">
                    Hola, <?php echo htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
                
                <a href="logout.php" class="header-icon-btn" style="text-decoration: none; color: #f18aa2; font-weight: bold; margin-right: 15px;" aria-label="Cerrar sesión">
                    Cerrar sesión
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

             <!-- boton hamburguesa -->
            <button
    type="button"
    class="header-menu-toggle"
    id="header-menu-toggle"
    aria-expanded="false"
    aria-controls="header-menu"
    aria-label="Abrir menú"
>
    <img
        src="img/iconos/icon-menu.png"
        alt=""
        class="header-menu-icon"
    >
</button>

        </div>

    </div>

</header>