-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-03-2025 a las 21:24:18
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cajas_chicas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos_permisos`
--

CREATE TABLE `accesos_permisos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `permiso` varchar(50) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `accesos_permisos`
--

INSERT INTO `accesos_permisos` (`id`, `id_usuario`, `id_modulo`, `permiso`, `estado`, `created_at`) VALUES
(1, 1, 1, 'create_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(2, 1, 2, 'create_detalles', 'ACTIVO', '2025-03-21 21:14:15'),
(3, 1, 3, 'manage_cajachica', 'ACTIVO', '2025-03-21 21:14:15'),
(4, 1, 4, 'manage_facturas', 'ACTIVO', '2025-03-21 21:14:15'),
(5, 1, 5, 'autorizar_facturas', 'ACTIVO', '2025-03-21 21:14:15'),
(6, 1, 6, 'manage_impuestos', 'ACTIVO', '2025-03-21 21:14:15'),
(7, 1, 7, 'manage_cuentas_contables', 'ACTIVO', '2025-03-21 21:14:15'),
(8, 1, 8, 'manage_tipos_gastos', 'ACTIVO', '2025-03-21 21:14:15'),
(9, 1, 9, 'manage_centros_costos', 'ACTIVO', '2025-03-21 21:14:15'),
(10, 1, 10, 'manage_roles', 'ACTIVO', '2025-03-21 21:14:15'),
(11, 1, 11, 'manage_usuarios', 'ACTIVO', '2025-03-21 21:14:15'),
(12, 1, 12, 'autorizar_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(13, 1, 13, 'revisar_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(14, 1, 14, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(15, 1, 15, 'manage_reportes', 'ACTIVO', '2025-03-21 21:14:15'),
(16, 1, 16, 'manage_auditoria', 'ACTIVO', '2025-03-21 21:14:15'),
(17, 1, 17, 'manage_accesos', 'ACTIVO', '2025-03-21 21:14:15'),
(18, 1, 18, 'revisar_facturas', 'ACTIVO', '2025-03-21 21:14:15'),
(19, 8, 1, 'create_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(20, 8, 2, 'create_detalles', 'ACTIVO', '2025-03-21 21:14:15'),
(21, 8, 3, 'manage_cajachica', 'ACTIVO', '2025-03-21 21:14:15'),
(22, 8, 4, 'manage_facturas', 'ACTIVO', '2025-03-21 21:14:15'),
(23, 8, 5, 'autorizar_facturas', 'ACTIVO', '2025-03-21 21:14:15'),
(24, 8, 6, 'manage_impuestos', 'ACTIVO', '2025-03-21 21:14:15'),
(25, 8, 7, 'manage_cuentas_contables', 'ACTIVO', '2025-03-21 21:14:15'),
(26, 8, 8, 'manage_tipos_gastos', 'ACTIVO', '2025-03-21 21:14:15'),
(27, 8, 9, 'manage_centros_costos', 'ACTIVO', '2025-03-21 21:14:15'),
(28, 8, 10, 'manage_roles', 'ACTIVO', '2025-03-21 21:14:15'),
(29, 8, 11, 'manage_usuarios', 'ACTIVO', '2025-03-21 21:14:15'),
(30, 8, 12, 'autorizar_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(31, 8, 13, 'revisar_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(32, 8, 14, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(33, 8, 15, 'manage_reportes', 'ACTIVO', '2025-03-21 21:14:15'),
(34, 8, 16, 'manage_auditoria', 'ACTIVO', '2025-03-21 21:14:15'),
(35, 8, 17, 'manage_accesos', 'ACTIVO', '2025-03-21 21:14:15'),
(36, 8, 18, 'revisar_facturas', 'ACTIVO', '2025-03-21 21:14:15'),
(37, 2, 1, 'create_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(38, 2, 2, 'create_detalles', 'ACTIVO', '2025-03-21 21:14:15'),
(39, 2, 3, 'manage_cajachica', 'ACTIVO', '2025-03-21 21:14:15'),
(40, 10, 1, 'create_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(41, 10, 2, 'create_detalles', 'ACTIVO', '2025-03-21 21:14:15'),
(42, 10, 3, 'manage_cajachica', 'ACTIVO', '2025-03-21 21:14:15'),
(43, 3, 12, 'autorizar_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(44, 3, 13, 'revisar_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(45, 3, 14, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-03-21 21:14:15'),
(46, 3, 5, 'autorizar_facturas', 'ACTIVO', '2025-03-21 21:14:15'),
(47, 3, 18, 'revisar_facturas', 'ACTIVO', '2025-03-21 21:14:15'),
(48, 4, 6, 'manage_impuestos', 'INACTIVO', '2025-03-21 21:14:15'),
(49, 4, 7, 'manage_cuentas_contables', 'ACTIVO', '2025-03-21 21:14:15'),
(50, 4, 8, 'manage_tipos_gastos', 'INACTIVO', '2025-03-21 21:14:15'),
(51, 4, 15, 'manage_reportes', 'ACTIVO', '2025-03-21 21:14:15'),
(52, 4, 16, 'manage_auditoria', 'ACTIVO', '2025-03-21 21:14:15'),
(53, 9, 6, 'manage_impuestos', 'INACTIVO', '2025-03-21 21:14:15'),
(54, 9, 7, 'manage_cuentas_contables', 'ACTIVO', '2025-03-21 21:14:15'),
(55, 9, 8, 'manage_tipos_gastos', 'INACTIVO', '2025-03-21 21:14:15'),
(56, 9, 15, 'manage_reportes', 'ACTIVO', '2025-03-21 21:14:15'),
(57, 9, 16, 'manage_auditoria', 'ACTIVO', '2025-03-21 21:14:15'),
(58, 4, 3, 'manage_cajachica', 'INACTIVO', '2025-03-23 18:17:02'),
(59, 4, 13, 'revisar_liquidaciones', 'ACTIVO', '2025-03-23 18:17:02'),
(60, 4, 14, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-03-23 18:17:02'),
(61, 4, 4, 'manage_facturas', 'ACTIVO', '2025-03-23 18:17:02'),
(62, 4, 9, 'manage_centros_costos', 'ACTIVO', '2025-03-23 18:17:02'),
(63, 4, 18, 'revisar_facturas', 'ACTIVO', '2025-03-23 18:17:02'),
(64, 4, 17, 'manage_accesos', 'INACTIVO', '2025-03-23 20:01:35'),
(65, 9, 1, 'create_liquidaciones', 'INACTIVO', '2025-03-23 20:00:27'),
(66, 9, 13, 'revisar_liquidaciones', 'ACTIVO', '2025-03-23 20:00:27'),
(67, 9, 14, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-03-23 20:00:27'),
(68, 9, 4, 'manage_facturas', 'ACTIVO', '2025-03-23 20:00:27'),
(69, 9, 9, 'manage_centros_costos', 'ACTIVO', '2025-03-23 20:00:27'),
(70, 9, 18, 'revisar_facturas', 'ACTIVO', '2025-03-23 20:00:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `id_liquidacion` int(11) DEFAULT NULL,
  `id_detalle_liquidacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` enum('APROBADO','RECHAZADO','EXPORTADO_SAP') NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_accion` varchar(50) NOT NULL,
  `usuario_nombre` varchar(100) NOT NULL,
  `detalles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `id_liquidacion`, `id_detalle_liquidacion`, `id_usuario`, `accion`, `comentario`, `fecha`, `tipo_accion`, `usuario_nombre`, `detalles`) VALUES
(1, 1, NULL, 1, 'APROBADO', 'Liquidación creada', '2025-03-21 20:07:21', 'CREADO', 'Administrador', 'Liquidación creada por admin'),
(2, 2, NULL, 2, 'APROBADO', NULL, '2025-03-21 20:15:47', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(3, 1, 2, 2, 'APROBADO', NULL, '2025-03-21 20:19:50', 'CREADO', 'Encargado 1', 'Detalle de liquidación creado por encargado'),
(4, NULL, 2, 1, 'APROBADO', NULL, '2025-03-21 20:21:01', 'CREAR_FACTURA', 'Administrador', '{\"cuenta_id\":1,\"base_id\":1,\"numero_factura\":\"FACT-003\",\"fecha\":\"2025-03-20\",\"proveedor\":\"Proveedor AAA\",\"monto\":1000,\"estado\":\"PENDIENTE\"}'),
(5, NULL, 1, 3, 'APROBADO', NULL, '2025-03-21 20:21:42', 'AUTORIZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"APROBADO\",\"comentario\":\"fue aprobada\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(6, NULL, 2, 3, 'APROBADO', NULL, '2025-03-21 20:21:59', 'RECHAZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"RECHAZADO\",\"comentario\":\"fue rechazada\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"RECHAZADO\"}'),
(7, 2, NULL, 2, 'APROBADO', NULL, '2025-03-21 21:29:29', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por usuario'),
(8, 1, 2, 2, 'APROBADO', NULL, '2025-03-21 21:31:45', 'ACTUALIZADO', 'Encargado 1', 'Detalle de liquidación actualizado'),
(9, 1, NULL, 2, 'APROBADO', NULL, '2025-03-21 21:31:45', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(10, NULL, 1, 3, 'APROBADO', NULL, '2025-03-21 21:54:37', 'PAGAR_FACTURA', 'Supervisor 1', '{\"accion\":\"PAGADA\",\"comentario\":\"\",\"estado_anterior\":\"APROBADO\",\"estado_nuevo\":\"PAGADA\"}'),
(11, 2, NULL, 2, 'APROBADO', NULL, '2025-03-23 18:16:16', 'ACTUALIZADO', 'Encargado 1', 'Liquidación actualizada por usuario'),
(12, 1, 2, 2, 'APROBADO', NULL, '2025-03-23 18:22:15', 'ACTUALIZADO', 'Encargado 1', 'Detalle de liquidación actualizado'),
(13, 1, NULL, 2, 'APROBADO', NULL, '2025-03-23 18:22:15', 'PENDIENTE', 'Encargado 1', 'Liquidación restaurada a PENDIENTE tras corrección de detalles'),
(14, NULL, NULL, 1, 'APROBADO', NULL, '2025-03-23 18:28:52', 'CREAR_USUARIO', 'Administrador', 'Usuario creado: usuario@gmail.com'),
(15, 2, NULL, 3, 'APROBADO', NULL, '2025-03-23 18:30:53', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'Autorizado por supervisor'),
(16, NULL, 3, 2, 'APROBADO', NULL, '2025-03-23 20:03:35', 'CREAR_FACTURA', 'Encargado 1', '{\"cuenta_id\":1,\"base_id\":1,\"numero_factura\":\"FACT-002\",\"fecha\":\"2025-03-23\",\"proveedor\":\"Pepe\",\"monto\":1000,\"estado\":\"PENDIENTE\"}'),
(17, NULL, 3, 3, 'APROBADO', NULL, '2025-03-23 20:04:31', 'AUTORIZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"APROBADO\",\"comentario\":\"Fue aprobada satisfactoriamente\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(18, NULL, 3, 4, 'APROBADO', NULL, '2025-03-23 20:05:39', 'PAGAR_FACTURA', 'Contador 1', '{\"accion\":\"PAGADA\",\"comentario\":\"fue pagada y aprobada en contabilidad\",\"estado_anterior\":\"APROBADO\",\"estado_nuevo\":\"PAGADA\"}'),
(19, 3, NULL, 2, 'APROBADO', NULL, '2025-03-23 20:08:25', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(20, 3, 3, 2, 'APROBADO', NULL, '2025-03-23 20:09:38', 'CREADO', 'Encargado 1', 'Detalle de liquidación creado por encargado'),
(21, 3, NULL, 3, 'APROBADO', NULL, '2025-03-23 20:10:39', 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', 'Supervisor 1', 'Autorizado por supervisor '),
(22, NULL, 4, 1, 'APROBADO', NULL, '2025-03-23 20:12:17', 'CREAR_FACTURA', 'Administrador', '{\"cuenta_id\":1,\"base_id\":2,\"numero_factura\":\"FACT-004\",\"fecha\":\"2025-03-23\",\"proveedor\":\"Proveedor AA\",\"monto\":1000,\"estado\":\"PENDIENTE\"}'),
(23, NULL, 4, 3, 'APROBADO', NULL, '2025-03-23 20:13:05', 'AUTORIZAR_FACTURA', 'Supervisor 1', '{\"accion\":\"APROBADO\",\"comentario\":\"Esta aprobada\",\"estado_anterior\":\"PENDIENTE\",\"estado_nuevo\":\"APROBADO\"}'),
(24, 1, NULL, 4, 'APROBADO', NULL, '2025-03-23 20:16:38', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'autorizado '),
(25, 3, 3, 4, 'APROBADO', NULL, '2025-03-23 20:17:10', 'RECHAZADO_POR_CONTABILIDAD', 'Contador 1', 'no esta completo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bases`
--

CREATE TABLE `bases` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `data_base` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `bases`
--

INSERT INTO `bases` (`id`, `nombre`, `data_base`) VALUES
(1, 'AGROCENTRO GUATEMALA', NULL),
(2, 'AGROCENTRO HONDURAS', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_chicas`
--

CREATE TABLE `cajas_chicas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_actual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_asignado` decimal(10,2) NOT NULL,
  `monto_disponible` decimal(10,2) NOT NULL,
  `id_usuario_encargado` int(11) NOT NULL,
  `id_supervisor` int(11) NOT NULL,
  `id_centro_costo` int(11) DEFAULT NULL,
  `estado` enum('ACTIVA','INACTIVA') DEFAULT 'ACTIVA',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `cajas_chicas`
--

INSERT INTO `cajas_chicas` (`id`, `nombre`, `monto_inicial`, `monto_actual`, `monto_asignado`, `monto_disponible`, `id_usuario_encargado`, `id_supervisor`, `id_centro_costo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Caja Chica 1', 5000.00, 5000.00, 5000.00, 5000.00, 2, 3, 1, 'ACTIVA', '2025-03-21 20:05:25', '2025-03-21 20:05:25'),
(2, 'Caja chica 2', 0.00, 0.00, 6000.00, 6000.00, 2, 3, 1, 'ACTIVA', '2025-03-21 21:30:21', '2025-03-21 21:30:59'),
(3, 'Caja Chica 3', 0.00, 0.00, 5000.00, 5000.00, 2, 3, 1, 'ACTIVA', '2025-03-23 20:07:30', '2025-03-23 20:07:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centros_costos`
--

CREATE TABLE `centros_costos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `centros_costos`
--

INSERT INTO `centros_costos` (`id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Centro de Costos 1', 'Centro de costos para pruebas', 'ACTIVO', '2025-03-21 18:56:42', '2025-03-21 18:56:42'),
(2, 'Centro de Costos 2', 'Costo de viajes ', 'ACTIVO', '2025-03-23 18:24:56', '2025-03-23 18:24:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_contables`
--

CREATE TABLE `cuentas_contables` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(10) DEFAULT '5',
  `base_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `cuentas_contables`
--

INSERT INTO `cuentas_contables` (`id`, `codigo`, `nombre`, `estado`, `creado_en`, `tipo`, `base_id`) VALUES
(1, '1001', 'Cuenta de Activos', 'ACTIVO', '2025-03-21 20:05:53', '5', 1),
(2, '2001', 'Cuenta de Pasivos', 'ACTIVO', '2025-03-21 20:05:53', '5', 2),
(3, '100', 'Angel', 'ACTIVO', '2025-03-21 21:44:22', '1', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_liquidaciones`
--

CREATE TABLE `detalle_liquidaciones` (
  `id` int(11) NOT NULL,
  `id_liquidacion` int(11) NOT NULL,
  `no_factura` varchar(50) NOT NULL,
  `regimen` varchar(50) DEFAULT NULL,
  `c_costo` varchar(50) DEFAULT NULL,
  `nit_proveedor` varchar(20) DEFAULT NULL,
  `nombre_proveedor` varchar(100) DEFAULT NULL,
  `fecha` date NOT NULL,
  `bien_servicio` varchar(255) DEFAULT NULL,
  `t_gasto` varchar(50) DEFAULT NULL,
  `codigo_ccta` varchar(50) DEFAULT NULL,
  `descripcion_factura` text DEFAULT NULL,
  `p_unitario` decimal(10,2) DEFAULT NULL,
  `iva` decimal(10,2) DEFAULT NULL,
  `total_factura` decimal(10,2) NOT NULL,
  `idp` decimal(10,2) DEFAULT NULL,
  `inguat` decimal(10,2) DEFAULT NULL,
  `porcentajeiva` decimal(5,2) DEFAULT NULL,
  `porcentajeidp` decimal(5,2) DEFAULT NULL,
  `tipo_combustible` varchar(50) DEFAULT NULL,
  `estado` enum('PENDIENTE','EN_REVISIÓN','AUTORIZADO_POR_ADMIN','RECHAZADO_POR_ADMIN','AUTORIZADO_POR_CONTABILIDAD','RECHAZADO_POR_CONTABILIDAD','AUTORIZADO_POR_SUPERVISOR','RECHAZADO_POR_SUPERVISOR','DESCARTADO') NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rutas_archivos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `detalle_liquidaciones`
--

INSERT INTO `detalle_liquidaciones` (`id`, `id_liquidacion`, `no_factura`, `regimen`, `c_costo`, `nit_proveedor`, `nombre_proveedor`, `fecha`, `bien_servicio`, `t_gasto`, `codigo_ccta`, `descripcion_factura`, `p_unitario`, `iva`, `total_factura`, `idp`, `inguat`, `porcentajeiva`, `porcentajeidp`, `tipo_combustible`, `estado`, `created_at`, `updated_at`, `rutas_archivos`) VALUES
(1, 1, 'FACT-001', NULL, NULL, '123456-7', 'Proveedor Prueba', '2025-03-21', 'Materiales de Oficina', 'OPERATIVO', NULL, 'Compra de materiales', 800.00, 96.00, 896.00, NULL, NULL, 12.00, NULL, NULL, 'PENDIENTE', '2025-03-21 20:06:59', '2025-03-21 20:06:59', '[]'),
(2, 1, 'Fact-002', NULL, NULL, NULL, 'Proveedor Prueba 2', '2025-03-20', 'servicio', 'Gasto Operativo', NULL, NULL, 100.00, NULL, 100.00, NULL, NULL, NULL, NULL, NULL, 'PENDIENTE', '2025-03-21 20:19:50', '2025-03-23 18:22:15', '[\"uploads\\/67ddc9e5f41d6_Captura.PNG\",\"uploads\\/67dddac1d92b3_adaptar.JPG\",\"uploads\\/67dddac1d98af_Captura.PNG\"]'),
(3, 3, 'FACT-004', NULL, NULL, NULL, 'Miguel perez', '2025-03-23', 'servicio', 'Gasto Operativo', NULL, NULL, 1000.00, NULL, 1000.00, NULL, NULL, NULL, NULL, NULL, 'RECHAZADO_POR_CONTABILIDAD', '2025-03-23 20:09:38', '2025-03-23 20:17:10', '[\"uploads\\/67e06a8250300_Captura.PNG\"]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `numero_factura` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `proveedor` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `base_id` int(11) NOT NULL,
  `cuenta_id` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `numero_factura`, `fecha`, `proveedor`, `monto`, `base_id`, `cuenta_id`, `estado`, `created_at`) VALUES
(1, 'FACT-001', '2025-03-21', 'Proveedor Prueba', 1000.00, 1, 1, 'PAGADA', '2025-03-21 20:07:11'),
(2, 'FACT-003', '2025-03-20', 'Proveedor AAA', 1000.00, 1, 1, 'RECHAZADO', '2025-03-21 20:21:01'),
(3, 'FACT-002', '2025-03-23', 'Pepe', 1000.00, 1, 1, 'PAGADA', '2025-03-23 20:03:35'),
(4, 'FACT-004', '2025-03-23', 'Proveedor AA', 1000.00, 2, 1, 'APROBADO', '2025-03-23 20:12:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos`
--

CREATE TABLE `impuestos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidaciones`
--

CREATE TABLE `liquidaciones` (
  `id` int(11) NOT NULL,
  `id_caja_chica` int(11) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT 0.00,
  `estado` enum('PENDIENTE','AUTORIZADO_POR_ADMIN','RECHAZADO_POR_ADMIN','AUTORIZADO_POR_CONTABILIDAD','RECHAZADO_POR_CONTABILIDAD','AUTORIZADO_POR_SUPERVISOR','RECHAZADO_POR_SUPERVISOR','AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR','RECHAZADO_POR_SUPERVISOR_AUTORIZADOR','PENDIENTE_CORRECCIÓN','DESCARTADO') NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exportado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `liquidaciones`
--

INSERT INTO `liquidaciones` (`id`, `id_caja_chica`, `fecha_creacion`, `fecha_inicio`, `fecha_fin`, `monto_total`, `estado`, `created_at`, `updated_at`, `exportado`) VALUES
(1, 1, '2025-03-21', '2025-03-01', '2025-03-21', 1000.00, 'AUTORIZADO_POR_CONTABILIDAD', '2025-03-21 20:06:47', '2025-03-23 20:16:38', 0),
(2, 2, '2025-03-21', '2025-03-07', '2025-03-14', 1500.00, 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', '2025-03-21 20:15:47', '2025-03-23 18:30:53', 0),
(3, 3, '2025-03-23', '2025-03-08', '2025-03-23', 1000.00, 'AUTORIZADO_POR_SUPERVISOR_AUTORIZADOR', '2025-03-23 20:08:25', '2025-03-23 20:10:39', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `permiso_predeterminado` varchar(50) DEFAULT NULL,
  `ruta` varchar(255) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `nombre`, `permiso_predeterminado`, `ruta`, `estado`, `created_at`) VALUES
(1, 'Gestión de Liquidaciones', 'create_liquidaciones', 'index.php?controller=liquidacion&action=list', 'ACTIVO', '2025-03-21 20:06:05'),
(2, 'Gestión de Liquidaciones Detalles', 'create_detalles', 'index.php?controller=detalleliquidacion&action=list', 'ACTIVO', '2025-03-21 20:06:05'),
(3, 'Gestión de Cajas Chicas', 'manage_cajachica', '', 'ACTIVO', '2025-03-21 20:06:05'),
(4, 'Gestión de Facturas', 'manage_facturas', 'index.php?controller=factura&action=list', 'ACTIVO', '2025-03-21 20:06:05'),
(5, 'Autorizar Facturas', 'autorizar_facturas', 'index.php?controller=factura&action=list&mode=autorizar', 'ACTIVO', '2025-03-21 20:06:05'),
(6, 'Gestión de Impuestos', 'manage_impuestos', 'index.php?controller=impuesto&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(7, 'Gestión de Cuentas Contables', 'manage_cuentas_contables', 'index.php?controller=cuentacontable&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(8, 'Gestión de Tipos de Gastos', 'manage_tipos_gastos', 'index.php?controller=tipogasto&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(9, 'Gestión de Centros de Costos', 'manage_centros_costos', 'index.php?controller=centrocosto&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(10, 'Gestión de Roles', 'manage_roles', 'index.php?controller=rol&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(11, 'Gestión de Usuarios', 'manage_usuarios', 'index.php?controller=usuario&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(12, 'Autorizar Liquidaciones', 'autorizar_liquidaciones', 'index.php?controller=liquidacion&action=list&mode=autorizar', 'ACTIVO', '2025-03-21 20:47:24'),
(13, 'Revisar Liquidaciones', 'revisar_liquidaciones', 'index.php?controller=liquidacion&action=list&mode=revisar', 'ACTIVO', '2025-03-21 20:47:24'),
(14, 'Revisar Detalles de Liquidaciones', 'revisar_detalles_liquidaciones', 'index.php?controller=detalleliquidacion&action=revisar', 'ACTIVO', '2025-03-21 20:47:24'),
(15, 'Generar Reportes', 'manage_reportes', 'index.php?controller=reportes&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(16, 'Consultar Auditoría', 'manage_auditoria', 'index.php?controller=auditoria&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(17, 'Administración de Accesos', 'manage_accesos', 'index.php?controller=acceso&action=list', 'ACTIVO', '2025-03-21 20:47:24'),
(18, 'Revisar Facturas', 'revisar_facturas', 'index.php?controller=factura&action=list&mode=revisar', 'ACTIVO', '2025-03-21 20:47:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `descripcion`, `creado_en`) VALUES
(1, 'create_liquidaciones', 'Permite crear liquidaciones', '2025-03-21 20:06:19'),
(2, 'create_detalles', 'Permite crear detalles de liquidaciones', '2025-03-21 20:06:19'),
(3, 'manage_cajachica', 'Permite gestionar cajas chicas', '2025-03-21 20:06:19'),
(4, 'manage_facturas', 'Permite gestionar facturas', '2025-03-21 20:06:19'),
(5, 'autorizar_facturas', 'Permite autorizar facturas', '2025-03-21 20:06:19'),
(6, 'manage_impuestos', 'Permite gestionar impuestos', '2025-03-21 20:46:41'),
(7, 'manage_cuentas_contables', 'Permite gestionar cuentas contables', '2025-03-21 20:46:41'),
(8, 'manage_tipos_gastos', 'Permite gestionar tipos de gastos', '2025-03-21 20:46:41'),
(9, 'manage_centros_costos', 'Permite gestionar centros de costos', '2025-03-21 20:46:41'),
(10, 'manage_roles', 'Permite gestionar roles', '2025-03-21 20:46:41'),
(11, 'manage_usuarios', 'Permite gestionar usuarios', '2025-03-21 20:46:41'),
(12, 'autorizar_liquidaciones', 'Permite autorizar liquidaciones', '2025-03-21 20:46:41'),
(13, 'revisar_liquidaciones', 'Permite revisar liquidaciones', '2025-03-21 20:46:41'),
(14, 'revisar_detalles_liquidaciones', 'Permite revisar detalles de liquidaciones', '2025-03-21 20:46:41'),
(15, 'manage_reportes', 'Permite generar reportes', '2025-03-21 20:46:41'),
(16, 'manage_auditoria', 'Permite consultar auditoría', '2025-03-21 20:46:41'),
(17, 'manage_accesos', 'Permite administrar accesos', '2025-03-21 20:46:41'),
(18, 'revisar_facturas', 'Permite revisar facturas', '2025-03-21 20:46:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `estado`, `creado_en`) VALUES
(1, 'ADMIN', 'Administrador del sistema', 'ACTIVO', '2025-03-04 14:22:33'),
(2, 'ENCARGADO_CAJA_CHICA', 'Encargado de caja chica', 'ACTIVO', '2025-03-04 14:22:33'),
(3, 'SUPERVISOR_AUTORIZADOR', 'Supervisor autorizador', 'ACTIVO', '2025-03-04 14:22:33'),
(4, 'CONTABILIDAD', 'contabilidad', 'ACTIVO', '2025-03-04 14:22:33'),
(8, 'Angel De Leon', 'Admin', 'ACTIVO', '2025-03-11 14:22:36'),
(9, 'ROL_TEST', 'sera admin', 'ACTIVO', '2025-03-23 18:27:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_gastos`
--

CREATE TABLE `tipos_gastos` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `tipos_gastos`
--

INSERT INTO `tipos_gastos` (`id`, `name`, `description`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Gasto Operativo', 'gasolina', 'ACTIVO', '2025-03-21 20:18:54', '2025-03-23 18:21:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `id_rol`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@example.com', '$2y$10$O6SOYvh8GIgVYhFB4kP.QOz6WDK1rbh/c49Btx9ttlydoVCT8e7FO', 1, '2025-02-28 21:39:35', '2025-03-04 16:39:07'),
(2, 'Encargado 1', 'encargado1@example.com', '$2y$10$arxyBaOnstSEoqZilvck7eIVHpRq8bBNvx58d5jqomPl.sKozZ4SK', 2, '2025-02-28 21:39:35', '2025-03-04 14:37:53'),
(3, 'Supervisor 1', 'supervisor1@example.com', '$2y$10$arxyBaOnstSEoqZilvck7eIVHpRq8bBNvx58d5jqomPl.sKozZ4SK', 3, '2025-02-28 21:39:35', '2025-03-04 14:37:53'),
(4, 'Contador 1', 'contador1@example.com', '$2y$10$arxyBaOnstSEoqZilvck7eIVHpRq8bBNvx58d5jqomPl.sKozZ4SK', 4, '2025-02-28 21:39:35', '2025-03-10 18:41:53'),
(8, 'Angel De León', 'angel.deleon@agrocentro.com', '$2y$10$eH8mm/PCb5aKUgcrONlcKuYHl6xCkcDCuWRzvJGjAvQ6aU59PZvmS', 1, '2025-03-11 14:20:20', '2025-03-21 15:10:12'),
(9, 'Pepe', 'pepe@gmail.com', '$2y$10$RzA5C/TVfnbz1A/MQuRkkOR0inYLpPY4NbndCNxtsLaObaP5PSCba', 4, '2025-03-18 16:56:34', '2025-03-18 16:56:34'),
(10, 'Omar ', 'omar@gmail.com', '$2y$10$RRI9rARJHg2bKODIK0WOMOYKqzxdSpngia8Ny7lphCPLWk1G8dC/e', 2, '2025-03-20 20:45:37', '2025-03-21 15:07:39'),
(11, 'Usruario de pureba', 'usuario@gmail.com', '$2y$10$vJ8dxND0P020hJ.GsAVNX.k0e71Tghxh5VyqY/O0D0RMUMC5toQEm', 9, '2025-03-23 18:28:52', '2025-03-23 18:28:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_modulos`
--

CREATE TABLE `usuario_modulos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_modulo` varchar(50) NOT NULL,
  `estado` varchar(10) DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos_permisos`
--
ALTER TABLE `accesos_permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_liquidacion` (`id_liquidacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `auditoria_ibfk_2` (`id_detalle_liquidacion`);

--
-- Indices de la tabla `bases`
--
ALTER TABLE `bases`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cajas_chicas_centro_costo` (`id_centro_costo`),
  ADD KEY `fk_cajas_chicas_usuario_encargado` (`id_usuario_encargado`),
  ADD KEY `fk_cajas_chicas_supervisor` (`id_supervisor`);

--
-- Indices de la tabla `centros_costos`
--
ALTER TABLE `centros_costos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_cuenta_base` (`base_id`);

--
-- Indices de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detalle_liquidacion_id` (`id_liquidacion`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD KEY `fk_facturas_cuenta_id` (`cuenta_id`),
  ADD KEY `fk_facturas_base_id` (`base_id`);

--
-- Indices de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_caja_chica` (`id_caja_chica`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_permiso_nombre` (`nombre`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`rol_id`,`permiso_id`),
  ADD KEY `fk_rol_permisos_permiso` (`permiso_id`);

--
-- Indices de la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_rol` (`id_rol`);

--
-- Indices de la tabla `usuario_modulos`
--
ALTER TABLE `usuario_modulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos_permisos`
--
ALTER TABLE `accesos_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `bases`
--
ALTER TABLE `bases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `centros_costos`
--
ALTER TABLE `centros_costos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuario_modulos`
--
ALTER TABLE `usuario_modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos_permisos`
--
ALTER TABLE `accesos_permisos`
  ADD CONSTRAINT `accesos_permisos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accesos_permisos_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  ADD CONSTRAINT `cajas_chicas_ibfk_1` FOREIGN KEY (`id_usuario_encargado`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `cajas_chicas_ibfk_2` FOREIGN KEY (`id_supervisor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_centro_costo` FOREIGN KEY (`id_centro_costo`) REFERENCES `centros_costos` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_supervisor` FOREIGN KEY (`id_supervisor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_usuario_encargado` FOREIGN KEY (`id_usuario_encargado`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD CONSTRAINT `fk_cuenta_base` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`);

--
-- Filtros para la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  ADD CONSTRAINT `fk_detalle_liquidacion_id` FOREIGN KEY (`id_liquidacion`) REFERENCES `liquidaciones` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`),
  ADD CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_contables` (`id`),
  ADD CONSTRAINT `fk_facturas_base_id` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`),
  ADD CONSTRAINT `fk_facturas_cuenta_id` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_contables` (`id`);

--
-- Filtros para la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  ADD CONSTRAINT `liquidaciones_ibfk_1` FOREIGN KEY (`id_caja_chica`) REFERENCES `cajas_chicas` (`id`);

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `fk_rol_permisos_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rol_permisos_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `usuario_modulos`
--
ALTER TABLE `usuario_modulos`
  ADD CONSTRAINT `usuario_modulos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
