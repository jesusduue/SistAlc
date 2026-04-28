
CREATE DATABASE IF NOT EXISTS alcaldiaV2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alcaldiaV2;

/*
================================================================================
    SECCIÓN 1: GESTIÓN DE ACCESOS Y ENTIDADES BÁSICAS
================================================================================
*/

/* Tabla para gestionar los roles de los usuarios */
CREATE TABLE IF NOT EXISTS rol (
  id_rol INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nom_rol VARCHAR(50) NOT NULL UNIQUE,          -- Nombre del rol (Ej: Administrador, Tesorero)
  est_registro CHAR(1) NOT NULL DEFAULT 'A'     -- Estado del registro: 'A' activo, 'I' inactivo
) ENGINE=InnoDB;

/* Tabla para gestionar los usuarios del sistema */
CREATE TABLE IF NOT EXISTS usuario (
  id_usu INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nom_usu VARCHAR(50) NOT NULL UNIQUE,
  cla_usu VARCHAR(255) NOT NULL,                -- Clave de acceso (debe ser cifrada con hash en producción)
  fky_rol INT NOT NULL,
  est_registro CHAR(1) NOT NULL DEFAULT 'A',    -- Estado del registro: 'A' activo, 'I' inactivo
  FOREIGN KEY (fky_rol) REFERENCES rol(id_rol) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Tabla para gestionar los contribuyentes y comercios que pagan impuestos */
CREATE TABLE IF NOT EXISTS contribuyente (
  id_con INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nom_con VARCHAR(100) NOT NULL,                -- Nombre del contribuyente o razón social
  rif_con VARCHAR(15) NOT NULL UNIQUE,          -- Cédula o RIF del contribuyente
  tel_con VARCHAR(20) NOT NULL,
  ema_con VARCHAR(100) NOT NULL UNIQUE,
  dir_con VARCHAR(255) NOT NULL,
  fky_usu_registro INT NOT NULL,                -- Usuario que realizó el registro
  est_registro CHAR(1) NOT NULL DEFAULT 'A',    -- Estado del registro: 'A' activo, 'I' inactivo
  FOREIGN KEY (fky_usu_registro) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/*
================================================================================
    SECCIÓN 2: GESTIÓN DE IMPUESTOS Y FACTURACIÓN
================================================================================
*/

/* Tabla para gestionar los tipos de impuestos con valor y descripción */
CREATE TABLE IF NOT EXISTS tipo_impuesto (
  id_tip INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nom_tip VARCHAR(100) NOT NULL UNIQUE,
  des_tip VARCHAR(255) NULL,                    -- Descripción opcional del impuesto
  est_registro CHAR(1) NOT NULL DEFAULT 'A'     -- Estado del registro: 'A' activo, 'I' inactivo
) ENGINE=InnoDB;

/* Tabla para gestionar las facturas de los contribuyentes */
CREATE TABLE IF NOT EXISTS factura (
  id_fac INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  num_fac VARCHAR(20) NOT NULL UNIQUE,          -- Número de la factura/control (VARCHAR para flexibilidad)
  fec_fac DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Fecha de emisión
  fky_usu INT NOT NULL,
  fky_con INT NOT NULL,
  des_fac VARCHAR(255) NOT NULL,                -- Descripción general (Ej: "Pago de impuestos municipales")
  tot_fac DECIMAL(16,2) NOT NULL,
  est_pago CHAR(1) NOT NULL DEFAULT 'E',        -- Estado del pago: 'E' En espera, 'P' Pagada, 'A' Anulada
  fec_anulacion DATETIME NULL,               -- Fecha de anulación (si aplica)
  est_registro CHAR(1) NOT NULL DEFAULT 'A',    -- Estado del registro: 'A' activo, 'I' inactivo (para borrado lógico)
  FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
  
 /*
================================================================================
    SECCIÓN 3: GESTIÓN DE CATASTRO
================================================================================
*/

CREATE TABLE IF NOT EXISTS linderos (
    id_lin INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    lin_norte VARCHAR(100) NOT NULL, lin_sur VARCHAR(100) NOT NULL,
    lin_este VARCHAR(100) NOT NULL,  lin_oeste VARCHAR(100) NOT NULL,
    mt2_norte DECIMAL(10,2) NOT NULL, mt2_sur DECIMAL(10,2) NOT NULL,
    mt2_este DECIMAL(10,2) NOT NULL,  mt2_oeste DECIMAL(10,2) NOT NULL,
    mt2_total DECIMAL(10,2) NOT NULL,
    est_registro CHAR(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS documentos_legales (
    id_doc INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    tip_doc ENUM('Registrado','Notariado', 'Juzgado') NOT NULL,
    num_doc VARCHAR(50) NOT NULL,   matr_doc VARCHAR(50) NOT NULL,
    fol_doc VARCHAR(50) NOT NULL,   fec_doc DATE NOT NULL,
    des_doc VARCHAR(255) NOT NULL,
    est_registro CHAR(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inmueble (
    id_inm INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    dir_inm VARCHAR(255) NOT NULL,
    tip_inm ENUM('Terreno', 'Casa', 'Local', 'Galpon') NOT NULL,
    area_inm ENUM('Urbano', 'Rural') NOT NULL,
    est_registro CHAR(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cedula_catastral (
    id_ced INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    num_ced VARCHAR(50) NOT NULL UNIQUE,
    num_exp VARCHAR(50) NOT NULL,
    fky_con INT NOT NULL,       fky_inm INT NOT NULL,
    fky_lin INT NOT NULL,       fky_doc INT NOT NULL,
    val_ced DECIMAL(16,2) NOT NULL, -- Valor del inmueble
    fec_exp_ced DATE NOT NULL,
    vig_ced DATE NOT NULL,      -- Fecha de vencimiento
    sol_ced VARCHAR(255) NOT NULL,  -- Motivo de la solicitud
    fky_usu INT NOT NULL,
    est_ced CHAR(1) NOT NULL DEFAULT 'S', -- Estado del ciclo de vida: 'S' solicitada, 'P' pagada, 'V' vencida
    est_registro CHAR(1) NOT NULL DEFAULT 'A',
    FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fky_inm) REFERENCES inmueble(id_inm) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fky_lin) REFERENCES linderos(id_lin) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fky_doc) REFERENCES documentos_legales(id_doc) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS solvencia_municipal (
    id_sol INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    num_sol VARCHAR(50) NOT NULL UNIQUE,
    fky_con INT NOT NULL,       fky_ced INT NOT NULL,
    sol_sol VARCHAR(255) NOT NULL,
    fec_exp_sol DATE NOT NULL,
    vig_sol DATE NOT NULL,
    fky_usu INT NOT NULL,
    est_sol CHAR(1) NOT NULL DEFAULT 'S', -- Estado del ciclo de vida: 'S' solicitada, 'P' pagada, 'V' vencida
    est_registro CHAR(1) NOT NULL DEFAULT 'A',
    FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fky_ced) REFERENCES cedula_catastral(id_ced) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/*
================================================================================
    SECCIÓN 4: GESTIÓN DE PATENTES Y LICENCIAS COMERCIALES
================================================================================
*/

CREATE TABLE IF NOT EXISTS patente (
    id_pat INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    fec_pat DATE NOT NULL,          -- Fecha de registro de la patente
    num_pat VARCHAR(50) NOT NULL UNIQUE,
    rif_pat VARCHAR(15) NOT NULL UNIQUE,
    nom_pat VARCHAR(150) NOT NULL,  -- Nombre de la patente o Razón social
    fky_con INT NOT NULL,           -- Representante Legal (contribuyente)
    dir_pat VARCHAR(255) NOT NULL,
    rub_pat VARCHAR(255) NOT NULL,
    fky_usu INT NOT NULL,
    est_pat CHAR(1) NOT NULL DEFAULT 'A', -- Estado de la patente: 'A' Activa, 'I' Inactiva/Clausurada
    est_registro CHAR(1) NOT NULL DEFAULT 'A',
    FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS licencia (
  id_lic INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  num_lic VARCHAR(50) NOT NULL UNIQUE,          -- Número de licencia para impresión
  fky_pat INT NOT NULL,
  fky_fac INT NOT NULL,                         -- Factura asociada al pago de la licencia
  fec_exp_lic DATE NOT NULL,                    -- Fecha de expedición
  vig_lic DATE NOT NULL,                        -- Fecha de vencimiento
  est_lic CHAR(1) NOT NULL DEFAULT 'S',         -- Estado: 'S' solicitada, 'P' pagada, 'V' vencida
  est_registro CHAR(1) NOT NULL DEFAULT 'A',
  FOREIGN KEY (fky_pat) REFERENCES patente(id_pat) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (fky_fac) REFERENCES factura(id_fac) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS solvencia_comercial (
  id_sol_com INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  num_sol_com VARCHAR(50) NOT NULL UNIQUE,      -- Número de solvencia para impresión
  fky_pat INT NOT NULL,
  fec_exp_sol_com DATE NOT NULL,                -- Fecha de expedición
  vig_sol_com DATE NOT NULL,                    -- Fecha de vencimiento
  est_sol_com CHAR(1) NOT NULL DEFAULT 'S',     -- Estado: 'S' solicitada, 'P' pagada, 'V' vencida
  est_registro CHAR(1) NOT NULL DEFAULT 'A',
  FOREIGN KEY (fky_pat) REFERENCES patente(id_pat) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
 


/* Tabla para gestionar el detalle de impuestos pagados por factura (CONECTOR CENTRAL) */
CREATE TABLE IF NOT EXISTS detalle_impuesto (
  id_det_imp INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  fky_fac INT NOT NULL,
  fky_tip_imp INT NOT NULL,
  mon_imp DECIMAL(16,2) NOT NULL,
  -- Claves foráneas opcionales para vincular el pago a un documento específico
  fky_ced INT NULL,                             -- ID de la cédula catastral que se paga
  fky_sol_mun INT NULL,                         -- ID de la solvencia municipal que se paga
  fky_lic INT NULL,                             -- ID de la licencia que se paga
  fky_sol_com INT NULL,                         -- ID de la solvencia comercial que se paga
  est_registro CHAR(1) NOT NULL DEFAULT 'A',
  FOREIGN KEY (fky_fac) REFERENCES factura(id_fac) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (fky_tip_imp) REFERENCES tipo_impuesto(id_tip) ON DELETE RESTRICT ON UPDATE CASCADE,
  -- Constraints para asegurar que el detalle se asocia a la entidad correcta
  FOREIGN KEY (fky_ced) REFERENCES cedula_catastral(id_ced) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (fky_sol_mun) REFERENCES solvencia_municipal(id_sol) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (fky_lic) REFERENCES licencia(id_lic) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (fky_sol_com) REFERENCES solvencia_comercial(id_sol_com) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

/* 
================================================================================
  SECCION 5: PATENTE DE VEHICULOS FISCALES
  Importante: La patente de vehiculos fiscales es una patente municipal que se paga anualmente
  agregar esta tabla antes de la tabla de impuestos para que se relacione con la tabla de contribuyentes
  y evitar errores de clave foránea
================================================================================
*/

CREATE TABLE vehiculo_fiscal (
  id_veh_fis INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  fky_con INT NOT NULL comment 'clave foránea de la tabla contribuyente',
  pla_veh VARCHAR(20) NOT NULL comment 'placa del vehiculo',
  mar_veh VARCHAR(50) NOT NULL comment 'marca del vehiculo',
  mod_veh VARCHAR(50) NOT NULL comment 'modelo del vehiculo',
  año_veh INT NOT NULL comment 'año del vehiculo',
  col_veh VARCHAR(50)  NULL comment 'color del vehiculo',
  tip_veh VARCHAR(50) NOT NULL comment 'tipo del vehiculo (automovil particular, motocicleta, transporte publico, camioneta, gandola, chuto etc)',
  cap_car_veh VARCHAR(10) NULL comment 'capacidad de carga del vehiculo vehiculo',
  num_ser_veh VARCHAR(50) NULL comment 'numero serial del vehiculo',
  est_veh CHAR(1) NOT NULL DEFAULT 'p' comment 'estado del vehiculo: p: propio, t: traspaso, e: en venta',
  est_registro CHAR(1) NOT NULL DEFAULT 'a' comment 'estado del registro: A: Activo, I: Inactivo',
  FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE patente_vehiculo_fiscal (
    id_pat_vf INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    num_pat_vf VARCHAR(20) NOT NULL UNIQUE comment 'numero de factura de la patente de vehiculo fiscal',
    fky_veh_fis INT NOT NULL comment 'clave foránea de la tabla vehiculo_fiscal',
    fec_emi_pat DATE NOT NULL comment 'fecha de emisión',
    fec_ven_pat DATE NOT NULL comment 'fecha de vencimiento',
    mon_pat DECIMAL(16,2) NOT NULL comment 'monto de la patente',
    est_pat CHAR(1) NOT NULL DEFAULT 'e' comment 'estado de la patente: e: emitida, v: vencida, a: anulada',
    est_registro CHAR(1) NOT NULL DEFAULT 'a' comment 'estado del registro: a: activo, i: inactivo',
    fky_usu INT NOT NULL comment 'clave foránea de la tabla usuario',
    FOREIGN KEY (fky_veh_fis) REFERENCES vehiculo_fiscal(id_veh_fis) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/*
================================================================================
    SECCIÓN 6: AUDITORÍA Y REGISTRO DE ACTIVIDADES
================================================================================
*/

CREATE TABLE IF NOT EXISTS log_actividad (
  id_log        INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  fec_log       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fky_usu       INT NOT NULL,
  nom_usu       VARCHAR(50) NOT NULL,
  modulo        VARCHAR(50) NOT NULL,
  accion        VARCHAR(50) NOT NULL,
  detalle       VARCHAR(255) NOT NULL,
  entidad_tipo  VARCHAR(50)  NULL,
  entidad_id    INT          NULL,
  metadata      JSON         NULL,
  ip            VARCHAR(45)  NULL,
  user_agent    VARCHAR(255) NULL,
  est_log       CHAR(1)      NOT NULL DEFAULT 'A',
  CONSTRAINT fk_log_actividad_usuario FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/*
================================================================================
    SECCIÓN 7: AUTOMATIZACIÓN DE ESTADOS DE VENCIMIENTO
    IMPORTANTE: El Event Scheduler de MySQL debe estar activado.
    Ejecutar: SET GLOBAL event_scheduler = ON;
================================================================================
*/

DELIMITER $$
CREATE EVENT IF NOT EXISTS `actualizar_estados_vencidos`
ON SCHEDULE EVERY 1 DAY
STARTS TIMESTAMP(CURRENT_DATE, '01:00:00') -- Se ejecuta todos los días a la 1 AM
DO
BEGIN
  -- Actualizar Licencias Vencidas
  UPDATE licencia SET est_lic = 'V' WHERE vig_lic < CURDATE() AND est_lic = 'P';
  
  -- Actualizar Solvencias Comerciales Vencidas
  UPDATE solvencia_comercial SET est_sol_com = 'V' WHERE vig_sol_com < CURDATE() AND est_sol_com = 'P';
  
  -- Actualizar Cédulas Catastrales Vencidas
  UPDATE cedula_catastral SET est_ced = 'V' WHERE vig_ced < CURDATE() AND est_ced = 'P';
  
  -- Actualizar Solvencias Municipales Vencidas
  UPDATE solvencia_municipal SET est_sol = 'V' WHERE vig_sol < CURDATE() AND est_sol = 'P';
END$$
DELIMITER ;