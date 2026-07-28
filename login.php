<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="css/header-menu.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Registro | Aroma a Café</title>

    <link rel="icon" type="image/jpeg" href="img/Logo/isotipo.jpg">
</head>

<body>
    <?php include("includes/header-menu.php"); ?>
    
    <main class="login-contenido">
    <div class = "container-login">
        <div class="form-container">
            <div class="form" id="form">
                <!-- FORMULARIO DE INICIO DE SESIÓN -->
<form class="login" action="controlador\Autenticacion.php" method="POST">
    <h1 class="txt-titulo">Bienvenid@ </h1>

    <input type="hidden" name="operacion" value="login">

    <div class="input">
        <input type="correo" name="correo" id="login_correo" placeholder="Ingrese su correo" required>
    </div>
    <div class="input">
        <input type="password" name="password" id="login_password" placeholder="Ingrese su contraseña" required>
    </div>
    <div class="buttons">
        <button type="submit" id="btnIniciarSesion">Iniciar sesión</button>
    </div>
    <p class="cuenta">¿No tiene una cuenta? <a href="#" id="sign-up">Crear cuenta</a></p>
</form>

<!-- FORMULARIO DE REGISTRO -->
<form class="registro" action="controlador\Autenticacion.php" method="POST">
    <h2 class="txt-titulo">Crear cuenta</h2>
    <p>Usa tu correo para registrarte</p>

    <input type="hidden" name="operacion" value="registro">

    <div class="input">
        <input type="text" name="nombre" id="reg_nombre" placeholder="Ingrese su nombre" required>
        <input type="text" name="apellido" id="reg_apellido" placeholder="Ingrese su apellido" required>
    </div>
    <div class="input">
        <input type="correo" name="correo" id="reg_correo" placeholder="Ingrese su correo" required>
    </div>
    <div class="input">
        <input type="password" name="password" id="reg_password" placeholder="Ingrese su contraseña" required>
    </div>
    <div class="buttons">
        <button type="submit">Registrar</button>
    </div>
    <p class="cuenta">¿Ya tiene una cuenta? <a href="#" id="sign-in">Iniciar sesión</a></p>
</form>
            </div>

            <div class="banner">
                <div class="logo_container">
                    <img src="img/Logo/Imagotipo.png" alt="Aroma a Café Logo">
                </div>
            </div>
            
        </div>
    </main>
  
    <script src="js/login.js"></script>
    <script src="js/menu.js"></script>
</body>
</html>