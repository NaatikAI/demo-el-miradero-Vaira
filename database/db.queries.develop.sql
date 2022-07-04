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
                    #id, crear, modificar, eliminar
INSERT INTO permisos VALUES
                    (1, 1, 1, 1, 1), # SUPER-ADMIN
                    (2, 1, 0, 0, 1), # ADMIN
                    (3, 0, 0, 0, 1)  # USER
                    ;
                    
                    #id, fkpermisos, tipo,
INSERT INTO tipo VALUES
                    (1, 1, 'SUPER-ADMIN'),
                    (2, 2, 'ADMIN'),
                    (3, 3, 'VENDEDOR')
                    ;

INSERT INTO pais VALUES (1, 'Mexico');

# Las ciudades deben ser precargadas en esta base de datos, acorde a lo establecido por la Secretaría de Hacienda y Crédito Público de la Republica
# Mexicana.
                          #id, fkpais, nombre
INSERT INTO ciudad VALUES (1, 1, 'Cuernavaca'),
                          (2, 1, 'Emiliano Zapata'),
                          (3, 1, 'Temixco'),
                          (4, 1, 'Tijuana');

                          #id, fkciudad, iva
INSERT INTO region_iva VALUES (1, 1, 0.16),
                              (2, 2, 0.16),
                              (3, 3, 0.16),
                              (4, 4, 0.08);
                           
                                
INSERT INTO motivo_egreso VALUES (1, 'Compra producto'),
                                 (2, 'Devolucion');

INSERT INTO tipo_pago VALUES (1,'Tarjeta de credito'),
                             (2,'Tarjeta de debito'),
                             (3,'Efectivo');

INSERT INTO regimen_fiscal VALUES (1,  'Régimen Simplificado de Confianza'),
                                  (2,  'Sueldos y salarios e ingresos asimilados a salarios'),
                                  (3,  'Régimen de Actividades Empresariales y Profesionales'),
                                  (4,  'Régimen de Incorporación Fiscal'),
                                  (5,  'Enajenación de bienes'),
                                  (6,  'Régimen de Actividades Empresariales con ingresos a través de Plataformas Tecnológicas'),
                                  (7,  'Régimen de Arrendamiento'),
                                  (8,  'Intereses'),
                                  (9,  'Obtención de premios'),
                                  (10, 'Dividendos'),
                                  (11, 'Demás ingresos');

CALL insertar_usuario('{"nombre":"Nombre1","apellidoP":"ApellidoP1","apellidoM":"ApellidoM1","usuario":"super-admin","password":"123","correo":"a@a.com","telefono":"1234567890","rol":"1"}');
CALL insertar_sucursal('{"nombre":"Tienda inicial", "calle":"nombre calle", "colonia":"nombre colonia", "cp":"54356", "telefono":"1234567890","region":1}');