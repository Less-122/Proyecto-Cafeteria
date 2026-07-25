CREATE DATABASE IF NOT EXISTS cafe_db;
USE cafe_db;

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    descripcion VARCHAR(100)
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    apellido VARCHAR(30) NOT NULL,
    telefono VARCHAR(10) UNIQUE NOT NULL, 
    password VARCHAR(255) NOT NULL
);

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    descripcion VARCHAR(100), 
    id_categoria_fk INT,
    precio DECIMAL(10,2) NOT NULL,
    imagen_url VARCHAR(100),
    tiene_promocion BOOLEAN DEFAULT FALSE,
    etiqueta_promo VARCHAR(30) DEFAULT NULL,
	precio_descuento DECIMAL(10,2) DEFAULT NULL,
    CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria_fk) 
        REFERENCES categorias(id_categoria) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario_fk INT,
    fecha_pedido DATE NOT NULL,
    fecha_vencimiento DATE,
    clave_retiro VARCHAR(50),
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'entregado', 'vencido') NOT NULL DEFAULT 'pendiente',
    CONSTRAINT fk_pedido_usuario FOREIGN KEY (id_usuario_fk) 
        REFERENCES usuarios(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE detalles_pedido (
    id_detalle_p INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido_fk INT,
    id_producto_fk INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_pedido FOREIGN KEY (id_pedido_fk) 
        REFERENCES pedidos(id_pedido) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detalle_producto FOREIGN KEY (id_producto_fk) 
        REFERENCES productos(id_producto) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Inserción de registros
INSERT INTO categorias (nombre, descripcion) VALUES
('Bebidas Calientes', 'Selección de cafés clásicos.'),
('Bebidas Frías', 'Opciones refrescantes con hielo.'),
('Postres', 'Variedad de pasteles para acompañar las bebidas.');

-- Inserción de productos
INSERT INTO productos (nombre, descripcion, id_categoria_fk, precio, imagen_url, promocion) VALUES 
('Blanco plano', 'Microespuma terciopelo vertida sobre un doble ristretto.', 1, 59.00, 'blanco_plano.jpg', FALSE),
('Latte Macchiato', 'Leche manchada con un shot de espresso sedoso y una capa ligera de espuma.', 1, 49.00, 'latte_macchiato.jpg', FALSE),
('Café Mocha', 'Perfecta armonía entre espresso, salsa de chocolate oscuro y leche vaporizada.', 1, 59.00, 'cafe_mocha.jpg', FALSE),
('Cold Brew Clásico', 'Café extraído en frío por 18 horas, resultando en una bebida suave y de baja acidez.', 1, 89.00, 'cold_brew.jpg', FALSE),
('Iced Americano', 'Doble shot de espresso vertido sobre agua fría y hielos, refrescante e intenso.', 2, 55.00, 'iced_americano.jpg', FALSE),
('Iced Latte Vainilla', 'Espresso con leche fría, hielo y un toque exacto de jarabe artesanal de vainilla.', 2, 68.00, 'iced_latte_vainilla.jpg', FALSE),
('Frappé de Oreo', 'Base cremosa licuada con hielo y galletas Oreo, decorado con crema batida y chocolate.', 2, 75.00, 'frappe_oreo.jpg', FALSE),
('Espresso Tónica', 'Una combinación audaz de agua tónica premium, hielo y un shot de espresso flotando.', 2, 65.00, 'espresso_tonica.jpg', FALSE),
('Pastel de Zanahoria', 'Bizcocho especiado con nuez y zanahoria rallada, cubierto de betún cremoso de queso de cabra.', 3, 78.00, 'pastel_zanahoria.jpg', FALSE),
('Brownie Fudge', 'Bizcocho denso de chocolate semi-amargo con trozos de nuez pecana, crujiente por fuera.', 3, 52.00, 'brownie_fudge.jpg', FALSE),
('Tartaleta de Frutas', 'Base de masa quebrada rellena de crema pastelera de vainilla, decorada con fresas y kiwi.', 3, 65.00, 'tartaleta_frutas.jpg', FALSE),
('Tiramisú de la Casa', 'Soletas bañadas en café espresso y licor, intercaladas con crema de queso mascarpone.', 3, 85.00, 'tiramisu_casa.jpg', FALSE);


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
ADD CONSTRAINT chk_fechas_pedido CHECK (fecha_vencimiento >= fecha_pedido);

-- 4. Restricciones para Detalles del Pedido
-- Un cliente no puede pedir "0" o cantidades negativas de un producto, y el precio guardado no puede ser negativo.
ALTER TABLE detalles_pedido
ADD CONSTRAINT chk_cantidad_valida CHECK (cantidad > 0),
ADD CONSTRAINT chk_precio_unitario_positivo CHECK (precio_unitario >= 0);
