<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aroma a Café - Tu Carrito</title>
    <script src="js/carrito.js"></script>
    
    <link rel="stylesheet" href="css/header-menu.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/carrito.css">
</head>

<body>
    <?php include("includes/header-menu.php"); ?>

    <!-- Contenedor del Carrito -->
    <main class="cart-main-container">
        <h1 class="cart-title">Tu Pedido </h1>
        <p class="cart-subtitle">Revisa tus productos elegidos antes de confirmar </p>

        <div class="cart-content">
            <!-- Lista de productos agregados -->
            <section class="cart-items-section">
            
            </section>
            <aside class="cart-summary-section">
                <div class="summary-card">
                    <h2>Resumen del Pedido</h2>
                    <hr class="summary-divider">
                    <div class="summary-row">
                        <span>Subtotal (3 productos)</span>
                        <span>$297.00</span>
                    </div>
                    <div class="summary-row discount">
                        <span>Descuento aplicado</span>
                        <span>-$0.00</span>
                    </div>
                    <hr class="summary-divider">
                    <div class="summary-row total">
                        <span>Total estimado</span>
                        <span>$297.00</span>
                    </div>
                    <p class="summary-note">* Recuerda que tu pedido se pagará directamente en la sucursal física.</p>
                    <button class="btn-confirm-order">Confirmar Pedido</button>
                    <a href="#" class="keep-shopping">← Ver menú</a>
                </div>
            </aside>
        </div>
    </main>

</body>
<?php include("includes/footer.php"); ?>

    <script src="js/carrito.js"></script>
    <script src="js/menu.js"></script> </body>
</html>