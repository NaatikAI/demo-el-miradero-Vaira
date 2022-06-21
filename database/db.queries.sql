-- ================================ --
-- | Created on Wed April 05 2022 | --
-- |   Copyright (c) 2022 VAIRA   | --
-- |     All Rights Reserved.     | --
-- ================================ --
-- | Código encargado de realizar | --
-- | consultas a la base de datos | --
-- ================================ --

USE naatika1_db_vaira;

DROP PROCEDURE IF EXISTS insertar_usuario;
DELIMITER //
CREATE PROCEDURE insertar_usuario(IN _jsonA JSON)
    BEGIN
        DECLARE _fkUsuario  INT;
        DECLARE _fkSucursal INT;

        DECLARE _json       JSON;

        DECLARE _fkTipo     VARCHAR(10);
        DECLARE _nombre     VARCHAR(50);
        DECLARE _apellidoP  VARCHAR(50);
        DECLARE _apellidoM  VARCHAR(50);
        DECLARE _correo     VARCHAR(50);
        DECLARE _telefono   VARCHAR(50);
        DECLARE _usuario    VARCHAR(50);
        DECLARE _password   VARCHAR(50);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json      = JSON_EXTRACT(_jsonA, '$[0]');

        SET _nombre    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.nombre'   ));
        SET _apellidoP = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.apellidoP'));
        SET _apellidoM = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.apellidoM'));
        SET _usuario   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.usuario'  ));
        SET _password  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.password' ));
        SET _correo    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.correo'   ));
        SET _telefono  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.telefono' ));
        SET _fkTipo    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.rol'      ));

        START TRANSACTION;
            INSERT INTO usuario VALUES (0, _fkTipo, _usuario, sha2(_password, 512), _nombre, _apellidoP, _apellidoM, _correo, _telefono, 1);
            SELECT idUsuario INTO _fkUsuario FROM usuario WHERE usuario = _usuario;
            INSERT INTO log_usuario VALUES (0, _fkUsuario, NOW(), NOW(), NULL);

            IF (_fkTipo = 3) THEN
                SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.sucursal'));
                INSERT INTO sucursal_usuario VALUES(_fkUsuario, _fkSucursal);
                SELECT 'Usuario agregado' as 'Resultado';
            ELSE
                SELECT 'Super admin agregado' as 'Resultado';
            END IF;
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS eliminar_usuario;
DELIMITER //
CREATE PROCEDURE eliminar_usuario(IN _jsonA JSON)
    BEGIN
        DECLARE _json JSON;
        DECLARE _idUsuario VARCHAR(5);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json      = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idUsuario    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idUsuario'));


        START TRANSACTION;
            IF ((SELECT fkTipo FROM usuario WHERE idUsuario = _idUsuario) = 2) THEN
                IF ((SELECT idSucursal FROM sucursal WHERE fkAdmin = _idUsuario)) THEN
                    UPDATE sucursal SET fkAdmin = NULL WHERE fkAdmin = _idUsuario;
                END IF ;
            END IF ;

            UPDATE log_usuario SET desactivar = NOW() WHERE fkUsuario = _idUsuario;
            UPDATE usuario SET activo = 0 WHERE idUsuario = _idUsuario;
            DELETE FROM sucursal_usuario WHERE fkUsuario = _idUsuario;

            SELECT 'Success' AS 'RESULTADO';
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS eliminar_usuario_fisico;
DELIMITER //
CREATE PROCEDURE eliminar_usuario_fisico(IN _jsonA JSON)
    BEGIN
        DECLARE _json JSON;
        DECLARE _idUsuario INT;
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json      = JSON_EXTRACT(_jsonA, '$[0]');

        SET _idUsuario    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idUsuario'   ));

        START TRANSACTION;
            DELETE FROM log_usuario WHERE fkUsuario = _idUsuario;
            DELETE FROM usuario WHERE idUsuario = _idUsuario;
            SELECT * FROM usuario WHERE idUsuario = _idUsuario;
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS eliminar_proveedor;
DELIMITER //
CREATE PROCEDURE eliminar_proveedor(IN _jsonA JSON)
    BEGIN
        DECLARE _json JSON;
        DECLARE _idProveedor VARCHAR(5);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json      = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idProveedor    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idProveedor'));


        START TRANSACTION;
            UPDATE proveedor SET activo = 0 WHERE idProveedor = _idProveedor;
            SELECT 'Success' AS 'RESULTADO';
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS insertar_multiple_productos;
DELIMITER //
CREATE PROCEDURE insertar_multiple_productos(IN _jsonA JSON)
    BEGIN
        DECLARE _fkCategoria INT;
        DECLARE _fkProveedor INT;
        DECLARE _idProducto  INT;
        DECLARE _cantidad    INT;

        DECLARE _json        JSON;

        DECLARE _activo      TINYINT;
        DECLARE _servicio    TINYINT;

        DECLARE _nombre      VARCHAR(50);
        DECLARE _sku         VARCHAR(20);
        DECLARE _imagen      VARCHAR(50);
        DECLARE _categoria   VARCHAR(50);
        DECLARE _proveedor   VARCHAR(50);

        DECLARE _costo       DECIMAL(10,2);
        DECLARE _precio      DECIMAL(10,2);

        DECLARE _index           INT DEFAULT 0;
        DECLARE _limite          INT DEFAULT 0;
        DECLARE _idSucursal      INT;


        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json        = JSON_EXTRACT(_jsonA, '$[0]');
        SET _categoria   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.categoria'));
        SET _proveedor   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.proveedor'));
        SET _nombre      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.nombre'   ));
        SET _costo       = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.costo'    ));
        SET _precio      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.precio'   ));
        SET _imagen      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.imgen'   ));
        SET _activo      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.activo'   ));
        SET _servicio    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.servicio' ));

        START TRANSACTION;

            IF((SELECT COUNT(*) FROM categoria WHERE nombre = _categoria) = 0) THEN
                SELECT CONCAT('No existe la categoria: ',_categoria) as 'Status';
                ROLLBACK;
            ELSE
                SELECT idCategoria INTO _fkCategoria FROM categoria WHERE nombre = _categoria;
                IF((SELECT COUNT(*) FROM proveedor WHERE nombre = _proveedor) = 0) THEN
                    SELECT CONCAT('No existe el proveedor: ',_proveedor) as 'Status';
                    ROLLBACK;
                ELSE
                    SELECT idProveedor INTO _fkProveedor FROM proveedor WHERE nombre = _proveedor;
                    SELECT COUNT(*) INTO _cantidad FROM producto INNER JOIN proveedor p on producto.fkProveedor = p.idProveedor WHERE idProveedor = _fkProveedor;

                    SET _sku = UPPER(CONCAT(LEFT(_proveedor,3),'-', LEFT(_nombre,3),'-',(100 + _cantidad)));

                    INSERT INTO producto VALUES (0, _fkCategoria, _fkProveedor, _nombre, _costo, _precio, _sku, _imagen, _activo, _servicio);
                    SELECT idProducto INTO _idProducto FROM producto WHERE nombre = _nombre LIMIT 1;
                    INSERT INTO log_producto VALUES (0, _idProducto, NOW(), NULL, NULL);

                    SELECT COUNT(*) INTO _limite FROM sucursal;
                    WHILE _index < _limite DO
                        SELECT idSucursal INTO _idSucursal FROM sucursal ORDER BY idSucursal LIMIT _index, 1;
                        INSERT INTO existencia VALUE (_idProducto, _idSucursal, 0);
                        SET _index = _index + 1;
                    END WHILE ;
                    SELECT 'Producto agregado correctamente' as 'Status';
                    COMMIT;
                END IF;
            END IF;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS insertar_producto;
DELIMITER //
CREATE PROCEDURE insertar_producto(IN _jsonA JSON)
    BEGIN
        DECLARE _fkCategoria INT;
        DECLARE _fkProveedor INT;
        DECLARE _idProducto  INT;
        DECLARE _cantidad    INT;

        DECLARE _json        JSON;

        DECLARE _activo      TINYINT;
        DECLARE _servicio    TINYINT;

        DECLARE _nombre      VARCHAR(50);
        DECLARE _sku         VARCHAR(20);
        DECLARE _imagen      VARCHAR(50);
        DECLARE _proveedor   VARCHAR(50);

        DECLARE _costo       DECIMAL(10,2);
        DECLARE _precio      DECIMAL(10,2);

        DECLARE _index           INT DEFAULT 0;
        DECLARE _limite          INT DEFAULT 0;
        DECLARE _idSucursal      INT;


        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json        = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkCategoria = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.categoria'));
        SET _fkProveedor = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.proveedor'));
        SET _nombre      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.nombre'   ));
        SET _costo       = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.costo'    ));
        SET _precio      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.precio'   ));
        SET _imagen      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.imgen'   ));
        SET _activo      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.activo'   ));
        SET _servicio    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.servicio' ));

        SELECT nombre INTO _proveedor FROM proveedor WHERE idProveedor = _fkProveedor;

        SELECT COUNT(*) INTO _cantidad FROM producto INNER JOIN proveedor p on producto.fkProveedor = p.idProveedor WHERE idProveedor = _fkProveedor;

        SET _sku = UPPER(CONCAT(LEFT(_proveedor,3),'-', LEFT(_nombre,3),'-',(100 + _cantidad)));

        START TRANSACTION;
            INSERT INTO producto VALUES (0, _fkCategoria, _fkProveedor, _nombre, _costo, _precio, _sku, _imagen, _activo, _servicio);
            SELECT idProducto INTO _idProducto FROM producto WHERE nombre = _nombre LIMIT 1;
            INSERT INTO log_producto VALUES (0, _idProducto, NOW(), NULL, NULL);

            SELECT COUNT(*) INTO _limite FROM sucursal;
            WHILE _index < _limite DO
                SELECT idSucursal INTO _idSucursal FROM sucursal ORDER BY idSucursal LIMIT _index, 1;
                INSERT INTO existencia VALUE (_idProducto, _idSucursal, 0);
                SET _index = _index + 1;
            END WHILE ;
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_productos_super_admin;
DELIMITER //
CREATE PROCEDURE obtener_productos_super_admin(IN _jsonA JSON)
    BEGIN
        DECLARE _idSucursal INT;
        DECLARE _json       JSON;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.sucursal'));

        START TRANSACTION ;
            SELECT idProducto, producto.nombre, e.cantidad, sku, imagen,  precio AS TOTAL, c.nombre AS CATEGORIA, fkProveedor, costo FROM producto
                JOIN existencia e on producto.idProducto = e.fkProducto
                JOIN sucursal s on e.fkSucursal = s.idSucursal
                JOIN categoria c on c.idCategoria = producto.fkCategoria
                JOIN region_iva ri on s.fkRegion = ri.idRegion WHERE fkSucursal = _idSucursal AND activo = 1;
        COMMIT ;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_productos;
DELIMITER //
CREATE PROCEDURE obtener_productos(IN _jsonA JSON)
    BEGIN
        DECLARE _idSucursal INT;
        DECLARE _json       JSON;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.sucursal'));

        START TRANSACTION ;
            SELECT idProducto, producto.nombre, e.cantidad, sku, imagen,  TRUNCATE ((precio + (precio * ri.iva)), 2) AS TOTAL, c.nombre AS CATEGORIA, fkProveedor, costo FROM producto
                JOIN existencia e on producto.idProducto = e.fkProducto
                JOIN sucursal s on e.fkSucursal = s.idSucursal
                JOIN categoria c on c.idCategoria = producto.fkCategoria
                JOIN region_iva ri on s.fkRegion = ri.idRegion WHERE fkSucursal = _idSucursal AND activo = 1;
        COMMIT ;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS eliminar_producto;
DELIMITER //
CREATE PROCEDURE eliminar_producto(IN _jsonA JSON)
    BEGIN
        DECLARE _json JSON;
        DECLARE _idProducto INT;
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idProducto = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idProducto'));

        START TRANSACTION;
            UPDATE log_producto SET desactivar = NOW() WHERE fkProducto = _idProducto;
            UPDATE producto SET activo = 0 WHERE idProducto = _idProducto;

            SELECT * FROM producto WHERE idProducto = _idProducto;
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS eliminar_producto_fisico;
DELIMITER //
CREATE PROCEDURE eliminar_producto_fisico(IN id INT)
    BEGIN
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;
        START TRANSACTION;
            DELETE FROM log_producto WHERE fkProducto = id;
            DELETE FROM producto WHERE idProducto = id;
            SELECT * FROM producto WHERE idProducto = id;
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_busqueda;
DELIMITER //
CREATE PROCEDURE obtener_busqueda(IN _jsonA JSON)
    BEGIN
        DECLARE _json       JSON;
        DECLARE _idSucural  JSON;

        DECLARE _busqueda   VARCHAR(50);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json      = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idSucural = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.sucursal'));

        SET _busqueda  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.busqueda'));
        SET _busqueda  = CONCAT('%', _busqueda, '%');

        START TRANSACTION ;
            SELECT idProducto, producto.nombre, e.cantidad, sku, imagen,  TRUNCATE ((precio + (precio * ri.iva)), 2) AS TOTAL FROM producto
                JOIN existencia e on producto.idProducto = e.fkProducto
                JOIN sucursal s on e.fkSucursal = s.idSucursal
                JOIN region_iva ri on s.fkRegion = ri.idRegion 
                WHERE fkSucursal = _idSucural AND (producto.nombre LIKE _busqueda OR sku LIKE _busqueda);
        COMMIT ;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_filtro;
DELIMITER //
CREATE PROCEDURE obtener_filtro(IN _jsonA JSON)
    BEGIN
        DECLARE _json       JSON;
        DECLARE _idSucursal JSON;

        DECLARE _categoria  VARCHAR(50);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.sucursal' ));
        SET _categoria   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.categoria'));

        START TRANSACTION ;
            SELECT idProducto, producto.nombre, e.cantidad, sku, imagen,  TRUNCATE ((precio + (precio * ri.iva)), 2) AS TOTAL, c.nombre AS CATEGORIA FROM producto
                JOIN existencia e on producto.idProducto = e.fkProducto
                JOIN sucursal s on e.fkSucursal = s.idSucursal
                JOIN region_iva ri on s.fkRegion = ri.idRegion
                JOIN categoria c on producto.fkCategoria = c.idCategoria
                WHERE fkSucursal = _idSucursal AND fkCategoria = _categoria AND activo = 1;
        COMMIT ;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_filtro_super_admin;
DELIMITER //
CREATE PROCEDURE obtener_filtro_super_admin(IN _jsonA JSON)
    BEGIN
        DECLARE _json       JSON;
        DECLARE _idSucursal JSON;

        DECLARE _categoria  VARCHAR(50);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.sucursal' ));
        SET _categoria   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.categoria'));

        START TRANSACTION ;
            SELECT idProducto, producto.nombre, e.cantidad, sku, imagen,  precio AS TOTAL, c.nombre AS CATEGORIA, fkProveedor, costo FROM producto
                JOIN existencia e on producto.idProducto = e.fkProducto
                JOIN sucursal s on e.fkSucursal = s.idSucursal
                JOIN region_iva ri on s.fkRegion = ri.idRegion
                JOIN categoria c on producto.fkCategoria = c.idCategoria
                WHERE fkSucursal = _idSucursal AND fkCategoria = _categoria;
        COMMIT ;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS realizar_venta;
DELIMITER //
CREATE PROCEDURE realizar_venta(IN _jsonA JSON)
BEGIN
    DECLARE _fkPunto        INT;
    DECLARE _idVenta        INT;
    DECLARE _cantidad       INT;
    DECLARE _fkUsuario      INT;
    DECLARE _fkTipoPago     INT;
    DECLARE _fkSucursal     INT;
    DECLARE _fkProducto     INT;

    DECLARE _json           JSON;
    DECLARE _productosJson  JSON;

    DECLARE _sku            VARCHAR(20);

    DECLARE _iva            DECIMAL(5,2);
    DECLARE _isr            DECIMAL(5,2);
    DECLARE _ieps           DECIMAL(5,2);
    DECLARE _total          DECIMAL(12,2);
    DECLARE _subTotal       DECIMAL(12,2);

    DECLARE _contador       INT DEFAULT 0;
    DECLARE _index          INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT '¡Error!' as 'Resultado';
        ROLLBACK;
    END;

    SET _json          = JSON_EXTRACT(_jsonA, '$[0]');
    SET _productosJson = JSON_EXTRACT(_json, '$.productos');
    SET _fkUsuario     = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
    SET _fkPunto       = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkPunto'   ));
    SET _fkTipoPago    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkTipoPago'));

    SET _contador = JSON_LENGTH(_productosJson) - 1;
    SET _total    = 0;

    START TRANSACTION;
        SELECT iva INTO _iva FROM sucursal 
            INNER JOIN punto_venta on sucursal.idSucursal = punto_venta.fkSucursal 
            INNER JOIN region_iva ri on sucursal.fkRegion = ri.idRegion WHERE idPunto = _fkPunto;

        SELECT fkSucursal INTO _fkSucursal FROM punto_venta WHERE idPunto = _fkPunto;
        INSERT INTO venta VALUES(0,_fkUsuario,_fkTipoPago,_fkSucursal,_total,NOW());

        WHILE _contador >= 0 DO
            SET _contador = _contador - 1;
            SET _json = JSON_EXTRACT(_productosJson, CONCAT('$[',_index,']'));
            SET _index = _index + 1;

            SET _sku = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.sku'));
            SET _cantidad = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.cantidad'));

            SELECT SUM(_cantidad * precio * (_iva + 1)) INTO _subTotal FROM producto JOIN existencia ON producto.idProducto = existencia.fkProducto WHERE sku = _sku AND fkSucursal = (SELECT fkSucursal FROM punto_venta WHERE idPunto = _fkPunto);
            SELECT idVenta into _idVenta FROM venta WHERE fkUsuario = _fkUsuario && fkTipoPago = _fkTipoPago && fecha = NOW() && total = _total;

            SELECT fkSucursal INTO _fkSucursal FROM punto_venta WHERE idPunto = _fkPunto;

            SET _total = _total + _subTotal;

            SELECT idProducto INTO _fkProducto FROM producto WHERE sku = _sku;

            SELECT ieps INTO _ieps FROM categoria INNER JOIN producto p on categoria.idCategoria = p.fkCategoria WHERE idProducto = _fkProducto;
            SELECT isr INTO _isr FROM categoria INNER JOIN producto p on categoria.idCategoria = p.fkCategoria WHERE idProducto = _fkProducto;

            IF ((SELECT cantidad FROM existencia WHERE fkSucursal =_fkSucursal AND fkProducto = _fkProducto) >= _cantidad) THEN
                INSERT INTO info_venta VALUES (0,_fkProducto, _idVenta, _cantidad, _iva, _ieps, _isr, _subtotal);
                UPDATE existencia SET cantidad = cantidad - _cantidad WHERE fkProducto = _fkProducto AND fkSucursal = (SELECT fkSucursal FROM punto_venta WHERE idPunto = _fkPunto);
            ELSE
                SET _contador = -10;
                ROLLBACK;
            END IF;
        END WHILE;

        IF(_contador != -10) THEN
            UPDATE venta SET total = _total WHERE idVenta = _idVenta;
            SELECT _idVenta AS 'FOLIO';
        ELSE
            SELECT 'No se puede realizar la venta' AS 'RESULTADO';
        END IF;
    COMMIT;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_detalles_compra;
DELIMITER //
CREATE PROCEDURE obtener_detalles_compra(_jsonA JSON)
BEGIN
    DECLARE _idVenta INT;
    DECLARE _json    JSON;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT '¡Error!' as 'Resultado';
        ROLLBACK;
    END;

    SET _json    = JSON_EXTRACT(_jsonA, '$[0]');
    SET _idVenta = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idVenta'));

    START TRANSACTION;
        SELECT u.usuario, venta.fecha, p.nombre, iv.cantidad, iv.subtotal ,venta.total FROM venta
            INNER JOIN usuario u on venta.fkUsuario = u.idUsuario
            INNER JOIN info_venta iv on venta.idVenta = iv.fkVenta
            INNER JOIN producto p on iv.fkProducto = p.idProducto
            WHERE _idVenta = idVenta;
    COMMIT;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS generar_factura;
DELIMITER //
CREATE PROCEDURE generar_factura(IN _jsonA JSON)
    BEGIN
        DECLARE _fkVenta       INT;
        DECLARE _fkRegimen     INT;
        
        DECLARE _json          JSON;

        DECLARE _rfc           VARCHAR(13);
        DECLARE _cp_persona    VARCHAR(10);
        DECLARE _nombre        VARCHAR(50);
        DECLARE _apellidoP     VARCHAR(50);
        DECLARE _apellidoM     VARCHAR(50);
        DECLARE _correo        VARCHAR(50);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkVenta    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkVenta'   ));
        SET _fkRegimen  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkRegimen' ));
        SET _rfc        = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.rfc'       ));
        SET _cp_persona = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.cp_persona'));
        SET _nombre     = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.nombre'    ));
        SET _apellidoP  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.apellidoP' ));
        SET _apellidoM  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.apellidoM' ));
        SET _correo     = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.correo'    ));

        START TRANSACTION;
            INSERT INTO datos_factura VALUES (0,_fkVenta,_fkRegimen,_rfc,_cp_persona,_nombre,_apellidoP,_apellidoM,_correo);
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS generar_devolucion;
DELIMITER //
CREATE PROCEDURE generar_devolucion(IN _jsonA JSON)
    BEGIN
        DECLARE _idVenta    INT;
        DECLARE _fkSucursal INT;
        DECLARE _fkUsuario  INT;
        DECLARE _permitido  INT;
        DECLARE _fkProducto INT;
        DECLARE _cantidad   INT;
        DECLARE _restaurar  INT;
        DECLARE _total      DECIMAL(12,2);
        DECLARE _tipoUser   INT;
        DECLARE _json       JSON;
        DECLARE _productos  JSON;

        DECLARE _date       DATE;
        DECLARE _usuario    VARCHAR(50);
        DECLARE _password   VARCHAR(128);

        DECLARE _index      INT DEFAULT 0;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _date       = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha'     ));
        SET _idVenta    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idVenta'   ));
        SET _usuario    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.usuario'   ));
        SET _password   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.password'  ));
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
        SET _fkUsuario  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
        SET _restaurar  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.restaurar' ));

        SELECT COUNT(*) INTO _permitido FROM usuario INNER JOIN sucursal s on usuario.idUsuario = s.fkAdmin
            WHERE usuario = _usuario AND password = SHA2(_password,512) AND idSucursal = _fkSucursal AND fkTipo = 2;

        START TRANSACTION;
            IF (_permitido = 1)
                THEN
                    SELECT COUNT(*) INTO _permitido FROM venta INNER JOIN info_venta iv on venta.idVenta = iv.fkVenta WHERE DATE(fecha) = DATE(_date) AND idVenta = _idVenta;
                    IF (_permitido > 0) THEN
                        SELECT fkTipo INTO _tipoUser FROM usuario WHERE idUsuario = _fkUsuario;
                        IF(_tipoUser = 2) THEN
                            SELECT fkUsuario INTO _fkUsuario FROM venta WHERE DATE(fecha) = DATE(_date) AND idVenta = _idVenta;
                        END IF;
                        SELECT total INTO _total FROM venta WHERE DATE(fecha) = DATE(_date) AND fkUsuario = _fkUsuario AND idVenta = _idVenta;

                        IF (_restaurar = 1) THEN
                            SELECT JSON_ARRAYAGG(JSON_OBJECT('idProducto',fkProducto,'cantidad',cantidad)) INTO _productos FROM info_venta WHERE fkVenta = _idVenta;
                            SET _index = JSON_LENGTH(_productos) - 1;
                            WHILE _index >= 0 DO
                                 SET _cantidad = JSON_EXTRACT(_productos, CONCAT('$[',_index,'].cantidad'));
                                 SET _fkProducto = JSON_EXTRACT(_productos, CONCAT('$[',_index,'].idProducto'));

                                 UPDATE existencia SET cantidad = (cantidad +  _cantidad)
                                    WHERE fkProducto = _fkProducto AND fkSucursal = _fkSucursal;

                                 SET _index = _index - 1;
                            END WHILE ;
                        END IF;

                        DELETE FROM info_venta WHERE fkVenta = _idVenta;
                        DELETE FROM venta WHERE DATE(fecha) = DATE(_date) AND fkUsuario = _fkUsuario AND idVenta = _idVenta;
                        INSERT INTO egresos VALUES (0,2,_fkUsuario,_fkSucursal,_total,NOW());
                        SELECT 'Devolución autorizada' as 'Status';
                    ELSE
                        SELECT 'La venta no existe' as 'Status';
                    END IF;
                ELSE
                    SELECT 'Devolución no autorizada' as 'Status';
            END IF;
        COMMIT;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_puntos_venta;
DELIMITER //
CREATE PROCEDURE obtener_puntos_venta(IN _jsonA JSON)
    BEGIN
       DECLARE _idUsuario INT;
       DECLARE _json      JSON;

       SET _json      = JSON_EXTRACT(_jsonA, '$[0]');
       SET _idUsuario = JSON_UNQUOTE(JSON_EXTRACT(_jsonA, '$.idUsuario'));

       SELECT idPunto, nombre FROM punto_venta WHERE fkUsuario = _idUsuario;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_sucursal;
DELIMITER //
CREATE PROCEDURE obtener_sucursal(IN _jsonA JSON)
    BEGIN
       DECLARE _idUsuario  INT;
       DECLARE _puntoVenta INT;

       DECLARE _json       JSON;

       SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
       SET _idUsuario  = JSON_UNQUOTE(JSON_EXTRACT(_jsonA, '$.idUsuario' ));
       SET _puntoVenta = JSON_UNQUOTE(JSON_EXTRACT(_jsonA, '$.puntoVenta'));

       SELECT fkSucursal FROM punto_venta WHERE fkUsuario = _idUsuario AND idPunto = _puntoVenta;
    END //
DELIMITER ;

# RANGO
# 1.- Día
# 2.- Semana
# 3.- Mensual
# 4.- Anual

DROP PROCEDURE IF EXISTS filtrar_ventas;
DELIMITER //
CREATE PROCEDURE filtrar_ventas(IN _jsonA JSON)
    BEGIN
       DECLARE _rango       INT;
       DECLARE _fkUsuario   INT;
       DECLARE _fkSucursal  INT;
       DECLARE _tipoUsuario INT;

       DECLARE _json        JSON;

       DECLARE _fecha       VARCHAR(50);

       DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

       SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
       SET _fkUsuario  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
       SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
       SET _fecha      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha'     ));
       SET _rango      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.rango'     ));

       START TRANSACTION;
           SELECT fkTipo INTO _tipoUsuario FROM usuario WHERE idUsuario = _fkUsuario;

            IF(_rango = 1) THEN
                SELECT * FROM venta WHERE DATE(fecha)  = DATE(_fecha)  AND ((fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal AND _tipoUsuario = 3) OR (fkSucursal = _fkSucursal AND _tipoUsuario = 2) OR (_tipoUsuario = 1));
            ELSEIF(_rango = 2) THEN
                SELECT * FROM venta WHERE WEEK(fecha)  = WEEK(_fecha)  AND ((fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal AND _tipoUsuario = 3) OR (fkSucursal = _fkSucursal AND _tipoUsuario = 2) OR (_tipoUsuario = 1));
            ELSEIF(_rango = 3) THEN
                SELECT * FROM venta WHERE MONTH(fecha) = MONTH(_fecha) AND ((fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal AND _tipoUsuario = 3) OR (fkSucursal = _fkSucursal AND _tipoUsuario = 2) OR (_tipoUsuario = 1));
            ELSE
                SELECT * FROM venta WHERE YEAR(fecha)  = YEAR(_fecha)  AND ((fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal AND _tipoUsuario = 3) OR (fkSucursal = _fkSucursal AND _tipoUsuario = 2) OR (_tipoUsuario = 1));
            END IF;
       COMMIT;

    END //
DELIMITER ;

USE naatika1_db_vaira;

DROP PROCEDURE IF EXISTS h_filtrar_vmensual_user;
DELIMITER //
CREATE PROCEDURE h_filtrar_vmensual_user(IN _jsonA JSON, OUT _salida TEXT)
    BEGIN
        DECLARE _fkUsuario   INT;
        DECLARE _fkSucursal  INT;
        DECLARE _totalVentas INT;
        DECLARE _json        JSON;
        DECLARE _resultado   TEXT;
        DECLARE _tempJson    JSON;
        DECLARE _tempJson2   JSON;
        DECLARE _monthName   VARCHAR(50);
        DECLARE _mes         INT DEFAULT 1;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
        SET _mes        = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.mes'));

        START TRANSACTION;
            IF ((SELECT COUNT(*) FROM venta WHERE MONTH(fecha) = _mes AND fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal) > 0) THEN
                SELECT COUNT(*) INTO _totalVentas FROM venta WHERE MONTH(fecha) = _mes AND fkSucursal = _fkSucursal AND fkUsuario = _fkUsuario;
                SELECT JSON_OBJECT('Ventas',_totalVentas,'Total',SUM(total)) INTO _tempJson FROM venta
                    WHERE MONTH(fecha) = _mes AND fkSucursal = _fkSucursal AND fkUsuario = _fkUsuario;

                SELECT MONTHNAME(fecha) INTO _monthName FROM venta WHERE fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal AND MONTH(fecha) = _mes LIMIT 1;

                SELECT JSON_ARRAYAGG(JSON_OBJECT('idVenta',idVenta,'fkTipoPago',fkTipoPago,'total',total,'fecha',fecha)) as Resultado INTO _tempJson2 FROM venta
                    WHERE MONTH(fecha) = _mes AND fkSucursal = _fkSucursal AND fkUsuario = _fkUsuario;
                SET _resultado = JSON_INSERT(_tempJson,'$.Detalles',_tempJson2);
            ELSE
                SET _resultado = 'Sin Registros';
            END IF;

            SET _salida = _resultado;
        COMMIT;
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS h_filtrar_vmensual_admin;
DELIMITER //
CREATE PROCEDURE h_filtrar_vmensual_admin(IN _jsonA JSON, OUT _salida TEXT)
    BEGIN
        DECLARE _fkSucursal  INT;
        DECLARE _totalVentas INT;
        DECLARE _json        JSON;
        DECLARE _resultado   TEXT;
        DECLARE _tempJson    JSON;
        DECLARE _tempJson2   JSON;
        DECLARE _monthName   VARCHAR(50);
        DECLARE _mes         INT DEFAULT 1;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
        SET _mes        = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.mes'));

        START TRANSACTION;
            IF ((SELECT COUNT(*) FROM venta WHERE MONTH(fecha) = _mes AND fkSucursal = _fkSucursal) > 0) THEN
                SELECT COUNT(*) INTO _totalVentas FROM venta WHERE MONTH(fecha) = _mes AND fkSucursal = _fkSucursal;
                SELECT JSON_OBJECT('Ventas',_totalVentas,'Total',SUM(total)) INTO _tempJson FROM venta
                    WHERE MONTH(fecha) = _mes AND fkSucursal = _fkSucursal;

                SELECT MONTHNAME(fecha) INTO _monthName FROM venta WHERE fkSucursal = _fkSucursal AND MONTH(fecha) = _mes LIMIT 1;

                SELECT JSON_ARRAYAGG(JSON_OBJECT('fkUsuario',fkUsuario,'idVenta',idVenta,'fkTipoPago',fkTipoPago,'total',total,'fecha',fecha)) as Resultado INTO _tempJson2 FROM venta
                    WHERE MONTH(fecha) = _mes AND fkSucursal = _fkSucursal;
                SET _resultado = JSON_INSERT(_tempJson,'$.Detalles',_tempJson2);
            ELSE
                SET _resultado = 'Sin Registros';
            END IF;

            SET _salida = _resultado;
        COMMIT;
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS h_filtrar_vmensual_sadmin;
DELIMITER //
CREATE PROCEDURE h_filtrar_vmensual_sadmin(IN _jsonA JSON, OUT _salida TEXT)
    BEGIN
        DECLARE _fkSucursal  INT;
        DECLARE _totalVentas INT;
        DECLARE _limit       INT;
        DECLARE _json        JSON;
        DECLARE _resultado   TEXT;
        DECLARE _tempResult  TEXT;
        DECLARE _tempJson    JSON;
        DECLARE _tempJson2   JSON;
        DECLARE _sucurName   TEXT;
        DECLARE _monthName   VARCHAR(50);
        DECLARE _mes         INT DEFAULT 1;
        DECLARE _index       INT DEFAULT 0;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
        SET _mes        = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.mes'));

        START TRANSACTION;
            IF ((SELECT COUNT(*) FROM venta WHERE MONTH(fecha) = _mes) > 0) THEN
                SELECT COUNT(*) INTO _totalVentas FROM venta WHERE MONTH(fecha) = _mes;

                SELECT JSON_OBJECT('Ventas',_totalVentas,'Total',SUM(total)) INTO _tempJson FROM venta WHERE MONTH(fecha) = _mes;

                SELECT MONTHNAME(fecha) INTO _monthName FROM venta WHERE MONTH(fecha) = _mes LIMIT 1;
                SET _resultado = JSON_INSERT(_tempJson,'$.Sacursal',JSON_ARRAY());

                SELECT JSON_ARRAYAGG(JSON_OBJECT('idSucursal',idSucursal)) INTO _tempJson2 FROM sucursal;
                SET _limit = JSON_LENGTH(_tempJson2);

                WHILE _index < _limit DO
                    SELECT JSON_EXTRACT(_tempJson2,CONCAT('$[',_index,'].idSucursal')) INTO _fkSucursal;
                    CALL h_filtrar_vmensual_admin(JSON_ARRAY(JSON_OBJECT('fkSucursal',_fkSucursal,'mes',_mes)),@jsonResult);
                    SET _tempResult = @jsonResult;

                    SELECT nombre INTO _sucurName FROM sucursal WHERE idSucursal = _fkSucursal;

                    SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Sacursal[',_index,']'),JSON_OBJECT(_fkSucursal,JSON_OBJECT('Nombre',_sucurName,'Detalles',CONVERT(_tempResult,JSON))));
                    SET _index = _index + 1;
                END WHILE;
            ELSE
                SET _resultado = 'Sin Registros';
            END IF;

            SET _salida = _resultado;
        COMMIT;
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS filtrar_ventas_mensuales;
DELIMITER //
CREATE PROCEDURE filtrar_ventas_mensuales(IN _jsonA JSON)
    BEGIN
        DECLARE _fkUsuario   INT;
        DECLARE _tipoUsuario INT;
        DECLARE _fkSucursal  INT;
        DECLARE _json        JSON;
        DECLARE _resultado   JSON;
        DECLARE _tempResult  TEXT;
        DECLARE _monthName   VARCHAR(50);
        DECLARE _mes         INT DEFAULT 1;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
        SET _resultado  = JSON_OBJECT('Resultado',JSON_ARRAY());

        START TRANSACTION;
            SELECT fkTipo INTO _tipoUsuario FROM usuario WHERE idUsuario = _fkUsuario;
            WHILE _mes <= 12 DO
                IF(_tipoUsuario = 1) THEN
                    CALL h_filtrar_vmensual_sadmin(JSON_ARRAY(JSON_OBJECT('mes',_mes)),@jsonResult);
                    SET _tempResult = @jsonResult;

                    IF ((SELECT COUNT(*) FROM venta WHERE MONTH(fecha) = _mes) > 0) THEN
                        SELECT MONTHNAME(fecha) INTO _monthName FROM venta WHERE MONTH(fecha) = _mes LIMIT 1;
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_mes-1,']'),JSON_OBJECT(_monthName,CONVERT(_tempResult,JSON)));
                    ELSE
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_mes-1,']'),'Sin registros');
                    END IF;
                ELSEIF(_tipoUsuario = 2) THEN
                    CALL h_filtrar_vmensual_admin(JSON_ARRAY(JSON_OBJECT('fkSucursal',_fkSucursal,'mes',_mes)),@jsonResult);
                    SET _tempResult = @jsonResult;

                    IF ((SELECT COUNT(*) FROM venta WHERE MONTH(fecha) = _mes AND fkSucursal = _fkSucursal) > 0) THEN
                        SELECT MONTHNAME(fecha) INTO _monthName FROM venta WHERE MONTH(fecha) = _mes LIMIT 1;
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_mes-1,']'),JSON_OBJECT(_monthName,CONVERT(_tempResult,JSON)));
                    ELSE
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_mes-1,']'),'Sin registros');
                    END IF;
                ELSE
                    CALL h_filtrar_vmensual_user(JSON_ARRAY(JSON_OBJECT('fkSucursal',_fkSucursal,'mes',_mes,'fkUsuario',_fkUsuario)),@jsonResult);
                    SET _tempResult = @jsonResult;

                    IF ((SELECT COUNT(*) FROM venta WHERE MONTH(fecha) = _mes AND fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal) > 0) THEN
                        SELECT MONTHNAME(fecha) INTO _monthName FROM venta WHERE MONTH(fecha) = _mes LIMIT 1;
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_mes-1,']'),JSON_OBJECT(_monthName,CONVERT(_tempResult,JSON)));
                    ELSE
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_mes-1,']'),'Sin registros');
                    END IF;
                END IF;

                SET _mes = _mes + 1;
            END WHILE ;
        COMMIT;

        SELECT _resultado AS 'Resultado';
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS h_filtrar_vsemanal_user;
DELIMITER //
CREATE PROCEDURE h_filtrar_vsemanal_user(IN _jsonA JSON, OUT _salida TEXT)
    BEGIN
        DECLARE _fkUsuario   INT;
        DECLARE _fkSucursal  INT;
        DECLARE _totalVentas INT;
        DECLARE _json        JSON;
        DECLARE _resultado   TEXT;
        DECLARE _tempJson    JSON;
        DECLARE _tempJson2   JSON;
        DECLARE _dayName     VARCHAR(50);
        DECLARE _fecha       DATE;
        DECLARE _dia         INT DEFAULT 1;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
        SET _fecha      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha'));
        SET _dia        = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.dia'));

        START TRANSACTION;
            IF ((SELECT COUNT(*) FROM venta WHERE fkUsuario = _fkUsuario AND DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkSucursal = _fkSucursal) > 0) THEN
                SELECT COUNT(*) INTO _totalVentas FROM venta
                    WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal;
                SELECT JSON_OBJECT('Ventas',COUNT(*),'Total',IFNULL(SUM(total),0)) INTO _tempJson FROM venta
                    WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal;


                SELECT DAYNAME(fecha) INTO _dayName FROM venta WHERE fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal AND DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) LIMIT 1;

                SELECT JSON_ARRAYAGG(JSON_OBJECT('idVenta',idVenta,'fkTipoPago',fkTipoPago,'total',total,'fecha',fecha)) as Resultado INTO _tempJson2 FROM venta
                WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkUsuario = _fkUsuario AND fkSucursal = _fkSucursal;

                SET _resultado = JSON_INSERT(_tempJson,'$.Detalles',_tempJson2);
            ELSE
                SET _resultado = 'Sin Registros';
            END IF;

            SET _salida = _resultado;
        COMMIT;
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS h_filtrar_vsemanal_admin;
DELIMITER //
CREATE PROCEDURE h_filtrar_vsemanal_admin(IN _jsonA JSON, OUT _salida TEXT)
    BEGIN
        DECLARE _fkSucursal  INT;
        DECLARE _totalVentas INT;
        DECLARE _json        JSON;
        DECLARE _resultado   TEXT;
        DECLARE _tempJson    JSON;
        DECLARE _tempJson2   JSON;
        DECLARE _fecha       DATE;
        DECLARE _dayName     VARCHAR(50);
        DECLARE _dia         INT DEFAULT 1;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
        SET _dia        = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.dia'));
        SET _fecha      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha'));

        START TRANSACTION;
            IF ((SELECT COUNT(*) FROM venta WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkSucursal = _fkSucursal) > 0) THEN
                SELECT COUNT(*) INTO _totalVentas FROM venta
                    WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkSucursal = _fkSucursal;

                SELECT JSON_OBJECT('Ventas',_totalVentas,'Total',SUM(total)) INTO _tempJson FROM venta
                    WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkSucursal = _fkSucursal;

                SELECT DAYNAME(fecha) INTO _dayName FROM venta WHERE fkSucursal = _fkSucursal AND DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) LIMIT 1;

                SELECT JSON_ARRAYAGG(JSON_OBJECT('fkUsuario',fkUsuario,'idVenta',idVenta,'fkTipoPago',fkTipoPago,'total',total,'fecha',fecha)) as Resultado INTO _tempJson2 FROM venta
                WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkSucursal = _fkSucursal;
#
                SET _resultado = JSON_INSERT(_tempJson,'$.Detalles',_tempJson2);
            ELSE
                SET _resultado = 'Sin Registros';
            END IF;

            SET _salida = _resultado;
        COMMIT;
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS h_filtrar_vsemanal_sadmin;
DELIMITER //
CREATE PROCEDURE h_filtrar_vsemanal_sadmin(IN _jsonA JSON, OUT _salida TEXT)
    BEGIN
        DECLARE _fkSucursal  INT;
        DECLARE _totalVentas INT;
        DECLARE _limit       INT;
        DECLARE _json        JSON;
        DECLARE _resultado   TEXT;
        DECLARE _tempResult  TEXT;
        DECLARE _tempJson    JSON;
        DECLARE _tempJson2   JSON;
        DECLARE _sucurName   TEXT;
        DECLARE _fecha       DATE;
        DECLARE _dayName     VARCHAR(50);
        DECLARE _dia         INT DEFAULT 1;
        DECLARE _index       INT DEFAULT 0;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _dia        = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.dia'));
        SET _fecha      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha'));

        START TRANSACTION;
            IF ((SELECT COUNT(*) FROM venta WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha)) > 0) THEN
                SELECT COUNT(*) INTO _totalVentas FROM venta WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha);

                SELECT JSON_OBJECT('Ventas',_totalVentas,'Total',SUM(total)) INTO _tempJson FROM venta
                    WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha);

                SELECT DAYNAME(fecha) INTO _dayName FROM venta WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) LIMIT 1;
                SET _resultado = JSON_INSERT(_tempJson,'$.Sacursal',JSON_ARRAY());

                SELECT JSON_ARRAYAGG(JSON_OBJECT('idSucursal',idSucursal)) INTO _tempJson2 FROM sucursal;
                SET _limit = JSON_LENGTH(_tempJson2);

                WHILE _index < _limit DO
                    SELECT JSON_EXTRACT(_tempJson2,CONCAT('$[',_index,'].idSucursal')) INTO _fkSucursal;
                    CALL h_filtrar_vsemanal_admin(JSON_ARRAY(JSON_OBJECT('fkSucursal',_fkSucursal,'dia',_dia,'fecha',_fecha)),@jsonResult);
                    SET _tempResult = @jsonResult;

                    SELECT nombre INTO _sucurName FROM sucursal WHERE idSucursal = _fkSucursal;

                    SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Sacursal[',_index,']'),JSON_OBJECT(_fkSucursal,JSON_OBJECT('Nombre',_sucurName,'Detalles',CONVERT(_tempResult,JSON))));

                    SET _index = _index + 1;
                END WHILE;
            ELSE
                SET _resultado = 'Sin Registros';
            END IF;

            SET _salida = _resultado;
        COMMIT;
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS filtrar_ventas_semanal;
DELIMITER //
CREATE PROCEDURE filtrar_ventas_semanal(IN _jsonA JSON)
    BEGIN
        DECLARE _fkUsuario   INT;
        DECLARE _tipoUsuario INT;
        DECLARE _fkSucursal  INT;

        DECLARE _json        JSON;
        DECLARE _resultado   JSON;

        DECLARE _tempResult  TEXT;

        DECLARE _fecha       VARCHAR(50);
        DECLARE _dayName     VARCHAR(50);

        DECLARE _dia         INT DEFAULT 1;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
        SET _fecha      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha'     ));
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));
        SET _resultado  = JSON_OBJECT('Resultado',JSON_ARRAY());

        START TRANSACTION;
            SELECT fkTipo INTO _tipoUsuario FROM usuario WHERE idUsuario = _fkUsuario;

            WHILE _dia <= 7 DO
                IF(_tipoUsuario = 1) THEN
                    CALL h_filtrar_vsemanal_sadmin(JSON_ARRAY(JSON_OBJECT('dia',_dia,'fecha',_fecha)),@jsonResult);
                    SET _tempResult = @jsonResult;

                    IF ((SELECT COUNT(*) FROM venta WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha)) > 0) THEN
                        SELECT DAYNAME(fecha) INTO _dayName FROM venta WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) LIMIT 1;
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_dia-1,']'),JSON_OBJECT(_dayName,CONVERT(_tempResult,JSON)));
                    ELSE
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_dia-1,']'),'Sin registros');
                    END IF;
                ELSEIF(_tipoUsuario = 2) THEN
                    CALL h_filtrar_vsemanal_admin(JSON_ARRAY(JSON_OBJECT('fkSucursal',_fkSucursal,'dia',_dia,'fecha',_fecha)),@jsonResult);
                    SET _tempResult = @jsonResult;

                    IF ((SELECT COUNT(*) FROM venta WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkSucursal = _fkSucursal) > 0) THEN
                        SELECT DAYNAME(fecha) INTO _dayName FROM venta WHERE DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkSucursal = _fkSucursal LIMIT 1;
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_dia-1,']'),JSON_OBJECT(_dayName,CONVERT(_tempResult,JSON)));
                    ELSE
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_dia-1,']'),'Sin registros');
                    END IF;
                ELSE
                    CALL h_filtrar_vsemanal_user(JSON_ARRAY(JSON_OBJECT('fkSucursal',_fkSucursal,'dia',_dia,'fkUsuario',_fkUsuario,'fecha',_fecha)),@jsonResult);
                    SET _tempResult = @jsonResult;

                    IF ((SELECT COUNT(*) FROM venta WHERE fkUsuario = _fkUsuario AND DAYOFWEEK(fecha) = _dia AND WEEK(fecha) = WEEK(_fecha) AND fkSucursal = _fkSucursal) > 0) THEN
                        SELECT DAYNAME(fecha) INTO _dayName FROM venta WHERE WEEK(fecha) = WEEK(_fecha) LIMIT 1;
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_dia-1,']'),JSON_OBJECT(_dayName,CONVERT(_tempResult,JSON)));
                    ELSE
                        SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_dia-1,']'),'Sin registros');
                    END IF;
                END IF;

                SET _dia = _dia + 1;
            END WHILE;
        COMMIT;

        IF(_tipoUsuario = 1) THEN
            SELECT _resultado as 'Resultado Super-Administrador';
        ELSEIF(_tipoUsuario = 2) THEN
            SELECT _resultado as 'Resultado Administrador';
        ELSE
            SELECT _resultado as 'Resultado';
        END IF;
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS obtener_usuarios_admin;
DELIMITER //
CREATE PROCEDURE obtener_usuarios_admin(IN _jsonA JSON)
    BEGIN
        DECLARE _idAdmin INT;
        DECLARE _json    JSON;

        SET _json    = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idAdmin = JSON_UNQUOTE(JSON_EXTRACT(_jsonA, '$.idAdmin'));

        SELECT DISTINCT idUsuario, usuario.nombre, correo, usuario.telefono, usuario, s.nombre, tipo FROM usuario
            JOIN sucursal_usuario su ON fkUsuario = idUsuario
            JOIN sucursal s on su.fkSucursal = s.idSucursal
            JOIN tipo t on usuario.fkTipo = t.idTipo
            WHERE fkTipo = 3 AND fkAdmin = _idAdmin AND activo = 1;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS actualizar_usuario;
DELIMITER //
CREATE PROCEDURE actualizar_usuario(IN _jsonA JSON)
    BEGIN
        DECLARE _json JSON;
        DECLARE _idUsuario VARCHAR(50);
        DECLARE _correo VARCHAR(50);
        DECLARE _telefono VARCHAR(50);
        DECLARE _password VARCHAR(128);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json      = JSON_EXTRACT(_jsonA, '$[0]');
        SET _idUsuario = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idUsuario'));
        SET _correo    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.correo'));
        SET _telefono  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.telefono'));
        SET _password  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.password'));

        IF (_password = '') THEN
            IF(_correo != (SELECT correo FROM usuario WHERE idUsuario = _idUsuario) && _telefono != (SELECT telefono FROM usuario WHERE idUsuario = _idUsuario)) THEN
                UPDATE usuario SET correo = _correo, telefono = _telefono WHERE idUsuario = _idUsuario;
            ELSEIF(_correo = (SELECT correo FROM usuario WHERE idUsuario = _idUsuario) && _telefono != (SELECT telefono FROM usuario WHERE idUsuario = _idUsuario)) THEN
                UPDATE usuario SET telefono = _telefono WHERE idUsuario = _idUsuario;
            ELSEIF(_correo != (SELECT correo FROM usuario WHERE idUsuario = _idUsuario) && _telefono = (SELECT telefono FROM usuario WHERE idUsuario = _idUsuario)) THEN
                UPDATE usuario SET correo = _correo WHERE idUsuario = _idUsuario;
            END IF;
        ELSE
            IF(_correo != (SELECT correo FROM usuario WHERE idUsuario = _idUsuario) && _telefono != (SELECT telefono FROM usuario WHERE idUsuario = _idUsuario)) THEN
                UPDATE usuario SET correo = _correo, telefono = _telefono, password = sha2(_password, 512) WHERE idUsuario = _idUsuario;
            ELSEIF(_correo = (SELECT correo FROM usuario WHERE idUsuario = _idUsuario) && _telefono != (SELECT telefono FROM usuario WHERE idUsuario = _idUsuario)) THEN
                UPDATE usuario SET telefono = _telefono, password = sha2(_password, 512) WHERE idUsuario = _idUsuario;
            ELSEIF(_correo != (SELECT correo FROM usuario WHERE idUsuario = _idUsuario) && _telefono = (SELECT telefono FROM usuario WHERE idUsuario = _idUsuario)) THEN
                UPDATE usuario SET correo = _correo, password = sha2(_password, 512) WHERE idUsuario = _idUsuario;
            END IF;
        END IF;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS actualizar_puntos_usuario;
DELIMITER //
CREATE PROCEDURE actualizar_puntos_usuario(IN _jsonA JSON)
        BEGIN
            DECLARE _json JSON;
            DECLARE _fkUsuario INT;
            DECLARE _idPunto INT;
            DECLARE _puntosJSON JSON;

            DECLARE _contador INT DEFAULT 0;
            DECLARE _index INT DEFAULT 0;

            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                SELECT '¡Error!' as 'Resultado';
                ROLLBACK;
                END;

            SET _json          = JSON_EXTRACT(_jsonA, '$[0]');
            SET _puntosJson    = JSON_EXTRACT(_json, '$.puntos');
            SET _fkUsuario     = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idUsuario' ));
            SET _contador      = JSON_LENGTH(_puntosJson) - 1;

            START TRANSACTION ;
                UPDATE punto_venta SET fkUsuario = NULL WHERE fkUsuario = _fkUsuario;
                WHILE _contador >= 0 DO
                    SET _contador = _contador - 1;
                    SET _json = JSON_EXTRACT(_puntosJson, CONCAT('$[',_index,']'));
                    SET _index = _index + 1;

                    SET _idPunto = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idPunto'));

                    IF((SELECT fkUsuario FROM punto_venta WHERE idPunto = _idPunto) IS NULL) THEN
                        UPDATE punto_venta SET fkUsuario = _fkUsuario WHERE idPunto = _idPunto;
                    ELSE
                        SET _contador = -10;
                        ROLLBACK;
                    END IF ;
                END WHILE ;

                IF(_contador != -10) THEN
                    SELECT 'Actualizado' AS 'Resultado';
                ELSE
                    SELECT 'No se pudo actualizar los puntos de venta' AS 'Resultado';
                END IF ;
            COMMIT ;
        END //
DELIMITER ;

# RANGO
# 1.- Día
# 2.- Semana
# 3.- Mensual
# 4.- Anual

DROP PROCEDURE IF EXISTS filtrar_ventas_categoria;
DELIMITER //
CREATE PROCEDURE filtrar_ventas_categoria(IN _jsonA JSON)
    BEGIN
        DECLARE _fkUsuario   INT;
        DECLARE _fkSucursal  INT;
        DECLARE _limite      INT;
        DECLARE _idCategoria INT;
        DECLARE _tipoUsuario INT;
        DECLARE _rango       INT;
        DECLARE _fecha       DATE;

        DECLARE _tempJson    JSON;

        DECLARE _json        JSON;
        DECLARE _resultado   JSON;

        DECLARE _nombre      VARCHAR(50);

        DECLARE _index       INT DEFAULT 0;
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _resultado   = '{"Resultado": []}';
        SET _json        = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
        SET _rango       = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.rango'     ));
        SET _fecha       = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha'     ));

        START TRANSACTION;
            SELECT fkTipo INTO _tipoUsuario FROM usuario WHERE idUsuario = _fkUsuario;

            SELECT fkSucursal INTO _fkSucursal FROM sucursal_usuario WHERE fkUsuario = _fkUsuario;

            IF(_tipoUsuario = 1) THEN
                SELECT COUNT(*) INTO _limite FROM categoria;
            ELSE
                SELECT COUNT(DISTINCT categoria.idCategoria) INTO _limite FROM categoria INNER JOIN producto p3 on categoria.idCategoria = p3.fkCategoria INNER JOIN existencia e on p3.idProducto = e.fkProducto
                    WHERE fkSucursal = _fkSucursal;
            END IF;

            WHILE _index < _limite DO
                IF(_tipoUsuario = 1) THEN
                    SELECT idCategoria INTO _idCategoria FROM categoria
                        ORDER BY idCategoria
                        LIMIT _index,1;
                ELSE
                    SELECT DISTINCT idCategoria INTO _idCategoria FROM categoria INNER JOIN producto p3 on categoria.idCategoria = p3.fkCategoria INNER JOIN existencia e on p3.idProducto = e.fkProducto
                        WHERE fkSucursal = _fkSucursal
                        ORDER BY idCategoria
                        LIMIT _index,1;
                END IF;

                SELECT nombre INTO _nombre FROM categoria WHERE idCategoria = _idCategoria;

                IF(_tipoUsuario = 1) THEN
                    SELECT JSON_OBJECT('Ventas',COUNT(*),'Subtotal',IFNULL(SUM(subtotal),0),'fkCategoria',_idCategoria) INTO _tempJson FROM venta
                        INNER JOIN info_venta iv on venta.idVenta = iv.fkVenta
                        INNER JOIN producto p on iv.fkProducto = p.idProducto
                        WHERE fkCategoria = _idCategoria AND
                              (    (DATE(fecha)  = DATE(_fecha)  AND _rango = 1)
                                OR (WEEK(fecha)  = WEEK(_fecha)  AND _rango = 2)
                                OR (MONTH(fecha) = MONTH(_fecha) AND _rango = 3)
                                OR (YEAR(fecha)  = YEAR(_fecha)  AND _rango = 4));
                ELSEIF(_tipoUsuario = 2) THEN
                    SELECT JSON_OBJECT('Ventas',COUNT(*),'Subtotal',IFNULL(SUM(subtotal),0),'fkCategoria',_idCategoria,'fkSucursal',_fkSucursal) INTO _tempJson FROM venta
                        INNER JOIN info_venta iv on venta.idVenta = iv.fkVenta
                        INNER JOIN producto p on iv.fkProducto = p.idProducto
                        WHERE fkCategoria = _idCategoria AND fkSucursal = _fkSucursal AND
                              (    (DATE(fecha)  = DATE(_fecha)  AND _rango = 1)
                                OR (WEEK(fecha)  = WEEK(_fecha)  AND _rango = 2)
                                OR (MONTH(fecha) = MONTH(_fecha) AND _rango = 3)
                                OR (YEAR(fecha)  = YEAR(_fecha)  AND _rango = 4));
                ELSE
                    SELECT JSON_OBJECT('Ventas',COUNT(*),'Subtotal',IFNULL(SUM(subtotal),0),'fkCategoria',_idCategoria,'fkSucursal',_fkSucursal,'fkUsuario',_fkUsuario) INTO _tempJson FROM venta
                        INNER JOIN info_venta iv on venta.idVenta = iv.fkVenta
                        INNER JOIN producto p on iv.fkProducto = p.idProducto
                        WHERE fkCategoria = _idCategoria AND fkSucursal = _fkSucursal AND fkUsuario = _fkUsuario AND
                              (    (DATE(fecha)  = DATE(_fecha)  AND _rango = 1)
                                OR (WEEK(fecha)  = WEEK(_fecha)  AND _rango = 2)
                                OR (MONTH(fecha) = MONTH(_fecha) AND _rango = 3)
                                OR (YEAR(fecha)  = YEAR(_fecha)  AND _rango = 4));
                END IF;

                IF(_tempJson IS NULL) THEN
                    SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_index,']'),JSON_OBJECT('id',_index, 'nombre',_nombre, 'detalles', 'Sin ventas'));
                ELSE
                    SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_index,']'),JSON_OBJECT('id',_index, 'nombre',_nombre, 'detalles', _tempJson));
                END IF;

                SET _index = _index + 1;
            END WHILE;
        COMMIT;

        IF(_tipoUsuario = 1) THEN
            SELECT _resultado as 'Resultado Super-Administrador';
        ELSEIF(_tipoUsuario = 2) THEN
            SELECT _resultado as 'Resultado Administrador';
        ELSE
            SELECT _resultado as 'Resultado';
        END IF;
    END//
DELIMITER ;

# RANGO
# 1.- Día
# 2.- Semana
# 3.- Mensual
# 4.- Anual

DROP PROCEDURE IF EXISTS filtrar_ventas_producto;
DELIMITER //
CREATE PROCEDURE filtrar_ventas_producto(IN _jsonA JSON)
    BEGIN
        DECLARE _fkUsuario   INT;
        DECLARE _fkSucursal  INT;
        DECLARE _limite      INT;
        DECLARE _idProducto  INT;
        DECLARE _tipoUsuario INT;
        DECLARE _rango       INT;
        DECLARE _fecha       DATE;

        DECLARE _tempJson    JSON;

        DECLARE _json        JSON;
        DECLARE _resultado   JSON;

        DECLARE _nombre      VARCHAR(50);

        DECLARE _index       INT DEFAULT 0;
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _resultado   = '{"Resultado": []}';
        SET _json        = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario' ));
        SET _rango       = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.rango'     ));
        SET _fecha       = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha'     ));
        SET _fkSucursal  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'));

        START TRANSACTION;
            SELECT fkTipo INTO _tipoUsuario FROM usuario WHERE idUsuario = _fkUsuario;

            IF(_tipoUsuario = 1) THEN
                SELECT COUNT(*) INTO _limite FROM producto;
            ELSE
                SELECT COUNT(*) INTO _limite FROM existencia INNER JOIN producto p on existencia.fkProducto = p.idProducto
                    WHERE fkSucursal = _fkSucursal;
            END IF;

            WHILE _index < _limite DO
                IF(_tipoUsuario = 1) THEN
                    SELECT idProducto INTO _idProducto FROM producto
                        ORDER BY idProducto
                        LIMIT _index,1;
                ELSE
                    SELECT idProducto INTO _idProducto FROM existencia INNER JOIN producto p2 on existencia.fkProducto = p2.idProducto
                        WHERE fkSucursal = _fkSucursal
                        ORDER BY fkProducto, fkSucursal
                        LIMIT _index,1;
                END IF;

                SELECT nombre INTO _nombre FROM producto WHERE idProducto = _idProducto;

                IF(_tipoUsuario = 1) THEN
                    SELECT JSON_OBJECT('Ventas',COUNT(*),'Subtotal',IFNULL(SUM(subtotal),0),'fkProducto',_idProducto) INTO _tempJson FROM venta
                        INNER JOIN info_venta iv on venta.idVenta = iv.fkVenta
                        WHERE fkProducto = _idProducto AND
                              (    (DATE(fecha)  = DATE(_fecha)  AND _rango = 1)
                                OR (WEEK(fecha)  = WEEK(_fecha)  AND _rango = 2)
                                OR (MONTH(fecha) = MONTH(_fecha) AND _rango = 3)
                                OR (YEAR(fecha)  = YEAR(_fecha)  AND _rango = 4));
                ELSEIF(_tipoUsuario = 2) THEN
                    SELECT JSON_OBJECT('Ventas',COUNT(*),'Subtotal',IFNULL(SUM(subtotal),0),'fkProducto',_idProducto,'fkSucursal',_fkSucursal) INTO _tempJson FROM venta
                        INNER JOIN info_venta iv on venta.idVenta = iv.fkVenta
                        WHERE fkProducto = _idProducto AND fkSucursal = _fkSucursal AND
                              (    (DATE(fecha)  = DATE(_fecha)  AND _rango = 1)
                                OR (WEEK(fecha)  = WEEK(_fecha)  AND _rango = 2)
                                OR (MONTH(fecha) = MONTH(_fecha) AND _rango = 3)
                                OR (YEAR(fecha)  = YEAR(_fecha)  AND _rango = 4));
                ELSE
                    SELECT JSON_OBJECT('Ventas',COUNT(*),'Subtotal',IFNULL(SUM(subtotal),0),'fkProducto',_idProducto,'fkSucursal',_fkSucursal,'fkUsuario',_fkUsuario) INTO _tempJson FROM venta
                        INNER JOIN info_venta iv on venta.idVenta = iv.fkVenta
                        WHERE fkProducto = _idProducto AND fkSucursal = _fkSucursal AND fkUsuario = _fkUsuario AND
                              (    (DATE(fecha)  = DATE(_fecha)  AND _rango = 1)
                                OR (WEEK(fecha)  = WEEK(_fecha)  AND _rango = 2)
                                OR (MONTH(fecha) = MONTH(_fecha) AND _rango = 3)
                                OR (YEAR(fecha)  = YEAR(_fecha)  AND _rango = 4));
                END IF;

                IF(_tempJson IS NULL) THEN
                    SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_index,']'),JSON_OBJECT('nombre', _nombre,'detalles', 'Sin ventas'));
                ELSE
                    SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado[',_index,']'),JSON_OBJECT('nombre', _nombre,'detalles', _tempJson));
                END IF;

                SET _index = _index + 1;
            END WHILE;
        COMMIT;

        IF(_tipoUsuario = 1) THEN
            SELECT _resultado as 'Resultado Super-Administrador';
        ELSEIF(_tipoUsuario = 2) THEN
            SELECT _resultado as 'Resultado Administrador';
        ELSE
            SELECT _resultado as 'Resultado';
        END IF;
    END//
DELIMITER ;

DROP PROCEDURE IF EXISTS actualizar_producto_inventario;
DELIMITER //
CREATE PROCEDURE actualizar_producto_inventario(IN _jsonA JSON)
    BEGIN
        DECLARE _json       JSON;
        DECLARE _fkUsuario  INT;
        DECLARE _fkProducto INT;
        DECLARE _fkSucursal  INT;
        DECLARE _cantidad   INT;

        DECLARE _costo     DECIMAL(10,2);
        DECLARE _total     DECIMAL(10,2);


        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json        = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario'   ));
        SET _fkProducto  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkProducto'  ));
        SET _fkSucursal  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'  ));
        SET _cantidad    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.cantidad'     ));

        START TRANSACTION ;
            SELECT costo INTO _costo FROM producto WHERE idProducto = _fkProducto;
            IF(_cantidad > 0) THEN
                SELECT _costo * _cantidad INTO _total;
                IF((SELECT COUNT(*) FROM existencia WHERE fkSucursal = _fkSucursal AND fkProducto = _fkProducto) > 0) THEN
                    IF((SELECT cantidad FROM existencia WHERE fkSucursal = _fkSucursal AND fkProducto = _fkProducto) < _cantidad) THEN
                        UPDATE existencia SET cantidad = _cantidad WHERE fkSucursal = _fkSucursal AND fkProducto = _fkProducto;
                        INSERT INTO egresos VALUE (0, 1, _fkUsuario, _fkSucursal, _total, NOW());
                        SELECT 'SUCCESS' AS "RESULTADO";
                    ELSE
                        SELECT 'La cantidad debe ser mayor a la existente' AS "RESULTADO";
                    END IF;
                ELSE
                    SELECT 'No existe ese producto en la sucursal indicada' AS "RESULTADO";
                END IF ;
            ELSE
                SELECT 'La cantidad debe ser mayor a 0' AS "RESULTADO";
            END IF;
        COMMIT ;

    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS insertar_proveedor;
DELIMITER //
CREATE PROCEDURE insertar_proveedor(IN _jsonA JSON)
    BEGIN
        DECLARE _json JSON;
        DECLARE _nombre VARCHAR(50);
        DECLARE _telefono VARCHAR(50);
        DECLARE _correo VARCHAR(50);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _nombre     = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.nombre'     ));
        SET _telefono   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.telefono'   ));
        SET _correo     = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.correo'     ));

        START TRANSACTION ;
            INSERT INTO proveedor VALUES (0, _nombre, _telefono, _correo, 1);
            SELECT idProveedor AS 'IDENTIFICADOR' FROM proveedor WHERE nombre = _nombre;
        COMMIT ;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS h_insertar_sucursal;
DELIMITER //
CREATE PROCEDURE h_insertar_sucursal(IN _jsonA JSON)
    BEGIN
        DECLARE _json JSON;
        DECLARE _tempJson JSON;
        DECLARE _fkSucursal INT;
        DECLARE _fkProducto INT;
        DECLARE _index INT DEFAULT 0;
        DECLARE _limit INT;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡ErrorDDD!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkSucursal = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idSucursal'    ));

        START TRANSACTION ;
            SELECT JSON_ARRAYAGG(JSON_OBJECT('idProducto',idProducto)) INTO _tempJson FROM producto;
            SET _limit = JSON_LENGTH(_tempJson);
            WHILE _index < _limit DO
                SELECT JSON_EXTRACT(_tempJson,CONCAT('$[',_index,'].idProducto')) INTO _fkProducto;

                INSERT INTO existencia VALUES (_fkProducto,_fkSucursal,0);

                SET _index = _index + 1;
            END WHILE ;
        COMMIT ;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS insertar_sucursal;
DELIMITER //
CREATE PROCEDURE insertar_sucursal(IN _jsonA JSON)
    BEGIN
        DECLARE _json JSON;
        DECLARE _nombre VARCHAR(50);
        DECLARE _calle VARCHAR(50);
        DECLARE _colonia VARCHAR(50);
        DECLARE _cp VARCHAR(50);
        DECLARE _telefono VARCHAR(50);
        DECLARE _region INT;
        DECLARE _idSucursal INT;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json       = JSON_EXTRACT(_jsonA, '$[0]');
        SET _nombre     = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.nombre'    ));
        SET _calle      = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.calle'     ));
        SET _colonia    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.colonia'   ));
        SET _cp         = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.cp'        ));
        SET _telefono   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.telefono'  ));
        SET _region     = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.region'    ));

        START TRANSACTION ;
            INSERT INTO sucursal VALUES (0, _region, null, _nombre, _calle, _colonia, _cp, _telefono);
            SELECT idSucursal INTO _idSucursal FROM sucursal WHERE nombre = _nombre AND calle = _calle AND CP = _cp ORDER BY idSucursal DESC LIMIT 1;
            CALL h_insertar_sucursal(CONCAT('[{"idSucursal":',_idSucursal,'}]'));
            SELECT 'Sucursal agregada' as 'Resultado';
        COMMIT ;
    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS realizar_corte_caja;
DELIMITER //
CREATE PROCEDURE realizar_corte_caja(IN _jsonA JSON)
    BEGIN
        DECLARE _fkUsuario    INT;
        DECLARE _fkSucursal   INT;
        DECLARE _tipoUsuario  INT;
        DECLARE _limit        INT;
        DECLARE _json         JSON;
        DECLARE _resultado    JSON;
        DECLARE _nombreSucur  TEXT;
        DECLARE _index        INT DEFAULT 0;
        DECLARE _ingresos     DECIMAL(10,2);
        DECLARE _egresos      DECIMAL(10,2);
        DECLARE _fecha_inicio DATETIME;
        DECLARE _fecha_final  DATETIME;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json         = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario'   ));
        SET _fkSucursal   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkSucursal'  ));
        SET _fecha_inicio = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha_inicio'));
        SET _fecha_final  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fecha_final' ));
        SET _resultado    = JSON_OBJECT('Resultado',JSON_OBJECT('Fecha/Hora Inicio',_fecha_inicio,'Fecha/Hora Final',_fecha_final,'Info',JSON_ARRAY()));

        START TRANSACTION ;
            SELECT fkTipo INTO _tipoUsuario FROM usuario WHERE idUsuario = _fkUsuario;

            IF (_tipoUsuario = 1) THEN
                SELECT JSON_ARRAYAGG(JSON_OBJECT('fkSucursal',idSucursal)) INTO _json FROM sucursal;
            ELSEIF (_tipoUsuario = 2) THEN
                SELECT JSON_ARRAYAGG(JSON_OBJECT('fkSucursal',fkSucursal)) INTO _json FROM sucursal_usuario WHERE fkUsuario = _fkUsuario;
            END IF;

            IF (_tipoUsuario = 3) THEN
                SELECT IFNULL(SUM(total),0) INTO _ingresos FROM venta WHERE fecha >= _fecha_inicio AND fecha <= _fecha_final AND fkSucursal = _fkSucursal AND fkUsuario = _fkUsuario;
                SELECT IFNULL(SUM(total),0) INTO _egresos FROM egresos WHERE fecha >= _fecha_inicio AND fecha <= _fecha_final AND fkSucursal = _fkSucursal AND fkUsuario = _fkUsuario;

                SET _resultado = JSON_INSERT(_resultado,'$.Resultado.Info[0]',JSON_OBJECT('Ingresos',_ingresos,'Egresos',_egresos,'Total',_ingresos-_egresos));
            ELSE
                SET _limit = JSON_LENGTH(_json);
                WHILE _index < _limit DO
                    SELECT JSON_EXTRACT(_json,CONCAT('$[',_index,'].fkSucursal')) INTO _fkSucursal;
                    SELECT IFNULL(SUM(total),0) INTO _ingresos FROM venta WHERE fecha >= _fecha_inicio AND fecha <= _fecha_final AND fkSucursal = _fkSucursal;
                    SELECT IFNULL(SUM(total),0) INTO _egresos FROM egresos WHERE fecha >= _fecha_inicio AND fecha <= _fecha_final AND fkSucursal = _fkSucursal;

                    SELECT nombre INTO _nombreSucur FROM sucursal WHERE idSucursal = _fkSucursal;

                    SET _resultado = JSON_INSERT(_resultado,CONCAT('$.Resultado.Info[',_index,']'),JSON_OBJECT(_fkSucursal,JSON_OBJECT('Sucursal',_nombreSucur,'Ingresos',_ingresos,'Egresos',_egresos,'Total',_ingresos-_egresos)));
                    SET _index = _index + 1;
                END WHILE ;
            END IF;

            SELECT _resultado AS 'Resultado';
        COMMIT ;

    END //
DELIMITER ;

DROP PROCEDURE IF EXISTS actualizar_sucursal_admin;
DELIMITER //
CREATE PROCEDURE actualizar_sucursal_admin(IN _jsonA JSON)
    BEGIN
        DECLARE _json            JSON;
        DECLARE _fkUsuario       INT;
        DECLARE _sucursalActual  INT;
        DECLARE _idSucursal      INT;

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json        = JSON_EXTRACT(_jsonA, '$[0]');
        SET _fkUsuario   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.fkUsuario'   ));
        SET _idSucursal  = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.idSucursal'  ));

        START TRANSACTION ;
            IF((SELECT idSucursal FROM sucursal WHERE fkAdmin = _fkUsuario) IS NOT NULL) THEN
                SELECT idSucursal INTO _sucursalActual FROM sucursal WHERE fkAdmin = _fkUsuario;
                UPDATE sucursal SET fkAdmin = NULL WHERE idSucursal = _sucursalActual;
            END IF;
            IF((SELECT fkSucursal FROM sucursal_usuario WHERE fkUsuario = _fkUsuario) IS NOT NULL) THEN
                UPDATE sucursal_usuario SET fkSucursal = _idSucursal WHERE fkUsuario = _fkUsuario;
            ELSE
                INSERT INTO sucursal_usuario VALUE (_fkUsuario, _idSucursal);
            END IF;

            UPDATE sucursal SET fkAdmin = _fkUsuario WHERE idSucursal = _idSucursal;
            SELECT fkAdmin FROM sucursal WHERE idSucursal = _idSucursal;
        COMMIT ;
    END //

DELIMITER ;

DROP PROCEDURE IF EXISTS agregar_categoria;
DELIMITER //
CREATE PROCEDURE agregar_categoria(IN _jsonA JSON)
    BEGIN
        DECLARE _json           JSON;
        DECLARE _nombre         VARCHAR(25);
        DECLARE _impuesto_iva   TINYINT;
        DECLARE _descripcion    TEXT;
        DECLARE _ieps           DECIMAL(10,8);
        DECLARE _isr            DECIMAL(10,8);

        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SELECT '¡Error!' as 'Resultado';
            ROLLBACK;
        END;

        SET _json           = JSON_EXTRACT(_jsonA, '$[0]');
        SET _nombre         = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.nombre'       ));
        SET _impuesto_iva   = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.impuestoIVA'  ));
        SET _descripcion    = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.descripcion'  ));
        SET _ieps           = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.ieps'         ));
        SET _isr            = JSON_UNQUOTE(JSON_EXTRACT(_json, '$.isr'          ));

        START TRANSACTION ;
            INSERT INTO categoria VALUES (0, _nombre, _descripcion, _impuesto_iva, _ieps, _isr);
            SELECT idCategoria FROM categoria WHERE nombre = _nombre;
        COMMIT ;


    END //

DELIMITER ;
