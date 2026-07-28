-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-07-2026 a las 05:22:05
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cafe_db`
--

DELIMITER $$
--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `generar_clave_retiro` () RETURNS VARCHAR(10) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE chars VARCHAR(36) DEFAULT 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    DECLARE clave VARCHAR(10) DEFAULT '';
    DECLARE i INT DEFAULT 0;
    WHILE i < 6 DO
        SET clave = CONCAT(clave, SUBSTRING(chars, FLOOR(1 + RAND() * 36), 1));
        SET i = i + 1;
    END WHILE;
    RETURN clave;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'Bebidas Calientes', 'Selección de cafés clásicos.'),
(2, 'Bebidas Frías', 'Opciones refrescantes con hielo.'),
(3, 'Postres', 'Variedad de pasteles para acompañar las bebidas.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pedido`
--

CREATE TABLE `detalles_pedido` (
  `id_detalle_p` int(11) NOT NULL,
  `id_pedido_fk` int(11) DEFAULT NULL,
  `id_producto_fk` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario_fk` int(11) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `clave_retiro` varchar(10) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','entregado','vencido') NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Disparadores `pedidos`
--
DELIMITER $$
CREATE TRIGGER `before_insert_pedidos` BEFORE INSERT ON `pedidos` FOR EACH ROW BEGIN
    IF NEW.clave_retiro IS NULL THEN
        SET NEW.clave_retiro = generar_clave_retiro();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_categoria_fk` int(11) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `tiene_promocion` tinyint(1) DEFAULT 0,
  `etiqueta_promo` varchar(30) DEFAULT NULL,
  `precio_descuento` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `id_categoria_fk`, `precio`, `imagen_url`, `tiene_promocion`, `etiqueta_promo`, `precio_descuento`, `stock`) VALUES
(1, 'Blanco plano', 'Microespuma terciopelo vertida sobre un doble ristretto.', 1, 59.00, 'blanco_plano.jpg', 0, NULL, NULL, 10),
(2, 'Latte Macchiato', 'Leche manchada con un shot de espresso sedoso y una capa ligera de espuma.', 1, 49.00, 'latte_macchiato.jpg', 0, NULL, NULL, 15),
(3, 'Café Mocha', 'Perfecta armonía entre espresso, salsa de chocolate oscuro y leche vaporizada.', 1, 59.00, 'cafe_mocha.jpg', 0, NULL, NULL, 12),
(4, 'Cold Brew Clásico', 'Café extraído en frío por 18 horas, resultando en una bebida suave y de baja acidez.', 1, 89.00, 'cold_brew.jpg', 0, NULL, NULL, 8),
(5, 'Iced Americano', 'Doble shot de espresso vertido sobre agua fría y hielos, refrescante e intenso.', 2, 55.00, 'iced_americano.jpg', 0, NULL, NULL, 20),
(6, 'Iced Latte Vainilla', 'Espresso con leche fría, hielo y un toque exacto de jarabe artesanal de vainilla.', 2, 68.00, 'iced_latte_vainilla.jpg', 0, NULL, NULL, 18),
(7, 'Frappé de Oreo', 'Base cremosa licuada con hielo y galletas Oreo, decorado con crema batida y chocolate.', 2, 75.00, 'frappe_oreo.jpg', 0, NULL, NULL, 10),
(8, 'Espresso Tónica', 'Una combinación audaz de agua tónica premium, hielo y un shot de espresso flotando.', 2, 65.00, 'espresso_tonica.jpg', 0, NULL, NULL, 12),
(9, 'Pastel de Zanahoria', 'Bizcocho especiado con nuez y zanahoria rallada, cubierto de betún cremoso de queso de cabra.', 3, 78.00, 'pastel_zanahoria.jpg', 0, NULL, NULL, 5),
(10, 'Brownie Fudge', 'Bizcocho denso de chocolate semi-amargo con trozos de nuez pecana, crujiente por fuera.', 3, 52.00, 'brownie_fudge.jpg', 0, NULL, NULL, 8),
(11, 'Tartaleta de Frutas', 'Base de masa quebrada rellena de crema pastelera de vainilla, decorada con fresas y kiwi.', 3, 65.00, 'tartaleta_frutas.jpg', 0, NULL, NULL, 6),
(12, 'Tiramisú de la Casa', 'Soletas bañadas en café espresso y licor, intercaladas con crema de queso mascarpone.', 3, 85.00, 'tiramisu_casa.jpg', 0, NULL, NULL, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cliente') NOT NULL DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD PRIMARY KEY (`id_detalle_p`),
  ADD KEY `fk_detalle_pedido` (`id_pedido_fk`),
  ADD KEY `fk_detalle_producto` (`id_producto_fk`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `fk_pedido_usuario` (`id_usuario_fk`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `fk_producto_categoria` (`id_categoria_fk`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  MODIFY `id_detalle_p` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`id_pedido_fk`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto_fk`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`id_usuario_fk`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria_fk`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
