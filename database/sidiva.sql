/*
 Navicat Premium Data Transfer

 Source Server         : 103-MYSQL
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : sidiva

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 25/08/2023 13:54:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for layanan_hc
-- ----------------------------
DROP TABLE IF EXISTS `layanan_hc`;
CREATE TABLE `layanan_hc`  (
  `id_layanan_hc` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_layanan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_layanan_hc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of layanan_hc
-- ----------------------------
INSERT INTO `layanan_hc` VALUES (1, 'Persalinan Bayi', NULL, NULL);
INSERT INTO `layanan_hc` VALUES (2, 'Perawatan kecantikan', NULL, NULL);
INSERT INTO `layanan_hc` VALUES (3, 'Perwatan Ibu Dan Bayi', NULL, NULL);

-- ----------------------------
-- Table structure for layanan_mcu
-- ----------------------------
DROP TABLE IF EXISTS `layanan_mcu`;
CREATE TABLE `layanan_mcu`  (
  `id_layanan` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `jenis_layanan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_layanan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_layanan`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of layanan_mcu
-- ----------------------------
INSERT INTO `layanan_mcu` VALUES (1, 'paket', 'Paket Lavender', 'Deskripsi Paket Lavender', 120000, '2023-05-09 07:35:10', '2023-05-09 07:35:10');
INSERT INTO `layanan_mcu` VALUES (2, 'aps', 'Pemeriksaan', 'Deskripsi Pemeriksaan', 100000, '2023-05-09 07:37:32', '2023-05-09 07:37:32');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 53 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (7, '2023_04_28_100215_create_detail_permintaan_mcu_table', 3);
INSERT INTO `migrations` VALUES (35, '2014_10_12_000000_create_users_table', 4);
INSERT INTO `migrations` VALUES (36, '2014_10_12_100000_create_password_resets_table', 4);
INSERT INTO `migrations` VALUES (37, '2019_08_19_000000_create_failed_jobs_table', 4);
INSERT INTO `migrations` VALUES (38, '2019_12_14_000001_create_personal_access_tokens_table', 4);
INSERT INTO `migrations` VALUES (39, '2023_04_28_020755_create_layanan_mcu_table', 4);
INSERT INTO `migrations` VALUES (40, '2023_04_28_094929_create_permintaan_mcu_table', 4);
INSERT INTO `migrations` VALUES (41, '2023_04_29_153641_create_paket_hc_table', 4);
INSERT INTO `migrations` VALUES (42, '2023_05_02_065300_create_layanan_hc_table', 4);
INSERT INTO `migrations` VALUES (43, '2023_05_02_074841_create_tenaga_medis_table', 4);
INSERT INTO `migrations` VALUES (44, '2023_05_05_025729_create_permintaan_hc_table', 4);
INSERT INTO `migrations` VALUES (45, '2023_05_08_033258_create_pengaturan_hc_table', 4);
INSERT INTO `migrations` VALUES (46, '2023_05_08_050109_create_pengaturan_mcu_table', 4);
INSERT INTO `migrations` VALUES (47, '2023_05_09_025834_create_syarat_mcu_table', 4);
INSERT INTO `migrations` VALUES (48, '2023_05_11_060804_create_syarat_hc_table', 5);
INSERT INTO `migrations` VALUES (49, '2023_05_23_095401_create_rating_mcu_table', 6);
INSERT INTO `migrations` VALUES (50, '2023_05_23_095454_create_rating_hc_table', 6);
INSERT INTO `migrations` VALUES (51, '2023_05_24_084917_create_transaksi_hc_table', 7);
INSERT INTO `migrations` VALUES (52, '2023_05_24_085348_create_transaksi_mcu_table', 7);

-- ----------------------------
-- Table structure for paket_hc
-- ----------------------------
DROP TABLE IF EXISTS `paket_hc`;
CREATE TABLE `paket_hc`  (
  `id_paket_hc` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `jenis_layanan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_paket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` int NOT NULL,
  `jumlah_hari` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_paket_hc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of paket_hc
-- ----------------------------
INSERT INTO `paket_hc` VALUES (1, 'Paket', 'LILY', 160000, NULL, 'Deskripsi Paket Lily', '2023-05-09 07:43:15', '2023-05-09 07:43:15');
INSERT INTO `paket_hc` VALUES (2, 'Paket', 'DAHLIA', 400000, NULL, 'Deskripsi Paket Dahlia', '2023-05-09 07:43:40', '2023-05-09 07:43:40');
INSERT INTO `paket_hc` VALUES (4, 'Aps', 'GULA DARAH', 550000, '5', 'deskripsi gula darah', '2023-06-05 03:47:21', '2023-06-05 03:48:44');

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  INDEX `password_resets_email_index`(`email` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for pengaturan_hc
-- ----------------------------
DROP TABLE IF EXISTS `pengaturan_hc`;
CREATE TABLE `pengaturan_hc`  (
  `id_pengaturan_hc` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seninBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `seninTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `selasaBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `selasaTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `rabuBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `rabuTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kamisBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kamisTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jumatBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jumatTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `sabtuBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `sabtuTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mingguBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mingguTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `liburNasionalBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `liburNasionalTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `biaya_per_km` int NULL DEFAULT NULL,
  `jarak_maksimal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `batas_waktu` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pengaturan_hc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of pengaturan_hc
-- ----------------------------
INSERT INTO `pengaturan_hc` VALUES (1, '08:00', '12:00', '08:00', '12:00', '08:00', '12:00', '08:00', '12:00', '09:00', '11:00', NULL, NULL, NULL, NULL, NULL, NULL, 10000, '100', '05', '2023-05-15 09:43:47', '2023-05-15 09:43:47');

-- ----------------------------
-- Table structure for pengaturan_mcu
-- ----------------------------
DROP TABLE IF EXISTS `pengaturan_mcu`;
CREATE TABLE `pengaturan_mcu`  (
  `id_pengaturan_mcu` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seninBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `seninTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `selasaBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `selasaTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `rabuBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `rabuTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kamisBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kamisTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jumatBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jumatTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `sabtuBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `sabtuTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mingguBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `mingguTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `liburNasionalBuka` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `liburNasionalTutup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pengaturan_mcu`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of pengaturan_mcu
-- ----------------------------
INSERT INTO `pengaturan_mcu` VALUES (1, '08:00', '12:00', NULL, NULL, '08:00', '12:00', NULL, NULL, '08:00', '12:00', '09:00', '11:00', NULL, NULL, NULL, NULL, '2023-05-10 07:58:28', '2023-05-10 07:58:28');

-- ----------------------------
-- Table structure for permintaan_hc
-- ----------------------------
DROP TABLE IF EXISTS `permintaan_hc`;
CREATE TABLE `permintaan_hc`  (
  `id_permintaan_hc` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `layanan_hc_id` int NOT NULL,
  `paket_hc_id` int NOT NULL,
  `no_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_registrasi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_bpjs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_rujukan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `tanggal_order` date NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `tanggal_lahir` date NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `alergi_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `latitude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `biaya_layanan` int NULL DEFAULT NULL,
  `biaya_ke_lokasi` int NULL DEFAULT NULL,
  `status_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'belum, proses, batal, selesai',
  `status_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `metode_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `tenaga_medis_id` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_permintaan_hc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of permintaan_hc
-- ----------------------------
INSERT INTO `permintaan_hc` VALUES (1, 2, 2, NULL, NULL, '3527081020102101', 'CHICHA PUTRI AMALIA', NULL, NULL, '2023-05-09', '2023-05-09', '2001-12-10', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'selesai', NULL, NULL, 1, '2023-05-09 14:41:41', '2023-05-09 07:45:34');
INSERT INTO `permintaan_hc` VALUES (2, 2, 2, NULL, NULL, '3527081020102101', 'CHICHA PUTRI AMALIA', NULL, NULL, '2023-05-10', '2023-05-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 09:15:04', '2023-05-10 09:15:04');
INSERT INTO `permintaan_hc` VALUES (3, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-10', '2023-05-10', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', '-', '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'selesai', NULL, NULL, 1, '2023-05-10 09:21:14', '2023-05-10 09:53:22');
INSERT INTO `permintaan_hc` VALUES (4, 2, 2, NULL, NULL, '3527081010201001', 'ANA UHIBBUKI', NULL, NULL, '2023-05-10', '2023-05-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 15:06:55', '2023-05-10 15:06:55');
INSERT INTO `permintaan_hc` VALUES (5, 2, 2, NULL, NULL, '3527081010201002', 'WISNU HIDAYAT', NULL, NULL, '2023-05-10', '2023-05-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 15:07:45', '2023-05-10 15:07:45');
INSERT INTO `permintaan_hc` VALUES (6, 2, 2, NULL, NULL, '3527081010201003', 'ARIEF HIDAYAT', NULL, NULL, '2023-05-10', '2023-05-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 15:08:21', '2023-05-10 15:08:21');
INSERT INTO `permintaan_hc` VALUES (7, 2, 2, NULL, NULL, '3527081010201004', 'FAHRIS AFFANDI', NULL, NULL, '2023-05-10', '2023-05-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 15:08:33', '2023-05-10 15:08:33');
INSERT INTO `permintaan_hc` VALUES (8, 2, 2, NULL, NULL, '3527081010201005', 'SYAIFULLOH', NULL, NULL, '2023-05-10', '2023-05-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 15:08:49', '2023-05-10 15:08:49');
INSERT INTO `permintaan_hc` VALUES (9, 2, 2, NULL, NULL, '3527081010201006', 'KOMARUL BADRIYAH', NULL, NULL, '2023-04-10', '2023-04-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 15:10:41', '2023-05-10 15:10:41');
INSERT INTO `permintaan_hc` VALUES (10, 2, 2, NULL, NULL, '3527081010201007', 'ANDI IBRAHIM', NULL, NULL, '2023-04-10', '2023-04-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 15:11:12', '2023-05-10 15:11:12');
INSERT INTO `permintaan_hc` VALUES (11, 2, 2, NULL, NULL, '3527081010201008', 'ALI AFFAN', NULL, NULL, '2023-04-10', '2023-04-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'selesai', NULL, NULL, NULL, '2023-05-10 15:11:21', '2023-05-10 15:11:21');
INSERT INTO `permintaan_hc` VALUES (12, 2, 2, NULL, NULL, '3527081010201009', 'ANITA ISNAINI', NULL, NULL, '2023-04-10', '2023-04-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'selesai', NULL, NULL, NULL, '2023-05-10 15:11:37', '2023-05-10 15:11:37');
INSERT INTO `permintaan_hc` VALUES (13, 2, 2, NULL, NULL, '3527081010201010', 'BUDI DIRGANTARA', NULL, NULL, '2023-04-10', '2023-04-10', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-10 15:11:52', '2023-05-10 15:11:52');
INSERT INTO `permintaan_hc` VALUES (14, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-11', '2023-05-11', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', '-', '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-11 15:20:55', '2023-05-11 15:20:55');
INSERT INTO `permintaan_hc` VALUES (15, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-12', '2023-05-12', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', '-', '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-12 09:07:57', '2023-05-12 09:07:57');
INSERT INTO `permintaan_hc` VALUES (16, 2, 2, NULL, NULL, '3527081010201010', 'BUDI DIRGANTARA', NULL, NULL, '2023-05-12', '2023-05-12', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', '-', '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-12 09:08:28', '2023-05-12 09:08:28');
INSERT INTO `permintaan_hc` VALUES (17, 2, 2, NULL, NULL, '3527081010266666', 'SISKA AMALIA', NULL, NULL, '2023-05-12', '2023-05-12', '2001-12-11', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-12 09:25:17', '2023-05-12 09:25:17');
INSERT INTO `permintaan_hc` VALUES (18, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-15', '2023-05-15', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', NULL, '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-15 15:25:42', '2023-05-15 15:25:42');
INSERT INTO `permintaan_hc` VALUES (19, 2, 2, NULL, NULL, '3527081010266666', 'SISKA AMALIA', NULL, NULL, '2023-05-15', '2023-05-15', '2001-12-15', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'proses', NULL, NULL, 3, '2023-05-15 15:26:13', '2023-05-15 09:06:46');
INSERT INTO `permintaan_hc` VALUES (20, 2, 2, NULL, NULL, '3527081010266111', 'AJENG NATALIA', NULL, NULL, '2023-05-15', '2023-05-15', '2001-12-15', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'proses', NULL, NULL, 3, '2023-05-15 15:27:05', '2023-05-15 09:06:59');
INSERT INTO `permintaan_hc` VALUES (21, 2, 2, NULL, NULL, '3527081010266222', 'LUTFI HASYIM', NULL, NULL, '2023-05-15', '2023-05-15', '2001-12-15', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-15 15:27:18', '2023-05-15 15:27:18');
INSERT INTO `permintaan_hc` VALUES (22, 2, 2, NULL, NULL, '3527081010266333', 'LAILATUL KARIMAH', NULL, NULL, '2023-05-15', '2023-05-15', '2001-12-15', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-15 15:27:37', '2023-05-15 15:27:37');
INSERT INTO `permintaan_hc` VALUES (23, 2, 2, NULL, NULL, '3527081010266444', 'YULI IRMAS MILLENIA', NULL, NULL, '2023-05-15', '2023-05-15', '2001-12-15', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-15 15:27:59', '2023-05-15 15:27:59');
INSERT INTO `permintaan_hc` VALUES (24, 2, 2, NULL, NULL, '3527081010266555', 'JAMILATUL AINI', NULL, NULL, '2023-05-15', '2023-05-15', '2001-12-15', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-15 15:28:11', '2023-05-15 15:28:11');
INSERT INTO `permintaan_hc` VALUES (25, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-19', '2023-05-19', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', NULL, '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'selesai', NULL, NULL, 2, '2023-05-19 11:30:32', '2023-05-19 13:30:25');
INSERT INTO `permintaan_hc` VALUES (26, 2, 2, NULL, NULL, '3527081010266555', 'JAMILATUL AINI', NULL, NULL, '2023-05-19', '2023-05-19', '2001-12-19', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-19 11:36:28', '2023-05-19 11:36:28');
INSERT INTO `permintaan_hc` VALUES (27, 2, 2, NULL, NULL, '3527081010266555', 'JAMILATUL AINI', NULL, NULL, '2023-05-22', '2023-05-22', '2001-12-19', 'Jl. Kebonagung, Kec. Puri', NULL, '-7.488674', '112.4593772', 'L', '081203344401', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-22 11:38:08', '2023-05-22 11:38:08');
INSERT INTO `permintaan_hc` VALUES (28, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-22', '2023-05-22', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', NULL, '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-22 11:39:29', '2023-05-22 11:39:29');
INSERT INTO `permintaan_hc` VALUES (29, 1, 1, 'W2305373407', NULL, '3527082010201001', 'RESITA MAYRA', NULL, NULL, '2023-05-24', '2023-05-24', '2000-06-01', 'Jl. Raya Mlirip, Jetis, Mojokerto', NULL, '-7.4488766', '112.4465693', 'P', '081345955888', 'UMUM', NULL, NULL, 'proses', NULL, NULL, 2, '2023-05-24 13:25:50', '2023-05-24 07:11:23');
INSERT INTO `permintaan_hc` VALUES (30, 1, 1, 'W2305373408', NULL, '3527082010201002', 'RETA ARVIANI', NULL, NULL, '2023-05-24', '2023-05-24', '2003-06-01', 'Jl. Raya Mlirip, Jetis, Mojokerto', NULL, '-7.4488766', '112.4465693', 'P', '081345955212', 'UMUM', NULL, NULL, 'proses', NULL, NULL, 2, '2023-05-24 13:29:58', '2023-05-24 07:11:49');
INSERT INTO `permintaan_hc` VALUES (31, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-24', '2023-05-24', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', NULL, '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'proses', NULL, NULL, 2, '2023-05-24 14:12:44', '2023-05-24 07:13:06');
INSERT INTO `permintaan_hc` VALUES (32, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-25', '2023-05-25', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', NULL, '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-25 10:15:18', '2023-05-25 10:15:18');
INSERT INTO `permintaan_hc` VALUES (33, 1, 1, 'W2304372023', NULL, '3516186302830001', 'WIWIK ERNAWATI', NULL, NULL, '2023-05-26', '2023-05-26', '1983-02-23', 'MEJERO 01/01 JUMENENG MOJOANYAR', NULL, '-7.488674', '112.4593772', 'P', '085730027402', 'UMUM', NULL, NULL, 'proses', NULL, NULL, 3, '2023-05-26 15:41:21', '2023-05-26 09:29:08');
INSERT INTO `permintaan_hc` VALUES (34, 1, 1, 'W2305373404', NULL, '3516136610200001', 'JENNAIRA LAVINA MEHRUNISA', NULL, NULL, '2023-05-26', '2023-05-26', '2020-10-26', 'KEDUNGPRING  RT 05/03 JAMPIROGO SOOKO', NULL, '-7.488674', '112.4593772', 'P', '085257409615', 'UMUM', NULL, NULL, 'proses', NULL, NULL, 3, '2023-05-26 15:42:08', '2023-05-26 09:47:01');
INSERT INTO `permintaan_hc` VALUES (35, 1, 1, 'W2305373403', NULL, '3516144303050001', 'NAZWA FAIYASA', NULL, NULL, '2023-05-26', '2023-05-26', '2005-03-03', 'TUMPAK 031/006 SIDOHARJO GEDEG', NULL, '-7.488674', '112.4593772', 'P', '085804473993', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-26 15:42:26', '2023-05-26 15:42:26');
INSERT INTO `permintaan_hc` VALUES (36, 1, 1, 'W2305373403', 'Reg-001', '3516144303050001', 'NAZWA FAIYASA', NULL, NULL, '2023-05-30', '2023-05-30', '2005-03-03', 'TUMPAK 031/006 SIDOHARJO GEDEG', NULL, '-7.488674', '112.4573387', 'P', '085804473993', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-30 10:52:38', '2023-05-30 10:52:38');
INSERT INTO `permintaan_hc` VALUES (37, 1, 1, 'W2305373410', 'Reg-001', '3527081900000001', 'MUHAMMAD SALAH', NULL, NULL, '2023-05-30', '2023-05-30', '1997-10-01', 'Jl. Raya Mlirip, Jetis, Mojokerto', NULL, '-7.488674', '112.4573387', 'L', '081230444566', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-30 10:54:42', '2023-05-30 10:54:42');
INSERT INTO `permintaan_hc` VALUES (38, 1, 1, 'W2305373409', 'Reg-001', '3526176121721911', 'RESITA MAYRA', NULL, NULL, '2023-05-30', '2023-05-30', '2000-01-06', 'Jl. Raya Mlirip, Jetis, Mojokerto', NULL, '-7.488674', '112.4573387', 'P', '081233445466', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-30 10:58:10', '2023-05-30 10:58:10');
INSERT INTO `permintaan_hc` VALUES (39, 1, 1, 'W2305373408', 'Reg-001', '3527082010201002', 'RETA ARVIANI', NULL, NULL, '2023-05-30', '2023-05-30', '2003-06-01', 'Jl. Raya Mlirip, Jetis, Mojokerto', NULL, '-7.488674', '112.4573387', 'P', '081345955212', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-30 11:02:12', '2023-05-30 11:02:12');
INSERT INTO `permintaan_hc` VALUES (40, 1, 1, 'W2305373407', 'Reg-001', '3527082010201001', 'RESITA MAYRA', NULL, NULL, '2023-05-30', '2023-05-30', '2000-06-01', 'Jl. Raya Mlirip, Jetis, Mojokerto', NULL, '-7.488674', '112.4573387', 'P', '081345955888', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-30 11:02:57', '2023-05-30 11:02:57');
INSERT INTO `permintaan_hc` VALUES (41, 1, 1, 'W2305373406', 'Reg-001', '3427081201000001', 'DWI ALIM SUSANTO', NULL, NULL, '2023-05-30', '2023-05-30', '2001-05-19', 'DUSUN JERUK RT 004 RW 001', NULL, '-7.488674', '112.4573387', 'L', '081230344555', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-30 11:11:18', '2023-05-30 11:11:18');
INSERT INTO `permintaan_hc` VALUES (42, 1, 1, 'W2305373405', 'Reg-001', '3516183112460006', 'M. ANWAR', NULL, NULL, '2023-05-30', '2023-05-30', '1946-12-31', 'KWATU RT 03 RW 01 MOJOANYAR', NULL, '-7.488674', '112.4573387', 'L', '085649527192', 'UMUM', NULL, NULL, 'belum', NULL, NULL, NULL, '2023-05-30 11:14:31', '2023-05-30 11:14:31');
INSERT INTO `permintaan_hc` VALUES (43, 1, 1, NULL, 'Reg-001', '3527081010000004', 'AGUNG PRATAMA', NULL, NULL, '2023-05-30', '2023-05-30', '2000-10-10', 'Village Ave. No. 89, Tambak Rejo, Gayaman, Mojoanyar', NULL, '-7.488674', '112.4573387', 'L', '081230585313', 'UMUM', 160000, 40000, 'belum', NULL, NULL, NULL, '2023-05-30 11:50:21', '2023-05-30 11:50:21');
INSERT INTO `permintaan_hc` VALUES (44, 1, 1, 'W2305372774', 'Reg-001', '3517136004620001', 'KARMINTEN', NULL, NULL, '2023-05-30', '2023-05-30', '1962-04-20', 'KEPUH GISIK 002/003 KEPUH DOKO TEMBELANG', NULL, '-7.488674', '112.4573387', 'P', '085785201973', 'UMUM', 160000, 40000, 'belum', NULL, NULL, NULL, '2023-05-30 11:52:31', '2023-05-30 11:52:31');

-- ----------------------------
-- Table structure for permintaan_mcu
-- ----------------------------
DROP TABLE IF EXISTS `permintaan_mcu`;
CREATE TABLE `permintaan_mcu`  (
  `id_permintaan` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_registrasi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kode_booking` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_antrian` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `layanan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_order` date NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `jenis_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_bpjs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_rujukan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jenis_kelamin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `tanggal_lahir` date NULL DEFAULT NULL,
  `telepon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `biaya` int NULL DEFAULT NULL,
  `status_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `metode_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'belum, selesai',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_permintaan`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 43 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of permintaan_mcu
-- ----------------------------
INSERT INTO `permintaan_mcu` VALUES (1, 'W2304371981', NULL, NULL, NULL, '3516126006450001', 'KASMINI', 'TEMBORO 002/005 DOMAS TROWULAN', '1', '2023-05-09', '2023-05-09', 'UMUM', NULL, NULL, 'P', '1945-06-20', '085731416004', NULL, 'sudah', NULL, 'selesai', '2023-05-09 14:33:15', '2023-05-09 07:35:55');
INSERT INTO `permintaan_mcu` VALUES (2, 'W2304371981', NULL, NULL, NULL, '3516126006450001', 'KASMINI', 'TEMBORO 002/005 DOMAS TROWULAN', '1', '2023-05-10', '2023-05-10', 'UMUM', NULL, NULL, 'P', '1945-06-20', '085731416004', NULL, NULL, NULL, 'batal', '2023-05-10 09:22:29', '2023-05-19 14:03:00');
INSERT INTO `permintaan_mcu` VALUES (3, NULL, NULL, NULL, NULL, '3527018000000001', 'NESTI ANDRIANI', 'jl. apa aja', '1', '2023-05-10', '2023-05-10', 'UMUM', NULL, NULL, 'P', '2004-05-12', '08123033334000', NULL, 'sudah', NULL, 'selesai', '2023-05-10 09:23:55', '2023-05-10 09:23:55');
INSERT INTO `permintaan_mcu` VALUES (4, 'W2304371981', NULL, NULL, NULL, '3516126006450001', 'KASMINI', 'TEMBORO 002/005 DOMAS TROWULAN', '1', '2023-05-11', '2023-05-11', 'UMUM', NULL, NULL, 'P', '1945-06-20', '085731416004', NULL, 'sudah', NULL, 'selesai', '2023-05-11 15:22:51', '2023-05-11 15:22:51');
INSERT INTO `permintaan_mcu` VALUES (5, 'W2304371981', NULL, NULL, NULL, '3516126006450001', 'KASMINI', 'TEMBORO 002/005 DOMAS TROWULAN', '1', '2023-05-19', '2023-05-19', 'UMUM', NULL, NULL, 'P', '1945-06-20', '085731416004', NULL, NULL, NULL, 'batal', '2023-05-19 14:04:40', '2023-05-19 14:07:08');
INSERT INTO `permintaan_mcu` VALUES (6, NULL, NULL, NULL, NULL, '3527018000000001', 'NESTI ANDRIANI', 'jl. apa aja', '1', '2023-05-19', '2023-05-19', 'UMUM', NULL, NULL, 'P', '2004-05-12', '08123033334000', NULL, 'sudah', NULL, 'selesai', '2023-05-19 14:06:00', '2023-05-19 14:06:00');
INSERT INTO `permintaan_mcu` VALUES (7, 'W2304371981', NULL, NULL, NULL, '3516126006450001', 'KASMINI', 'TEMBORO 002/005 DOMAS TROWULAN', '1', '2023-05-22', '2023-05-22', 'UMUM', NULL, NULL, 'P', '1945-06-20', '085731416004', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:36:01', '2023-05-22 06:24:28');
INSERT INTO `permintaan_mcu` VALUES (8, NULL, NULL, NULL, NULL, '3527018000000001', 'NESTI ANDRIANI', 'jl. apa aja', '1', '2023-05-22', '2023-05-22', 'UMUM', NULL, NULL, 'P', '2004-05-12', '08123033334000', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:37:18', '2023-05-22 06:24:30');
INSERT INTO `permintaan_mcu` VALUES (9, 'W2304371981', NULL, NULL, NULL, '3516126006450001', 'KASMINI', 'TEMBORO 002/005 DOMAS TROWULAN', '1', '2023-05-22', '2023-04-22', 'UMUM', NULL, NULL, 'P', '1945-06-20', '085731416004', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:41:08', '2023-05-22 11:41:08');
INSERT INTO `permintaan_mcu` VALUES (10, NULL, NULL, NULL, NULL, '3527018000000001', 'NESTI ANDRIANI', 'jl. apa aja', '1', '2023-05-22', '2023-04-22', 'UMUM', NULL, NULL, 'P', '2004-05-12', '08123033334000', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:41:25', '2023-05-22 11:41:25');
INSERT INTO `permintaan_mcu` VALUES (11, NULL, NULL, NULL, NULL, '3527018000000002', 'DWI ALIM', 'jl. apa aja', '1', '2023-05-22', '2023-04-22', 'UMUM', NULL, NULL, 'L', '2004-05-12', '081230333340', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:42:31', '2023-05-22 11:42:31');
INSERT INTO `permintaan_mcu` VALUES (12, NULL, NULL, NULL, NULL, '3527018000000003', 'SYIFAA', 'jl. apa aja', '1', '2023-05-22', '2023-04-22', 'UMUM', NULL, NULL, 'L', '2004-05-12', '081230333340', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:42:48', '2023-05-22 11:42:48');
INSERT INTO `permintaan_mcu` VALUES (13, NULL, NULL, NULL, NULL, '3527018000000005', 'MIFTAHUL CHOIR', 'jl. apa aja', '1', '2023-05-22', '2023-04-22', 'UMUM', NULL, NULL, 'L', '2004-05-12', '081230333340', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:43:05', '2023-05-22 11:43:05');
INSERT INTO `permintaan_mcu` VALUES (14, NULL, NULL, NULL, NULL, '3527018000000005', 'MIFTAHUL CHOIR', 'jl. apa aja', '1', '2023-05-22', '2023-05-22', 'UMUM', NULL, NULL, 'L', '2004-05-12', '081230333340', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:51:07', '2023-05-22 06:24:32');
INSERT INTO `permintaan_mcu` VALUES (15, NULL, NULL, NULL, NULL, '3527018000000006', 'LAILATUR RAHMAH', 'jl. apa aja', '1', '2023-05-22', '2023-05-22', 'UMUM', NULL, NULL, 'L', '2004-05-12', '081230333340', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:51:23', '2023-05-22 06:24:34');
INSERT INTO `permintaan_mcu` VALUES (16, NULL, NULL, NULL, NULL, '3527018000000007', 'ROBIATUL HUSNAYATI', 'jl. apa aja', '1', '2023-05-22', '2023-05-22', 'UMUM', NULL, NULL, 'L', '2004-05-12', '081230333340', NULL, 'sudah', NULL, 'selesai', '2023-05-22 11:51:35', '2023-05-22 06:24:36');
INSERT INTO `permintaan_mcu` VALUES (17, NULL, NULL, NULL, NULL, '3527018000000007', 'ROBIATUL HUSNAYATI', 'jl. apa aja', '1', '2023-05-23', '2023-05-23', 'UMUM', NULL, NULL, 'L', '2004-05-12', '081230333340', NULL, NULL, NULL, 'belum', '2023-05-23 10:02:01', '2023-05-23 10:02:01');
INSERT INTO `permintaan_mcu` VALUES (18, 'W2304371981', NULL, NULL, NULL, '3516126006450001', 'KASMINI', 'TEMBORO 002/005 DOMAS TROWULAN', '1', '2023-05-25', '2023-05-25', 'UMUM', NULL, NULL, 'P', '1945-06-20', '085731416004', 120000, 'sudah', NULL, 'selesai', '2023-05-25 10:26:01', '2023-05-25 06:56:51');
INSERT INTO `permintaan_mcu` VALUES (19, 'W2305373409', NULL, NULL, NULL, '3526176121721911', 'RESITA MAYRA', 'Jl. Raya Mlirip, Jetis, Mojokerto', '1', '2023-05-25', '2023-05-25', 'UMUM', NULL, NULL, 'P', '2000-01-06', '081233445466', 120000, 'sudah', NULL, 'selesai', '2023-05-25 10:33:47', '2023-05-25 06:58:27');
INSERT INTO `permintaan_mcu` VALUES (20, 'W2305373408', NULL, NULL, NULL, '3527082010201002', 'RETA ARVIANI', 'Jl. Raya Mlirip, Jetis, Mojokerto', '1', '2023-05-25', '2023-05-25', 'UMUM', NULL, NULL, 'P', '2003-06-01', '081345955212', 120000, 'sudah', NULL, 'selesai', '2023-05-25 10:34:02', '2023-05-25 06:58:48');
INSERT INTO `permintaan_mcu` VALUES (21, 'W2305373408', NULL, NULL, NULL, '3527082010201002', 'RETA ARVIANI', 'Jl. Raya Mlirip, Jetis, Mojokerto', '1', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '2003-06-01', '081345955212', NULL, NULL, NULL, 'belum', '2023-05-29 08:47:50', '2023-05-29 08:47:50');
INSERT INTO `permintaan_mcu` VALUES (22, 'W2305373365', 'Reg-001', 'iCY89S6', NULL, '3517115004070002', 'BILQIS NAVISSA AZZAHRO', 'TALUN LOR RT 03 RW 01 MADIOPURO SUMOBITO', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '2007-04-10', '085735604754', NULL, NULL, NULL, 'belum', '2023-05-29 10:56:47', '2023-05-29 10:56:47');
INSERT INTO `permintaan_mcu` VALUES (23, 'W2305373357', 'Reg-002', 'ek1tASS', NULL, '3578162509090001', 'bintang firjatullah putra yahya', 'TENGGUMUNG WETAN3/8 WONOKUSUMO SEMAMPIR', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'L', '2009-09-25', '08996019914', NULL, NULL, NULL, 'belum', '2023-05-29 11:11:13', '2023-05-29 11:11:13');
INSERT INTO `permintaan_mcu` VALUES (24, 'W2305373356', 'Reg-003', 'phHS2YN', NULL, '3525060111840001', 'PRENJAK SAPTA MAHANANI', 'WRINGIN ANOM GRESIK', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '1984-11-01', NULL, NULL, NULL, NULL, 'belum', '2023-05-29 11:17:07', '2023-05-29 11:17:07');
INSERT INTO `permintaan_mcu` VALUES (25, 'W2305373355', 'Reg-004', 'YPiKYGx', NULL, '3516184605970001', 'MEI VITA HARTINI', 'GROGOL GEDE 003/004 MOJOANYAR', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '1997-05-06', '085859230046', NULL, NULL, NULL, 'belum', '2023-05-29 11:18:39', '2023-05-29 11:18:39');
INSERT INTO `permintaan_mcu` VALUES (26, 'W2305373354', 'Reg-005', 'Cekjz42', NULL, '3516015204740003', 'CICIK LINDAWATI', 'NGRAMBUT 015/005 PADANGASRI JATIREJO', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '1974-04-12', '0895803298601', NULL, NULL, NULL, 'belum', '2023-05-29 11:23:12', '2023-05-29 11:23:12');
INSERT INTO `permintaan_mcu` VALUES (27, 'W2305373353', 'Reg-006', 'hZkXENB', NULL, '3315122902840002', 'RISNA YOKI ANDRA', 'SIDOMULYO III/11 MENTIKAN', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'L', '1984-02-29', '088989952648', NULL, NULL, NULL, 'belum', '2023-05-29 11:25:04', '2023-05-29 11:25:04');
INSERT INTO `permintaan_mcu` VALUES (28, 'W2305373352', 'Reg-007', '7ZfLeus', NULL, '3174040907081003', 'ADHITYA HURDYANSA ASMONO', 'JL RIYANTO 49 LINGK PRAJURIT KULON', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'L', '2008-07-09', '082234928212', 220000, 'sudah', 'Tunai', 'belum', '2023-05-29 15:14:22', '2023-05-29 08:48:27');
INSERT INTO `permintaan_mcu` VALUES (29, 'W2305373351', 'Reg-008', 'GMFzdf4', NULL, '3515014505680001', 'FAHIMA', 'TARIK RT 16 RW 04 ', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '1968-05-05', '085733053392', 220000, 'sudah', 'Tunai', 'belum', '2023-05-29 15:49:44', '2023-05-29 08:53:51');
INSERT INTO `permintaan_mcu` VALUES (30, 'W2305373350', 'Reg-009', '58MUZN3', NULL, '3516135806680001', 'KISNAWATI', 'KEDUNGMALING RT 11/05 SOOKO MOJOKERTO', '1,2', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '1968-12-11', '08504745639', 220000, 'sudah', 'Tunai', 'belum', '2023-05-29 15:51:42', '2023-05-29 08:54:19');
INSERT INTO `permintaan_mcu` VALUES (31, 'W2305373348', 'Reg-010', 'e2ihW9X', NULL, '3576025410620001', 'SUPRIATI', 'JL TENGGER IV /07 WATES', '1', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '1962-10-14', '0895380928100', 120000, 'sudah', 'Tunai', 'selesai', '2023-05-29 15:52:03', '2023-05-29 16:06:36');
INSERT INTO `permintaan_mcu` VALUES (32, 'W2305373347', 'Reg-011', 'N9KBxQr', NULL, '3516117006750001', 'SRI LULUT', 'PURI KENCANA D 1 SUMBER GIRANG PURI', '1', '2023-05-29', '2023-05-29', 'UMUM', NULL, NULL, 'P', '1975-06-30', '081330911475', 120000, 'sudah', 'Tunai', 'selesai', '2023-05-29 15:57:06', '2023-05-29 08:58:13');
INSERT INTO `permintaan_mcu` VALUES (33, 'W2305373347', 'Reg-001', '6BLzYBi', NULL, '3516117006750001', 'SRI LULUT', 'PURI KENCANA D 1 SUMBER GIRANG PURI', '1', '2023-05-31', '2023-05-31', 'UMUM', NULL, NULL, 'P', '1975-06-30', '081330911475', NULL, NULL, NULL, 'belum', '2023-05-31 10:07:08', '2023-05-31 10:07:08');
INSERT INTO `permintaan_mcu` VALUES (34, 'W2305373347', 'Reg-001', 'h59HJHv', NULL, '3516117006750001', 'SRI LULUT', 'PURI KENCANA D 1 SUMBER GIRANG PURI', '1', '2023-06-06', '2023-06-06', 'UMUM', NULL, NULL, 'P', '1975-06-30', '081330911475', NULL, NULL, NULL, 'belum', '2023-06-06 09:22:15', '2023-06-06 09:22:15');
INSERT INTO `permintaan_mcu` VALUES (35, 'W2306374265', 'Reg-002', 'G5rNfBi', NULL, '3516141804050002', 'FAISHAL NAFI\' RABBANI', 'SURONATAN III/29 RT 03 RW 02 MAGERSARI', '1', '2023-06-06', '2023-06-06', 'UMUM', NULL, NULL, 'L', '2005-04-18', '085731707313', 120000, NULL, 'Tunai', 'belum', '2023-06-06 09:41:09', '2023-06-06 09:42:16');
INSERT INTO `permintaan_mcu` VALUES (36, 'W2306374264', 'Reg-003', 'y71qA7A', NULL, '3514082111830001', 'AGUS PRIANTO', 'MERI DUKUHAN 03/02 MERI KRANGGAN', '1', '2023-06-06', '2023-06-06', 'UMUM', NULL, NULL, 'L', '1983-11-21', '082233840440', 120000, NULL, 'Tunai', 'belum', '2023-06-06 09:44:16', '2023-06-06 09:48:54');
INSERT INTO `permintaan_mcu` VALUES (37, 'W2306374264', 'Reg-001', 'uXc6vbp', NULL, '3514082111830001', 'AGUS PRIANTO', 'MERI DUKUHAN 03/02 MERI KRANGGAN', '1', '2023-06-07', '2023-06-07', 'UMUM', NULL, NULL, 'L', '1983-11-21', '082233840440', 120000, NULL, 'Tunai', 'belum', '2023-06-07 15:01:34', '2023-06-07 15:15:14');
INSERT INTO `permintaan_mcu` VALUES (38, 'W2306374264', 'Reg-001', 'eGdi4gA', NULL, '3514082111830001', 'AGUS PRIANTO', 'MERI DUKUHAN 03/02 MERI KRANGGAN', '1', '2023-06-08', '2023-06-08', 'UMUM', NULL, NULL, 'L', '1983-11-21', '082233840440', 120000, NULL, 'Tunai', 'belum', '2023-06-08 11:05:37', '2023-06-08 04:13:19');
INSERT INTO `permintaan_mcu` VALUES (39, NULL, 'Reg-002', 'rcC3dy5', NULL, '3527018000000006', 'AJENG IFANA PRATIWI MALIK', 'Jl. Raya Jabon', '1,2', '2023-06-08', '2023-06-08', 'UMUM', NULL, NULL, 'P', '2000-05-12', '081230333401', NULL, NULL, NULL, 'belum', '2023-06-08 12:02:51', '2023-06-08 12:02:51');
INSERT INTO `permintaan_mcu` VALUES (40, 'W2306374267', 'Reg-001', '4h6T4gr', NULL, '3527018000000099', 'ANNISA NUR PRADHITA', 'Jl. Raya Jabon', '1,2', '2023-06-09', '2023-06-09', 'UMUM', NULL, NULL, 'P', '2000-05-12', '081230333401', 220000, 'sudah', 'Tunai', 'proses', '2023-06-09 11:21:53', '2023-06-09 15:12:44');
INSERT INTO `permintaan_mcu` VALUES (41, NULL, 'Reg-002', 'rM12Jg8', NULL, '3527018000000009', 'NUR HASANAH', 'Jl. Raya Jabon', '1,2', '2023-06-09', '2023-06-09', 'UMUM', NULL, NULL, 'P', '1967-05-12', '081230333411', 220000, NULL, 'Tunai', 'belum', '2023-06-09 13:49:40', '2023-06-09 06:50:39');
INSERT INTO `permintaan_mcu` VALUES (42, NULL, 'Reg-003', 'z4brWbD', NULL, '3527018000000023', 'HABSATUL AINI', 'Jl. Raya Jabon', '1,2', '2023-06-09', '2023-06-09', 'UMUM', NULL, NULL, 'P', '1967-05-12', '081230333411', 220000, NULL, 'Tunai', 'belum', '2023-06-09 13:56:55', '2023-06-09 13:57:11');

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personal_access_tokens_token_unique`(`token` ASC) USING BTREE,
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`(`tokenable_type` ASC, `tokenable_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for rating_hc
-- ----------------------------
DROP TABLE IF EXISTS `rating_hc`;
CREATE TABLE `rating_hc`  (
  `id_rating_hc` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `permintaan_hc_id` int NOT NULL,
  `comments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `star_rating` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_rating_hc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of rating_hc
-- ----------------------------
INSERT INTO `rating_hc` VALUES (1, 11, 'pelayanan sangat baik', 4, NULL, NULL);
INSERT INTO `rating_hc` VALUES (2, 12, 'kurang puas, karena masih harus gini gitu', 2, NULL, NULL);

-- ----------------------------
-- Table structure for rating_mcu
-- ----------------------------
DROP TABLE IF EXISTS `rating_mcu`;
CREATE TABLE `rating_mcu`  (
  `id_rating_mcu` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `permintaan_mcu_id` int NOT NULL,
  `comments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `star_rating` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_rating_mcu`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of rating_mcu
-- ----------------------------
INSERT INTO `rating_mcu` VALUES (1, 6, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua', 5, NULL, NULL);
INSERT INTO `rating_mcu` VALUES (2, 7, 'sangat puas, pelayanan luar biasa', 5, NULL, NULL);

-- ----------------------------
-- Table structure for syarat_hc
-- ----------------------------
DROP TABLE IF EXISTS `syarat_hc`;
CREATE TABLE `syarat_hc`  (
  `id_syarat_hc` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `isi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_syarat_hc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of syarat_hc
-- ----------------------------
INSERT INTO `syarat_hc` VALUES (1, '<p><strong>Syarat dan Ketentuan</strong></p>\r\n\r\n<p>Halaman ini mengatur mengatur mengenai Syarat dan Ketentuan (&quot;<strong>Syarat dan Ketentuan</strong>&quot;) dimana anda (&ldquo;<strong>Anda</strong>&rdquo;) menggunakan layanan Legalitas.org (&ldquo;<strong>Legalitas.org</strong>&rdquo;). Jika Anda tidak setuju atas Syarat dan Ketentuan ini, silahkan tidak menggunakan layanan Legalitas.org.</p>\r\n\r\n<p><strong>1. Umum</strong></p>\r\n\r\n<p>Syarat dan Ketentuan ini mengatur penggunaan seluruh layanan dan fitur-fitur yang tersedia di website Legalitas.org berikut segala informasi, tulisan, gambar atau material lainnya yang di upload, di download atau ditampilkan dalam website Legalitas.org (secara bersama-sama disebut sebagai &ldquo;<strong>Layanan</strong>&rdquo;).</p>\r\n\r\n<p>Layanan ini dimiliki, dioperasikan dan diselenggarakan oleh PT Infiniti Global Ventura, perseroan terbatas yang didirikan berdasarkan hukum Republik Indonesia yang telah memperoleh ijin dalam melakukan kegiatan usaha dan Layanan Legalitas.org tersedia secara online melalui website Legalitas.org atau berbagai akses, media, perangkat dan platform lainnya, baik yang sudah atau akan tersedia dikemudian hari.</p>\r\n\r\n<p>Dengan telah melakukan menggunakan Layanan Legalitas.org, Anda dianggap:</p>\r\n\r\n<p>1. memberikan persetujuan kepada Legalitas.org untuk menggunakan informasi yang diberikan untuk tujuan sebagaimana diatur dalam Syarat dan Ketentuan ini.</p>\r\n\r\n<p>2. melepaskan hak untuk menggugat maupun menuntut atas segala pernyataan, kekeliruan, ketidaktepatan atau kekurangan dalam setiap konten yang dicantumkan dan disampaikan dalam situs Legalitas.org</p>\r\n\r\n<p>Pemberian layanan dari Legalitas.org atas hal-hal yang diatur dalam bagian Services, akan diatur lebih lanjut di dalam Perjanjian lebih rinci.</p>\r\n\r\n<p>Apabila Anda memiliki pertanyaan sehubungan dengan Syarat Ketentuan ini, Anda dapat menghubungi kami pada bagian Kontak Kami di bagian bawah Syarat Ketentuan ini.&nbsp;</p>\r\n\r\n<p>Syarat dan Ketentuan ini dapat diubah, modifikasi, tambah, hapus atau koreksi (&ldquo;<strong>Perubahan</strong>&rdquo;) setiap saat dan setiap perubahan itu berlaku sejak saat Legalitas.org nyatakan berlaku atau pada waktu lain yang ditetapkan oleh Legalitas.org. Legalitas.org tidak memberikan pemberitahuan apabila terjadi Perubahan Syarat dan Ketentuan dan karenanya, Legalitas.org menganjurkan untuk mengunjungi Layanan Legalitas.org secara berkala agar dapat mengetahui adanya perubahan tersebut</p>\r\n\r\n<p><strong>2. Berlangganan Blog</strong></p>\r\n\r\n<p>Ketika Anda mengisikan email untuk berlangganan blog Legalitas.org, Anda bersedia untuk menerima email dari Legalitas.org berupa informasi hukum terbaru, promosi-promosi, serta hal-hal lain yang dapat membantu Legalitas.org agar dapat memberikan layanan terbaik.</p>\r\n\r\n<p><strong>3. Pemberian Layanan Oleh Legalitas.org</strong></p>\r\n\r\n<p>Seluruh informasi dan data yang disediakan pada Legalitas.org adalah bersifat umum dan disediakan untuk tujuan pendidikan dan pengenalan hukum terhadap masyarakat. Dengan demikian tidak dianggap sebagai suatu kebenaran mutlak.</p>\r\n\r\n<p>Pada dasarnya Legalitas.org tidak menyediakan informasi yang bersifat rahasia.</p>\r\n\r\n<p>Legalitas.org dengan ini menjamin kerahasiaan untuk menjaga informasi yang telah yang telah anda berikan, kecuali apa yang akan akan digunakan dalam Layanan Legalitas.org.</p>\r\n\r\n<p>Akses Anda terhadap Layanan Legalitas.org tidak selalu tersedia sewaktu-waktu, karena terhadap Layanan dapat dilakukan perbaikan, perawatan, penambahan konten baru, fasilitas atau layanan lainnya. Legalitas.org akan memberikan pemberitahuan apabila sewaktu-waktu terjadi pembatasan akses.</p>\r\n\r\n<p>Legalitas.org tidak dapat menjamin bahwa Layanan Legalitas.org akan bebas dari gangguan, kerusakan atau memiliki masalah server, bebas dari virus dan masalah lainnya. Apabila terjadi gangguan dalam Layanan Legalitas.org, Anda harus memberi tahu kepada Legalitas.org dan Legalitas.org akan melakukan perbaikan secepat mungkin.</p>\r\n\r\n<p><strong>4. Penyangkalan / Dislaimer</strong></p>\r\n\r\n<p>Legalitas.org tidak dapat digugat maupun dituntut atas segala pernyataan, kekeliruan, ketidaktepatan atau kekurangan dalam setiap konten yang dicantumkan dan disampaikan dalam situs Legalitas.org.</p>\r\n\r\n<p>Legalitas.org berhak sepenuhnya mengubah judul dan/atau isi pertanyaan tanpa mengubah substansi dari hal-hal yang ditanyakan kepada Legalitas.org.</p>\r\n\r\n<p>Artikel- Artikel tertentu pada Legalitas.org mungkin sudah tidak sesuai / tidak relevan dengan peraturan perundang-undangan yang berlaku saat ini.</p>\r\n\r\n<p>Disarankan untuk mengecek kembali dasar hukum yang digunakan dalam artikel-artikel Legalitas.org untuk memastikan keberlakuan peraturan perundang-undangan.</p>\r\n\r\n<p>Untuk memastikan suatu kebenaran atas artikel, anda dapat menghubungi seorang penasehat hukum yang ahli di bidangnya.</p>\r\n\r\n<p><strong>5. Iklan</strong></p>\r\n\r\n<p>Dalam Layanan Legalitas.org terdapat iklan yang dilakukan oleh pengguna sponsor pihak ketiga (&ldquo;<strong>Pengiklan</strong>&rdquo;).</p>\r\n\r\n<p>Legalitas.org berhak menghapus atau mengubah atau mengganti atau menolak pemasangan materi iklan oleh Pengiklan tanpa memberikan alasan.</p>\r\n\r\n<p>Pengiklan bertanggung jawab atas materi iklan dalam Layanan Legalitas.org, dan karenanya Pengiklan melepaskan Legalitas.org dari tanggung jawab atas materi iklan yang dilakukan oleh Pengiklan.</p>\r\n\r\n<p><strong>6. Pembayaran Fee</strong></p>\r\n\r\n<p>Pemberian Layanan pada dasarnya adalah gratis.</p>\r\n\r\n<p>Khusus untuk pemberian Layanan Services, akan didasarkan pada perjanjian terpisah diluar dari Syarat dan Ketentuan ini.</p>\r\n\r\n<p><strong>7. Pelepasan Hak</strong></p>\r\n\r\n<p>Anda setuju bahwa pada dasarnya Layanan Legalitas.org adalah bertujuan untuk pendidikan dan pengenalan hukum kepada masyarakat, dan karenanya Anda dengan ini melepaskan Legalitas.org, karyawan Legalitas.org, penulis artikel serta pihak manapun yang bekerjasama dengan Legalitas.org atas segala tanggung jawab sehubungan dengan pemberian Layanan dalam Legalitas.org.</p>\r\n\r\n<p>Anda setuju bahwa Anda tidak akan melakukan klaim, gugatan, maupun tuntutan kepada Legalitas.org, karyawan Legalitas.org, penulis artikel serta pihak manapun yang bekerjasama dengan Legalitas.org, baik untuk sekarang maupun di kemudian hari.</p>\r\n\r\n<p>Apabila terdapat artikel yang setidak-tidaknya terdapat ketidaktepatan atas fakta dan/atau penggunaan peraturan dan/atau penggunaan sumber tulisan dan kejadian lainnya yang dapat mengakibatkan pemberian Layanan menjadi merugikan pihak lain, maka Anda dapat meminta kepada Legalitas.org untuk tidak menampilkan artikel dalam Layanan dengan mengirimkan email keberatan yang ditujukan kepada alamat email sebagaimana dimaksud dalam Kontak Kami di bagian paling bawah Syarat dan Ketentuan ini.</p>\r\n\r\n<p><strong>8. Layanan Tersedia &ldquo;As Is&rdquo;</strong></p>\r\n\r\n<p>Seluruh informasi, atau konten dalam bentuk apapun yang tersedia pada website layanan ini disediakan sebagaimana adanya dan sebagaimana tersedia tanpa adanya jaminan apapun baik tersirat maupun tersurat.</p>\r\n\r\n<p><strong>9. Keamanan</strong></p>\r\n\r\n<p>Dalam mengakses informasi Akun Anda dalam Layanan Legalitas.org, Anda akan menggunakan akses Secure Server Layer (SSL) yang akan mengenkripsi informasi yang ditampilkan dalam Layanan Legalitas.org.</p>\r\n\r\n<p>Legalitas.org tidak bisa menjamin seberapa kuat atau efektifnya enkripsi ini dan Legalitas.org tidak akan bertanggung jawab atas masalah yang terjadi akibat pengaksesan tanpa ijin dari informasi yang Anda sediakan.</p>\r\n\r\n<p><strong>10. Hukum Yang Berlaku</strong></p>\r\n\r\n<p>Hukum yang berlaku dalam Syarat dan Ketentuan ini adalah Hukum Negara Republik Indonesia</p>\r\n\r\n<p><strong>11. Lain-lain</strong></p>\r\n\r\n<p>Versi asli dari Syarat dan Ketentuan ini adalah dalam Bahasa Indonesia, dan dapat diterjemahkan ke dalam bahasa lain. Versi terjemahan dibuat untuk memberi kemudahan bagi pengguna asing, dan tidak bisa dianggap sebagai terjemahan resmi. Jika ditemukan adanya perbedaan antara versi Bahasa Indonesia dan versi bahasa lainnya dari syarat dan ketentuan ini, maka yang berlaku dan mengikat adalah versi Bahasa Indonesia.</p>\r\n\r\n<p>Setiap perselisihan dalam Layanan Legalitas.org akan diselesaikan secara musyawarah mufakat dan apabila tidak tercapai musyawarah mufakat, maka akan diselesaikan di badan arbitrase.</p>\r\n\r\n<p><strong>12. Kontak Kami</strong></p>\r\n\r\n<p>Apabila Anda memiliki pertanyaan terkait dengan Syarat dan Ketentuan ini, hubungi kami di:</p>\r\n\r\n<p><a href=\"mailto:legal@legalitas.org\">legallegalitas.org</a></p>', '2023-05-11 06:21:58', '2023-05-11 06:21:58');

-- ----------------------------
-- Table structure for syarat_mcu
-- ----------------------------
DROP TABLE IF EXISTS `syarat_mcu`;
CREATE TABLE `syarat_mcu`  (
  `id_syarat_mcu` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `isi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_syarat_mcu`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of syarat_mcu
-- ----------------------------
INSERT INTO `syarat_mcu` VALUES (1, '<p>Dengan mengunjungi atau menggunakan situs Justika.com, Anda menerima ketentuan dan persyaratan yang ditentukan oleh PT Justika Media Indonesia, suatu perseroan terbatas yang memiliki domisili di AD Premier Office Park Lantai 9, Jl. TB Simatupang No. 5 Ragunan, Pasar Minggu, Jakarta 12550, sebagai pengelola situs Justika.com yang beralamat di http://www.justika.com (&ldquo;Justika.com&rdquo;). Anda sepakat untuk mengikat diri dengan ketentuan dan persyaratan yang ditentukan oleh Justika.com, sebagaimana suatu perjanjian yang sah dan mengikat (&ldquo;Syarat dan Ketentuan&rdquo;), sebagai berikut:</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>1. Definisi dan Penafsiran</p>\r\n\r\n<p>Kecuali ditentukan lain dalam Syarat dan Ketentuan, definisi ini berlaku:</p>\r\n\r\n<p>(a) &ldquo;Anda&rdquo; berarti (i) pengunjung, pembaca, pengguna Situs, (ii) Pengguna Jasa, dan/atau (iii) pihak lain menggunakan dan menerima Layanan, selain Penyedia Jasa.</p>\r\n\r\n<p>(b) &ldquo;Akun&rdquo; berarti akun yang Anda peroleh setelah pendaftaran melalui Situs untuk menerima layanan dari Justika.com melalui Situs.</p>\r\n\r\n<p>(c) &ldquo;Daftar Keterangan&rdquo; berarti keterangan yang diperlukan untuk pelaksanaan layanan, termasuk tapi tidak terbatas pada Informasi Pribadi Anda, keterangan yang disediakan baik melalui Situs atau di luar Situs, sebagaimana dapat diminta oleh dan apabila dipandang perlu oleh Justika.com untuk pelaksanaan layanan dari waktu ke waktu.</p>\r\n\r\n<p>(d) &ldquo;Penyedia Jasa&rdquo;, berarti advokat yang memberikan jasa hukum, baik di dalam maupun di luar pengadilan yang memenuhi persyaratan dan berdasarkan ketentuan UU Nomor 18 Tahun 2003 tentang Advokat, yang menjadi mitra Justika.com.</p>\r\n\r\n<p>(e) &ldquo;Pengguna Jasa&rdquo;, berarti orang yang telah menggunakan dan/atau mengakses Situs dan/atau mengajukan permohonan untuk mendapatkan layanan dari Penyedia Jasa melalui Justika.com.</p>\r\n\r\n<p>(f) &ldquo;Hari Kerja&rdquo; adalah hari selain Sabtu, Minggu, dan hari libur nasional yang ditetapkan pemerintah.</p>\r\n\r\n<p>(g) &ldquo;Jam Kerja&rdquo; berarti jam 09.00 &ndash; 18.00 Waktu Indonesia Barat setiap Hari Kerja.</p>\r\n\r\n<p>(h) &ldquo;Informasi Pribadi&rdquo;, berarti tiap dan seluruh data pribadi yang diberikan oleh Penyedia Jasa dan Pengguna Jasa, yaitu nama, nomor identitas, data masalah hukum, lokasi, kontak, serta dokumen dan data lainnya sebagaimana diminta pada formulir pendaftaran Akun atau formulir lainnya pada saat menggunakan Justika.com.</p>\r\n\r\n<p>(i) &ldquo;Kebijakan Privasi&rdquo; adalah ketentuan kebijakan privasi atas Layanan sebagaimana tercantum dalam Situs.</p>\r\n\r\n<p>(j) &ldquo;Layanan&rdquo; adalah seluruh layanan yang disediakan oleh Justika.com, baik melalui Situs atau di luar Situs, yang meliputi fasilitas untuk pemberian jasa hukum oleh Penyedia Jasa melalui telepon, surat elektronik (e-mail), pertemuan, pesan singkat (chat), dan layanan lainnya yang ditentukan oleh Justika.com dari waktu ke waktu.</p>\r\n\r\n<p>(k) &ldquo;Materi&rdquo; berarti artikel, panduan, penjelasan dan/atau keterangan lainnya sehubungan dengan Layanan yang disediakan oleh Justika.com pada Situs.</p>\r\n\r\n<p>(l) &ldquo;Situs&rdquo; adalah situs yang dikelola Justika.com dan beralamat di https://www.justika.com/</p>\r\n\r\n<p>(m) &ldquo;Biaya Layanan&rdquo; adalah biaya yang akan dibayarkan oleh Pengguna Jasa kepada Penyedia Jasa melalui Justika.com untuk mendapatkan Layanan.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>2. Layanan</p>\r\n\r\n<p>(a) Justika.com adalah platform yang mempertemukan pihak yang membutuhkan jasa hukum dari Penyedia Jasa yang menjadi mitra Justika.com. Oleh karena itu, Justika.com tidak memberikan jasa hukum secara langsung maupun tidak langsung kepada pihak manapun. Anda memahami dan menyetujui bahwa pemberian Layanan dan pelaksanaan layanan oleh Justika.com: (i) bukan merupakan, tidak dimaksudkan dan/atau tidak dapat ditafsirkan sebagai suatu pemberian jasa hukum, dan/atau (ii) tidak menimbulkan hubungan advokat dan klien antara Justika.com dengan Anda dan/atau Pengguna, sebagaimana dimaksud dalam Undang-Undang Republik Indonesia Nomor 18 Tahun 2003 tentang Advokat, sehingga Justika.com tidak bertanggung jawab dan tidak dapat dimintai pertanggungjawaban dalam bentuk apapun berkenaan dengan jasa hukum yang diberikan oleh Penyedia Jasa.</p>\r\n\r\n<p>(b) Justika.com tidak memiliki kewajiban untuk menerjemahkan dokumen dalam Bahasa Indonesia ke dalam bahasa asing dan/atau sebaliknya.</p>\r\n\r\n<p>(c) Justika.com tidak memiliki keahlian dalam memberi saran atau pendapat hukum terkait dengan masalah hukum Anda.</p>\r\n\r\n<p>(d) Dalam pelaksanaan layanan, apabila dipandang perlu oleh Justika.com, Justika.com berhak dan diberikan kewenangan oleh Pengguna Jasa untuk menunjuk atau mengalihkan seluruh atau sebagian Layanan kepada pihak lain tanpa pemberitahuan terlebih dahulu kepada Pengguna Jasa.</p>\r\n\r\n<p>(e) Justika.com hanya akan memulai Layanan berdasarkan tahap yang ditentukan setelah Justika.com menerima pembayaran dari Pengguna Jasa berdasarkan ketentuan pembayaran sebagaimana diatur dalam Syarat dan Ketentuan ini.</p>\r\n\r\n<p>(f) Justika.com berhak menolak permintaan atau mengakhiri Layanan apabila berdasarkan pandangan dan pendapat Justika.com terdapat indikasi pemalsuan, penipuan dan/atau tindakan lain yang melanggar peraturan perundang-undangan yang berlaku.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>3. Pelaksanaan Layanan</p>\r\n\r\n<p>Dengan tidak mengesampingkan ketentuan lainnya dalam Syarat dan Ketentuan ini:</p>\r\n\r\n<p>(a) Anda bertanggungjawab atas segala pertanyaan, pernyataan, keterangan yang disampaikan atau dikirim kepada Justika.com baik melalui Situs maupun di luar Situs. Segala keterangan yang diperlukan untuk pelaksanaan layanan sehubungan dengan Anda sebagaimana diperlukan untuk pelaksanaan layanan, harus dipilih, ditentukan, disediakan, diisi dan/atau dilengkapi oleh Anda dalam Daftar Keterangan.</p>\r\n\r\n<p>(b) Anda menyatakan dan menjamin seluruh dokumen dan keterangan yang diberikan kepada Justika.com, baik secara tertulis maupun tidak tertulis, baik itu dokumen yang disiapkan oleh Justika.com maupun tidak, baik melalui Situs maupun di luar Situs, baik secara langsung maupun tidak langsung sehubungan dengan Layanan, sebagaimana diberikan oleh Anda atau pihak lain yang ditunjuk oleh Anda (i) telah dibaca, dipahami, disetujui dan/atau ditandatangani oleh atau pihak lainnya yang memiliki kepentingan atas keterangan yang diberikan kepada Justika.com tersebut, (ii) merupakan keterangan yang benar, tepat, akurat, tidak menyesatkan, sesuai keadaan yang sebenarnya termasuk tapi tidak terbatas pada kebenaran, keaslian dan keabsahan identitas dan tanda tangan oleh pihak penandatangan dalam setiap dokumen yang diberikan kepada Justika.com oleh Anda.</p>\r\n\r\n<p>(c) Anda menyatakan dan menjamin pemberian keterangan kepada Justika.com tidak melanggar peraturan perundang-undangan yang berlaku, rahasia dagang dan perjanjian apapun yang terkait termasuk tapi tidak terbatas pada perjanjian kerahasiaan (apabila ada).</p>\r\n\r\n<p>(d) Anda memahami bahwa peraturan perundang-undangan dan kebijakan pemerintah yang berwenang dapat berubah sewaktu-waktu.</p>\r\n\r\n<p>(e) Anda mengakui dan memahami bahwa dengan adanya penundaan, kelalaian dan/atau tidak dapat dipenuhinya Daftar Keterangan oleh Anda dapat mengakibatkan: tertundanya pengajuan Layanan; kelanjutan Layanan; memperlambat waktu proses pengerjaan Layanan; dan/atau timbulnya penambahan Biaya Layanan sesuai dengan harga satuan Layanan yang berlaku.</p>\r\n\r\n<p>(f) Penyedia Jasa akan menghubungi Pengguna Jasa melalui fitur yang disediakan Justika.com setelah Pengguna Jasa melunasi pembayaran Biaya Layanan.</p>\r\n\r\n<p>(g) Apabila Pengguna Jasa tidak dapat dihubungi, maka Penyedia Jasa akan mencoba menghubungi kembali Pengguna Jasa paling banyak 3 (tiga) kali kesempatan dalam rentang waktu yang telah Pengguna Jasa pilih. Apabila dalam rentang waktu dimaksud, Pengguna Jasa masih belum dapat dihubungi, maka Justika menganggap Pengguna Jasa telah melepaskan haknya atas Layanan. Dengan demikian Biaya Layanan yang telah dibayar dengan sendirinya menjadi milik Justika.com.</p>\r\n\r\n<p>(h) Segala percakapan melalui fitur yang disediakan Justika.com antara Penyedia Jasa dengan Pengguna Jasa akan secara otomatis terekam oleh sistem di Justika.com sebagai arsip. Justika.com tidak akan membuka arsip rekaman percakapan tersebut tanpa persetujuan dari Penyedia Jasa dan Pengguna Jasa.</p>\r\n\r\n<p>(i) Penjadwalan ulang atas waktu konsultasi dan/atau Penyedia Jasa, dimungkinkan dengan ketentuan sebagai berikut:</p>\r\n\r\n<ul>\r\n	<li>\r\n	<p>1. Pengguna Jasa harus menghubungi Justika 12 (dua belas) jam sebelum waktu konsultasi yang telah dipilih sebelumnya. Apabila kurang dari itu maka penjadwalan ulang tidak dapat dilakukan.</p>\r\n\r\n	<ol>\r\n	</ol>\r\n\r\n	<p>2. Justika akan memberikan pilihan waktu dan/atau Penyedia Jasa yang lain secara manual melalui layanan chat.</p>\r\n\r\n	<ol>\r\n	</ol>\r\n\r\n	<p>3. Hanya dapat diberikan 1 (satu) kali kesempatan.</p>\r\n\r\n	<ol>\r\n	</ol>\r\n\r\n	<p>4. Dalam hal waktu konsultasi mengalami penjadwalan ulang yang diakibatkan adanya permintaan dari Penyedia Jasa maka, Justika akan memberitahukan kepada Pengguna Jasa melalui layanan chat.</p>\r\n\r\n	<ol>\r\n	</ol>\r\n	</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>4. Pendaftaran, Akun dan Akses Pada Situs</p>\r\n\r\n<p>Dengan menggunakan atau berpartisipasi dalam Justika.com, Anda menyatakan dan menjamin, bahwa:</p>\r\n\r\n<p>(a) Anda berusia minimal 21 (dua puluh tahun) tahun atau sudah menikah;</p>\r\n\r\n<p>(b) Anda setuju untuk menjadi Pengguna Jasa sesuai dengan Syarat dan Ketentuan ini;</p>\r\n\r\n<p>(c) Penggunaan Situs dan Layanan tidak ditujukan untuk melanggar hukum dan peraturan yang berlaku;</p>\r\n\r\n<p>(d) Anda bertanggungjawab terhadap ketersediaan koneksi internet, biaya pulsa, biaya telekomunikasi dan/atau biaya lainnya berdasarkan peraturan perundang-undangan yang berlaku agar Anda dapat melakukan akses pada Situs.</p>\r\n\r\n<p>(e) Justika.com melakukan upaya sebaik-baiknya agar Situs dapat diakses dan digunakan oleh Anda tanpa gangguan dalam bentuk apapun, namun Justika.com tidak memberikan jaminan Situs akan senantiasa dapat digunakan dan dilakukan akses dari waktu ke waktu.</p>\r\n\r\n<p>(f) Layanan melalui Situs sewaktu-waktu dapat dihentikan atau ditunda untuk sementara waktu karena pemeriksaan, pemeliharaan, perbaikan, perubahan, penambahan sistem pada Situs. Apabila terdapat gangguan terhadap Situs dan/atau sistem pembayaran yang disediakan pada Situs yang disebabkan karena alasan apapun termasuk tapi tidak terbatas pada gangguan virus, jaringan internet, Justika.com akan memberikan pemberitahuan mengenai gangguan tersebut melalui Situs.</p>\r\n\r\n<p>(g) Justika.com tidak bertanggungjawab kepada Anda dan/atau pihak manapun atas segala akibat yang timbul sehubungan dengan penggunaan Situs atau tidak dapat digunakannya Situs, baik sebagian maupun seluruhnya, yang disebabkan karena, termasuk namun tidak terbatas pada, gangguan virus, malware atau gangguan lainnya yang berada di luar kekuasaan Justika.com dan dapat mempengaruhi beroperasinya Situs.</p>\r\n\r\n<p>(h) Satu Pengguna Jasa hanya dapat memiliki satu Akun. Anda tidak dapat mengalihkan penggunaan Akun kepada pihak lain. Justika.com tidak bertanggung jawab atas penggunaan Akun Anda oleh pihak lain dengan alasan apapun.</p>\r\n\r\n<p>(i) Akses masuk ke Situs memerlukan nomor telepon genggam Pengguna Jasa. Anda bertanggungjawab secara penuh atas keamanan kata sandi Akun Anda dan akan segera memberitahukan kepada Justika.com apabila keamanan tersebut telah atau dicurigai telah dirusak, ditembus atau digunakan oleh pihak lain tanpa persetujuan Anda.</p>\r\n\r\n<p>(j) Justika.com berhak untuk menangguhkan atau menghentikan sebuah Akun untuk jangka waktu sementara ataupun seterusnya, dengan melakukan pemberitahuan sesegera mungkin, apabila Justika.com memiliki dugaan bahwa Akun telah dirusak, ditembus atau digunakan oleh pihak lain tanpa persetujuan pemegang hak yang sah atas suatu Akun.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>5. Materi dan Layanan</p>\r\n\r\n<p>(a) Keterangan yang disampaikan melalui Materi dan Layanan diperuntukkan atau dimaksudkan sebagai pemberian keterangan atau informasi secara umum dan bukan merupakan pendapat hukum. Justika.com melakukan upaya sebaik-baiknya untuk menyediakan Materi dan Layanan yang terkini dan sesuai dengan keadaan yang sebenarnya namun Justika.com tidak menjamin kelengkapan, kebenaran, keberlakuan, ketepatan, kepastian dan/atau kesesuaian Materi dan Layanan pada keadaan yang sebenarnya.</p>\r\n\r\n<p>(b) Pemanfaatan keterangan yang berasal dari Materi dan Layanan oleh Anda merupakan tanggungjawab Anda sepenuhnya. Anda disarankan untuk tidak mengandalkan sepenuhnya pada Materi atau keterangan dari Layanan untuk mengambil suatu keputusan atau suatu tindakan apapun tanpa memperoleh pendapat hukum dari Penyedia Jasa dan/atau pendapat dari praktisi hukum lainnya yang kompeten di bidang yang berhubungan dengan masalah hukum Anda.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>6. Pernyataan dan Jaminan</p>\r\n\r\n<p>Justika menyatakan bahwa:</p>\r\n\r\n<p>a) tidak menjamin bahwa permasalahan hukum Pengguna Jasa dapat diselesaikan cukup melalui fitur digital seperti chat dan/atau telepon. Mengingat tingkat kebutuhan jasa hukum bisa berbeda setiap subyek hukum. Pengguna dan/atau Advokat nantinya bisa mempertimbangkan kebutuhan untuk melanjutkan ke layanan lanjutan yang tersedia.</p>\r\n\r\n<p>(b) Dalam menggunakan Layanan, Anda menjamin Anda tidak akan:</p>\r\n\r\n<ul>\r\n	<li>\r\n	<p>1. mengirimkan atau mengunggah ke Situs, pesan elektronik, dokumen dan/atau konten yang memuat atau berisi virus atau perangkat lain yang dapat merusak, mengganggu, membuat tidak berfungsi dan/atau memberikan dampak buruk lainnya baik sebagian maupun keseluruhan bagian dari Situs; dan</p>\r\n\r\n	<ol>\r\n	</ol>\r\n\r\n	<p>2. mengubah, meretas, memodifikasi dan/atau melakukan akses ke Situs dengan melawan hukum.</p>\r\n\r\n	<ol>\r\n	</ol>\r\n	</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>7. Biaya Layanan dan Ketentuan Pembayaran</p>\r\n\r\n<p>Layanan ini adalah layanan berbayar yang besar dan ketentuannya akan diatur pada ketentuan tersendiri dan merupakan satu kesatuan dengan syarat dan ketentuan ini.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>8. Pengakhiran Layanan</p>\r\n\r\n<p>Dengan tidak mengesampingkan hak-hak Justika.com berdasarkan Syarat dan Ketentuan serta peraturan perundang-undangan yang berlaku, Layanan diakhiri apabila (mana yang terjadi lebih dahulu):</p>\r\n\r\n<p>(a) Pengguna Jasa tidak memenuhi pembayaran sesuai dengan Biaya Layanan dan ketentuan pembayaran;</p>\r\n\r\n<p>(b) Pengguna Jasa mengajukan permohonan secara tertulis kepada Justika.com;</p>\r\n\r\n<p>(c) Pengguna Jasa dinilai oleh Justika.com telah melakukan pelanggaran Syarat dan Ketentuan dan peraturan perundangan yang berlaku;</p>\r\n\r\n<p>(d) Adanya indikasi penipuan, pemalsuan dan/atau kejahatan sehubungan dengan Layanan yang dilakukan oleh Pengguna Jasa.</p>\r\n\r\n<p>Dengan berakhirnya Layanan, Pengguna Jasa mengakui dan menyatakan Justika.com tidak lagi memiliki kewajiban untuk melanjutkan Layanan.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>9. Hak Kekayaan Intelektual</p>\r\n\r\n<p>Justika.com memiliki seluruh hak kekayaan intelektual yang terdapat dalam Materi dan Situs. Anda tidak diperkenankan untuk menyalin, mendistribusikan, menerbitkan, menyebarkan dan/atau menjual bagian atau seluruh dari Materi atau Situs tanpa persetujuan tertulis terlebih dahulu dari Justika.com.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>10. Pemberitahuan dan Komunikasi</p>\r\n\r\n<p>(a) Segala korespondensi kepada Justika.com sehubungan dengan Syarat dan Ketentuan agar ditujukan secara tertulis pada alamat berikut:</p>\r\n\r\n<p>PT Justika Media Indonesia AD Premier Office Park Lantai 9 Jl. TB Simatupang No. 5 Ragunan, Pasar Minggu, Jakarta Faksimili: +6221 2270 891 email : tanya[at]justika.info</p>\r\n\r\n<p>(b) Komunikasi sehubungan dengan Layanan dapat dilakukan secara langsung melalui pertemuan, melalui telepon dan secara elektronik termasuk tapi tidak terbatas lewat e-mail dan layanan aplikasi melalui internet seperti daring percakapan (chatting).</p>\r\n\r\n<p>(c) Alamat e-mail dan nomor telepon yang didaftarkan oleh Pengguna Jasa pada Justika.com melalui Situs atau di luar Situs akan digunakan oleh Justika.com untuk mengirim kata sandi Akun, status Layanan, keterangan dan pemberitahuan lainnya sehubungan dengan Layanan Justika.com. Pengguna Jasa wajib memberitahukan adanya perubahan alamat e-mail dan nomor telepon yang terdaftar pada Justika.com. Justika.com tidak bertanggungjawab atas kebenaran alamat e-mail dan nomor telepon yang didaftarkan pada Justika.com oleh Pengguna Jasa.</p>\r\n\r\n<p>(d) Dengan adanya komunikasi secara elektronik dalam bentuk apapun antara Anda dengan Justika.com, Anda menyetujui komunikasi secara elektronik sebagai bentuk komunikasi yang sah.</p>\r\n\r\n<p>(e) Justika.com berhak, tapi tidak memiliki kewajiban, untuk menjawab, membalas pesan, menerima telepon, mengirim e-mail, mengirim pesan, mengirim dokumen, menyanggupi permintaan untuk bertemu dan/atau menerima komunikasi dalam bentuk apapun kepada atau dari Anda mengenai Layanan di luar Jam Kerja dan Hari Kerja.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>11. Hukum dan Penyelesaian Sengketa</p>\r\n\r\n<p>(a) Syarat dan Ketentuan dan/atau Daftar Keterangan ditafsirkan dan dilaksanakan berdasarkan ketentuan hukum dan peraturan perundang-undangan yang berlaku di Negara Republik Indonesia.</p>\r\n\r\n<p>(b) Apabila timbul perselisihan mengenai penafsiran dan/atau pelaksanaan Syarat dan Ketentuan dan/atau Daftar Keterangan perselisihan tersebut akan diselesaikan secara musyawarah untuk mufakat.</p>\r\n\r\n<p>(c) Apabila perselisihan tersebut diatas tidak dapat diselesaikan secara musyawarah untuk mufakat, maka perselisihan tersebut akan diselesaikan melalui Badan Arbitrase Nasional Indonesia (BANI) menurut peraturan dan prosedur pada BANI.</p>\r\n\r\n<p>(d) Tempat pelaksanaan arbitrase adalah Jakarta dan menggunakan Bahasa Indonesia.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>12. Pembebasan</p>\r\n\r\n<p>(a) Anda menyatakan bahwa Justika.com, direksi, komisaris, karyawan, mitra, afiliasi, dan/atau perwakilannya tidak bertanggungjawab atas tuntutan, klaim, kerugian (baik secara langsung maupun tidak langsung termasuk tapi tidak terbatas pada kehilangan laba, penghasilan, produksi, nama baik, data atau kesempatan), kompensasi, biaya, pengeluaran, beban dan/atau kewajiban dalam bentuk apapun (termasuk biaya jasa hukum advokat) dan kepada pihak manapun, termasuk tapi tidak terbatas pada yang diakibatkan karena atau sehubungan dengan (i) Materi, (ii) Layanan, (iii) kelalaian Anda dalam memenuhi Syarat dan Ketentuan, (iv) pernyataan atau jaminan oleh Anda sebagaimana tertuang dalam Syarat dan Ketentuandinilai atau ditemukan tidak benar, (v) kunjungan pada Situs atau penggunaan Situs oleh Anda, (vi) penggunaan Akun oleh pihak lain yang menggunakan kata sandi dan kata pengguna Anda dan/atau Justika.com, (vii) kegagalan operasional Situs atau tidak dapat digunakannya Situs baik secara sebagian maupun keseluruhan, atau (viii) tindakan Anda yang tidak sesuai dengan peraturan perundang-undangan yang berlaku.</p>\r\n\r\n<p>(b) Dalam hal Pengguna Jasa memberikan kuasa pada Justika.com, karyawan Justika.com atau pihak lain yang ditunjuk oleh Justika.com sebagai penerima kuasa untuk melakukan tindakan untuk dan atas nama Pengguna Jasa sehubungan dengan Layanan, dengan ini Pengguna Jasa menyatakan bahwa Pengguna Jasa membebaskan penerima kuasa termasuk penerima kuasa substitusinya tersebut dari segala tuntutan, klaim, kerugian (baik secara langsung maupun tidak langsung termasuk tapi tidak terbatas pada kehilangan laba, penghasilan, produksi, nama baik, data atau kesempatan), kompensasi, biaya, pengeluaran, beban dan/atau kewajiban dalam bentuk apapun (termasuk biaya jasa hukum advokat) atas segala hal yang diakibatkan oleh pemberian kuasa tersebut.</p>\r\n\r\n<p>(c) Pengguna Jasa menyatakan bahwa Justika.com, direksi, komisaris, karyawan, mitra, afiliasi, perwakilannya, dan/atau pihak yang ditunjuk oleh Pengguna Jasa sebagai penerima kuasa sehubungan dengan Layanan tidak akan menjadi suatu pihak dalam satu atau lebih sengketa yang melibatkan Pengguna Jasa.</p>\r\n\r\n<p>(d) Ketentuan ini akan tetap berlaku meskipun terjadi pengakhiran Layanan dan/atau penutupan Akun.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>13. Ketentuan Lain-lain</p>\r\n\r\n<p>(a) Dengan Anda menggunakan dan melanjutkan penggunaan Situs dan Layanan, maka Anda telah menyetujui Syarat dan Ketentuan serta Kebijakan Privasi.</p>\r\n\r\n<p>(b) Justika.com berhak untuk mengubah sewaktu-waktu Syarat dan Ketentuan, Materi dan/atau Layanan, dengan pemberitahuan melalui pengumuman di Situs atau sarana lainnya yang dipandang wajar oleh Justika.com. Anda disarankan untuk senantiasa memeriksa Situs secara berkala untuk memastikan adanya perubahan tersebut. Anda diberi hak sepenuhnya untuk keluar dari Situs Justika.com dan/atau mengakhiri penggunaan layanan Justika.com apabila Anda tidak setuju dengan Syarat dan Ketentuan, Kebijakan Privasi serta perubahan-perubahannya tersebut. Dengan dilanjutkannya penggunaan Situs atau penggunaan Layanan oleh Anda setelah perubahan tersebut merupakan bentuk persetujuan dari Anda atas perubahan tersebut.</p>\r\n\r\n<p>(c) Apabila terdapat 1 (satu) atau lebih ketentuan dalam Syarat dan Ketentuan dan/atau Daftar Keterangan ini dinyatakan tidak sah, tidak sesuai dengan peraturan yang berlaku, tidak dapat dilaksanakan dan/atau dinyatakan tidak berlaku oleh Pejabat Yang Berwenang, maka ketentuan lainnya dalam Syarat dan Ketentuan dan/atau Daftar Keterangan tetap berlaku.</p>\r\n\r\n<p>(d) Kegagalan, penundaan atau pengesampingan oleh Justika.com untuk pelaksanaan atau penegakan setiap ketentuan Syarat dan Ketentuan dan/atau Daftar Keterangan, tidak dapat diartikan sebagai pengesampingan hak-hak Justika.com atas ketentuan tersebut atau ketentuan lainnya dalam Syarat dan Ketentuan dan/atau Daftar Keterangan.</p>', '2023-05-11 06:23:26', '2023-05-11 06:23:26');

-- ----------------------------
-- Table structure for tenaga_medis
-- ----------------------------
DROP TABLE IF EXISTS `tenaga_medis`;
CREATE TABLE `tenaga_medis`  (
  `id_tenaga_medis` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_dokter` int NULL DEFAULT NULL,
  `layanan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tenaga_medis`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tenaga_medis
-- ----------------------------
INSERT INTO `tenaga_medis` VALUES (2, 1443, '3', 'MELAYANI', '2023-05-12 08:45:38', '2023-05-12 08:45:38');
INSERT INTO `tenaga_medis` VALUES (3, 1445, '2', 'MELAYANI', '2023-05-15 08:32:43', '2023-05-15 08:32:43');

-- ----------------------------
-- Table structure for transaksi_hc
-- ----------------------------
DROP TABLE IF EXISTS `transaksi_hc`;
CREATE TABLE `transaksi_hc`  (
  `id_transaksi_hc` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_permintaan_hc` int NOT NULL,
  `nominal` int NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_transaksi_hc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of transaksi_hc
-- ----------------------------

-- ----------------------------
-- Table structure for transaksi_mcu
-- ----------------------------
DROP TABLE IF EXISTS `transaksi_mcu`;
CREATE TABLE `transaksi_mcu`  (
  `id_transaksi_mcu` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_permintaan_mcu` int NOT NULL,
  `nominal` int NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_transaksi_mcu`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of transaksi_mcu
-- ----------------------------
INSERT INTO `transaksi_mcu` VALUES (1, 18, 120000, 'PAID', 'mcu_134', '2023-05-25 06:55:48', '2023-05-25 06:55:48');
INSERT INTO `transaksi_mcu` VALUES (2, 19, 120000, 'PAID', 'mcu_100', '2023-05-25 06:58:21', '2023-05-25 06:58:21');
INSERT INTO `transaksi_mcu` VALUES (3, 20, 130000, 'PAID', 'mcu_197', '2023-05-25 06:58:44', '2023-05-25 06:58:44');
INSERT INTO `transaksi_mcu` VALUES (4, 27, 220000, 'pending', 'mcu_158', '2023-05-29 14:21:23', '2023-05-29 14:21:23');
INSERT INTO `transaksi_mcu` VALUES (5, 28, 220000, 'pending', 'mcu_132', '2023-05-29 15:16:56', '2023-05-29 15:16:56');
INSERT INTO `transaksi_mcu` VALUES (6, 28, 220000, 'PAID', 'mcu_69', '2023-05-29 08:48:27', '2023-05-29 08:48:27');
INSERT INTO `transaksi_mcu` VALUES (7, 28, 220000, 'pending', 'mcu_146', '2023-05-29 15:50:21', '2023-05-29 15:50:21');
INSERT INTO `transaksi_mcu` VALUES (8, 29, 220000, 'pending', 'mcu_118', '2023-05-29 15:52:58', '2023-05-29 15:52:58');
INSERT INTO `transaksi_mcu` VALUES (9, 30, 220000, 'pending', 'mcu_94', '2023-05-29 15:53:17', '2023-05-29 15:53:17');
INSERT INTO `transaksi_mcu` VALUES (10, 31, 120000, 'pending', 'mcu_64', '2023-05-29 15:53:22', '2023-05-29 15:53:22');
INSERT INTO `transaksi_mcu` VALUES (11, 29, 250000, 'PAID', 'mcu_117', '2023-05-29 08:53:51', '2023-05-29 08:53:51');
INSERT INTO `transaksi_mcu` VALUES (12, 30, 300000, 'PAID', 'mcu_99', '2023-05-29 08:54:19', '2023-05-29 08:54:19');
INSERT INTO `transaksi_mcu` VALUES (13, 31, 120000, 'PAID', 'mcu_191', '2023-05-29 08:54:41', '2023-05-29 08:54:41');
INSERT INTO `transaksi_mcu` VALUES (14, 32, 120000, 'PAID', 'mcu_148', '2023-05-29 15:57:32', '2023-05-29 08:57:47');
INSERT INTO `transaksi_mcu` VALUES (15, 35, 120000, 'pending', 'INV/2023-06-06MCU147', '2023-06-06 09:42:17', '2023-06-06 09:42:17');
INSERT INTO `transaksi_mcu` VALUES (16, 36, 120000, 'pending', 'INV/20230606/MCU/67', '2023-06-06 09:48:54', '2023-06-06 09:48:54');
INSERT INTO `transaksi_mcu` VALUES (17, 37, 120000, 'pending', 'INV/20230607/MCU/114', '2023-06-07 15:15:14', '2023-06-07 15:15:14');
INSERT INTO `transaksi_mcu` VALUES (36, 38, 120000, 'pending', 'INV/20230608/MCU/127', '2023-06-08 04:58:55', '2023-06-08 04:58:55');
INSERT INTO `transaksi_mcu` VALUES (37, 40, 220000, 'PAID', 'INV/20230609/MCU/30', '2023-06-09 04:33:21', '2023-06-09 15:07:59');
INSERT INTO `transaksi_mcu` VALUES (38, 41, 220000, 'pending', 'INV/20230609/MCU/151', '2023-06-09 06:50:39', '2023-06-09 06:50:39');
INSERT INTO `transaksi_mcu` VALUES (39, 42, 220000, 'pending', 'INV/20230609/MCU/174', '2023-06-09 13:57:11', '2023-06-09 13:57:11');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Admin', 'admin', 'admin@example.com', NULL, '1', '$2y$10$eMnomQEtQknua.YunH2JkeKoKeWPOzkkHbet3smaZBjNjIhAUsGbS', NULL, '2023-08-25 13:53:48', '2023-08-25 13:53:48');
INSERT INTO `users` VALUES (2, 'Admin MCU', 'adminmcu', 'adminmcu@example.com', NULL, '2', '$2y$10$HRnMyWdbjx3BpGRVf.3ftOL8cOc2gR9yoHfYmV71VwqEVnuwyoDfe', NULL, '2023-08-25 13:53:49', '2023-08-25 13:53:49');
INSERT INTO `users` VALUES (3, 'Admin Homecare', 'adminhc', 'adminhomecare@example.com', NULL, '3', '$2y$10$WvoWuqktkL47EnsoTbnL3.0urwrngbHMzpjAgldYoPmk3rCOV6Ip6', NULL, '2023-08-25 13:53:49', '2023-08-25 13:53:49');
INSERT INTO `users` VALUES (4, 'Admin PSC', 'adminpsc', 'adminpsc@example.com', NULL, '4', '$2y$10$cytEFXyB3A78PozFAqsTX.tGztv5.1nSVav4n9F9aKRPz/L3PSacW', NULL, '2023-08-25 13:53:49', '2023-08-25 13:53:49');

SET FOREIGN_KEY_CHECKS = 1;
