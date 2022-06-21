-- ================================ --
-- |  Created on Wed May 25 2022  | --
-- |   Copyright (c) 2022 VAIRA   | --
-- |     All Rights Reserved.     | --
-- ================================ --
-- |  Código encargado de llenar  | --
-- |   la base de datos durante   | --
-- |    la fase de desarrollo     | --
-- ================================ --

USE naatika1_db_vaira;

INSERT INTO permisos VALUES
                    (0, 1, 1, 1, 1), # SUPER-ADMIN
                    (0, 1, 0, 0, 1), # ADMIN
                    (0, 0, 0, 0, 1)  # USER
                    ;

INSERT INTO tipo VALUES
                    (0, 1, 'SUPER-ADMIN'),
                    (0, 2, 'ADMIN'),
                    (0, 3, 'VENDEDOR')
                    ;

INSERT INTO pais VALUES (0, 'Mexico');

# Las ciudades deben ser precargadas en esta base de datos, acorde a lo establecido por la Secretaría de Hacienda y Crédito Público de la Republica
# Mexicana.

INSERT INTO ciudad VALUES (0, 1, 'Cuernavaca'),
                          (0, 1, 'Emiliano Zapata'),
                          (0, 1, 'Temixco'),
                          (0, 1, 'Tijuana');

INSERT INTO region_iva VALUES (0, 1, 0.16),
                              (0, 2, 0.16),
                              (0, 3, 0.16),
                              (0, 4, 0.08);

INSERT INTO motivo_egreso VALUES (0, 'Compra producto'),
                                 (0, 'Devolucion');

INSERT INTO tipo_pago VALUES (0,'Tarjeta de credito'),
                             (0,'Tarjeta de debito'),
                             (0,'Efectivo');

INSERT INTO regimen_fiscal VALUES (0, 'Régimen Simplificado de Confianza'),
                                  (0, 'Sueldos y salarios e ingresos asimilados a salarios'),
                                  (0, 'Régimen de Actividades Empresariales y Profesionales'),
                                  (0, 'Régimen de Incorporación Fiscal'),
                                  (0, 'Enajenación de bienes'),
                                  (0, 'Régimen de Actividades Empresariales con ingresos a través de Plataformas Tecnológicas'),
                                  (0, 'Régimen de Arrendamiento'),
                                  (0, 'Intereses'),
                                  (0, 'Obtención de premios'),
                                  (0, 'Dividendos'),
                                  (0, 'Demás ingresos');

CALL insertar_usuario('{"nombre":"Nombre1","apellidoP":"ApellidoP1","apellidoM":"ApellidoM1","usuario":"super-admin","password":"123","correo":"a@a.com","telefono":"1234567890","rol":"1"}');
CALL insertar_sucursal('{"nombre":"Tienda inicial", "calle":"nombre calle", "colonia":"nombre colonia", "cp":"54356", "telefono":"1234567890","region":1}');