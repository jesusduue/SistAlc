/* Datos de prueba para la tabla rol */
-- Ajustes de esquema requeridos por los nuevos modulos
ALTER TABLE factura
    ADD COLUMN IF NOT EXISTS fec_anulacion DATETIME NULL AFTER est_pago;

ALTER TABLE licencia
    ADD COLUMN IF NOT EXISTS fky_fac INT NULL AFTER fky_pat;

-- Insertar un usuario 'admin' con clave 'Admin123' encriptada

/* Datos de prueba para la tabla rol */
INSERT INTO rol (nom_rol, est_registro) VALUES ('Administrador', 'A');
INSERT INTO rol (nom_rol, est_registro) VALUES ('Jefe de Catastro', 'A');
INSERT INTO rol (nom_rol, est_registro) VALUES ('Jefe de Patentes', 'A');
INSERT INTO rol (nom_rol, est_registro) VALUES ('Tesorero', 'A');
INSERT INTO rol (nom_rol, est_registro) VALUES ('Secretaria de Catastro', 'A');
INSERT INTO rol (nom_rol, est_registro) VALUES ('Secretaria de Patentes', 'A');
INSERT INTO rol (nom_rol, est_registro) VALUES ('Calculista', 'A');

/* Datos de prueba para la tabla usuario */
INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES ('admin', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 1, 'A');
INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES ('Alejandro', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 2, 'A');
INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES ('lic. Yelitza', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 3, 'A');
INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES ('Eliana', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 4, 'A');
INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES ('Marta', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 5, 'A');
INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES ('Juan', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 6, 'A');
INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES ('Jose', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 7, 'A');

-- Datos de contribuyentes de prueba
INSERT INTO contribuyente (nom_con, rif_con, tel_con, ema_con, dir_con, fky_usu_registro, est_registro) VALUES
('Comercializadora Los Andes C.A.', 'J-123456789', '04141234567', 'contacto@losandes.com', 'Av. Principal #100, Sector Centro', 1, 'A'),
('Farmacias Farmatodo', 'J-987654321', '04241234567', 'info@farmatodo.com', 'Calle Comercio #25, Zona Industrial', 1, 'A'),
('Distribuidora Alimentos Polar', 'J-456789123', '04161234567', 'ventas@alimentospolar.com', 'Av. Bolívar #200, Urbanización La Floresta', 1, 'A'),
('Constructora Edifica C.A.', 'J-321654987', '04121234567', 'proyectos@edifica.com', 'Calle 5 con Av. 10, Edif. Torreón', 1, 'A'),
('Tiendas Daka', 'J-789123456', '04261234567', 'atencion@daka.com', 'Centro Comercial Lider, Local 15', 1, 'A'),
('Supermercado Central Madeirense', 'J-654987321', '04151234567', 'clientes@madeirense.com', 'Av. Libertador #150, El Rosal', 1, 'A'),
('Automercados Plaza''s', 'J-147258369', '04221234567', 'plazas@contacto.com', 'C.C. Sambil, Nivel Alimentos', 1, 'A'),
('Ferretería EPA', 'J-369258147', '04131234567', 'servicio@epa.com', 'Av. Fuerzas Armadas #300', 1, 'A'),
('Banco de Venezuela', 'J-258147369', '04231234567', 'bancodevenezuela@contacto.com', 'Av. Urdaneta, Esq. de Carmelitas', 1, 'A'),
('Telecomunicaciones Movistar', 'J-852963741', '04171234567', 'atencion@movistar.com', 'Centro Empresarial Polar, Torre Este', 1, 'A'),
('Restaurante Trattoria da Pippo', 'J-741852963', '04251234567', 'reservas@trattoriapippo.com', 'Calle Negrín, Sabana Grande', 1, 'A'),
('Clínica El Ávila', 'J-963852741', '04181234567', 'contacto@clinicaelavila.com', 'Transversal 6, Altamira', 1, 'A'),
('Colegio San Ignacio de Loyola', 'J-159357486', '04271234567', 'info@sanignacio.edu', 'Av. Boyacá, Urb. La Campiña', 1, 'A'),
('Panadería Francesa Le Petit', 'J-357159486', '04191234567', 'ventas@lepetit.com', 'Calle California, Las Mercedes', 1, 'A'),
('Taller Mecánico Automundo', 'J-486357159', '04281234567', 'taller@automundo.com', 'Av. Victoria, Zona Industrial', 1, 'A'),
('Joyeria Diamante Azul', 'J-753951846', '04211234567', 'joyeria@diamanteazul.com', 'Centro Comercial Tolón, Local 22', 1, 'A'),
('Librería Tecni-Ciencia', 'J-846753951', '04101234567', 'pedidos@tecniencia.com', 'Av. Universidad, Edif. Galerías', 1, 'A'),
('Agencia de Viajes Turisven', 'J-951753846', '04291234567', 'turisven@viajes.com', 'Plaza Venezuela, Torre A, Piso 3', 1, 'A'),
('Gimnasio Power Fit', 'J-624837159', '04201234567', 'powerfit@gimnasio.com', 'Calle Chacao, Edif. Fitness Center', 1, 'A'),
('Estudio Jurídico Pérez & Asociados', 'J-159624837', '04111234567', 'consultas@perezabogados.com', 'Av. Francisco de Miranda, Torre Banesco', 1, 'A');

/* Datos de prueba para la tabla tipo_impuesto */
INSERT INTO tipo_impuesto (nom_tip, des_tip, est_registro) VALUES
('DEUDA MOROSA DE CATASTRO', 'Pago por deudas morosas del impuesto catastral', 'A'),
('DEUDA ACTUAL DE CATASTRO', 'Pago del impuesto catastral del período actual', 'A'),
('SOLVENCIA TIPO A', 'Certificación de solvencia municipal tipo A', 'A'),
('CEDULA CATASTRAL', 'Tramitación y expedición de cédula catastral', 'A'),
('SOLVENCIA MUNICIPAL', 'Certificación general de solvencia municipal', 'A'),
('PATENTE DE INDUSTRIA Y COMERCIO', 'Impuesto por patente de industria y comercio', 'A'),
('SOLVENCIA PATENTE', 'Certificación de solvencia para patentes', 'A'),
('RENOVACION PATENTE', 'Renovación anual de patente comercial', 'A'),
('TRAMITACION PATENTE', 'Tramitación inicial de patente comercial', 'A'),
('SOLVENCIA LICORES', 'Certificación para establecimientos de licores', 'A'),
('RENOVACION LICORES', 'Renovación de licencia para venta de licores', 'A'),
('RENOVACION LICENCIA DE LICORES', 'Renovación de licencia específica para licores', 'A'),
('PUBLICIDAD Y PROPAGANDA', 'Impuesto por publicidad y propaganda comercial', 'A'),
('DECLARACION ESTIMADA', 'Pago por declaración estimada de ingresos', 'A'),
('DEFINITIVA INGRESOS BRUTOS', 'Pago definitivo por ingresos brutos', 'A'),
('ESPECTACULOS PUBLICOS', 'Impuesto por espectáculos públicos', 'A'),
('PATENTE DE VEHICULO FISCAL', 'Patente para vehículos de uso fiscal', 'A'),
('PERMISO EVENTUAL POR ENFERMEDAD LICORES', 'Permiso eventual por enfermedad para licores', 'A'),
('PERMISO EVENTUAL ESPECTACULOS PUBLICOS LICORES', 'Permiso eventual para espectáculos con licores', 'A'),
('MULTAS LICORES', 'Multas por infracciones en venta de licores', 'A'),
('TRAMITACION LICENCIA DE LICORES', 'Tramitación inicial de licencia para licores', 'A'),
('APUESTAS LICITAS', 'Impuesto por apuestas lícitas', 'A'),
('TRASPASO LICENCIA LICORES', 'Traspaso de licencia para venta de licores', 'A'),
('PERMISO MUNICIPALES', 'Permisos municipales varios', 'A'),
('PERMISO DE CONSTRUCCION', 'Permiso para obras de construcción', 'A'),
('USO CONFORME', 'Certificado de uso conforme', 'A'),
('ZONIFICACION', 'Estudio y certificado de zonificación', 'A'),
('PERMISO DE ROTURA', 'Permiso para rotura de vías públicas', 'A'),
('TERMINAL DE PASAJEROS LA FRIA', 'Impuesto terminal de pasajeros La Fría', 'A'),
('MULTAS INGENIERIA', 'Multas por infracciones de ingeniería', 'A'),
('PERMISO TEMPORAL DE LICORES', 'Permiso temporal para venta de licores', 'A'),
('MANTENIMIENTO ANUAL', 'Mantenimiento anual de instalaciones', 'A'),
('LEY DE CONTRATACIONES PUBLICAS', 'Impuesto por ley de contrataciones públicas', 'A'),
('OTROS INGRESOS EXTRAORDINARIOS', 'Otros ingresos extraordinarios municipales', 'A');

/* Datos de prueba para la tabla patente */
INSERT INTO patente (fec_pat, num_pat, rif_pat, nom_pat, fky_con, dir_pat, rub_pat, fky_usu, est_pat, est_registro) VALUES
('2025-01-15', 'PAT-2025-001', 'J-123456789', 'Comercializadora Los Andes C.A.', 1, 'Av. Principal #100, Sector Centro', 'Comercio General y Distribución', 1, 'A', 'A'),
('2025-01-20', 'PAT-2025-002', 'J-987654321', 'Farmacias Farmatodo', 2, 'Calle Comercio #25, Zona Industrial', 'Farmacia y Productos Farmacéuticos', 1, 'A', 'A'),
('2025-02-10', 'PAT-2025-003', 'J-456789123', 'Distribuidora Alimentos Polar', 3, 'Av. Bolívar #200, Urbanización La Floresta', 'Distribución de Alimentos y Bebidas', 1, 'A', 'A'),
('2025-02-15', 'PAT-2025-004', 'J-321654987', 'Constructora Edifica C.A.', 4, 'Calle 5 con Av. 10, Edif. Torreón', 'Construcción y Obras Civiles', 1, 'A', 'A'),
('2025-03-01', 'PAT-2025-005', 'J-789123456', 'Tiendas Daka', 5, 'Centro Comercial Lider, Local 15', 'Electrodomésticos y Electrónicos', 1, 'A', 'A'),
('2025-03-10', 'PAT-2025-006', 'J-654987321', 'Supermercado Central Madeirense', 6, 'Av. Libertador #150, El Rosal', 'Supermercado y Abastos', 1, 'A', 'A'),
('2025-03-20', 'PAT-2025-007', 'J-147258369', 'Automercados Plaza''s', 7, 'C.C. Sambil, Nivel Alimentos', 'Supermercado y Autoservicio', 1, 'A', 'A'),
('2025-04-05', 'PAT-2025-008', 'J-369258147', 'Ferretería EPA', 8, 'Av. Fuerzas Armadas #300', 'Ferretería y Materiales de Construcción', 1, 'A', 'A'),
('2025-04-12', 'PAT-2025-009', 'J-258147369', 'Banco de Venezuela', 9, 'Av. Urdaneta, Esq. de Carmelitas', 'Servicios Bancarios y Financieros', 1, 'A', 'A'),
('2025-04-18', 'PAT-2025-010', 'J-852963741', 'Telecomunicaciones Movistar', 10, 'Centro Empresarial Polar, Torre Este', 'Telecomunicaciones y Servicios Móviles', 1, 'A', 'A');

/* Datos de prueba para licencias de patentes y sus solvencias comerciales */
INSERT INTO licencia (num_lic, fky_pat, fky_fac, fec_exp_lic, vig_lic, est_lic, est_registro) VALUES
('LIC-2025-001', 1, NULL, '2025-01-15', '2026-01-15', 'P', 'A'),
('LIC-2025-002', 2, NULL, '2025-01-20', '2026-01-20', 'P', 'A'),
('LIC-2025-003', 3, NULL, '2025-02-10', '2026-02-10', 'P', 'A'),
('LIC-2025-004', 4, '2025-02-15', '2026-02-15', 'P', 'A'),
('LIC-2025-005', 5, '2025-03-01', '2026-03-01', 'P', 'A'),
('LIC-2025-006', 6, '2025-03-10', '2026-03-10', 'P', 'A'),
('LIC-2025-007', 7, '2025-03-20', '2026-03-20', 'P', 'A'),
('LIC-2025-008', 8, '2025-04-05', '2026-04-05', 'P', 'A'),
('LIC-2025-009', 9, '2025-04-12', '2026-04-12', 'P', 'A'),
('LIC-2025-010', 10, '2025-04-18', '2026-04-18', 'P', 'A');

INSERT INTO solvencia_comercial (num_sol_com, fky_pat, fec_exp_sol_com, vig_sol_com, est_sol_com, est_registro) VALUES
('SOL-COM-2025-001', 1, '2025-01-15', '2025-10-15', 'P', 'A'),
('SOL-COM-2025-002', 2, '2025-01-20', '2025-10-20', 'P', 'A'),
('SOL-COM-2025-003', 3, '2025-02-10', '2025-11-10', 'P', 'A'),
('SOL-COM-2025-004', 4, '2025-02-15', '2025-11-15', 'P', 'A'),
('SOL-COM-2025-005', 5, '2025-03-01', '2025-12-01', 'P', 'A'),
('SOL-COM-2025-006', 6, '2025-03-10', '2025-12-10', 'P', 'A'),
('SOL-COM-2025-007', 7, '2025-03-20', '2025-12-20', 'P', 'A'),
('SOL-COM-2025-008', 8, '2025-04-05', '2026-01-05', 'P', 'A'),
('SOL-COM-2025-009', 9, '2025-04-12', '2026-01-12', 'P', 'A'),
('SOL-COM-2025-010', 10, '2025-04-18', '2026-01-18', 'P', 'A');

/* Datos de prueba para catastro */
-- Linderos
INSERT INTO linderos (lin_norte, lin_sur, lin_este, lin_oeste, mt2_norte, mt2_sur, mt2_este, mt2_oeste, mt2_total, est_registro) VALUES
('Calle Principal', 'Calle Secundaria', 'Av. Bolívar', 'Calle Comercio', 25.50, 25.50, 30.25, 30.25, 1545.38, 'A'),
('Av. Libertador', 'Calle Sur', 'Calle Este', 'Calle Oeste', 30.00, 30.00, 40.00, 40.00, 2400.00, 'A'),
('Calle Norte', 'Calle 5', 'Av. Universidad', 'Calle 10', 20.75, 20.75, 35.50, 35.50, 1473.63, 'A');

-- Documentos Legales
INSERT INTO documentos_legales (tip_doc, num_doc, matr_doc, fol_doc, fec_doc, des_doc, est_registro) VALUES
('Notariado', 'DOC-001-2025', 'MAT-001', 'FOL-001', '2020-05-15', 'Escritura pública de propiedad', 'A'),
('Registrado', 'DOC-002-2025', 'MAT-002', 'FOL-002', '2018-08-22', 'Registro de propiedad horizontal', 'A'),
('Juzgado', 'DOC-003-2025', 'MAT-003', 'FOL-003', '2019-11-30', 'Sentencia judicial de propiedad', 'A');

-- Inmuebles
INSERT INTO inmueble (dir_inm, tip_inm, area_inm, est_registro) VALUES
('Av. Principal #100, Sector Centro', 'Local', 'Urbano', 'A'),
('Calle Comercio #25, Zona Industrial', 'Galpon', 'Urbano', 'A'),
('Av. Bolívar #200, Urbanización La Floresta', 'Casa', 'Urbano', 'A');

-- Cédulas Catastrales
INSERT INTO cedula_catastral (num_ced, num_exp, fky_con, fky_inm, fky_lin, fky_doc, val_ced, fec_exp_ced, vig_ced, sol_ced, fky_usu, est_ced, est_registro) VALUES
('CED-001-2025', 'EXP-001-2025', 1, 1, 1, 1, 150000.00, '2025-01-10', '2026-01-10', 'Solicitud de cédula catastral para comercio', 1, 'P', 'A'),
('CED-002-2025', 'EXP-002-2025', 2, 2, 2, 2, 200000.00, '2025-01-18', '2026-01-18', 'Solicitud de cédula para galpón industrial', 1, 'P', 'A'),
('CED-003-2025', 'EXP-003-2025', 3, 3, 3, 3, 180000.00, '2025-02-05', '2026-02-05', 'Solicitud de cédula para vivienda', 1, 'P', 'A');

-- Solvencias Municipales
INSERT INTO solvencia_municipal (num_sol, fky_con, fky_ced, sol_sol, fec_exp_sol, vig_sol, fky_usu, est_sol, est_registro) VALUES
('SOL-MUN-001-2025', 1, 1, 'Solicitud de solvencia municipal para trámites comerciales', '2025-01-12', '2026-01-12', 1, 'P', 'A'),
('SOL-MUN-002-2025', 2, 2, 'Solicitud de solvencia para permisos industriales', '2025-01-22', '2026-01-22', 1, 'P', 'A'),
('SOL-MUN-003-2025', 3, 3, 'Solicitud de solvencia para crédito bancario', '2025-02-08', '2026-02-08', 1, 'P', 'A');

/* Datos de prueba para las tablas factura y detalle_impuesto */
-- Factura 1: Similar a la imagen proporcionada
INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro) VALUES
('3630', '2025-09-30 08:30:00', 1, 1, 'PAGO DE IMPUESTOS MUNICIPALES - BELKIS FIGUEROA', 138.00, 'P', 'A');

INSERT INTO detalle_impuesto (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro) VALUES
(1, 2, 50.00, NULL, NULL, NULL, NULL, 'A'), -- DEUDA ACTUAL DE CATASTRO
(1, 3, 45.00, NULL, 1, NULL, NULL, 'A'),    -- SOLVENCIA TIPO A (vinculada a solvencia municipal)
(1, 7, 25.00, NULL, NULL, NULL, 1, 'A'),    -- SOLVENCIA PATENTE (vinculada a solvencia comercial)
(1, 17, 18.00, NULL, NULL, NULL, NULL, 'A'); -- PATENTE DE VEHICULO FISCAL

-- Factura 2: Factura con múltiples impuestos catastrales
INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro) VALUES
('3631', '2025-09-29 09:15:00', 1, 2, 'LIQUIDACION CATASTRAL - COMERCIAL LOS ANDES', 285.50, 'P', 'A');

INSERT INTO detalle_impuesto (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro) VALUES
(2, 1, 120.00, 1, NULL, NULL, NULL, 'A'),   -- DEUDA MOROSA DE CATASTRO (vinculada a cédula)
(2, 2, 85.50, 1, NULL, NULL, NULL, 'A'),    -- DEUDA ACTUAL DE CATASTRO (vinculada a cédula)
(2, 4, 80.00, 1, NULL, NULL, NULL, 'A');    -- CEDULA CATASTRAL (vinculada a cédula)

-- Factura 3: Factura para patente de industria y comercio
INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro) VALUES
('3632', '2025-09-28 10:20:00', 1, 3, 'PATENTE COMERCIAL - FARMACIA FARMATODO', 420.75, 'P', 'A');

INSERT INTO detalle_impuesto (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro) VALUES
(3, 6, 250.00, NULL, NULL, 2, NULL, 'A'),   -- PATENTE DE INDUSTRIA Y COMERCIO (vinculada a licencia)
(3, 7, 85.75, NULL, NULL, NULL, 2, 'A'),    -- SOLVENCIA PATENTE (vinculada a solvencia comercial)
(3, 8, 85.00, NULL, NULL, 2, NULL, 'A');    -- RENOVACION PATENTE (vinculada a licencia)

-- Factura 4: Factura para licencias
INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro) VALUES
('3633', '2025-09-27 11:45:00', 1, 4, 'LICENCIAS Y PERMISOS - CONSTRUCTORA EDIFICA', 325.00, 'P', 'A');

INSERT INTO detalle_impuesto (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro) VALUES
(4, 13, 150.00, NULL, NULL, 4, NULL, 'A'),  -- PUBLICIDAD Y PROPAGANDA (vinculada a licencia)
(4, 16, 175.00, NULL, NULL, 4, NULL, 'A');  -- ESPECTACULOS PUBLICOS (vinculada a licencia)

-- Factura 5: Factura mixta con varios conceptos
INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro) VALUES
('3634', '2025-09-26 14:30:00', 1, 5, 'PAGOS VARIOS - TIENDA DAKA', 635.50, 'P', 'A');

INSERT INTO detalle_impuesto (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro) VALUES
(5, 3, 85.00, NULL, 2, NULL, NULL, 'A'),    -- SOLVENCIA TIPO A (vinculada a solvencia municipal)
(5, 5, 120.50, NULL, 2, NULL, NULL, 'A'),   -- SOLVENCIA MUNICIPAL (vinculada a solvencia municipal)
(5, 17, 75.00, NULL, NULL, 5, NULL, 'A'),   -- PATENTE DE VEHICULO FISCAL (vinculada a licencia)
(5, 34, 355.00, NULL, NULL, NULL, NULL, 'A'); -- OTROS INGRESOS EXTRAORDINARIOS
