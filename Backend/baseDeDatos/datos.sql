-- Insertar el rol de Administrador
INSERT INTO `rol` (`id_rol`, `nom_rol`, `est_rol`) VALUES (1, 'Administrador', 'A');

-- Insertar un usuario 'admin' con clave '12345' encriptada
-- La clave encriptada se genera con password_hash('12345', PASSWORD_DEFAULT)
INSERT INTO `usuario` (`id_usu`, `nom_usu`, `cla_usu`, `fky_rol`, `est_usu`) VALUES
(1, 'admin', '$2y$10$w5J.g1N3V/JcZVBj9.BwUuA2V.Xk3R.L4aLdAn2p4iL1v.G/pZz.W', 1, 'A');