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
                <form class="login">
                    <h1 class="txt-titulo">Bienvenid@ </h1>
                    <div class="input">
                        <input type="tel" name="telefono" id="telefono" placeholder="Ingrese su numero" required>
                    </div>
                    <div class="input">
                        <input type="password" name="password" id="password" placeholder="Ingrese su contraseña" required>
                    </div>
                    <div class="buttons">
                        <button id="btnIniciarSesion">Iniciar sesión</button>
                    </div>
                        <p class="cuenta">¿No tiene una cuenta? <a href="#" id="sign-up">Crear cuenta</a></p>
                </form>
                <form class="registro">
                    <h2 class="txt-titulo">Crear cuenta</h2>
                    <p>Usa tu numero de telefono para registrarte</p>
                    <div class="input">
                        <input type="text" name="nombre" id="nombre" placeholder="Ingrese su nombre" required>
                        <input type="text" name="apeliido" id="nombre" placeholder="Ingrese su apellidoombre" required>
                    </div>
                    <div class="input">
                        <input type="tel" name="telefono" id="telefono" placeholder="Ingrese su numero" required>
                    </div>
                    <div class="input">
                        <input type="password" name="password" id="password" placeholder="Ingrese su contraseña" required>
                    </div>
                    <div class="buttons">
                        <button>Registrar</button>
                    </div>
                        <p class="cuenta">¿Ya tiene una cuenta? <a href="#" id="sign-in">Iniciar sesión</a></p>
                </form>
            </div>
        </div>

            <div class="banner">
                <div class="logo_container">
                    <img src="img/Logo/Imagotipo.png" alt="">
                </div>
            </div>
            
    </div>
</main>
  
    <script src="js/login.js"></script>
    <script src="js/menu.js"></script>
</body>
</html>