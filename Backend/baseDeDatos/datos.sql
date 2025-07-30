

-- Insertar un usuario 'admin' con clave '12345' encriptada
-- La clave encriptada se genera con password_hash('12345', PASSWORD_DEFAULT)


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







