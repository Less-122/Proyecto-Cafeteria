CREATE DATABASE cafe_db;
USE cafe_db;

CREATE TABLE categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL,
  descripcion VARCHAR(100) DEFAULT NULL
);

CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL,
  apellido VARCHAR(30) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
  fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE productos (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  id_categoria INT(11) DEFAULT NULL,
  precio DECIMAL(10,2) NOT NULL,
  imagen_url VARCHAR(255) DEFAULT NULL,
  tiene_promocion TINYINT(1) DEFAULT 0,
  etiqueta_promo VARCHAR(30) DEFAULT NULL,
  precio_descuento DECIMAL(10,2) DEFAULT NULL,
  CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria)
);

CREATE TABLE pedidos (
  id_pedido INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT(11) NOT NULL,
  fecha_vencimiento DATE DEFAULT NULL,
  clave_retiro VARCHAR(10) DEFAULT NULL,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('pendiente','entregado','vencido') NOT NULL DEFAULT 'pendiente',
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  CONSTRAINT fk_pedido_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
);

CREATE TABLE detalle_pedido (
  id_detalle_p INT AUTO_INCREMENT PRIMARY KEY,
  id_pedido INT(11) DEFAULT NULL,
  id_producto INT(11) DEFAULT NULL,
  cantidad INT(11) NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalle_pedido FOREIGN KEY (id_pedido) REFERENCES pedidos (id_pedido),
  CONSTRAINT fk_detalle_producto FOREIGN KEY (id_producto) REFERENCES productos (id_producto)
);

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'Leslie', 'Contreras', 'leslie@gmail.com', '$2y$10$MmOL.QLwajjHpF/lMafbzOP2DGDfvVmcbYSvDRmAaQRrq3cYAGX8u', 'cliente', '2026-07-29 05:06:43'),
(3, 'administrador', 'admin', 'admin@gmail.com', '$2y$10$BPUAEJQK7LbocXGOxHmGzOYmZXYl925/bZeG9pG4yxOA.wJOictwu', 'admin', '2026-07-30 00:40:49'),
(4, 'prueba', 'prueba', 'prueba@gmail.com', '$2y$10$u8I2F0NWqf9nPyv2a6M8OOI4MkK3K4CPCVMEmeCDr7UBfgAa/LF72', 'cliente', '2026-07-30 00:42:19');


-- 1. Restricciones para Usuarios
ALTER TABLE usuarios
ADD CONSTRAINT ck_usuario_nombre 
    CHECK (nombre REGEXP '^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$'),
ADD CONSTRAINT ck_usuario_apellido 
    CHECK (apellido REGEXP '^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$'),
ADD CONSTRAINT ck_usuario_correo 
    CHECK (correo REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$');

-- 2. Restricciones para Categorías
ALTER TABLE categorias
ADD CONSTRAINT ck_categoria_nombre 
    CHECK (nombre REGEXP '^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$');

-- 3. Restricciones para Productos
-- Permite letras, números, espacios y guiones para los nombres de los productos.
ALTER TABLE productos
ADD CONSTRAINT ck_producto_nombre 
    CHECK (nombre REGEXP '^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ -]+$'),
-- Valida que la URL o ruta de la imagen termine en extensiones web comunes.
ADD CONSTRAINT ck_producto_imagen 
    CHECK (imagen_url REGEXP '\\.(jpg|jpeg|png|gif|webp)$');

-- 4. Restricciones para Pedidos
-- Valida que la clave de retiro sea estrictamente alfanumérica y en mayúsculas (ej. 4 a 10 caracteres).
ALTER TABLE pedidos
ADD CONSTRAINT ck_pedido_clave 
    CHECK (clave_retiro REGEXP '^[A-Z0-9]{4,10}$');
    

-- 1. Restricciones para Productos
-- Asegura que ningún producto en el catálogo pueda tener un precio negativo.
ALTER TABLE productos
ADD CONSTRAINT chk_precio_positivo CHECK (precio >= 0);

-- 3. Restricciones para Pedidos
-- El total a pagar no puede ser negativo y el vencimiento debe ser posterior o igual a la fecha de creación.
ALTER TABLE pedidos
ADD CONSTRAINT chk_total_pedido_positivo CHECK (total >= 0),
ADD CONSTRAINT chk_fecha_creacion CHECK (fecha_vencimiento >= fecha_creacion);

-- 4. Restricciones para Detalles del Pedido
-- Un cliente no puede pedir "0" o cantidades negativas de un producto, y el precio guardado no puede ser negativo.
ALTER TABLE detalle_pedido
ADD CONSTRAINT chk_cantidad_valida CHECK (cantidad > 0),
ADD CONSTRAINT chk_precio_unitario_positivo CHECK (precio_unitario >= 0);