DROP DATABASE IF EXISTS ticketera;
CREATE DATABASE ticketera;
USE ticketera;
-- Tabla base
CREATE TABLE Usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(100) NOT NULL,
    fechaRegistro DATETIME NOT NULL,
    imagen VARCHAR(255)
);

-- Subtipos
CREATE TABLE Organizador (
    id INT PRIMARY KEY,
    aprobado BOOLEAN DEFAULT FALSE,
    fechaAprobacion DATETIME NULL,
    FOREIGN KEY (id) REFERENCES Usuario(id)
);

CREATE TABLE Cliente (
    id INT PRIMARY KEY,
    FOREIGN KEY (id) REFERENCES Usuario(id)
);

CREATE TABLE Admin (
    id INT PRIMARY KEY,
    FOREIGN KEY (id) REFERENCES Usuario(id)
);

-- Tabla de Categorías
CREATE TABLE Categoria (
    id INT PRIMARY KEY,
    descripcion VARCHAR(255) NOT NULL
);

-- Ejemplos de categorías
INSERT INTO Categoria (id, descripcion) VALUES (1, 'Conciertos');
INSERT INTO Categoria (id, descripcion) VALUES (2, 'Deportes');
INSERT INTO Categoria (id, descripcion) VALUES (3, 'Teatro');
INSERT INTO Categoria (id, descripcion) VALUES (4, 'Conferencias');
INSERT INTO Categoria (id, descripcion) VALUES (5, 'Festivales');

-- Tabla de Eventos
CREATE TABLE Evento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha DATETIME NOT NULL,
    lugar VARCHAR(255),
    precio FLOAT,
    cupo INT,
    estado VARCHAR(20) CHECK (estado IN ('activo', 'cancelado', 'finalizado')),
    organizador_id INT NOT NULL,
    categoria_id INT NOT NULL,
    FOREIGN KEY (organizador_id) REFERENCES Organizador(id),
    FOREIGN KEY (categoria_id) REFERENCES Categoria(id)
);

-- Tabla de Pagos
CREATE TABLE Pago (
    id INT PRIMARY KEY AUTO_INCREMENT,
    metodoPago VARCHAR(50),
    estado VARCHAR(20) CHECK (estado IN ('pendiente', 'completado', 'fallido')),
    fechaPago DATETIME,
    monto float
);

-- Tabla de Tickets
CREATE TABLE Ticket (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cantidad INT NOT NULL,
    totalPago FLOAT NOT NULL,
    fechaCompra DATETIME NOT NULL,
    cliente_id INT NOT NULL,
    evento_id INT NOT NULL,
    pago_id INT NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES Cliente(id),
    FOREIGN KEY (evento_id) REFERENCES Evento(id),
    FOREIGN KEY (pago_id) REFERENCES Pago(id)
);

-- Tabla de Imágenes de Eventos
CREATE TABLE EventoImagen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    evento_id INT NOT NULL,
    url_imagen VARCHAR(255) NOT NULL,
    FOREIGN KEY (evento_id) REFERENCES Evento(id)
);

-- Insertar usuario admin
INSERT INTO Usuario (nombre, email, contrasena, fechaRegistro) VALUES 
('hphp', 'admin@admin.com', 'Php.2025#', NOW());

-- Insertar en tabla Admin (asumiendo que el ID será 1)
INSERT INTO Admin (id) VALUES (1);

