

-- Insertar un usuario 'admin' con clave 'Admin123' encriptada
-- La clave encriptada se genera con password_hash('Admin123', PASSWORD_DEFAULT)

insert into rol (nom_rol, est_rol) values ('Administrador', 'A');
insert into rol (nom_rol, est_rol) values ('Jefe de Catastro', 'A');
insert into rol (nom_rol, est_rol) values ('Jefe de Patentes', 'A');
insert into rol (nom_rol, est_rol) values ('Tesorero', 'A');
insert into rol (nom_rol, est_rol) values ('Secretaria de Catastro', 'A');
insert into rol (nom_rol, est_rol) values ('Secretaria de Patentes', 'A');
insert into rol (nom_rol, est_rol) values ('Calculista', 'A');

INSERT INTO usuario (id_usu, nom_usu, cla_usu, fky_rol, est_usu) VALUES (1, 'admin', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 1, 'A');
insert into usuario (nom_usu, cla_usu, fky_rol, est_usu) values ('Alejandro', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 2, 'A');
insert into usuario (nom_usu, cla_usu, fky_rol, est_usu) values ('lic. Yelitza', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 3, 'A');
insert into usuario (nom_usu, cla_usu, fky_rol, est_usu) values ('Eliana', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 4, 'A');
insert into usuario (nom_usu, cla_usu, fky_rol, est_usu) values ('Marta', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 5, 'A');
insert into usuario (nom_usu, cla_usu, fky_rol, est_usu) values ('Juan', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 6, 'A');
insert into usuario (nom_usu, cla_usu, fky_rol, est_usu) values ('Jose', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', 7, 'A');

-- Datos de contribuyentes de prueba
-- Insertar 20 registros de ejemplo en la tabla contribuyente
INSERT INTO contribuyente (nom_con, rif_con, tel_con, ema_con, dir_con, fky_usu, est_con) VALUES
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





