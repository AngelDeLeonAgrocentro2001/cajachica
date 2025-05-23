-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-05-2025 a las 16:09:57
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
  `id_modulo` int(11) DEFAULT NULL,
  `permiso` varchar(50) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `origen` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `accesos_permisos`
--

INSERT INTO `accesos_permisos` (`id`, `id_usuario`, `id_modulo`, `permiso`, `estado`, `origen`, `created_at`) VALUES
(1776, 2, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:49:51'),
(1777, 2, NULL, 'create_detalles', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:49:51'),
(1778, 2, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:49:51'),
(1779, 2, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:49:51'),
(1784, 1, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1785, 1, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1786, 1, NULL, 'create_detalles', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1787, 1, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1788, 1, NULL, 'manage_accesos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1789, 1, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1790, 1, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1791, 1, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1792, 1, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1793, 1, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1794, 1, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1795, 1, NULL, 'manage_reportes', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1796, 1, NULL, 'manage_roles', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1797, 1, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1798, 1, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1799, 1, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1800, 1, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1801, 1, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1802, 8, NULL, 'autorizar_facturas', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1803, 8, NULL, 'autorizar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1804, 8, NULL, 'create_detalles', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1805, 8, NULL, 'create_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1806, 8, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1807, 8, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1808, 8, NULL, 'manage_cajachica', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1809, 8, NULL, 'manage_centros_costos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1810, 8, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1811, 8, NULL, 'manage_facturas', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1812, 8, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1813, 8, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1814, 8, NULL, 'manage_roles', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1815, 8, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1816, 8, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1817, 8, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1818, 8, NULL, 'revisar_facturas', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1819, 8, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:21'),
(1820, 3, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1821, 3, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1822, 3, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1823, 3, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1824, 3, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1825, 3, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1826, 3, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:35'),
(1827, 4, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1828, 4, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1829, 4, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1830, 4, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1831, 4, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1832, 4, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1833, 4, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1834, 4, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1835, 4, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1836, 4, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 18:50:56'),
(1889, 10, NULL, 'autorizar_facturas', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1890, 10, NULL, 'autorizar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1891, 10, NULL, 'create_detalles', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1892, 10, NULL, 'create_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1893, 10, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1894, 10, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1895, 10, NULL, 'manage_cajachica', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1896, 10, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1897, 10, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1898, 10, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1899, 10, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1900, 10, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1901, 10, NULL, 'manage_roles', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1902, 10, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1903, 10, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1904, 10, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1905, 10, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(1906, 10, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DEFAULT', '2025-04-01 19:08:11'),
(2372, 10, NULL, 'manage_centros_costos', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2373, 10, NULL, 'manage_facturas', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2374, 10, NULL, 'revisar_facturas', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2375, 10, NULL, 'revisar_liquidaciones', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2376, 10, NULL, 'manage_cuentas_contables', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2377, 10, NULL, 'manage_impuestos', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2378, 10, NULL, 'manage_tipos_gastos', 'INACTIVO', 'MANUAL', '2025-04-02 14:32:38'),
(2473, 1, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2474, 1, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2475, 1, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2476, 1, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2477, 1, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2478, 1, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2479, 1, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2480, 1, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2481, 1, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2482, 1, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2483, 1, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2484, 1, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2485, 1, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2486, 1, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2487, 1, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2488, 1, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2489, 1, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2490, 1, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-02 15:10:27'),
(2582, 8, NULL, 'autorizar_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2583, 8, NULL, 'autorizar_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2584, 8, NULL, 'create_detalles', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2585, 8, NULL, 'create_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2586, 8, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2587, 8, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2588, 8, NULL, 'manage_cajachica', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2589, 8, NULL, 'manage_centros_costos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2590, 8, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2591, 8, NULL, 'manage_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2592, 8, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2593, 8, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2594, 8, NULL, 'manage_roles', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2595, 8, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2596, 8, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2597, 8, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2598, 8, NULL, 'revisar_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2599, 8, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-02 15:14:33'),
(2669, 3, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2670, 3, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2671, 3, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2672, 3, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2673, 3, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2674, 3, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2675, 3, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(2676, 3, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 17:28:54'),
(3387, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3388, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3389, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3390, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3391, 14, NULL, 'listar_bases', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3392, 14, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3393, 14, NULL, 'manage_auditoria', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3394, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3395, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3396, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3397, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3398, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3399, 14, NULL, 'manage_reportes', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3400, 14, NULL, 'manage_roles', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3401, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3402, 14, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3403, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3404, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3405, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-04 20:45:28'),
(3727, 8, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3728, 8, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3729, 8, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3730, 8, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3731, 8, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3732, 8, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3733, 8, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3734, 8, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3735, 8, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3736, 8, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3737, 8, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3738, 8, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3740, 8, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3742, 8, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3743, 8, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3744, 8, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3745, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3746, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3747, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3748, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3749, 14, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3750, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3751, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3752, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3753, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3754, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3755, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3756, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3757, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3758, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3759, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3760, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3761, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3762, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:03'),
(3781, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3782, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3783, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3784, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3785, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3786, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3787, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3788, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3789, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3790, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3791, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3792, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3793, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3794, 14, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3795, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3796, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3797, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3798, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 20:59:13'),
(3817, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3818, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3819, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3820, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3821, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3822, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3823, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3824, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3825, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3826, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3827, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3828, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3829, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3830, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3831, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-04 21:02:18'),
(3984, 4, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3985, 4, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3986, 4, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3987, 4, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3988, 4, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3989, 4, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3990, 4, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3991, 4, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3992, 4, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3993, 4, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3994, 4, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3995, 4, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3996, 4, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3997, 10, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3998, 10, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(3999, 10, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4000, 10, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4001, 10, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4002, 10, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4003, 15, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4004, 15, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4005, 15, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4006, 15, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4007, 15, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4008, 15, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4009, 15, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4010, 15, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4011, 15, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4012, 15, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4013, 15, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4014, 15, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4015, 15, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-07 21:10:27'),
(4016, 16, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-22 15:31:58'),
(4017, 16, NULL, 'create_detalles', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-22 15:31:58'),
(4018, 16, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-22 15:31:58'),
(4019, 16, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_DESCRIPCION', '2025-04-22 15:31:58'),
(4020, 8, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4021, 8, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4022, 8, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4023, 8, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4024, 8, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4025, 8, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4026, 8, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4027, 8, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4028, 8, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4029, 8, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4030, 8, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4031, 8, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4032, 8, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4033, 8, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4034, 8, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 17:08:38'),
(4038, 1, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-30 18:01:34'),
(4039, 2, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-30 18:01:50'),
(4040, 16, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-30 18:01:50'),
(4042, 4, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-30 18:02:01'),
(4043, 10, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-30 18:02:01'),
(4044, 15, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_DEFAULT', '2025-04-30 18:02:01'),
(4045, 8, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4046, 8, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4047, 8, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4048, 8, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4049, 8, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4050, 8, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4051, 8, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4052, 8, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4053, 8, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4054, 8, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4055, 8, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4056, 8, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4057, 8, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4058, 8, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4059, 8, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:03'),
(4060, 8, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4061, 8, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4062, 8, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4063, 8, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4064, 8, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4065, 8, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4066, 8, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4067, 8, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4068, 8, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4069, 8, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4070, 8, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4071, 8, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4072, 8, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4073, 8, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4074, 8, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4075, 8, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4076, 8, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:17:51'),
(4077, 8, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4078, 8, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4079, 8, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4080, 8, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4081, 8, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4082, 8, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4083, 8, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4084, 8, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4085, 8, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4086, 8, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4087, 8, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4088, 8, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4089, 8, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4090, 8, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4091, 8, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4092, 8, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-04-30 20:18:00'),
(4093, 8, NULL, 'manage_correcciones', 'INACTIVO', 'MANUAL', '2025-04-30 20:18:00'),
(4094, 17, NULL, 'autorizar_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4095, 17, NULL, 'autorizar_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4096, 17, NULL, 'create_detalles', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4097, 17, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4098, 17, NULL, 'listar_bases', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4099, 17, NULL, 'manage_accesos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4100, 17, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4101, 17, NULL, 'manage_cajachica', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4102, 17, NULL, 'manage_centros_costos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4103, 17, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4104, 17, NULL, 'manage_cuentas_contables', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4105, 17, NULL, 'manage_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4106, 17, NULL, 'manage_impuestos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4107, 17, NULL, 'manage_reportes', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4108, 17, NULL, 'manage_roles', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4109, 17, NULL, 'manage_tipos_gastos', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4110, 17, NULL, 'manage_usuarios', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4111, 17, NULL, 'revisar_detalles_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4112, 17, NULL, 'revisar_facturas', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4113, 17, NULL, 'revisar_liquidaciones', 'INACTIVO', 'ROL_DESCRIPCION', '2025-05-05 15:10:38'),
(4159, 17, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4160, 17, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4161, 17, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4162, 17, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4163, 17, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4164, 17, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4165, 17, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4166, 17, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4167, 17, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4168, 17, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4169, 17, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4170, 17, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4171, 17, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4172, 17, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4173, 17, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4174, 17, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4175, 17, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4176, 17, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4177, 17, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-05 15:18:25'),
(4178, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4179, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4180, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4181, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4182, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4183, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4184, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4185, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4186, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4187, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4188, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4189, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4190, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4191, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4192, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4193, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4194, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:00:45'),
(4195, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4196, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4197, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4198, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4199, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4200, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4201, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4202, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4203, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4204, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4205, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4206, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4207, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4208, 14, NULL, 'manage_accesos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4209, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4210, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4211, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4212, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:01:21'),
(4213, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4214, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4215, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4216, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4217, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4218, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4219, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4220, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4221, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4222, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4223, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4224, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4225, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4226, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4227, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4228, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4229, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:08'),
(4231, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4232, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4233, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4234, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4235, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4236, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4237, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4238, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4239, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4240, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4241, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4242, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4243, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4244, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4245, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4246, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4247, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4248, 14, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:40'),
(4249, 14, NULL, 'create_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4250, 14, NULL, 'create_detalles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4251, 14, NULL, 'manage_cajachica', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4252, 14, NULL, 'manage_impuestos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4253, 14, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4254, 14, NULL, 'manage_tipos_gastos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4255, 14, NULL, 'manage_roles', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4256, 14, NULL, 'manage_usuarios', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4257, 14, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4258, 14, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4259, 14, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4260, 14, NULL, 'manage_reportes', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4261, 14, NULL, 'manage_auditoria', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4262, 14, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4263, 14, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4264, 14, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4265, 14, NULL, 'manage_centros_costos', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4266, 14, NULL, 'manage_correcciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-21 17:02:58'),
(4267, 14, NULL, 'manage_accesos', 'INACTIVO', 'MANUAL', '2025-05-21 17:02:58'),
(4278, 18, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-22 14:25:01'),
(4279, 18, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-22 14:25:01'),
(4280, 18, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_MANUAL', '2025-05-22 14:25:01'),
(4281, 18, NULL, 'manage_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-22 14:25:01'),
(4282, 18, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-22 14:25:01'),
(4283, 18, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_MANUAL', '2025-05-22 14:25:01'),
(4284, 18, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_MANUAL', '2025-05-22 14:25:01'),
(4285, 18, NULL, 'autorizar_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-22 14:26:20'),
(4286, 18, NULL, 'autorizar_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-22 14:26:20'),
(4287, 18, NULL, 'manage_cuentas_contables', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-22 14:26:20'),
(4288, 18, NULL, 'manage_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-22 14:26:20'),
(4289, 18, NULL, 'revisar_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-22 14:26:20'),
(4290, 18, NULL, 'revisar_detalles_liquidaciones', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-22 14:26:20'),
(4291, 18, NULL, 'revisar_facturas', 'ACTIVO', 'ROL_DESCRIPCION', '2025-05-22 14:26:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `id_liquidacion` int(11) DEFAULT NULL,
  `id_detalle_liquidacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` enum('APROBADO','RECHAZADO','EXPORTADO_SAP','ELIMINADO') NOT NULL,
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
(1, 1, NULL, 2, 'APROBADO', NULL, '2025-05-23 11:57:26', 'CREADO', 'Encargado 1', 'Liquidación creada por encargado'),
(2, 1, 1, 2, 'RECHAZADO', NULL, '2025-05-23 11:58:20', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: FACT-006 para usuario ID 2'),
(3, 1, 2, 2, 'RECHAZADO', NULL, '2025-05-23 11:58:57', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: FACT-006222 para usuario ID 2'),
(4, 1, 3, 2, 'RECHAZADO', NULL, '2025-05-23 11:59:23', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: Fact-003 para usuario ID 2'),
(5, 1, 4, 2, 'RECHAZADO', NULL, '2025-05-23 11:59:58', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: Fact-003222 para usuario ID 2'),
(6, 1, 5, 2, 'RECHAZADO', NULL, '2025-05-23 12:00:20', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: FACT-005322 para usuario ID 2'),
(7, 1, NULL, 2, 'APROBADO', NULL, '2025-05-23 12:00:40', 'FINALIZADO', 'Encargado 1', 'Liquidación finalizada por encargado y asignada al supervisor Supervisor 1 (supervisor1@example.com)'),
(8, 1, 2, 3, 'RECHAZADO', NULL, '2025-05-23 12:05:38', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Detalle enviado a corrección con comentario: foto por supervisor ID 3'),
(9, 1, 1, 3, 'APROBADO', NULL, '2025-05-23 12:06:13', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(10, 1, 3, 3, 'APROBADO', NULL, '2025-05-23 12:06:13', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(11, 1, 4, 3, 'APROBADO', NULL, '2025-05-23 12:06:13', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(12, 1, 5, 3, 'APROBADO', NULL, '2025-05-23 12:06:13', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(13, 1, NULL, 3, 'APROBADO', NULL, '2025-05-23 12:06:13', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien  - Asignada a contador ID 4'),
(14, 1, 2, 2, 'RECHAZADO', NULL, '2025-05-23 12:09:42', 'ACTUALIZAR_DETALLE_EN_CORRECCION', 'Encargado 1', 'Factura actualizada en corrección: FACT-006222'),
(15, 1, 3, 2, 'RECHAZADO', NULL, '2025-05-23 12:10:00', 'ACTUALIZAR_DETALLE_EN_CORRECCION', 'Encargado 1', 'Factura actualizada en corrección: Fact-003'),
(16, 1, 2, 2, 'RECHAZADO', NULL, '2025-05-23 12:10:30', 'CORRECTION_ENVIADA', 'Encargado 1', 'Detalle corregido y enviado a PENDIENTE_AUTORIZACION para supervisor ID 3'),
(17, 1, NULL, 2, 'RECHAZADO', NULL, '2025-05-23 12:10:30', 'CORRECCIONES_ENVIADAS', 'Encargado 1', 'Correcciones enviadas a SUPERVISOR, liquidación sin cambio de estado'),
(18, 1, 3, 2, 'RECHAZADO', NULL, '2025-05-23 12:11:11', 'CORRECTION_ENVIADA', 'Encargado 1', 'Detalle corregido y enviado a PENDIENTE_REVISION_CONTABILIDAD para contador ID '),
(19, 1, NULL, 2, 'RECHAZADO', NULL, '2025-05-23 12:11:11', 'CORRECCIONES_ENVIADAS', 'Encargado 1', 'Correcciones enviadas a CONTABILIDAD, liquidación sin cambio de estado'),
(20, 1, 1, 3, 'APROBADO', NULL, '2025-05-23 12:11:45', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(21, 1, 2, 3, 'APROBADO', NULL, '2025-05-23 12:11:45', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(22, 1, 3, 3, 'APROBADO', NULL, '2025-05-23 12:11:45', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(23, 1, 4, 3, 'APROBADO', NULL, '2025-05-23 12:11:45', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(24, 1, 5, 3, 'APROBADO', NULL, '2025-05-23 12:11:45', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(25, 1, NULL, 3, 'APROBADO', NULL, '2025-05-23 12:11:45', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien  - Asignada a contador ID 4'),
(26, 1, NULL, 2, 'APROBADO', NULL, '2025-05-23 12:14:59', 'FINALIZADO', 'Encargado 1', 'Liquidación finalizada por encargado y asignada al supervisor Supervisor 1 (supervisor1@example.com)'),
(27, 1, 3, 3, 'RECHAZADO', NULL, '2025-05-23 12:16:58', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Detalle enviado a corrección con comentario: foto por supervisor ID 3'),
(28, 1, 4, 3, 'RECHAZADO', NULL, '2025-05-23 12:16:58', 'ENVIADO_A_CORRECCION', 'Supervisor 1', 'Detalle enviado a corrección con comentario: agregar foto por supervisor ID 3'),
(29, 1, 1, 3, 'APROBADO', NULL, '2025-05-23 12:16:58', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(30, 1, 2, 3, 'APROBADO', NULL, '2025-05-23 12:16:58', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(31, 1, 5, 3, 'APROBADO', NULL, '2025-05-23 12:16:58', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(32, 1, NULL, 3, 'APROBADO', NULL, '2025-05-23 12:16:58', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien  - Asignada a contador ID 4'),
(33, 1, 4, 2, 'RECHAZADO', NULL, '2025-05-23 12:18:37', 'ACTUALIZAR_DETALLE_EN_CORRECCION', 'Encargado 1', 'Factura actualizada en corrección: Fact-003222'),
(34, 1, 3, 2, 'RECHAZADO', NULL, '2025-05-23 12:18:41', 'CORRECTION_ENVIADA', 'Encargado 1', 'Detalle corregido y enviado a PENDIENTE_AUTORIZACION para supervisor ID 3'),
(35, 1, 4, 2, 'RECHAZADO', NULL, '2025-05-23 12:18:41', 'CORRECTION_ENVIADA', 'Encargado 1', 'Detalle corregido y enviado a PENDIENTE_AUTORIZACION para supervisor ID 3'),
(36, 1, NULL, 2, 'RECHAZADO', NULL, '2025-05-23 12:18:41', 'CORRECCIONES_ENVIADAS', 'Encargado 1', 'Correcciones enviadas a SUPERVISOR, liquidación sin cambio de estado'),
(37, 1, 3, 3, 'APROBADO', NULL, '2025-05-23 12:19:36', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(38, 1, 5, 4, 'RECHAZADO', NULL, '2025-05-23 12:19:59', 'ENVIADO_A_CORRECCION', 'Contador 1', 'Detalle enviado a corrección con comentario: foto por contador ID 0'),
(39, 1, 5, 2, 'RECHAZADO', NULL, '2025-05-23 12:21:12', 'ACTUALIZAR_DETALLE_EN_CORRECCION', 'Encargado 1', 'Factura actualizada en corrección: FACT-005322'),
(40, 1, 5, 2, 'RECHAZADO', NULL, '2025-05-23 12:21:19', 'CORRECTION_ENVIADA', 'Encargado 1', 'Detalle corregido y enviado a PENDIENTE_REVISION_CONTABILIDAD para contador ID 0'),
(41, 1, NULL, 2, 'RECHAZADO', NULL, '2025-05-23 12:21:19', 'CORRECCIONES_ENVIADAS', 'Encargado 1', 'Correcciones enviadas a CONTABILIDAD, liquidación sin cambio de estado'),
(42, 1, 3, 4, 'RECHAZADO', NULL, '2025-05-23 12:21:39', 'ENVIADO_A_CORRECCION', 'Contador 1', 'Detalle enviado a corrección con comentario: foto por contador ID 0'),
(43, 1, 5, 4, 'RECHAZADO', NULL, '2025-05-23 12:21:39', 'ENVIADO_A_CORRECCION', 'Contador 1', 'Detalle enviado a corrección con comentario: foto por contador ID 0'),
(44, 1, 1, 4, 'APROBADO', NULL, '2025-05-23 12:21:59', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'todo bien '),
(45, 1, 2, 4, 'APROBADO', NULL, '2025-05-23 12:21:59', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'todo bien '),
(46, 1, NULL, 4, 'APROBADO', NULL, '2025-05-23 12:21:59', 'AUTORIZADO_POR_CONTABILIDAD', 'Contador 1', 'todo bien '),
(47, 1, NULL, 4, 'EXPORTADO_SAP', NULL, '2025-05-23 12:22:05', 'EXPORTADO', 'Contador 1', 'Liquidación exportada a SAP como liquidacion_1_20250523_142205.csv'),
(48, 2, 3, 2, 'RECHAZADO', NULL, '2025-05-23 12:23:05', 'DETALLE_ASIGNADO', 'Encargado 1', 'Detalle asignado a nueva liquidación'),
(49, 2, 5, 2, 'RECHAZADO', NULL, '2025-05-23 12:23:53', 'DETALLE_ASIGNADO', 'Encargado 1', 'Detalle asignado a liquidación existente'),
(50, 3, 4, 3, 'RECHAZADO', NULL, '2025-05-23 12:24:28', 'DETALLE_ASIGNADO', 'Supervisor 1', 'Detalle asignado a nueva liquidación'),
(51, 1, 2, 2, 'RECHAZADO', NULL, '2025-05-23 12:52:10', 'ACTUALIZAR_DETALLE', 'Encargado 1', 'Factura actualizada: FACT-006222'),
(52, 1, 6, 2, 'RECHAZADO', NULL, '2025-05-23 12:52:38', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: Fact-003q para usuario ID 2'),
(53, 1, 7, 2, 'RECHAZADO', NULL, '2025-05-23 12:52:56', 'CREAR_DETALLE', 'Encargado 1', 'Factura creada: FACT-006211 para usuario ID 2'),
(54, 1, NULL, 2, 'APROBADO', NULL, '2025-05-23 12:53:56', 'FINALIZADO', 'Encargado 1', 'Liquidación finalizada por encargado y asignada al supervisor Supervisor 1 (supervisor1@example.com)'),
(55, 1, 1, 3, 'APROBADO', NULL, '2025-05-23 12:55:08', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(56, 1, 2, 3, 'APROBADO', NULL, '2025-05-23 12:55:08', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(57, 1, 6, 3, 'APROBADO', NULL, '2025-05-23 12:55:08', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(58, 1, 7, 3, 'APROBADO', NULL, '2025-05-23 12:55:08', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(59, 1, NULL, 3, 'APROBADO', NULL, '2025-05-23 12:55:08', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien  - Asignada a contador ID 4'),
(60, 2, 5, 2, 'RECHAZADO', NULL, '2025-05-23 13:03:03', 'ELIMINAR_DETALLE', 'Encargado 1', 'Factura eliminada: FACT-005322'),
(61, 2, 3, 2, 'RECHAZADO', NULL, '2025-05-23 13:03:06', 'ELIMINAR_DETALLE', 'Encargado 1', 'Factura eliminada: Fact-003'),
(62, 1, NULL, 2, 'APROBADO', NULL, '2025-05-23 13:16:54', 'FINALIZADO', 'Encargado 1', 'Liquidación finalizada por encargado y asignada al supervisor Supervisor 1 (supervisor1@example.com)'),
(63, 1, 1, 3, 'APROBADO', NULL, '2025-05-23 13:20:29', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(64, 1, 2, 3, 'APROBADO', NULL, '2025-05-23 13:20:29', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(65, 1, 6, 3, 'APROBADO', NULL, '2025-05-23 13:20:29', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(66, 1, 7, 3, 'APROBADO', NULL, '2025-05-23 13:20:29', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(67, 1, NULL, 3, 'APROBADO', NULL, '2025-05-23 13:20:29', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien  - Asignada a contador ID 4'),
(68, 1, 1, 3, 'APROBADO', NULL, '2025-05-23 13:34:46', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(69, 1, 2, 3, 'APROBADO', NULL, '2025-05-23 13:34:46', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(70, 1, 6, 3, 'APROBADO', NULL, '2025-05-23 13:34:46', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(71, 1, 7, 3, 'APROBADO', NULL, '2025-05-23 13:34:46', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien '),
(72, 1, NULL, 3, 'APROBADO', NULL, '2025-05-23 13:34:46', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien  - Asignada a contador ID 4'),
(73, 1, 1, 3, 'APROBADO', NULL, '2025-05-23 13:45:09', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', ''),
(74, 1, 2, 3, 'APROBADO', NULL, '2025-05-23 13:45:09', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', ''),
(75, 1, 6, 3, 'APROBADO', NULL, '2025-05-23 13:45:09', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', ''),
(76, 1, 7, 3, 'APROBADO', NULL, '2025-05-23 13:45:09', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', ''),
(77, 1, NULL, 3, 'APROBADO', NULL, '2025-05-23 13:45:09', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', ' - Asignada a contador ID 15'),
(78, 1, 1, 3, 'APROBADO', NULL, '2025-05-23 14:06:28', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien'),
(79, 1, 2, 3, 'APROBADO', NULL, '2025-05-23 14:06:28', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien'),
(80, 1, 6, 3, 'APROBADO', NULL, '2025-05-23 14:06:28', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien'),
(81, 1, 7, 3, 'APROBADO', NULL, '2025-05-23 14:06:28', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien'),
(82, 1, NULL, 3, 'APROBADO', NULL, '2025-05-23 14:06:28', 'AUTORIZADO_POR_SUPERVISOR', 'Supervisor 1', 'todo bien - Asignada a contador ID 4');

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
(1, 'Caja chica 1', 0.00, 0.00, 5000.00, 5000.00, 2, 3, 1, 'ACTIVA', '2025-05-05 13:11:52', '2025-05-05 13:11:52'),
(2, 'Caja chica 2', 0.00, 0.00, 5000.00, 5000.00, 2, 3, 2, 'ACTIVA', '2025-05-05 13:12:17', '2025-05-05 13:12:17'),
(3, 'Caja Chica 3', 0.00, 0.00, 2500.00, 2500.00, 2, 3, 1, 'ACTIVA', '2025-05-05 13:12:39', '2025-05-05 13:12:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centros_costos`
--

CREATE TABLE `centros_costos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(10) DEFAULT '5',
  `base_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `centros_costos`
--

INSERT INTO `centros_costos` (`id`, `codigo`, `nombre`, `estado`, `creado_en`, `tipo`, `base_id`) VALUES
(1, '1001', 'Cuenta de Activos', 'ACTIVO', '2025-03-21 20:05:53', '5', 1),
(2, '2001', 'Cuenta de Pasivos', 'ACTIVO', '2025-03-21 20:05:53', '5', 2),
(3, '100', 'Angel', 'ACTIVO', '2025-03-21 21:44:22', '2', 1),
(10, 'D20', 'TI', 'ACTIVO', '2025-04-28 22:06:02', '1', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_contables`
--

CREATE TABLE `cuentas_contables` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_centro_costo` int(11) DEFAULT NULL,
  `codigo_cuenta` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas_contables`
--

INSERT INTO `cuentas_contables` (`id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`, `id_centro_costo`, `codigo_cuenta`) VALUES
(1, 'Reparación y mantenimiento de licencias y software', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520401004'),
(2, 'Gastos de fabricación variables (producción)', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521401010'),
(3, 'Depreciación por contratos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '650109001'),
(4, 'Sueldos y salarios extraordinarios', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520101002'),
(5, 'Reparación y mantenimiento de maquinaria y equipo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520401001'),
(6, 'Útiles de limpieza', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '520900003'),
(7, 'Impuesto sobre inmuebles IUSI', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521001002'),
(8, 'Transporte de mercancías', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '521201004'),
(9, 'Amortización de marcas y patentes', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650202001'),
(10, 'Fianzas', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521201008'),
(11, 'Reembolso por depreciación y combustible de empleados', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520201004'),
(12, 'Capacitación interna de empleados', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520201007'),
(13, 'Depreciación de edificios e instalaciones', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521701001'),
(14, 'Vacaciones', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520101008'),
(15, 'Reparación y mantenimiento de vehículos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521101002'),
(16, 'Otras prestaciones de empleados', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520201005'),
(17, 'Servicios administrativos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520301001'),
(18, 'Reparación y mantenimiento de activos diversos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520401009'),
(19, 'Honorarios', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520301003'),
(20, 'Sueldos y salarios ordinarios', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520101001'),
(21, 'Depreciación de mejoras en bienes arrendados', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650107001'),
(22, 'Bonificación incentivo 78-89', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '520101003'),
(23, 'Alquiler de maquinaria', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520501005'),
(24, 'Depreciación de edificios e instalaciones', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650101001'),
(25, 'Comisiones y gastos bancarios', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 20:52:01', 1, '720201001'),
(26, 'Cuotas patronales IGSS', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520101004'),
(27, 'Muestras', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521401002'),
(28, 'Combustibles y lubricantes', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521101001'),
(29, 'Reparaciones y mejoras a bienes arrendados', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520401010'),
(30, 'Depreciación de mobiliario y equipo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650102001'),
(31, 'Regalías por uso de fórmulas', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521401007'),
(32, 'Servicios de vigilancia', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521301001'),
(33, 'Bono 14', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520101005'),
(34, 'Amortización de licencias y software', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650201001'),
(35, 'Aguinaldo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520101006'),
(36, 'Responsabilidad civil', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521201002'),
(37, 'Atención a empleados por metas (premios)', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520201003'),
(38, 'Indemnización', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '520101007'),
(39, 'Impuesto sobre la renta corriente', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 20:52:01', 1, '830101001'),
(40, 'Honorarios del exterior', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520301004'),
(41, 'Alquiler de equipo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520501002'),
(42, 'Almacenajes en bodegas fiscales', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521301003'),
(43, 'Gastos de fabricación fijos (producción)', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521401011'),
(44, 'Pérdida cambiaria de compañías relacionadas', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '740301001'),
(45, 'Reparación y mantenimiento de edificios', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520401002'),
(46, 'Herramientas', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520900005'),
(47, 'Seguro médico', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '520201001'),
(48, 'Vehículos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '521201005'),
(49, 'Reparación y mantenimiento de maquinaria', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520401005'),
(50, 'Teléfono, correo y comunicaciones', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520601002'),
(51, 'Viáticos del exterior', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '521501002'),
(52, 'Depreciación de mobiliario y equipo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521702001'),
(53, 'Equipo electrónico', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '521201003'),
(54, 'IDP', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521001001'),
(55, 'Depreciación de vehículos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521703001'),
(56, 'Suministros', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520900002'),
(57, 'Repuestos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521101003'),
(58, 'Incendio', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521201001'),
(59, 'Depreciación de vehículos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650103001'),
(60, 'Depreciación de maquinaria y equipo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521704001'),
(61, 'Depreciación de maquinaria y equipo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650104001'),
(62, 'Depreciación de equipo de cómputo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521705001'),
(63, 'Depreciación de equipo de cómputo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650105001'),
(64, 'Autoconsumos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521401001'),
(65, 'Reparación y mantenimiento de equipo de cómputo', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520401003'),
(66, 'Depreciación de activos diversos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521706001'),
(67, 'Depreciación de activos diversos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '650106001'),
(68, 'Gastos no deducibles', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521401009'),
(69, 'Arrendamiento de vehículos de personal', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520501004'),
(70, 'Alquiler de vehículos', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520501003'),
(71, 'Energía eléctrica', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '520601001'),
(72, 'Intereses bancarios', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 20:52:01', 1, '720101001'),
(73, 'Otros impuestos y tasas', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521001004'),
(74, 'Intereses bancarios del exterior', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 20:52:01', 1, '720101002'),
(75, 'Atención a empleados', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '520201002'),
(76, 'Agua', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520601003'),
(77, 'Viáticos locales', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '521501001'),
(78, 'Impuesto de turismo (INGUAT)', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521001003'),
(79, 'Equipo de seguridad', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520900004'),
(80, 'Papelería y útiles', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '520900001'),
(81, 'Capacitación externa de empleados', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '520201006'),
(82, 'Servicios prestados', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '520301002'),
(83, 'Pérdida cambiaria por partida contable de compañías relacionadas', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '740301002'),
(84, 'Pérdida por diferencial cambiario por partida contable', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '720401001'),
(85, 'Comisiones por uso de POS', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '720501001'),
(86, 'Fletes', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-05 13:10:54', 1, '521301002'),
(87, 'Regalías por uso de marcas (producción)', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521401008'),
(88, 'Pérdida por diferencial cambiario operacional', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:05', 1, '720301001'),
(89, 'Mano de obra directa (producción)', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-21 13:59:38', 1, '521401012'),
(90, 'Gastos de exportación', '', 'ACTIVO', '2025-04-29 23:16:57', '2025-05-16 15:55:06', 1, '521301005'),
(91, 'Otros impuestos y tasas', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641001004'),
(92, 'Gastos de exportación', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:06', 10, '641301005'),
(93, 'Gastos de regente', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641301004'),
(94, 'Fletes', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641301002'),
(95, 'Atención a clientes en general', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640701001'),
(96, 'Reparación y mantenimiento de equipo de cómputo', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640401003'),
(97, 'Sueldos y salarios extraordinarios', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640101002'),
(98, 'Vacaciones', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640101008'),
(99, 'Incentivo por logro académico', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640101009'),
(100, 'Comisiones', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640101010'),
(101, 'Bonificación a empleados de ventas', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640101011'),
(102, 'Otras prestaciones de empleados', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640201005'),
(103, 'Capacitación interna de empleados', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640201007'),
(104, 'Revisoría fiscal', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640301005'),
(105, 'Asesoría jurídica', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640301006'),
(106, 'Asesoría administrativa', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:06', 10, '640301007'),
(107, 'Responsabilidad civil', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641201002'),
(108, 'Alquiler de vehículos', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640501003'),
(109, 'Reparación y mantenimiento de maquinaria', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640401001'),
(110, 'Reparación y mantenimiento de vehículos', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '641101002'),
(111, 'Arrendamiento de inmuebles', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640501001'),
(112, 'Repuestos', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641101003'),
(113, 'Viáticos del exterior', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '641501002'),
(114, 'Estimación para cuentas incobrables', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:06', 10, '641601001'),
(115, 'Reparación y mantenimiento de licencias y software', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640401004'),
(116, 'Servicios de vigilancia', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641301001'),
(117, 'Sueldos y salarios ordinarios', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640101001'),
(118, 'Servicios administrativos de afiliadas', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640302001'),
(119, 'Bonificación incentivo 78-89', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640101003'),
(120, 'Cuotas patronales IGSS', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640101004'),
(121, 'Bono 14', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640101005'),
(122, 'Reparación y mantenimiento de activos diversos', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640401009'),
(123, 'Transporte de mercancías', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '641201004'),
(124, 'Aguinaldo', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640101006'),
(125, 'Indemnización', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640101007'),
(126, 'Atención a empleados', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640201002'),
(127, 'Registro de productos', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641301006'),
(128, 'Transporte de mercancías de importación/exportación', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '641201006'),
(129, 'Atención a empleados por metas (premios)', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640201003'),
(130, 'Herramientas', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640901005'),
(131, 'Reparaciones y mejoras a bienes arrendados', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640401010'),
(132, 'Reparación y mantenimiento de mobiliario y equipo', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640401005'),
(133, 'Vehículos', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '641201005'),
(134, 'Alquiler de equipo', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640501002'),
(135, 'Impuesto sobre inmuebles IUSI', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641001002'),
(136, 'Equipo electrónico', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '641201003'),
(137, 'Donaciones y contribuciones', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641401005'),
(138, 'Incendio y todo riesgo', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641201001'),
(139, 'Reparación y mantenimiento de edificios', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640401002'),
(140, 'Perfeccionamiento activo', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641301007'),
(141, 'Otros gastos diversos', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641401022'),
(142, 'Combustibles y lubricantes', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641101001'),
(143, 'IDP', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641001001'),
(144, 'Equipo de seguridad', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640901004'),
(145, 'Almacenajes en bodegas fiscales', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '641301003'),
(146, 'Teléfono, correo y comunicaciones', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640601002'),
(147, 'Seguro médico', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640201001'),
(148, 'Muestras', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641401002'),
(149, 'Útiles de limpieza', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640901003'),
(150, 'Reembolso por depreciación y combustible de empleados', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640201004'),
(151, 'Gastos no deducibles', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641401009'),
(152, 'Energía eléctrica', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640601001'),
(153, 'Impuestos y tasas municipales', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641001005'),
(154, 'Agua', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640601003'),
(155, 'Autoconsumos', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641401001'),
(156, 'Fianzas', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641201011'),
(157, 'Gastos por documentos de exportación', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '641301008'),
(158, 'Gastos de representación', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:06', 10, '641401006'),
(159, 'Papelería y útiles', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '640901001'),
(160, 'Suministros', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640901002'),
(161, 'Capacitación externa de empleados', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-21 13:59:38', 10, '640201006'),
(162, 'Honorarios del exterior', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640301004'),
(163, 'Servicios prestados', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640301002'),
(164, 'Honorarios profesionales', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '640301003'),
(165, 'Viáticos locales', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-16 15:55:05', 10, '641501001'),
(166, 'Impuesto de turismo (INGUAT)', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641001003'),
(167, 'Cuotas y suscripciones', '', 'ACTIVO', '2025-04-29 23:32:29', '2025-05-05 13:10:54', 10, '641401004'),
(168, 'Seguro médico', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '610201001'),
(169, 'Honorarios profesionales', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610301003'),
(170, 'Reparación y mantenimiento de maquinaria', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610401001'),
(171, 'Reparación y mantenimiento de edificios', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610401002'),
(172, 'Reparación y mantenimiento de activos diversos', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610401009'),
(173, 'Reparaciones y mejoras a bienes arrendados', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610401010'),
(174, 'Arrendamiento de vehículos de personal', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610501004'),
(175, 'Energía eléctrica', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '610601001'),
(176, 'Agua', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610601003'),
(177, 'Atención a clientes por promoción de metas anuales', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610701004'),
(178, 'Impuesto sobre inmuebles IUSI', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611001002'),
(179, 'Incendio y todo riesgo', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611201001'),
(180, 'Responsabilidad civil', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611201002'),
(181, 'Transporte de mercancías', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '611201004'),
(182, 'Fianzas', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611201011'),
(183, 'Cuotas y suscripciones', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611401004'),
(184, 'Gastos de representación', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:06', 3, '611401006'),
(185, 'Vacaciones', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610101008'),
(186, 'Sueldos y salarios extraordinarios', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610101002'),
(187, 'Bonificación a empleados de ventas', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610101011'),
(188, 'Incentivo por logro académico', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610101009'),
(189, 'Comisiones', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610101010'),
(190, 'Servicios administrativos', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610128003'),
(191, 'Equipo de seguridad', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610901004'),
(192, 'Servicios prestados', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610301002'),
(193, 'Servicios administrativos de afiliadas', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610302001'),
(194, 'Alquiler de vehículos', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610501003'),
(195, 'Atención a clientes por promoción comercial', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610701003'),
(196, 'Regalías por uso de marcas de afiliadas', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '611402007'),
(197, 'Atención a clientes en congresos', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610701005'),
(198, 'Suministros', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610901002'),
(199, 'Equipo electrónico', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '611201003'),
(200, 'Capacitación externa de empleados', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610201006'),
(201, 'Publicidad radial', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610801002'),
(202, 'Publicidad en impresiones', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610801004'),
(203, 'Reparación y mantenimiento de vehículos', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '611101002'),
(204, 'Útiles de limpieza', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '610901003'),
(205, 'Herramientas', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610901005'),
(206, 'Fletes', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611301002'),
(207, 'Papelería y útiles', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '610901001'),
(208, 'Vehículos', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '611201005'),
(209, 'Atención a clientes en general', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '610701001'),
(210, 'Atención a empleados', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '610201002'),
(211, 'Sueldos y salarios ordinarios', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610101001'),
(212, 'Bonificación incentivo 78-89', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '610101003'),
(213, 'Cuotas patronales IGSS', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610101004'),
(214, 'Bono 14', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610101005'),
(215, 'Aguinaldo', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610101006'),
(216, 'Indemnización', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '610101007'),
(217, 'Atención a empleados por metas (premios)', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610201003'),
(218, 'Reparación y mantenimiento de equipo de cómputo', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610401003'),
(219, 'Atención a clientes por promoción técnica', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610701002'),
(220, 'Publicidad en general', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '610801001'),
(221, 'Publicidad POP (producto)', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610801003'),
(222, 'Autoconsumos', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611401001'),
(223, 'Donaciones y contribuciones', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611401005'),
(224, 'Repuestos', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611101003'),
(225, 'Impuesto de turismo (INGUAT)', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611001003'),
(226, 'Teléfono, correo y comunicaciones', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610601002'),
(227, 'Impuestos y tasas municipales', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611001005'),
(228, 'Muestras', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611401002'),
(229, 'Gastos no deducibles', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611401009'),
(230, 'Viáticos del exterior', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '611501002'),
(231, 'Otros impuestos y tasas', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611001004'),
(232, 'Reembolso por depreciación y combustible de empleados', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-21 13:59:38', 3, '610201004'),
(233, 'Combustibles y lubricantes', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611101001'),
(234, 'IDP', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-05 13:10:54', 3, '611001001'),
(235, 'Viáticos locales', '', 'ACTIVO', '2025-04-30 15:22:04', '2025-05-16 15:55:05', 3, '611501001');

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
  `tipo_documento` varchar(50) NOT NULL DEFAULT 'FACTURA',
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
  `estado` enum('EN_PROCESO','PENDIENTE_AUTORIZACION','PENDIENTE_REVISION_CONTABILIDAD','FINALIZADO','RECHAZADO_AUTORIZACION','RECHAZADO_POR_CONTABILIDAD','DESCARTADO','EN_CORRECCION') NOT NULL DEFAULT 'EN_PROCESO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rutas_archivos` text DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `serie` varchar(50) DEFAULT NULL,
  `dpi` varchar(50) DEFAULT NULL,
  `id_centro_costo` int(11) DEFAULT NULL,
  `id_cuenta_contable` int(11) DEFAULT NULL,
  `original_role` varchar(50) DEFAULT NULL,
  `correccion_comentario` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_supervisor_correccion` int(11) DEFAULT NULL,
  `id_contador_correccion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `detalle_liquidaciones`
--

INSERT INTO `detalle_liquidaciones` (`id`, `id_liquidacion`, `no_factura`, `regimen`, `c_costo`, `nit_proveedor`, `tipo_documento`, `nombre_proveedor`, `fecha`, `bien_servicio`, `t_gasto`, `codigo_ccta`, `descripcion_factura`, `p_unitario`, `iva`, `total_factura`, `idp`, `inguat`, `porcentajeiva`, `porcentajeidp`, `tipo_combustible`, `estado`, `created_at`, `updated_at`, `rutas_archivos`, `cantidad`, `serie`, `dpi`, `id_centro_costo`, `id_cuenta_contable`, `original_role`, `correccion_comentario`, `id_usuario`, `id_supervisor_correccion`, `id_contador_correccion`) VALUES
(1, 1, 'FACT-006', NULL, NULL, '2424242', 'FACTURA', '3242', '2025-05-20', NULL, 'Alimentos', NULL, NULL, 288.39, 34.61, 323.00, 0.00, 0.00, NULL, NULL, NULL, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-05-23 11:58:20', '2025-05-23 13:45:09', '[\"Uploads\\/683062dc1a202_photo_1748001496701.jpg\"]', 0, '', NULL, 2, 56, NULL, NULL, 2, NULL, NULL),
(2, 1, 'FACT-006222', NULL, NULL, '32323', 'FACTURA', 'Miguel', '2025-05-20', NULL, 'Combustible', NULL, NULL, 294.20, 35.30, 353.00, 23.50, 0.00, NULL, NULL, 'Gasolina', 'PENDIENTE_REVISION_CONTABILIDAD', '2025-05-23 11:58:57', '2025-05-23 13:45:09', '[\"uploads\\/6830658631861_photo_1748002180450.jpg\"]', 5, '', NULL, 1, 48, NULL, NULL, 2, NULL, NULL),
(4, 3, 'Fact-003222', NULL, NULL, '322', 'COMPROBANTE', 'Miguel perez', '2025-05-27', NULL, 'Alimentos', NULL, NULL, 232.00, 0.00, 232.00, 0.00, 0.00, NULL, NULL, NULL, 'EN_PROCESO', '2025-05-23 11:59:58', '2025-05-23 12:24:28', '[\"uploads\\/6830679d81e05_agrosistemas.JPG\"]', 3, '323dd', NULL, 1, 56, NULL, NULL, 2, NULL, NULL),
(6, 1, 'Fact-003q', NULL, NULL, NULL, 'RECIBO', 'Proveedor Prueba 4', '2025-05-21', NULL, 'Alimentos', NULL, NULL, 221.00, 0.00, 221.00, 0.00, 0.00, NULL, NULL, NULL, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-05-23 12:52:38', '2025-05-23 13:45:09', '[]', 0, '', 'q212121', 10, 160, NULL, NULL, 2, NULL, NULL),
(7, 1, 'FACT-006211', NULL, NULL, '43433', 'COMPROBANTE', 'Proveedor Prueba 6', '2025-05-20', NULL, 'otros...', NULL, NULL, 221.00, 0.00, 221.00, 0.00, 0.00, NULL, NULL, NULL, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-05-23 12:52:56', '2025-05-23 13:45:09', '[]', 221, '332sws', NULL, 1, 25, NULL, NULL, 2, NULL, NULL);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos`
--

CREATE TABLE `impuestos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo_impuesto` enum('IVA','IDP','INGUAT') NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `impuestos`
--

INSERT INTO `impuestos` (`id`, `nombre`, `tipo_impuesto`, `porcentaje`, `estado`, `creado_en`) VALUES
(1, 'IVA', 'IVA', 12.00, 'ACTIVO', '2025-04-15 18:21:17'),
(2, 'IDP', 'IDP', 1.00, 'ACTIVO', '2025-04-15 18:21:17'),
(3, 'INGUAT', 'INGUAT', 5.00, 'ACTIVO', '2025-04-15 18:21:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidaciones`
--

CREATE TABLE `liquidaciones` (
  `id` int(11) NOT NULL,
  `id_caja_chica` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_centros_de_costos` int(11) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT 0.00,
  `estado` enum('EN_PROCESO','PENDIENTE_AUTORIZACION','PENDIENTE_REVISION_CONTABILIDAD','FINALIZADO','RECHAZADO_AUTORIZACION','RECHAZADO_POR_CONTABILIDAD') NOT NULL DEFAULT 'EN_PROCESO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exportado` tinyint(1) DEFAULT 0,
  `id_rol` int(11) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id_supervisor` int(11) DEFAULT NULL,
  `id_contador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `liquidaciones`
--

INSERT INTO `liquidaciones` (`id`, `id_caja_chica`, `id_usuario`, `id_centros_de_costos`, `fecha_creacion`, `fecha_inicio`, `fecha_fin`, `monto_total`, `estado`, `created_at`, `updated_at`, `exportado`, `id_rol`, `nombre`, `email`, `id_supervisor`, `id_contador`) VALUES
(1, 1, 2, 0, '2025-05-23', '2025-05-01', '2025-05-31', 1118.00, 'PENDIENTE_REVISION_CONTABILIDAD', '2025-05-23 11:57:26', '2025-05-23 14:06:28', NULL, NULL, NULL, NULL, 3, 4),
(2, 1, 2, 0, '2025-05-23', '2025-05-23', '2025-05-23', 0.00, 'EN_PROCESO', '2025-05-23 12:23:05', '2025-05-23 13:03:06', 0, NULL, NULL, NULL, NULL, NULL),
(3, 1, 2, 0, '2025-05-23', '2025-05-23', '2025-05-23', 232.00, 'EN_PROCESO', '2025-05-23 12:24:28', '2025-05-23 12:24:28', 0, NULL, NULL, NULL, NULL, NULL);

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
(18, 'Revisar Facturas', 'revisar_facturas', 'index.php?controller=factura&action=list&mode=revisar', 'ACTIVO', '2025-03-21 20:47:24'),
(19, 'Gestión de Correcciones', 'manage_correcciones', 'index.php?controller=liquidacion&action=listCorrecciones', 'ACTIVO', '2025-04-30 17:26:49');

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
(18, 'revisar_facturas', 'Permite revisar facturas', '2025-03-21 20:46:41'),
(19, 'listar_bases', 'Permite listar bases de centros de costos', '2025-04-02 15:35:17'),
(20, 'manage_correcciones', 'Permite gestionar correcciones de liquidaciones', '2025-04-23 13:40:32');

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
(3, 'SUPERVISOR', 'Supervisor autorizador', 'ACTIVO', '2025-03-04 14:22:33'),
(4, 'CONTABILIDAD', 'contabilidad', 'ACTIVO', '2025-03-04 14:22:33'),
(17, 'Contador admin', 'sera Contador', 'ACTIVO', '2025-04-02 14:33:27'),
(18, 'Admin Junior', 'sera Admin junior se limita accesos y permisos', 'ACTIVO', '2025-04-02 15:14:08'),
(19, 'Usuario', 'Admin', 'ACTIVO', '2025-05-05 15:09:32'),
(20, 'Supervisor,Encargado', 'sera Supervisor', 'ACTIVO', '2025-05-22 14:21:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `id` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `permiso` varchar(100) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`id`, `id_rol`, `permiso`, `estado`, `created_at`) VALUES
(33, 4, 'manage_impuestos', 'ACTIVO', '2025-03-27 17:17:15'),
(34, 4, 'manage_cuentas_contables', 'ACTIVO', '2025-03-27 17:17:15'),
(35, 4, 'manage_tipos_gastos', 'ACTIVO', '2025-03-27 17:17:15'),
(36, 4, 'revisar_liquidaciones', 'ACTIVO', '2025-03-27 17:17:15'),
(37, 4, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-03-27 17:17:15'),
(38, 4, 'manage_reportes', 'ACTIVO', '2025-03-27 17:17:15'),
(39, 4, 'manage_facturas', 'ACTIVO', '2025-03-27 17:17:15'),
(40, 4, 'revisar_facturas', 'ACTIVO', '2025-03-27 17:17:15'),
(41, 4, 'manage_centros_costos', 'ACTIVO', '2025-03-27 17:17:15'),
(42, 4, 'manage_auditoria', 'ACTIVO', '2025-03-27 17:17:15'),
(318, 2, 'create_liquidaciones', 'ACTIVO', '2025-03-27 21:39:30'),
(319, 2, 'create_detalles', 'ACTIVO', '2025-03-27 21:39:30'),
(320, 2, 'manage_cajachica', 'ACTIVO', '2025-03-27 21:39:30'),
(321, 2, 'manage_impuestos', 'INACTIVO', '2025-03-27 21:39:30'),
(322, 2, 'manage_cuentas_contables', 'INACTIVO', '2025-03-27 21:39:30'),
(323, 2, 'manage_tipos_gastos', 'INACTIVO', '2025-03-27 21:39:30'),
(324, 2, 'manage_roles', 'INACTIVO', '2025-03-27 21:39:30'),
(325, 2, 'manage_usuarios', 'INACTIVO', '2025-03-27 21:39:30'),
(326, 2, 'autorizar_liquidaciones', 'ACTIVO', '2025-03-27 21:39:30'),
(327, 2, 'revisar_liquidaciones', 'INACTIVO', '2025-03-27 21:39:30'),
(328, 2, 'revisar_detalles_liquidaciones', 'INACTIVO', '2025-03-27 21:39:30'),
(329, 2, 'manage_reportes', 'INACTIVO', '2025-03-27 21:39:30'),
(330, 2, 'manage_auditoria', 'INACTIVO', '2025-03-27 21:39:30'),
(331, 2, 'manage_accesos', 'INACTIVO', '2025-03-27 21:39:30'),
(332, 2, 'manage_facturas', 'ACTIVO', '2025-03-27 21:39:30'),
(333, 2, 'autorizar_facturas', 'INACTIVO', '2025-03-27 21:39:30'),
(334, 2, 'revisar_facturas', 'INACTIVO', '2025-03-27 21:39:30'),
(335, 2, 'manage_centros_costos', 'INACTIVO', '2025-03-27 21:39:30'),
(1132, 1, 'create_liquidaciones', 'ACTIVO', '2025-04-01 18:50:21'),
(1133, 1, 'create_detalles', 'ACTIVO', '2025-04-01 18:50:21'),
(1134, 1, 'manage_cajachica', 'ACTIVO', '2025-04-01 18:50:21'),
(1135, 1, 'manage_impuestos', 'ACTIVO', '2025-04-01 18:50:21'),
(1136, 1, 'manage_cuentas_contables', 'ACTIVO', '2025-04-01 18:50:21'),
(1137, 1, 'manage_tipos_gastos', 'ACTIVO', '2025-04-01 18:50:21'),
(1138, 1, 'manage_roles', 'ACTIVO', '2025-04-01 18:50:21'),
(1139, 1, 'manage_usuarios', 'ACTIVO', '2025-04-01 18:50:21'),
(1140, 1, 'autorizar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:21'),
(1141, 1, 'revisar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:21'),
(1142, 1, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-04-01 18:50:21'),
(1143, 1, 'manage_reportes', 'ACTIVO', '2025-04-01 18:50:21'),
(1144, 1, 'manage_auditoria', 'ACTIVO', '2025-04-01 18:50:21'),
(1145, 1, 'manage_accesos', 'ACTIVO', '2025-04-01 18:50:21'),
(1146, 1, 'manage_facturas', 'ACTIVO', '2025-04-01 18:50:21'),
(1147, 1, 'autorizar_facturas', 'ACTIVO', '2025-04-01 18:50:21'),
(1148, 1, 'revisar_facturas', 'ACTIVO', '2025-04-01 18:50:21'),
(1149, 1, 'manage_centros_costos', 'ACTIVO', '2025-04-01 18:50:21'),
(1150, 3, 'create_liquidaciones', 'ACTIVO', '2025-04-01 18:50:35'),
(1151, 3, 'create_detalles', 'INACTIVO', '2025-04-01 18:50:35'),
(1152, 3, 'manage_cajachica', 'INACTIVO', '2025-04-01 18:50:35'),
(1153, 3, 'manage_impuestos', 'INACTIVO', '2025-04-01 18:50:35'),
(1154, 3, 'manage_cuentas_contables', 'ACTIVO', '2025-04-01 18:50:35'),
(1155, 3, 'manage_tipos_gastos', 'INACTIVO', '2025-04-01 18:50:35'),
(1156, 3, 'manage_roles', 'INACTIVO', '2025-04-01 18:50:35'),
(1157, 3, 'manage_usuarios', 'INACTIVO', '2025-04-01 18:50:35'),
(1158, 3, 'autorizar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:35'),
(1159, 3, 'revisar_liquidaciones', 'ACTIVO', '2025-04-01 18:50:35'),
(1160, 3, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-04-01 18:50:35'),
(1161, 3, 'manage_reportes', 'INACTIVO', '2025-04-01 18:50:35'),
(1162, 3, 'manage_auditoria', 'INACTIVO', '2025-04-01 18:50:35'),
(1163, 3, 'manage_accesos', 'INACTIVO', '2025-04-01 18:50:35'),
(1164, 3, 'manage_facturas', 'ACTIVO', '2025-04-01 18:50:35'),
(1165, 3, 'autorizar_facturas', 'ACTIVO', '2025-04-01 18:50:35'),
(1166, 3, 'revisar_facturas', 'ACTIVO', '2025-04-01 18:50:35'),
(1167, 3, 'manage_centros_costos', 'ACTIVO', '2025-04-01 18:50:35'),
(1168, 4, 'create_liquidaciones', 'ACTIVO', '2025-04-01 18:50:56'),
(1169, 4, 'create_detalles', 'INACTIVO', '2025-04-01 18:50:56'),
(1170, 4, 'manage_cajachica', 'INACTIVO', '2025-04-01 18:50:56'),
(1174, 4, 'manage_roles', 'INACTIVO', '2025-04-01 18:50:56'),
(1175, 4, 'manage_usuarios', 'INACTIVO', '2025-04-01 18:50:56'),
(1176, 4, 'autorizar_liquidaciones', 'INACTIVO', '2025-04-01 18:50:56'),
(1181, 4, 'manage_accesos', 'ACTIVO', '2025-04-01 18:50:56'),
(1183, 4, 'autorizar_facturas', 'INACTIVO', '2025-04-01 18:50:56'),
(1389, 17, 'create_liquidaciones', 'INACTIVO', '2025-04-02 14:35:59'),
(1390, 17, 'create_detalles', 'INACTIVO', '2025-04-02 14:35:59'),
(1391, 17, 'manage_cajachica', 'INACTIVO', '2025-04-02 14:35:59'),
(1392, 17, 'manage_impuestos', 'ACTIVO', '2025-04-02 14:35:59'),
(1393, 17, 'manage_cuentas_contables', 'ACTIVO', '2025-04-02 14:35:59'),
(1394, 17, 'manage_tipos_gastos', 'ACTIVO', '2025-04-02 14:35:59'),
(1395, 17, 'manage_roles', 'ACTIVO', '2025-04-02 14:35:59'),
(1396, 17, 'manage_usuarios', 'ACTIVO', '2025-04-02 14:35:59'),
(1397, 17, 'autorizar_liquidaciones', 'INACTIVO', '2025-04-02 14:35:59'),
(1398, 17, 'revisar_liquidaciones', 'ACTIVO', '2025-04-02 14:35:59'),
(1399, 17, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-04-02 14:35:59'),
(1400, 17, 'manage_reportes', 'ACTIVO', '2025-04-02 14:35:59'),
(1401, 17, 'manage_auditoria', 'ACTIVO', '2025-04-02 14:35:59'),
(1402, 17, 'manage_accesos', 'INACTIVO', '2025-04-02 14:35:59'),
(1403, 17, 'manage_facturas', 'ACTIVO', '2025-04-02 14:35:59'),
(1404, 17, 'autorizar_facturas', 'INACTIVO', '2025-04-02 14:35:59'),
(1405, 17, 'revisar_facturas', 'ACTIVO', '2025-04-02 14:35:59'),
(1406, 17, 'manage_centros_costos', 'ACTIVO', '2025-04-02 14:35:59'),
(1619, 18, 'create_liquidaciones', 'ACTIVO', '2025-04-04 20:15:40'),
(1620, 18, 'create_detalles', 'ACTIVO', '2025-04-04 20:15:40'),
(1621, 18, 'manage_cajachica', 'ACTIVO', '2025-04-04 20:15:40'),
(1622, 18, 'manage_impuestos', 'ACTIVO', '2025-04-04 20:15:40'),
(1623, 18, 'manage_cuentas_contables', 'ACTIVO', '2025-04-04 20:15:40'),
(1624, 18, 'manage_tipos_gastos', 'ACTIVO', '2025-04-04 20:15:40'),
(1625, 18, 'manage_roles', 'ACTIVO', '2025-04-04 20:15:40'),
(1626, 18, 'manage_usuarios', 'ACTIVO', '2025-04-04 20:15:40'),
(1627, 18, 'autorizar_liquidaciones', 'ACTIVO', '2025-04-04 20:15:40'),
(1628, 18, 'revisar_liquidaciones', 'ACTIVO', '2025-04-04 20:15:40'),
(1629, 18, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-04-04 20:15:40'),
(1630, 18, 'manage_reportes', 'ACTIVO', '2025-04-04 20:15:40'),
(1631, 18, 'manage_auditoria', 'ACTIVO', '2025-04-04 20:15:40'),
(1632, 18, 'manage_accesos', 'ACTIVO', '2025-04-04 20:15:40'),
(1633, 18, 'manage_facturas', 'ACTIVO', '2025-04-04 20:15:40'),
(1634, 18, 'autorizar_facturas', 'ACTIVO', '2025-04-04 20:15:40'),
(1635, 18, 'revisar_facturas', 'ACTIVO', '2025-04-04 20:15:40'),
(1636, 18, 'manage_centros_costos', 'ACTIVO', '2025-04-04 20:15:40'),
(1658, 18, 'listar_bases', 'INACTIVO', '2025-04-04 20:21:05'),
(1895, 1, 'manage_correcciones', 'ACTIVO', '2025-04-23 13:43:49'),
(1897, 2, 'manage_correcciones', 'ACTIVO', '2025-04-30 18:00:44'),
(1898, 4, 'manage_correcciones', 'ACTIVO', '2025-04-30 18:00:57'),
(1899, 17, 'manage_correcciones', 'ACTIVO', '2025-04-30 18:01:05'),
(1900, 19, 'create_liquidaciones', 'ACTIVO', '2025-05-05 15:10:10'),
(1901, 19, 'create_detalles', 'ACTIVO', '2025-05-05 15:10:10'),
(1902, 19, 'manage_cajachica', 'ACTIVO', '2025-05-05 15:10:10'),
(1903, 19, 'manage_impuestos', 'ACTIVO', '2025-05-05 15:10:10'),
(1904, 19, 'manage_cuentas_contables', 'ACTIVO', '2025-05-05 15:10:10'),
(1905, 19, 'manage_tipos_gastos', 'ACTIVO', '2025-05-05 15:10:10'),
(1906, 19, 'manage_roles', 'ACTIVO', '2025-05-05 15:10:10'),
(1907, 19, 'manage_usuarios', 'ACTIVO', '2025-05-05 15:10:10'),
(1908, 19, 'autorizar_liquidaciones', 'ACTIVO', '2025-05-05 15:10:10'),
(1909, 19, 'revisar_liquidaciones', 'ACTIVO', '2025-05-05 15:10:10'),
(1910, 19, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-05-05 15:10:10'),
(1911, 19, 'manage_reportes', 'ACTIVO', '2025-05-05 15:10:10'),
(1912, 19, 'manage_auditoria', 'ACTIVO', '2025-05-05 15:10:10'),
(1913, 19, 'manage_accesos', 'ACTIVO', '2025-05-05 15:10:10'),
(1914, 19, 'manage_facturas', 'ACTIVO', '2025-05-05 15:10:10'),
(1915, 19, 'autorizar_facturas', 'ACTIVO', '2025-05-05 15:10:10'),
(1916, 19, 'revisar_facturas', 'ACTIVO', '2025-05-05 15:10:10'),
(1917, 19, 'manage_centros_costos', 'ACTIVO', '2025-05-05 15:10:10'),
(1936, 19, 'listar_bases', 'INACTIVO', '2025-05-05 15:11:58'),
(2004, 20, 'create_liquidaciones', 'INACTIVO', '2025-05-22 14:25:01'),
(2005, 20, 'create_detalles', 'INACTIVO', '2025-05-22 14:25:01'),
(2006, 20, 'manage_cajachica', 'INACTIVO', '2025-05-22 14:25:01'),
(2007, 20, 'manage_impuestos', 'INACTIVO', '2025-05-22 14:25:01'),
(2008, 20, 'manage_cuentas_contables', 'ACTIVO', '2025-05-22 14:25:01'),
(2009, 20, 'manage_tipos_gastos', 'INACTIVO', '2025-05-22 14:25:01'),
(2010, 20, 'manage_roles', 'INACTIVO', '2025-05-22 14:25:01'),
(2011, 20, 'manage_usuarios', 'INACTIVO', '2025-05-22 14:25:01'),
(2012, 20, 'autorizar_liquidaciones', 'ACTIVO', '2025-05-22 14:25:01'),
(2013, 20, 'revisar_liquidaciones', 'ACTIVO', '2025-05-22 14:25:01'),
(2014, 20, 'revisar_detalles_liquidaciones', 'ACTIVO', '2025-05-22 14:25:01'),
(2015, 20, 'manage_reportes', 'INACTIVO', '2025-05-22 14:25:01'),
(2016, 20, 'manage_auditoria', 'INACTIVO', '2025-05-22 14:25:01'),
(2017, 20, 'manage_accesos', 'INACTIVO', '2025-05-22 14:25:01'),
(2018, 20, 'manage_facturas', 'ACTIVO', '2025-05-22 14:25:01'),
(2019, 20, 'autorizar_facturas', 'ACTIVO', '2025-05-22 14:25:01'),
(2020, 20, 'revisar_facturas', 'ACTIVO', '2025-05-22 14:25:01'),
(2021, 20, 'manage_centros_costos', 'INACTIVO', '2025-05-22 14:25:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_documentos`
--

CREATE TABLE `tipos_documentos` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_documentos`
--

INSERT INTO `tipos_documentos` (`id`, `name`, `description`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'FACTURA', 'Documento de tipo factura', 'ACTIVO', '2025-04-03 18:33:36', '2025-04-03 18:33:36'),
(2, 'RECIBO', 'Documento de tipo recibo', 'ACTIVO', '2025-04-03 18:33:36', '2025-04-03 18:33:36'),
(3, 'COMPROBANTE', 'Documento de tipo comprobante', 'ACTIVO', '2025-04-03 18:33:36', '2025-04-03 18:33:36');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cuenta_contable_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `tipos_gastos`
--

INSERT INTO `tipos_gastos` (`id`, `name`, `description`, `estado`, `created_at`, `updated_at`, `cuenta_contable_id`) VALUES
(1, 'Gasto Operativo', 'gasolina', 'ACTIVO', '2025-03-21 20:18:54', '2025-03-23 18:21:45', NULL),
(2, 'Combustible', 'Combustible', 'ACTIVO', '2025-04-03 15:57:57', '2025-04-03 15:57:57', NULL),
(3, 'Hospedaje', 'Hospedaje', 'ACTIVO', '2025-04-03 15:58:29', '2025-04-03 15:58:29', NULL),
(4, 'Alimentos', 'Alimentos', 'ACTIVO', '2025-04-03 15:58:40', '2025-04-03 15:58:40', NULL),
(5, 'otros...', 'otros...', 'ACTIVO', '2025-04-03 15:58:57', '2025-04-03 15:58:57', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_gasto_impuestos`
--

CREATE TABLE `tipo_gasto_impuestos` (
  `id` int(11) NOT NULL,
  `tipo_gasto_id` int(11) NOT NULL,
  `impuesto_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_gasto_impuestos`
--

INSERT INTO `tipo_gasto_impuestos` (`id`, `tipo_gasto_id`, `impuesto_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-04-15 18:21:48', '2025-04-15 18:21:48'),
(2, 1, 2, '2025-04-15 18:21:48', '2025-04-15 18:21:48'),
(3, 1, 3, '2025-04-15 18:21:48', '2025-04-15 18:21:48');

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
(8, 'Angel De León', 'angel.deleon@agrocentro.com', '$2y$10$TCaN0HvXcz8Ex/oO3AdAH.47INMNGINBH5xoWBKzVGaeCqajXuB1e', 18, '2025-03-11 14:20:20', '2025-05-05 15:00:45'),
(10, 'Omar ', 'omar@gmail.com', '$2y$10$RRI9rARJHg2bKODIK0WOMOYKqzxdSpngia8Ny7lphCPLWk1G8dC/e', 4, '2025-03-20 20:45:37', '2025-04-02 13:16:44'),
(14, 'Miguel', 'miguel@gmail.com', '$2y$10$MheThlhiC3PBD01vDzYH1egIa7qjusfNXMVsi5nkgLeKUihvrHTR6', 18, '2025-04-02 14:34:09', '2025-04-02 15:16:30'),
(15, 'Pepe', 'pepe@gmail.com', '$2y$10$N1S2vLvPqDk6/lXmgcoXoeGlWWx6XUbZZN5TWSs8QsG74Ok1w8ypG', 4, '2025-04-02 14:35:36', '2025-04-07 21:09:28'),
(16, 'Encargado 2', 'encargado2@example.com', '$2y$10$KqqbMsE9fIjDxMUH2BaC1.Zx5AF2Vef/sdEJtTnmOBDyhY/hUO10i', 2, '2025-04-22 15:31:58', '2025-04-22 15:31:58'),
(17, 'Pedro', 'pedro@gmail.com', '$2y$10$jRm9Hm1CseCeNDthjeMDH.yMGUBA3Cf5JseqHjOoEVJXPf6IDAJ/m', 19, '2025-05-05 15:10:38', '2025-05-05 15:10:38'),
(18, 'supervisormix', 'supervisormix@gmail.com', '$2y$10$ZCNKRCSGyEkiaajz38y/NeARuKBJauBrk7guzbD1m8Pl./sZytFMG', 3, '2025-05-22 14:22:18', '2025-05-22 14:26:20');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_cuenta_base` (`base_id`);

--
-- Indices de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_cuenta_2` (`codigo_cuenta`),
  ADD UNIQUE KEY `uk_codigo_cuenta_centro` (`codigo_cuenta`,`id_centro_costo`),
  ADD KEY `fk_cuenta_centro_costo` (`id_centro_costo`);

--
-- Indices de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detalle_liquidacion_id` (`id_liquidacion`),
  ADD KEY `fk_detalle_centro_costo` (`id_centro_costo`),
  ADD KEY `fk_detalle_cuenta_contable` (`id_cuenta_contable`),
  ADD KEY `fk_detalle_supervisor_correccion` (`id_supervisor_correccion`),
  ADD KEY `fk_detalle_contador_correccion` (`id_contador_correccion`);

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
  ADD KEY `id_caja_chica` (`id_caja_chica`),
  ADD KEY `fk_liquidaciones_supervisor` (`id_supervisor`),
  ADD KEY `fk_liquidaciones_contador` (`id_contador`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rol_permiso` (`id_rol`,`permiso`);

--
-- Indices de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `fk_tipos_gastos_cuenta_contable` (`cuenta_contable_id`);

--
-- Indices de la tabla `tipo_gasto_impuestos`
--
ALTER TABLE `tipo_gasto_impuestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_gasto_id` (`tipo_gasto_id`,`impuesto_id`),
  ADD KEY `impuesto_id` (`impuesto_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4292;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;

--
-- AUTO_INCREMENT de la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `liquidaciones`
--
ALTER TABLE `liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2022;

--
-- AUTO_INCREMENT de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipo_gasto_impuestos`
--
ALTER TABLE `tipo_gasto_impuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  ADD CONSTRAINT `accesos_permisos_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_accesos_permisos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_auditoria_liquidacion` FOREIGN KEY (`id_liquidacion`) REFERENCES `liquidaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cajas_chicas`
--
ALTER TABLE `cajas_chicas`
  ADD CONSTRAINT `cajas_chicas_ibfk_1` FOREIGN KEY (`id_usuario_encargado`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `cajas_chicas_ibfk_2` FOREIGN KEY (`id_supervisor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_centro_costo` FOREIGN KEY (`id_centro_costo`) REFERENCES `cuentas_contables` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_supervisor` FOREIGN KEY (`id_supervisor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cajas_chicas_usuario_encargado` FOREIGN KEY (`id_usuario_encargado`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `centros_costos`
--
ALTER TABLE `centros_costos`
  ADD CONSTRAINT `fk_cuenta_base` FOREIGN KEY (`base_id`) REFERENCES `bases` (`id`);

--
-- Filtros para la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD CONSTRAINT `fk_cuenta_centro_costo` FOREIGN KEY (`id_centro_costo`) REFERENCES `centros_costos` (`id`);

--
-- Filtros para la tabla `detalle_liquidaciones`
--
ALTER TABLE `detalle_liquidaciones`
  ADD CONSTRAINT `fk_detalle_centro_costo` FOREIGN KEY (`id_centro_costo`) REFERENCES `centros_costos` (`id`),
  ADD CONSTRAINT `fk_detalle_cuenta_contable` FOREIGN KEY (`id_cuenta_contable`) REFERENCES `cuentas_contables` (`id`),
  ADD CONSTRAINT `fk_detalle_liquidacion_id` FOREIGN KEY (`id_liquidacion`) REFERENCES `liquidaciones` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detalle_supervisor_correccion` FOREIGN KEY (`id_supervisor_correccion`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `fk_liquidaciones_supervisor` FOREIGN KEY (`id_supervisor`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `liquidaciones_ibfk_1` FOREIGN KEY (`id_caja_chica`) REFERENCES `cajas_chicas` (`id`);

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tipos_gastos`
--
ALTER TABLE `tipos_gastos`
  ADD CONSTRAINT `fk_tipos_gastos_cuenta_contable` FOREIGN KEY (`cuenta_contable_id`) REFERENCES `cuentas_contables` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tipo_gasto_impuestos`
--
ALTER TABLE `tipo_gasto_impuestos`
  ADD CONSTRAINT `tipo_gasto_impuestos_ibfk_1` FOREIGN KEY (`tipo_gasto_id`) REFERENCES `tipos_gastos` (`id`),
  ADD CONSTRAINT `tipo_gasto_impuestos_ibfk_2` FOREIGN KEY (`impuesto_id`) REFERENCES `impuestos` (`id`);

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
