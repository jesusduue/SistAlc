/*
================================================================================
    SCRIPT DE DATOS DE PRUEBA (CORREGIDO Y ADAPTADO)
    Compatible con la estructura de BD refinada (bd.sql)
================================================================================
*/

USE alcaldiaV2;

/*
================================================================================
    SECCIÓN 1: ALTERACIONES DE ESQUEMA REQUERIDAS
================================================================================
*/
ALTER TABLE factura
    ADD COLUMN IF NOT EXISTS fec_anulacion DATETIME NULL AFTER est_pago;

ALTER TABLE licencia
    ADD COLUMN IF NOT EXISTS fky_fac INT NULL AFTER fky_pat,
    ADD CONSTRAINT fk_licencia_factura FOREIGN KEY (fky_fac) REFERENCES factura(id_fac) ON DELETE SET NULL ON UPDATE CASCADE;

/*
================================================================================
    SECCIÓN 2: DATOS DE ACCESO Y CONTRIBUYENTES
================================================================================
*/

/* Datos de prueba para la tabla rol (Basado en tu archivo) */
INSERT INTO rol (nom_rol, est_registro) VALUES 
('Administrador', 'A'),
('Jefe de Catastro', 'A'),
('Jefe de Patentes', 'A'),
('Tesorero', 'A'),
('Secretaria de Catastro', 'A'),
('Secretaria de Patentes', 'A'),
('Calculista', 'A')
ON DUPLICATE KEY UPDATE nom_rol = VALUES(nom_rol); -- Evita error si ya existen

/* Datos de prueba para la tabla usuario (Clave: Admin123) */
INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES 
('admin', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 1, 'A'),
('Alejandro', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 2, 'A'),
('lic. Yelitza', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 3, 'A'),
('Eliana', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 4, 'A'),
('Marta', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 5, 'A'),
('Juan', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 6, 'A'),
('Jose', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 7, 'A')
ON DUPLICATE KEY UPDATE cla_usu = VALUES(cla_usu), fky_rol = VALUES(fky_rol); -- Actualiza por si ya existen

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
('Telecomunicaciones Movistar', 'J-852963741', '04171234567', 'atencion@movistar.com', 'Centro Empresarial Polar, Torre Este', 1, 'A')
ON DUPLICATE KEY UPDATE nom_con = VALUES(nom_con);

/*
================================================================================
    SECCIÓN 3: DATOS DE IMPUESTOS Y PATENTES
================================================================================
*/

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
('PATENTE DE VEHICULO FISCAL', 'Patente para vehículos de uso fiscal', 'A')
ON DUPLICATE KEY UPDATE nom_tip = VALUES(nom_tip);

/* Datos de prueba para la tabla patente (Vinculada a contribuyente) */
INSERT INTO patente (fec_pat, num_pat, rif_pat, nom_pat, fky_con, dir_pat, rub_pat, fky_usu, est_pat, est_registro) VALUES
('2025-01-15', 'PAT-2025-001', 'J-123456789', 'Comercializadora Los Andes C.A.', 1, 'Av. Principal #100, Sector Centro', 'Comercio General y Distribución', 1, 'A', 'A'),
('2025-01-20', 'PAT-2025-002', 'J-987654321', 'Farmacias Farmatodo', 2, 'Calle Comercio #25, Zona Industrial', 'Farmacia y Productos Farmacéuticos', 1, 'A', 'A'),
('2025-02-10', 'PAT-2025-003', 'J-456789123', 'Distribuidora Alimentos Polar', 3, 'Av. Bolívar #200, Urbanización La Floresta', 'Distribución de Alimentos y Bebidas', 1, 'A', 'A'),
('2025-02-15', 'PAT-2025-004', 'J-321654987', 'Constructora Edifica C.A.', 4, 'Calle 5 con Av. 10, Edif. Torreón', 'Construcción y Obras Civiles', 1, 'A', 'A'),
('2025-03-01', 'PAT-2025-005', 'J-789123456', 'Tiendas Daka', 5, 'Centro Comercial Lider, Local 15', 'Electrodomésticos y Electrónicos', 1, 'A', 'A')
ON DUPLICATE KEY UPDATE nom_pat = VALUES(nom_pat);

/* Datos de prueba para licencias de patentes */
INSERT INTO licencia (num_lic, fky_pat, fky_fac, fec_exp_lic, vig_lic, est_lic, est_registro) VALUES
('LIC-2025-001', 1, NULL, '2025-01-15', '2026-01-15', 'P', 'A'),
('LIC-2025-002', 2, NULL, '2025-01-20', '2026-01-20', 'P', 'A'),
('LIC-2025-003', 3, NULL, '2025-02-10', '2026-02-10', 'P', 'A'),
('LIC-2025-004', 4, NULL, '2025-02-15', '2026-02-15', 'P', 'A'),
('LIC-2025-005', 5, NULL, '2025-03-01', '2026-03-01', 'P', 'A')
ON DUPLICATE KEY UPDATE fky_pat = VALUES(fky_pat);

INSERT INTO solvencia_comercial (num_sol_com, fky_pat, fec_exp_sol_com, vig_sol_com, est_sol_com, est_registro) VALUES
('SOL-COM-2025-001', 1, '2025-01-15', '2025-10-15', 'P', 'A'),
('SOL-COM-2025-002', 2, '2025-01-20', '2025-10-20', 'P', 'A'),
('SOL-COM-2025-003', 3, '2025-02-10', '2025-11-10', 'P', 'A'),
('SOL-COM-2025-004', 4, '2025-02-15', '2025-11-15', 'P', 'A'),
('SOL-COM-2025-005', 5, '2025-03-01', '2025-12-01', 'P', 'A')
ON DUPLICATE KEY UPDATE fky_pat = VALUES(fky_pat);

/*
================================================================================
    SECCIÓN 4: DATOS DE CATASTRO (CORREGIDOS SEGÚN NUEVO ESQUEMA)
================================================================================
*/

-- Linderos (Estos son independientes)
INSERT INTO linderos (lin_norte, lin_sur, lin_este, lin_oeste, mt2_norte, mt2_sur, mt2_este, mt2_oeste, mt2_total, est_registro) VALUES
('Calle Principal', 'Calle Secundaria', 'Av. Bolívar', 'Calle Comercio', 25.50, 25.50, 30.25, 30.25, 1545.38, 'A'),
('Av. Libertador', 'Calle Sur', 'Calle Este', 'Calle Oeste', 30.00, 30.00, 40.00, 40.00, 2400.00, 'A'),
('Calle Norte', 'Calle 5', 'Av. Universidad', 'Calle 10', 20.75, 20.75, 35.50, 35.50, 1473.63, 'A')
ON DUPLICATE KEY UPDATE mt2_total = VALUES(mt2_total);

-- Documentos Legales (Estos son independientes)
INSERT INTO documentos_legales (tip_doc, num_doc, matr_doc, fol_doc, fec_doc, des_doc, est_registro) VALUES
('Notariado', 'DOC-001-2025', 'MAT-001', 'FOL-001', '2020-05-15', 'Escritura pública de propiedad', 'A'),
('Registrado', 'DOC-002-2025', 'MAT-002', 'FOL-002', '2018-08-22', 'Registro de propiedad horizontal', 'A'),
('Juzgado', 'DOC-003-2025', 'MAT-003', 'FOL-003', '2019-11-30', 'Sentencia judicial de propiedad', 'A')
ON DUPLICATE KEY UPDATE num_doc = VALUES(num_doc);

-- Inmuebles (CORREGIDO: Ahora vincula Contribuyente, Linderos y Documentos)
INSERT INTO inmueble (fky_con, fky_lin, fky_doc, dir_inm, tip_inm, area_inm, est_registro) VALUES
(1, 1, 1, 'Av. Principal #100, Sector Centro', 'Local', 'Urbano', 'A'),
(2, 2, 2, 'Calle Comercio #25, Zona Industrial', 'Galpon', 'Urbano', 'A'),
(3, 3, 3, 'Av. Bolívar #200, Urbanización La Floresta', 'Casa', 'Urbano', 'A')
ON DUPLICATE KEY UPDATE dir_inm = VALUES(dir_inm);

-- Cédulas Catastrales (CORREGIDO: Ahora solo se vincula a fky_inm)
INSERT INTO cedula_catastral (fky_inm, num_ced, num_exp, val_ced, fec_exp_ced, vig_ced, sol_ced, fky_usu, est_ced, est_registro) VALUES
(1, 'CED-001-2025', 'EXP-001-2025', 150000.00, '2025-01-10', '2026-01-10', 'Solicitud de cédula catastral para comercio', 1, 'P', 'A'),
(2, 'CED-002-2025', 'EXP-002-2025', 200000.00, '2025-01-18', '2026-01-18', 'Solicitud de cédula para galpón industrial', 1, 'P', 'A'),
(3, 'CED-003-2025', 'EXP-003-2025', 180000.00, '2025-02-05', '2026-02-05', 'Solicitud de cédula para vivienda', 1, 'P', 'A')
ON DUPLICATE KEY UPDATE num_exp = VALUES(num_exp);

-- Solvencias Municipales (CORREGIDO: Se elimina fky_con)
INSERT INTO solvencia_municipal (fky_ced, num_sol, sol_sol, fec_exp_sol, vig_sol, fky_usu, est_sol, est_registro) VALUES
(1, 'SOL-MUN-001-2025', 'Solicitud de solvencia municipal para trámites comerciales', '2025-01-12', '2026-01-12', 1, 'P', 'A'),
(2, 'SOL-MUN-002-2025', 'Solicitud de solvencia para permisos industriales', '2025-01-22', '2026-01-22', 1, 'P', 'A'),
(3, 'SOL-MUN-003-2025', 'Solicitud de solvencia para crédito bancario', '2025-02-08', '2026-02-08', 1, 'P', 'A')
ON DUPLICATE KEY UPDATE sol_sol = VALUES(sol_sol);

/*
================================================================================
    SECCIÓN 5: DATOS DE FACTURACIÓN
================================================================================
*/

-- Factura 1: Similar a la imagen proporcionada
INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro) VALUES
('3630', '2025-09-30 08:30:00', 1, 1, 'PAGO DE IMPUESTOS MUNICIPALES - COMERCIALIZADORA LOS ANDES', 138.00, 'P', 'A');

INSERT INTO detalle_impuesto (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro) VALUES
(LAST_INSERT_ID(), 2, 50.00, NULL, NULL, NULL, NULL, 'A'), -- DEUDA ACTUAL DE CATASTRO
(LAST_INSERT_ID(), 3, 45.00, NULL, 1, NULL, NULL, 'A'),    -- SOLVENCIA TIPO A (vinculada a solvencia municipal 1)
(LAST_INSERT_ID(), 7, 25.00, NULL, NULL, NULL, 1, 'A'),    -- SOLVENCIA PATENTE (vinculada a solvencia comercial 1)
(LAST_INSERT_ID(), 17, 18.00, NULL, NULL, NULL, NULL, 'A'); -- PATENTE DE VEHICULO FISCAL

-- Factura 2: Factura con múltiples impuestos catastrales
INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro) VALUES
('3631', '2025-09-29 09:15:00', 1, 2, 'LIQUIDACION CATASTRAL - FARMACIAS FARMATODO', 285.50, 'P', 'A');

INSERT INTO detalle_impuesto (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro) VALUES
(LAST_INSERT_ID(), 1, 120.00, 2, NULL, NULL, NULL, 'A'),  -- DEUDA MOROSA DE CATASTRO (vinculada a cédula 2)
(LAST_INSERT_ID(), 2, 85.50, 2, NULL, NULL, NULL, 'A'),   -- DEUDA ACTUAL DE CATASTRO (vinculada a cédula 2)
(LAST_INSERT_ID(), 4, 80.00, 2, NULL, NULL, NULL, 'A');    -- CEDULA CATASTRAL (vinculada a cédula 2)

-- Factura 3: Factura para patente de industria y comercio
INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro) VALUES
('3632', '2025-09-28 10:20:00', 1, 3, 'PATENTE COMERCIAL - DISTRIBUIDORA ALIMENTOS POLAR', 420.75, 'P', 'A');

INSERT INTO detalle_impuesto (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro) VALUES
(LAST_INSERT_ID(), 6, 250.00, NULL, NULL, 3, NULL, 'A'),  -- PATENTE DE INDUSTRIA Y COMERCIO (vinculada a licencia 3)
(LAST_INSERT_ID(), 7, 85.75, NULL, NULL, NULL, 3, 'A'),   -- SOLVENCIA PATENTE (vinculada a solvencia comercial 3)
(LAST_INSERT_ID(), 8, 85.00, NULL, NULL, 3, NULL, 'A');    -- RENOVACION PATENTE (vinculada a licencia 3)

-- Vinculamos las facturas a las licencias que acaban de pagar
UPDATE licencia SET fky_fac = 1 WHERE id_lic = 1; -- (Factura 3630 no paga licencia, asumimos fue un error de datos)
UPDATE licencia SET fky_fac = 3 WHERE id_lic = 3; -- Factura 3632 paga la licencia 3