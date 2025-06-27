-- schema.sql
CREATE DATABASE IF NOT EXISTS sipc2;
USE sipc2;

CREATE TABLE IF NOT EXISTS institucion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  codigo VARCHAR(50) NOT NULL UNIQUE,
  num_sectores INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS usuario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rut VARCHAR(12) NOT NULL UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  rol ENUM('admin','jefe_zona','operador') NOT NULL,
  institucion_id INT DEFAULT NULL,
  fecha_habilitacion DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (institucion_id) REFERENCES institucion(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS sector (
  id INT AUTO_INCREMENT PRIMARY KEY,
  institucion_id INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  codigo VARCHAR(50) NOT NULL,
  descripcion TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (institucion_id) REFERENCES institucion(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS delincuente (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rut VARCHAR(12) NOT NULL,
  apellidos_nombres VARCHAR(150) NOT NULL,
  apodo VARCHAR(50),
  domicilio VARCHAR(200),
  ultimo_lugar_visto VARCHAR(200),
  fono_fijo VARCHAR(20),
  celular VARCHAR(20),
  email VARCHAR(100),
  imagen VARCHAR(255),
  fecha_nacimiento DATE,
  delitos TEXT,
  estado ENUM('P','L','A') NOT NULL,
  latitud DECIMAL(10,7) DEFAULT NULL,
  longitud DECIMAL(10,7) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tipo_delito (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  descripcion TEXT
);

INSERT INTO tipo_delito (nombre, descripcion) VALUES
  ('Robo', 'Hurto o sustracción de bienes ajenos'),
  ('Asalto', 'Ataque violento contra personas o propiedades'),
  ('Homicidio', 'Crimen que resulta en la muerte de una persona');

CREATE TABLE IF NOT EXISTS delito (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(50) NOT NULL UNIQUE,
  descripcion TEXT NOT NULL,
  direccion VARCHAR(200),
  comuna VARCHAR(100),
  sector VARCHAR(100),
  fecha DATE,
  latitud DECIMAL(10,7) DEFAULT NULL,
  longitud DECIMAL(10,7) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE delito
  ADD COLUMN tipo_id INT DEFAULT NULL,
  ADD COLUMN delincuente_id INT DEFAULT NULL,
  ADD FOREIGN KEY (tipo_id) REFERENCES tipo_delito(id) ON DELETE SET NULL,
  ADD FOREIGN KEY (delincuente_id) REFERENCES delincuente(id) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS operador (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rut VARCHAR(12) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  apellidos_nombres VARCHAR(150) NOT NULL,
  institucion_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (institucion_id) REFERENCES institucion(id) ON DELETE SET NULL
);

# New table for police controls

CREATE TABLE IF NOT EXISTS control_policial (
  id INT AUTO_INCREMENT PRIMARY KEY,
  operador_id INT DEFAULT NULL,
  tipo ENUM('identidad','vehicular','armas_drogas','transito') NOT NULL,
  rut VARCHAR(12),
  nombre VARCHAR(150),
  motivo_desplazamiento TEXT,
  ubicacion VARCHAR(200),
  latitud DECIMAL(10,7) DEFAULT NULL,
  longitud DECIMAL(10,7) DEFAULT NULL,
  observacion TEXT,
  licencia_conducir VARCHAR(100),
  padron_vehiculo VARCHAR(100),
  revision_seguro VARCHAR(100),
  rut_conductor VARCHAR(12),
  nombre_conductor VARCHAR(150),
  pertenencias TEXT,
  permisos_arma TEXT,
  revision_mochila TEXT,
  test_alcoholemia VARCHAR(100),
  doc_vehicular TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (operador_id) REFERENCES operador(id) ON DELETE SET NULL
);
