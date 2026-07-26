-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-07-2026 a las 12:44:17
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
  `id_usuario_fk` int(11) DEFAULT NULL,
  `detalle_pedido` text DEFAULT NULL,
  `fecha_pedido` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `clave_retiro` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','entregado','vencido') NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario_fk`, `detalle_pedido`, `fecha_pedido`, `fecha_vencimiento`, `clave_retiro`, `total`, `estado`, `fecha_creacion`) VALUES
(1, 1, '3x Doble felicidad ($88)\n1x Combo Dulce ($132.6)', '2026-07-26', NULL, '358427', 396.60, '', '2026-07-26 10:17:23'),
(2, 1, '3x Doble felicidad ($88)\n1x Combo Dulce ($132.6)', '2026-07-26', NULL, '748080', 396.60, '', '2026-07-26 10:17:23'),
(3, 1, '2x Doble felicidad ($88)', '2026-07-26', NULL, '715835', 176.00, '', '2026-07-26 10:21:50'),
(4, 1, '3x Combo Dulce ($132.6)\n2x Café Mocha ($59)', '2026-07-26', NULL, '338416', 515.80, '', '2026-07-26 10:25:44'),
(5, 1, '1x Latte Macchiato ($49)', '2026-07-26', NULL, '293300', 49.00, '', '2026-07-26 10:34:07'),
(6, 1, '1x Doble felicidad ($88)', '2026-07-26', NULL, '881500', 88.00, '', '2026-07-26 10:35:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `id_categoria_fk` int(11) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen_url` varchar(100) DEFAULT NULL,
  `tiene_promocion` tinyint(1) DEFAULT 0,
  `etiqueta_promo` varchar(30) DEFAULT NULL,
  `precio_descuento` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `id_categoria_fk`, `precio`, `imagen_url`, `tiene_promocion`, `etiqueta_promo`, `precio_descuento`, `stock`) VALUES
(1, 'Blanco plano', 'Microespuma terciopelo vertida sobre un doble ristretto.', 1, 59.00, 'blanco_plano.jpg', 0, NULL, NULL, 0),
(2, 'Latte Macchiato', 'Leche manchada con un shot de espresso sedoso y una capa ligera de espuma.', 1, 49.00, 'latte_macchiato.jpg', 0, NULL, NULL, 0),
(3, 'Café Mocha', 'Perfecta armonía entre espresso, salsa de chocolate oscuro y leche vaporizada.', 1, 59.00, 'cafe_mocha.jpg', 0, NULL, NULL, 0),
(4, 'Cold Brew Clásico', 'Café extraído en frío por 18 horas, resultando en una bebida suave y de baja acidez.', 1, 89.00, 'cold_brew.jpg', 0, NULL, NULL, 0),
(5, 'Iced Americano', 'Doble shot de espresso vertido sobre agua fría y hielos, refrescante e intenso.', 2, 55.00, 'iced_americano.jpg', 0, NULL, NULL, 0),
(6, 'Iced Latte Vainilla', 'Espresso con leche fría, hielo y un toque exacto de jarabe artesanal de vainilla.', 2, 68.00, 'iced_latte_vainilla.jpg', 0, NULL, NULL, 0),
(7, 'Frappé de Oreo', 'Base cremosa licuada con hielo y galletas Oreo, decorado con crema batida y chocolate.', 2, 75.00, 'frappe_oreo.jpg', 0, NULL, NULL, 0),
(8, 'Espresso Tónica', 'Una combinación audaz de agua tónica premium, hielo y un shot de espresso flotando.', 2, 65.00, 'espresso_tonica.jpg', 0, NULL, NULL, 0),
(9, 'Pastel de Zanahoria', 'Bizcocho especiado con nuez y zanahoria rallada, cubierto de betún cremoso de queso de cabra.', 3, 78.00, 'pastel_zanahoria.jpg', 0, NULL, NULL, 0),
(10, 'Brownie Fudge', 'Bizcocho denso de chocolate semi-amargo con trozos de nuez pecana, crujiente por fuera.', 3, 52.00, 'brownie_fudge.jpg', 0, NULL, NULL, 0),
(11, 'Tartaleta de Frutas', 'Base de masa quebrada rellena de crema pastelera de vainilla, decorada con fresas y kiwi.', 3, 65.00, 'tartaleta_frutas.jpg', 0, NULL, NULL, 0),
(12, 'Tiramisú de la Casa', 'Soletas bañadas en café espresso y licor, intercaladas con crema de queso mascarpone.', 3, 85.00, 'tiramisu_casa.jpg', 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `telefono`, `password`, `fecha_registro`) VALUES
(1, 'ALBERTO', 'PROCOPIO', '7341291742', '$2y$10$rSJqGULG7qcpEhff7hoBLeYqCgcPf7bJkXpVb3Ztf4xMizzwpN3YS', '2026-07-26 06:56:43');

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
  ADD UNIQUE KEY `telefono` (`telefono`);

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
