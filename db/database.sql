CREATE DATABASE IF NOT EXISTS clinica_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE clinica_erp;

-- 1. Tabla de Usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('Administrador', 'Recepcionista', 'Medico') NOT NULL,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla de Pacientes
CREATE TABLE pacientes (
    id_paciente INT AUTO_INCREMENT PRIMARY KEY,
    dni CHAR(8) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100),
    fecha_nacimiento DATE NOT NULL,
    sexo ENUM('M', 'F') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_dni (dni)
) ENGINE=InnoDB;

-- 3. Tabla de Médicos (Con columna CMP única e índice)
CREATE TABLE medicos (
    id_medico INT AUTO_INCREMENT PRIMARY KEY,
    dni CHAR(8) NOT NULL UNIQUE,
    cmp VARCHAR(6) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_especialidad (especialidad)
) ENGINE=InnoDB;

-- 4. Tabla de Citas Médicas
CREATE TABLE citas (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_medico INT NOT NULL,
    fecha_cita DATE NOT NULL,
    hora_cita TIME NOT NULL,
    motivo TEXT,
    estado ENUM('Pendiente', 'Atendido', 'Cancelado') DEFAULT 'Pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_medico) REFERENCES medicos(id_medico) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_fecha (fecha_cita)
) ENGINE=InnoDB;

-- 5. Tabla de Auditoría (Logs de Accesos y Cambios)
CREATE TABLE logs_sistema (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50) NULL,
    detalles TEXT NOT NULL,
    ip_direccion VARCHAR(45) NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Inserción de 5 registros por tabla (Contraseña por defecto de los usuarios: clinica2026 encriptada con MD5)
INSERT INTO usuarios (username, password, nombre, rol, estado) VALUES
('admin', MD5('clinica2026'), 'Administrador General', 'Administrador', 1),
('ana_recepcion', MD5('clinica2026'), 'Ana Gómez', 'Recepcionista', 1),
('carlos_med', MD5('clinica2026'), 'Dr. Carlos Mendoza', 'Medico', 1),
('luis_med', MD5('clinica2026'), 'Dr. Luis Torres', 'Medico', 1),
('maria_recepcion', MD5('clinica2026'), 'María Delgado', 'Recepcionista', 1);

INSERT INTO pacientes (dni, nombre, apellido, telefono, email, fecha_nacimiento, sexo) VALUES
('11111111', 'Juan', 'Pérez Quispe', '999888777', 'juan.perez@email.com', '1985-05-12', 'M'),
('22222222', 'María', 'Rodríguez Flores', '988777666', 'maria.rf@email.com', '1990-08-22', 'F'),
('33333333', 'Pedro', 'Castillo Diaz', '977666555', 'pedro.c@email.com', '1978-03-15', 'M'),
('44444444', 'Lucía', 'Fernández Vega', '966555444', 'lucia.fv@email.com', '2000-11-02', 'F'),
('55555555', 'Jorge', 'Sánchez Luna', '955444333', 'jorge.sl@email.com', '1965-12-30', 'M');

INSERT INTO medicos (dni, cmp, nombre, apellido, especialidad, telefono, email) VALUES
('66666666', '45892', 'Carlos', 'Mendoza Arce', 'Medicina General', '911222333', 'carlos.m@clinica.com'),
('77777777', '58124', 'Luis', 'Torres Prado', 'Pediatría', '922333444', 'luis.t@clinica.com'),
('88888888', '62415', 'Elena', 'Beltrán Rios', 'Ginecología', '933444555', 'elena.b@clinica.com'),
('99999999', '39518', 'Raúl', 'Villarán Saavedra', 'Cardiología', '944555666', 'raul.v@clinica.com'),
('10101010', '71243', 'Sofía', 'Guzmán Paz', 'Dermatología', '955666777', 'sofia.g@clinica.com');

INSERT INTO citas (id_paciente, id_medico, fecha_cita, hora_cita, motivo, estado) VALUES
(1, 1, CURDATE(), '09:00:00', 'Chequeo de rutina', 'Pendiente'),
(2, 2, CURDATE(), '10:30:00', 'Control pediátrico', 'Pendiente'),
(3, 4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:00:00', 'Dolor en el pecho', 'Pendiente'),
(4, 3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '11:00:00', 'Ecografía de control', 'Atendido'),
(5, 5, CURDATE(), '16:00:00', 'Alergía cutánea', 'Pendiente');