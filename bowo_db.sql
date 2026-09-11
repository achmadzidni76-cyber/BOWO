SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `deposits`;
DROP TABLE IF EXISTS `targets`;
DROP TABLE IF EXISTS `waste_types`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE DATABASE IF NOT EXISTS `bowo_db` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `bowo_db`;

-- TABEL USERS
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'nasabah') DEFAULT 'nasabah',
  `balance` DECIMAL(12,2) DEFAULT 0.00,
  `points` INT DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABEL WASTE_TYPES
CREATE TABLE `waste_types` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `price_per_kg` DECIMAL(10,2) NOT NULL,
  `description` TEXT,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABEL DEPOSITS
CREATE TABLE `deposits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `waste_type_id` INT NOT NULL,
  `weight` DECIMAL(8,2) NOT NULL,
  `price_per_kg` DECIMAL(10,2) NOT NULL,
  `total_value` DECIMAL(12,2) NOT NULL,
  `points` INT NOT NULL,
  `deposit_date` DATE NOT NULL,
  `notes` TEXT,
  `status` ENUM('menunggu', 'diverifikasi', 'ditolak') DEFAULT 'menunggu',
  `verified_by` INT DEFAULT NULL,
  `verified_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_deposits_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_deposits_waste` FOREIGN KEY (`waste_type_id`) REFERENCES `waste_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_deposits_verifier` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABEL TRANSACTIONS
CREATE TABLE `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `deposit_id` INT DEFAULT NULL,
  `type` ENUM('setoran_masuk', 'penarikan') NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_transactions_deposit` FOREIGN KEY (`deposit_id`) REFERENCES `deposits` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABEL TARGETS
CREATE TABLE `targets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `target_weight` DECIMAL(8,2) NOT NULL DEFAULT 10.00,
  `period` VARCHAR(20) DEFAULT 'Bulan Ini',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_targets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DATA INITIAL
-- Passwords: admin = admin123 | budi = password123 | siti = password123
INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `address`, `password`, `role`, `balance`, `points`, `status`) VALUES
(1, 'Administrator BOWO', 'admin', 'admin@bowo.id', '081234567890', 'Kantor Pusat BOWO', '$2y$10$abcdefghijklmnopqrstuuEZ4tpXm4W5SeJkDbuNdfJ.CF0.Wc2V2', 'admin', 0.00, 0, 'active'),
(2, 'Budi Santoso', 'budi', 'budi@gmail.com', '081298765432', 'Jl. Merdeka No. 12', '$2y$10$abcdefghijklmnopqrstuu0SYq8twpcthS10uxQ26rv0s3Obj8Ufu', 'nasabah', 25000.00, 50, 'active'),
(3, 'Siti Rahma', 'siti', 'siti@gmail.com', '085612345678', 'Jl. Mawar No. 5', '$2y$10$abcdefghijklmnopqrstuu0SYq8twpcthS10uxQ26rv0s3Obj8Ufu', 'nasabah', 40000.00, 80, 'active');

INSERT INTO `waste_types` (`id`, `name`, `category`, `price_per_kg`, `description`, `status`) VALUES
(1, 'Plastik PET (Botol Bening)', 'Plastik', 5000.00, 'Botol air mineral bekas bersih tanpa label', 'active'),
(2, 'Kertas Dupleks / Dus', 'Kardus', 2500.00, 'Kardus bekas kemasan, bersih dan kering', 'active'),
(3, 'Kertas HVS / Buku', 'Kertas', 3000.00, 'Kertas putih bekas cetak atau buku tulis', 'active'),
(4, 'Kaleng / Besi Tipis', 'Logam', 8000.00, 'Kaleng minuman atau wadah makanan logam', 'active');

INSERT INTO `targets` (`id`, `user_id`, `target_weight`, `period`) VALUES 
(1, 2, 10.00, 'Bulan Ini'),
(2, 3, 15.00, 'Bulan Ini');