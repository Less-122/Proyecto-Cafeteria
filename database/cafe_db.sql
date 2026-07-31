-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-07-2026 a las 02:00:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'Bebidas frias', 'todas las bebidas que se sirven frias'),
(2, 'Bebidas calientes', 'Tazas elaboradas con una tecnica experta'),
(3, 'Postres', 'Date un gusto de la vida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle_p` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `clave_retiro` varchar(10) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','preparacion','listo','entregado','vencido') NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha_vencimiento`, `clave_retiro`, `total`, `estado`, `fecha_creacion`) VALUES
(1, 1, NULL, '379345', 308.60, 'listo', '2026-07-29 05:29:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `tiene_promocion` tinyint(1) DEFAULT 0,
  `etiqueta_promo` varchar(30) DEFAULT NULL,
  `precio_descuento` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `id_categoria`, `precio`, `imagen_url`, `tiene_promocion`, `etiqueta_promo`, `precio_descuento`) VALUES
(4, 'Primer sorbo', 'Espresso doble. El equilibrio perfecto entre intensidad y aroma. Un café corto, fuerte y lleno de carácter para comenzar el día con energía', 2, 59.00, 'Bebidas-calientes/primer-sorbo.jpeg', 0, NULL, NULL),
(5, 'Brisa de canela', 'Latte con canela. El equilibrio perfecto entre intensidad y aroma Un cafe corto, fuerte y lleno de carácter para comenzar el dia con energia', 2, 49.00, 'Bebidas-calientes/brisa-de-canela.jpeg', 0, NULL, NULL),
(6, 'Moka Dorado', 'Cafe mocha. La mezcla ideal entre cafe espresso y chocolate, coronada con espuma de leche para un sabor dulce e irresistible.', 2, 59.00, 'Bebidas-calientes/moka-dorado.jpeg', 0, NULL, NULL),
(7, 'Nube de vainilla', 'Latte de vainilla Leche vaporizada, espresso y vainilla natural que crean una bebida suave, cremosa y reconfortante', 2, 89.00, 'Bebidas-calientes/nube-de-vainilla.jpeg', 0, NULL, NULL),
(8, 'Caramelo tostado', 'Cappuccino de caramelo. Un cappuccino clásico con espuma ligera y un toque de caramelo que aporta dulzura en cada taza.', 2, 96.00, 'Bebidas-calientes/caramelo-tostado.jpeg', 0, NULL, NULL),
(9, 'Cacao cremoso', 'Chocolate caliente. Chocolate preparado con leche caliente y una textura cremosa que recuerda el sabor de casa', 2, 66.00, 'Bebidas-calientes/cacao-cremoso.jpeg', 0, NULL, NULL),
(10, 'Té del jardín', 'Te de manzanilla con miel. Una infusión ligera acompañada de miel natural, perfecta para disfrutar un momento de tranquilidad.', 2, 45.00, 'Bebidas-calientes/t-e-del-jard-in.jpeg', 0, NULL, NULL),
(11, 'Otoño', 'Café de olla tradicional. Cafe preparado al estilo tradicional con piloncillo y canela, lleno de aromas que evocan los sabores de México', 2, 79.00, 'Bebidas-calientes/oto-no.jpeg', 0, NULL, NULL),
(12, 'Bosque púrpura', 'Mezcla de frutos naturales con una textura ligera y un sabor fresco lleno de color', 1, 55.00, 'Bebidas-frias/bosque-p-urpura.jpeg', 0, NULL, NULL),
(13, 'Brisa fría', 'Cold Brew. Café preparado lentamente en frio durante varias horas para lograr un sabor suave, refrescante y de baja acidez', 1, 68.00, 'Bebidas-frias/brisa-fr-ia.jpeg', 0, NULL, NULL),
(14, 'Nube helada', 'Frappe de vainilla. Bebida cremosa con hielo triturado y un delicado sabor a vainilla que refresca cada momento', 1, 75.00, 'Bebidas-frias/nube-helada.jpeg', 0, NULL, NULL),
(15, 'Moka ice', 'Frappe moka. Café, chocolate y hielo mezclados en una bebida cremosa ideal para los amantes del moka.', 1, 65.00, 'Bebidas-frias/moka-ice.jpeg', 0, NULL, NULL),
(16, 'Dulce felicidad', 'Frappé de caramelo. Una combinación de café, leche y caramelo con una textura suave y un dulzor perfectamente equilibrado.', 1, 88.00, 'Bebidas-frias/dulce-felicidad.jpeg', 1, '', NULL),
(17, 'Tropical fresh', 'Te helado de durazno. Te frio con un delicado toque de durazno que ofrece una bebida ligera y muy refrescante.', 1, 68.00, 'Bebidas-frias/tropical-fresh.jpeg', 0, NULL, NULL),
(18, 'Espuma Helada', 'Iced Latte, Espresso servido sobre hielo con leche fria, logrando una bebida cremosa y refrescante', 1, 96.00, 'Bebidas-frias/espuma-helada.jpeg', 0, NULL, NULL),
(19, 'Café Nevado', 'Cafe frío con helado. Cafe helado acompañado de una bola de helado que aporta suavidad y un toque especial en cada sorbo', 1, 99.00, 'Bebidas-frias/caf-e-nevado.jpeg', 0, NULL, NULL),
(20, 'Dulce Tentación', 'Brownie de chocolate. Brownie recién horneado con un intenso sabor a chocolate y una textura suave por dentro.', 3, 78.00, 'Postres/dulce-tentaci-on.jpeg', 0, NULL, NULL),
(21, 'Momento dulce', 'Cheesecake de frutos rojos. Suave pastel de queso acompañado de una deliciosa salsa de frutos rojos que equilibra cada bocado', 3, 69.00, 'Postres/momento-dulce.jpeg', 0, NULL, NULL),
(22, 'Delicia de Zanahoria', 'Pastel de zanahoria. Esponjoso pastel elaborado con zanahoria y especias, cubierto con un cremoso. betun de queso.', 3, 65.00, 'Postres/delicia-de-zanahoria.jpeg', 0, NULL, NULL),
(23, 'Nuez Dorada', 'Muffin de nuez Muffin suave con trozos de nuez que aportan un ligero toque crujiente y un sabor casero.', 3, 77.00, 'Postres/nuez-dorada.jpeg', 0, NULL, NULL),
(24, 'Deditos Dorados', 'Churros tradicionales Delicados churros recien preparados. espolvoreados con azúcar y canela, perfectos para acompañar una taza de café o chocolate caliente.', 3, 48.00, 'Postres/deditos-dorados.png', 0, NULL, NULL),
(25, 'Sueño de Chocolate', 'Helado de chocolate Cremoso helado de chocolate elaborado con cacao de alta calidad, de textura suave y un sabor intenso que conquista desde la primera cucharada.', 3, 62.00, 'Postres/sue-no-de-chocolate.jpeg', 0, NULL, NULL),
(26, 'Dulce Recuerdo', 'Arroz con leche. Un postre tradicional preparado con arroz, leche y un toque de canela, cuya zeceta casera evoca los sabores y recuerdos de hogar', 3, 55.00, 'Postres/dulce-recuerdo.jpeg', 0, NULL, NULL),
(27, 'Cielo de Chocolate', 'Pastel de chocolate Capas de bizcocho de chocolate cubiertas con una cremosa ganache que conquista desde el primer hocado', 3, 87.00, 'Postres/cielo-de-chocolate.jpeg', 0, NULL, NULL);

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
  `rol` enum('admin','cliente','barista') NOT NULL DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'Leslie', 'Contreras', 'leslie@gmail.com', '$2y$10$MmOL.QLwajjHpF/lMafbzOP2DGDfvVmcbYSvDRmAaQRrq3cYAGX8u', 'cliente', '2026-07-29 05:06:43'),
(3, 'administrador', 'admin', 'admin@gmail.com', '$2y$10$BPUAEJQK7LbocXGOxHmGzOYmZXYl925/bZeG9pG4yxOA.wJOictwu', 'admin', '2026-07-30 00:40:49'),
(4, 'prueba', 'prueba', 'prueba@gmail.com', '$2y$10$u8I2F0NWqf9nPyv2a6M8OOI4MkK3K4CPCVMEmeCDr7UBfgAa/LF72', 'cliente', '2026-07-30 00:42:19'),
(5, 'Regina', 'Leon', 'reginaleon@gmail.com', '$2y$10$2G54qylgyTFExXdaKCWWi.nYjbnmVDnBsqaCR40GPWINdy7A6VoQq', 'cliente', '2026-07-30 17:30:55'),
(6, 'Nicole', 'Velazquez', 'nicole@gmail.com', '$2y$10$cmI0Q61JOlROpJGpjcBz2OYPONfikeSYTDlcj2.FUK3upk3fmt3n2', 'cliente', '2026-07-30 18:49:47');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle_p`),
  ADD KEY `fk_detalle_pedido` (`id_pedido`),
  ADD KEY `fk_detalle_producto` (`id_producto`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `fk_pedido_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `fk_producto_categoria` (`id_categoria`);

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
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle_p` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
