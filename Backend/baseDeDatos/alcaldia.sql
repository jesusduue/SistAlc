/* base de datos para gestionar el pago de impuesto de la alcaldia de g. de hevia
 */

CREATE DATABASE IF NOT EXISTS alcaldia charset utf8mb4;
 USE alcaldia;

 /* Tabla para gestionar los roles de los usuarios   */
  CREATE TABLE IF NOT EXISTS rol (
  id_rol int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- Identificador unico autoincremental de roles
  nom_rol varchar(50) NOT NULL ,                       -- Nombre del rol (Ej: Administrador, Jefe de Catastro, Jefe de Patentes, Tesorero, Secretaria de Catastro, Secretaria de Patente, Calculista)
  est_rol varchar(1) NOT NULL                      -- Estado del rol: A activo I inactivo
) ENGINE=InnoDB;

/* tabla para gestionar los usuarios del sistema */
CREATE TABLE IF NOT EXISTS usuario (
  id_usu int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico del usuario
  nom_usu varchar(50) NOT NULL,                    -- Nombre de usuario para login
  cla_usu varchar(255) NOT NULL,                    -- Clave de acceso (deberia ser campo cifrado en produccion)
  fky_rol int NOT NULL,                            -- Rol asignado (relacion con tabla rol)
  est_usu varchar(1) NOT NULL,                     -- Estado usuario: A activo, I inactivo
  FOREIGN KEY (fky_rol) REFERENCES rol(id_rol) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/* tabla para gestionar los contribuyentes y comercios que pagan impuestos */
CREATE TABLE IF NOT EXISTS contribuyente (
  id_con int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico del contribuyente
  nom_con varchar(50) NOT NULL,                    -- Nombre del contribuyente o comercio
  rif_con varchar(50) NOT NULL,                    -- Cedula o rif del contribuyente
  tel_con varchar(50) NOT NULL,                    -- Telefono del contribuyente o comercio
  ema_con varchar(50) NOT NULL,                  -- Email del contribuyente o comercio
  dir_con varchar(50) NOT NULL,                    -- Direccion del contribuyente o comercio
  fky_usu int NOT NULL,                            -- Usuario que realizo el registro
  est_con varchar(1) NOT NULL,                     -- Estado del contribuyente: A activo, I inactivo
  FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/* ////////////////////////////////////////////////////////////// */
/* TABLAS PARA GESTIONAR LA INFORMACION DE LOS IMPUESTOS Y LOS PAGOS DE LOS CONTRIBUYENTES */

/* tabla para gestionar los tipos de impuestos con valor y descripcion */
CREATE TABLE IF NOT EXISTS tipo_impuesto (
  id_tip int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico del tipo de impuesto
  nom_tip varchar(50) NOT NULL,                    -- Nombre del tipo de impuesto
  est_tip varchar(1) NOT NULL                     -- Estado del tipo de impuesto: A activo, I inactivo
) ENGINE=InnoDB;

/* tabla para gestionar las facturas de los contribuyentes */
CREATE TABLE IF NOT EXISTS factura (
  id_fac int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico de la factura
  num_fac int NOT NULL UNIQUE,                      -- Numero de la factura
  fec_fac date NOT NULL,                            -- Fecha de la factura
  fky_usu int NOT NULL,                            -- ID del usuario que realizo la factura
  fky_con int NOT NULL,                            -- ID del contribuyente (relacion con tabla contribuyente)
  des_fac varchar(100) NOT NULL,                   -- Descripcion de la factura
  tot_fac decimal(10,2) NOT NULL,                   -- Monto total de la factura  
  est_fac varchar(1) NOT NULL,                     -- Estado de la factura: A activo, I inactivo
  FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
 
/* tabla para gestionar el detalle de impuestos pagados por factura */
CREATE TABLE IF NOT EXISTS detalle_impuesto (
  id_det_imp int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico del detalle de impuesto
  fky_fac int NOT NULL,                            -- ID de la factura (relacion con tabla factura)
  fky_tip_imp int NOT NULL,                        -- ID del tipo de impuesto (relacion con tabla tipo_impuesto)
  mon_imp decimal(10,2) NOT NULL,                   -- Monto del impuesto pagado
  fec_imp date NOT NULL,                            -- Fecha del pago del impuesto
  est_det_imp varchar(1) NOT NULL,                 -- Estado del detalle: A activo, I inactivo
  FOREIGN KEY (fky_fac) REFERENCES factura(id_fac) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (fky_tip_imp) REFERENCES tipo_impuesto(id_tip) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

 /* ////////////////////////////////////////////////////////////// */
 
/* TABLAS PARA GESTIONAR LA INFORMACION DE LA CEDULA CATASTRAL Y SOLVENCIA MUNICIPAL */

/* tabla para gestionar informacion de los linderos en la cedula catastral */   
CREATE TABLE IF NOT EXISTS linderos (
    id_lin int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico del linderos
    lin_norte varchar(50) NOT NULL,                    -- informacion del lindero norte 
    lin_sur varchar(50) NOT NULL,                     -- informacion del lindero sur
    lin_este varchar(50) NOT NULL,                    -- informacion del lindero este
    lin_oeste varchar(50) NOT NULL,                   -- informacion del lindero oeste
    mt2_norte decimal(10,2) NOT NULL,                  -- metros cuadrados del lindero norte
    mt2_sur decimal(10,2) NOT NULL,                   -- metros cuadrados del lindero sur
    mt2_este decimal(10,2) NOT NULL,                  -- metros cuadrados del lindero este
    mt2_oeste decimal(10,2) NOT NULL,                 -- metros cuadrados del lindero oeste
    mt2_total decimal(10,2) NOT NULL,                 -- metros cuadrados totales del terreno
    est_lin varchar(1) NOT NULL                     -- Estado del linderos: A activo, I inactivo
) ENGINE=InnoDB;

/* tabla para gestionar informacion de los documentos legales en la cedula catastral */
CREATE TABLE IF NOT EXISTS documentos_legales (
    id_doc int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico del documento legal
    tip_doc ENUM('Registrado','Notariado', 'Juzgado') NOT NULL, -- Tipo de documento legal
    num_doc varchar(50) NOT NULL,                    -- Numero de documento legal
    matr_doc varchar(50) NOT NULL,                    -- Matricula del documento legal
    fol_doc varchar(50) NOT NULL,                    -- Folio del documento legal
    fec_doc date NOT NULL,                            -- Fecha del documento legal
    des_doc varchar(100) NOT NULL,                   -- Breve descripcion del documento legal
    est_doc varchar(1) NOT NULL                     -- Estado del documento legal: A activo, I inactivo
) ENGINE=InnoDB;

/* tabla para gestionar la informacion del inmueble en la cedula catastral */
CREATE TABLE IF NOT EXISTS inmueble (
    id_inm int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico del inmueble
    dir_inm varchar(50) NOT NULL,                    -- Direccion del inmueble
    tip_inm ENUM('Terreno', 'Casa', 'Local', 'Galpon') NOT NULL, -- Tipo de inmueble
    area_inm ENUM('Urbano', 'Rural') NOT NULL,                 -- Area del inmueble
    est_inm varchar(1) NOT NULL                     -- Estado del inmueble: A activo, I inactivo
) ENGINE=InnoDB;

/* Tabla para gestionar la informaciond de las cedulas catastrales */
CREATE TABLE IF NOT EXISTS cedula_catastral (
    id_ced int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico de la cedula catastral
    num_ced varchar(50) NOT NULL UNIQUE,    -- Numero de la cedula catastral
    num_exp varchar(50) NOT NULL,                    -- Numero de expediente (carpeta  en donde se encuentra los documentos del contribuyente)
    fky_con int NOT NULL,                            -- ID del contribuyente (relacion con tabla contribuyente)
    fky_inm int NOT NULL,                            -- ID del inmueble (relacion con tabla inmueble)
    fky_lin int NOT NULL,                            -- ID del linderos (relacion con tabla linderos)
    fky_doc int NOT NULL,                            -- ID del documento legal (relacion con tabla documentos_legales)
    val_ced decimal(10,2) NOT NULL,                 -- Valor total en Bs.D. del inmueble
    fec_exp_ced date NOT NULL,                      -- Fecha de expedicion de la cedula catastral(Se debe registrar la fecha actual al crear la cedula)
    vig_ced date NOT NULL,                          -- Fecha de vencimiento de la cedula catastral(se maneja por Trimestres 1,2,3,4 o semestral segun sea el caso)
    sol_ced varchar(250) NOT NULL,                   -- campo para guardar para que se solicito la cedula catastral como (Credito de Banco, Liberacion de Hipoteca, Compra o Venta de Inmueble, etc.)
    fky_usu int NOT NULL,                            -- ID del usuario que realizo el registro
    est_ced varchar(1) NOT NULL,                     -- Estado de la cedula catastral: 'S' solicitada, 'P' pagada, 'V' vencida
    est_tab_ced varchar(1) NOT NULL,                 -- Estado de la tabla de la cedula catastral: A activo, I inactivo
    FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,    -- Relacion con la tabla contribuyente
    FOREIGN KEY (fky_inm) REFERENCES inmueble(id_inm) ON DELETE RESTRICT ON UPDATE CASCADE,        -- Relacion con la tabla inmueble
    FOREIGN KEY (fky_lin) REFERENCES linderos(id_lin) ON DELETE RESTRICT ON UPDATE CASCADE,        -- Relacion con la tabla linderos
    FOREIGN KEY (fky_doc) REFERENCES documentos_legales(id_doc) ON DELETE RESTRICT ON UPDATE CASCADE, -- Relacion con la tabla documentos_legales
    FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE -- Relacion con la tabla usuario
) ENGINE=InnoDB;

/* Tabla para gestionar la informacion de la solvencia municipal */
CREATE TABLE IF NOT EXISTS solvencia_municipal (
    id_sol int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico de la solvencia municipal
    num_sol varchar(50) NOT NULL UNIQUE,    -- Numero de la solvencia municipal
    fky_con int NOT NULL,                            -- ID del contribuyente (relacion con tabla contribuyente)
    fky_ced int NOT NULL,                            -- ID de la cedula catastral (relacion con tabla cedula_catastral (se puede extraer datos como el numero de cedula catastral, direccion del inmueble, etc.))
    sol_sol varchar(250) NOT NULL,                   -- campo para guardar para que se solicito la solvencia municipal como (Credito de Banco, Liberacion de Hipoteca, Compra o Venta de Inmueble, etc.)
    fec_exp_sol date NOT NULL,                      -- Fecha de expedicion de la solvencia municipal(Se debe registrar la fecha actual al crear la solvencia)
    vig_sol date NOT NULL,                          -- Fecha de vencimiento de la solvencia municipal(se maneja por Trimestres 1,2,3,4 o semestral segun sea el caso)
    fky_usu int NOT NULL,                            -- ID del usuario que realizo el registro
    est_sol varchar(1) NOT NULL,                     -- Estado de la solvencia municipal: 'S' solicitada, 'P' pagada, 'V' vencida
    est_tab_sol varchar(1) NOT NULL,                 -- Estado de la tabla de la solvencia municipal: A activo, I inactivo
    FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,    -- Relacion con la tabla contribuyente
    FOREIGN KEY (fky_ced) REFERENCES cedula_catastral(id_ced) ON DELETE RESTRICT ON UPDATE CASCADE, -- Relacion con la tabla cedula_catastral
    FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE -- Relacion con la tabla usuario
) ENGINE=InnoDB;

/* ////////////////////////////////////////////////////////////// */

/* TABLAS PARA GESTIONAR LA INFORMACION DE LAS PATENTES DE INDUSTRIA Y COMERCIO
    DE LOS CONTRIBUYENTES POR EJERCER UNA ACTIVIDAD ECONOMICA  ASI COMO LAS SOLVENCIAS COMERCIALES 
     */

  /* tabla para gestionar la informacion de las patentes de industria y comercio */   
CREATE TABLE IF NOT EXISTS patente (
    id_pat int PRIMARY KEY NOT NULL AUTO_INCREMENT,  -- ID unico de la patente
    fec_pat date NOT NULL,                            -- Fecha de expedicion de la patente(Se debe registrar la fecha actual al crear la patente)
    num_pat varchar(50) NOT NULL UNIQUE,    -- Numero de la patente
    rif_pat varchar(50) NOT NULL,                    -- Cedula o rif del comercio si tiene o la del contribuyente que representa la patente
    nom_pat varchar(50) NOT NULL,                    -- Nombre de la patente o Razon social ej: "Empresa de Construccion S.A."
    fky_con int NOT NULL,                            -- ID del contribuyente (relacion con tabla contribuyente) para indicar el Representante Legal de la patente
    dir_pat varchar(50) NOT NULL,                    -- Direccion del comercio o del contribuyente que representa la patente
    rub_pat varchar(155) NOT NULL,                    -- Rubro de la actividad economica ej: "Construccion, Telecomunicaciones,Fabricas,Tiendas, etc."
    fky_usu int NOT NULL,                            -- ID del usuario que realizo el registro (validar que el usuario tenga el rol adecuado para hacer esta accion)
    est_pat varchar(1) NOT NULL,                     -- Estado de la patente: 'S' solicitada, 'P' pagada, 'V' vencida
    est_tab_pat varchar(1) NOT NULL,                 -- Estado de la tabla de la patente: A activo, I inactivo
    FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,    -- Relacion con la tabla contribuyente
    FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE -- Relacion con la tabla usuario
) ENGINE=InnoDB;

/* TABLA LICENCIA: Controla vigencia de licencias comerciales */
CREATE TABLE IF NOT EXISTS licencia(
  id_lic int PRIMARY KEY NOT NULL AUTO_INCREMENT,   -- ID unico de licencia
  fky_pat int NOT NULL,                            -- ID de la patente asociada (debe ser id_pat)
  vig_lic date NOT NULL,                           -- Fecha de vencimiento de la licencia
  est_lic varchar(1) NOT NULL,                     -- Estado ('S' solicitada, 'P' pagada, 'V' vencida)
  est_tab_lic varchar(1) NOT NULL,                 -- Estado de la tabla de la licencia: A activo, I inactivo
  FOREIGN KEY (fky_pat) REFERENCES patente(id_pat) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/* TABLA SOLVENCIA COMERCIAL: Controla vigencia de solvencias comerciales */
CREATE TABLE IF NOT EXISTS solvencia_comercial(
  id_sol_com int PRIMARY KEY NOT NULL AUTO_INCREMENT,   -- ID unico de solvencia comercial
  fky_pat int NOT NULL,                            -- ID de la patente asociada (debe ser id_pat)
  vig_sol_com date NOT NULL,                           -- Fecha de vencimiento de la solvencia comercial
  est_sol_com varchar(1) NOT NULL,                     -- Estado ('S' solicitada, 'P' pagada, 'V' vencida)
  est_tab_sol_com varchar(1) NOT NULL,                 -- Estado de la tabla de la solvencia comercial: A activo, I inactivo
  FOREIGN KEY (fky_pat) REFERENCES patente(id_pat) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;


/* ////////////////////////////////////////////////////////////// */
