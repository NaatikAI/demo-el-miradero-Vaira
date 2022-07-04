-- ================================ --
-- | Created on Wed April 05 2022 | --
-- |   Copyright (c) 2022 VAIRA   | --
-- |     All Rights Reserved.     | --
-- ================================ --
-- | Código encargado de generar  | --
-- | tablas de la base de datos   | --
-- ================================ --

CREATE SCHEMA IF NOT EXISTS naatika1_db_vaira;
USE naatika1_db_vaira;

DROP TABLE IF EXISTS info_venta;
DROP TABLE IF EXISTS datos_factura;
DROP TABLE IF EXISTS regimen_fiscal;
DROP TABLE IF EXISTS venta;
DROP TABLE IF EXISTS tipo_pago;
DROP TABLE IF EXISTS existencia;
DROP TABLE IF EXISTS log_producto;
DROP TABLE IF EXISTS impuesto_extra;
DROP TABLE IF EXISTS producto;
DROP TABLE IF EXISTS proveedor;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS punto_venta;
DROP TABLE IF EXISTS egresos;
DROP TABLE IF EXISTS sucursal;
DROP TABLE IF EXISTS log_usuario;
DROP TABLE IF EXISTS usuario;
DROP TABLE IF EXISTS tipo;
DROP TABLE IF EXISTS permisos;
DROP TABLE IF EXISTS region_iva;
DROP TABLE IF EXISTS ciudad;
DROP TABLE IF EXISTS pais;
DROP TABLE IF EXISTS sucursal_usuario;
DROP TABLE IF EXISTS motivo_egreso;

CREATE TABLE IF NOT EXISTS permisos(
    idPermisos  INT         NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    crear       TINYINT     NOT NULL,
    modificar   TINYINT     NOT NULL,
    eliminar    TINYINT     NOT NULL,
    leer        TINYINT     NOT NULL
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS tipo(
    idTipo      INT         NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkPermisos  INT         NOT NULL,
    tipo        VARCHAR(20) NOT NULL,

    FOREIGN KEY (fkPermisos) REFERENCES permisos(idPermisos)   ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS usuario(
    idUsuario   INT          NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkTipo      INT          NOT NULL,
    usuario     VARCHAR(50)  NOT NULL,
    password    VARCHAR(128) NOT NULL,
    nombre      VARCHAR(50)  NOT NULL,
    apellidoP   VARCHAR(50)  NOT NULL,
    apellidoM   VARCHAR(50)  NOT NULL,
    correo      VARCHAR(50)  NOT NULL,
    telefono    VARCHAR(10)  NOT NULL,
    activo      TINYINT      NOT NULL,

    FOREIGN KEY (fkTipo) REFERENCES tipo(idTipo)   ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;


CREATE TABLE IF NOT EXISTS log_usuario(
    idLog       INT         NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkUsuario   INT         NOT NULL,
    crear       DATE,
    modificar   DATE,
    desactivar  DATE,

    FOREIGN KEY (fkUsuario) REFERENCES usuario(idUsuario)   ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS pais(
    idPais      INT          NOT NULL       PRIMARY KEY     AUTO_INCREMENT,
    nombre      VARCHAR(50)  NOT NULL
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS ciudad(
    idCiudad    INT          NOT NULL       PRIMARY KEY     AUTO_INCREMENT,
    fkPais      INT          NOT NULL,
    nombre      VARCHAR(50)  NOT NULL,

    FOREIGN KEY (fkPais) REFERENCES pais(idPais)   ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS region_iva(
    idRegion    INT          NOT NULL       PRIMARY KEY     AUTO_INCREMENT,
    fkCiudad    INT          NOT NULL,
    iva         DECIMAL(5,3)  NOT NULL,

    FOREIGN KEY (fkCiudad) REFERENCES ciudad(idCiudad)   ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS sucursal(
    idSucursal  INT          NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkRegion    INT          NOT NULL,
    fkAdmin     INT,
    nombre      VARCHAR(100) NOT NULL,
    calle       VARCHAR(100) NOT NULL,
    colonia     VARCHAR(100) NOT NULL,
    CP          VARCHAR(10)  NOT NULL,
    telefono    VARCHAR(15)  NOT NULL,

    FOREIGN KEY (fkRegion) REFERENCES region_iva(idRegion)   ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkAdmin)  REFERENCES usuario(idUsuario)     ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS punto_venta(
    idPunto   INT          NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkSucursal  INT          NOT NULL,
    fkUsuario   INT,
    nombre      VARCHAR(50)  NOT NULL,

    FOREIGN KEY (fkUsuario)  REFERENCES usuario(idUsuario)     ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkSucursal) REFERENCES sucursal(idSucursal)   ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS categoria(
    idCategoria INT          NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    nombre      VARCHAR(25)  NOT NULL,
    descripcion TEXT,
    impuestoIVA TINYINT,
    ieps        DECIMAL(10,8) NOT NULL,
    isr         DECIMAL(10,8) NOT NULL
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS proveedor(
    idProveedor  INT          NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    nombre       VARCHAR(50)  NOT NULL,
    telefono     VARCHAR(50)  NOT NULL,
    correo       VARCHAR(50)  NOT NULL,
    activo       TINYINT      NOT NULL
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS producto (
    idProducto      INT              NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkCategoria     INT              NOT NULL,
    fkProveedor     INT              NOT NULL,
    nombre          VARCHAR(50)      NOT NULL,
    costo           DECIMAL (10,2)   NOT NULL,
    precio          DECIMAL (10,2)   NOT NULL,
    sku             VARCHAR(20),
    imagen          VARCHAR(50),
    activo          TINYINT,
    servicio        TINYINT,

    FOREIGN KEY (fkCategoria) REFERENCES categoria(idCategoria)   ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkProveedor) REFERENCES proveedor(idProveedor)   ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS log_producto(
    idLog       INT         NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkProducto  INT         NOT NULL,
    crear       DATE,
    modificar   DATE,
    desactivar  DATE,

    FOREIGN KEY (fkProducto) REFERENCES producto(idProducto)   ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS existencia(
    fkProducto      INT         NOT NULL,
    fkSucursal      INT         NOT NULL,
    cantidad        INT         NOT NULL,

    FOREIGN KEY (fkProducto) REFERENCES producto(idProducto)   ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkSucursal) REFERENCES sucursal(idSucursal)   ON UPDATE CASCADE ON DELETE RESTRICT,
    PRIMARY KEY (fkProducto, fkSucursal)
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS impuesto_extra(
    idImpuesto      INT         NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkProducto      INT,
    nombre          VARCHAR(50),
    descripcion     TEXT,
    impuesto        DOUBLE,
    FOREIGN KEY (fkProducto) REFERENCES producto(idProducto)  ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS tipo_pago(
    idTipo      INT         NOT NULL    PRIMARY KEY     AUTO_INCREMENT,
    nombre      VARCHAR(20) NOT NULL

)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS venta (
    idVenta     INT         NOT NULL        PRIMARY KEY        AUTO_INCREMENT,
    fkUsuario   INT,
    fkTipoPago  INT,
    fkSucursal  INT,
    total       DECIMAL(12, 2),
    fecha       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (fkUsuario)  REFERENCES usuario(idUsuario) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkSucursal)  REFERENCES sucursal(idSucursal) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkTipoPago)  REFERENCES tipo_pago(idTipo) ON UPDATE CASCADE ON DELETE RESTRICT

)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS regimen_fiscal (
    idRegimen   INT         NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    nombre      VARCHAR(100) NOT NULL
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS datos_factura(
    idDatos       INT         NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkVenta       INT         NOT NULL,
    fkRegimen     INT         NOT NULL,
    rfc           VARCHAR(13) NOT NULL,
    cp_persona    VARCHAR(10) NOT NULL,
    nombre        VARCHAR(50) NOT NULL,
    apellidoP     VARCHAR(50) NOT NULL,
    apellidoM     VARCHAR(50) NOT NULL,
    correo        VARCHAR(50) NOT NULL,

    FOREIGN KEY (fkVenta)   REFERENCES venta(idVenta)            ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkRegimen) REFERENCES regimen_fiscal(idRegimen) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS info_venta(
    idInfo          INT          NOT NULL        PRIMARY KEY     AUTO_INCREMENT,
    fkProducto      INT          NOT NULL,
    fkVenta         INT          NOT NULL,
    cantidad        INT          NOT NULL,
    iva             DECIMAL(5,2) NOT NULL,
    ieps            DECIMAL(5,2) NOT NULL,
    isr             DECIMAL(5,2) NOT NULL,
    subtotal        DECIMAL(5,2) NOT NULL,

    FOREIGN KEY (fkProducto) REFERENCES producto(idProducto)   ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkVenta)    REFERENCES venta(idVenta)         ON UPDATE CASCADE ON DELETE RESTRICT

)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS motivo_egreso(
    idMotivo        INT             NOT NULL        PRIMARY KEY         AUTO_INCREMENT,
    nombre          VARCHAR(50)     NOT NULL
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS egresos(
    idEgreso        INT             NOT NULL        PRIMARY KEY         AUTO_INCREMENT,
    fkMotivo        INT             NOT NULL,
    fkUsuario       INT             NOT NULL,
    fkSucursal      INT             NOT NULL,
    total           DECIMAL(5,2)    NOT NULL,
    fecha           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (fkMotivo) REFERENCES motivo_egreso(idMotivo) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkUsuario) REFERENCES usuario(idUsuario) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkSucursal) REFERENCES sucursal(idSucursal) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS sucursal_usuario(
    fkUsuario   INT     NOT NULL ,
    fkSucursal  INT     NOT NULL ,

    FOREIGN KEY (fkSucursal) REFERENCES sucursal(idSucursal) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (fkUsuario) REFERENCES usuario(idUsuario) ON UPDATE CASCADE ON DELETE RESTRICT,
    PRIMARY KEY (fkUsuario, fkSucursal)
);