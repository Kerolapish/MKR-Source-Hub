-- ============================================================
-- MKR-Source-Hub Database Schema
-- MKR Hartamas Sdn. Bhd. - Agritech Sourcing & Contractor System
-- Generated: 2026-09-28
-- ============================================================

CREATE DATABASE IF NOT EXISTS `mkr_source_hub`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `mkr_source_hub`;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150)    NOT NULL,
    `email`      VARCHAR(191)    NOT NULL UNIQUE,
    `password`   VARCHAR(255)    NOT NULL,
    `role`       ENUM('Admin','Procurement_Staff','Project_Manager') NOT NULL DEFAULT 'Procurement_Staff',
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: suppliers
-- ============================================================
CREATE TABLE IF NOT EXISTS `suppliers` (
    `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `supplier_name`   VARCHAR(200)  NOT NULL,
    `contact_person`  VARCHAR(150)  NOT NULL,
    `email`           VARCHAR(191)  NOT NULL,
    `phone_number`    VARCHAR(30)   NOT NULL,
    `location`        VARCHAR(255)  NOT NULL,
    `created_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: hardware_items
-- ============================================================
CREATE TABLE IF NOT EXISTS `hardware_items` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `supplier_id`   INT UNSIGNED    NOT NULL,
    `item_name`     VARCHAR(200)    NOT NULL,
    `category`      ENUM('Sensor','Microcontroller','Fertigation','Network','Machinery') NOT NULL,
    `specifications` TEXT           NOT NULL,
    `unit_cost`     DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
    `lead_time_days` SMALLINT UNSIGNED NOT NULL DEFAULT 7,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_hardware_supplier`
        FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: contractors
-- ============================================================
CREATE TABLE IF NOT EXISTS `contractors` (
    `id`                 INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `company_name`       VARCHAR(200)    NOT NULL,
    `registration_no`    VARCHAR(100)    NOT NULL UNIQUE,
    `contact_person`     VARCHAR(150)    NOT NULL,
    `email`              VARCHAR(191)    NOT NULL,
    `phone_number`       VARCHAR(30)     NOT NULL,
    `address`            TEXT            NOT NULL,
    `performance_rating` DECIMAL(3,1)    NOT NULL DEFAULT 0.0 COMMENT 'Rating out of 5.0',
    `created_at`         TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: projects
-- ============================================================
CREATE TABLE IF NOT EXISTS `projects` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `project_title` VARCHAR(255)    NOT NULL,
    `client_agency` VARCHAR(200)    NOT NULL,
    `start_date`    DATE            NOT NULL,
    `end_date`      DATE            NOT NULL,
    `budget`        DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    `status`        ENUM('Planning','Active','On Hold','Completed','Cancelled') NOT NULL DEFAULT 'Planning',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: contractor_projects (Junction / Linking Table)
-- ============================================================
CREATE TABLE IF NOT EXISTS `contractor_projects` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `contractor_id`  INT UNSIGNED    NOT NULL,
    `project_id`     INT UNSIGNED    NOT NULL,
    `role_assigned`  VARCHAR(150)    NOT NULL,
    `contract_value` DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    `remarks`        TEXT            NULL,
    `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_contractor_project` (`contractor_id`, `project_id`),
    CONSTRAINT `fk_cp_contractor`
        FOREIGN KEY (`contractor_id`) REFERENCES `contractors`(`id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_cp_project`
        FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- SEED DATA
-- ============================================================

-- Users (passwords are bcrypt hashes of "password123")
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Ahmad Firdaus bin Kamarudin',   'firdaus@mkrhartamas.com.my',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHpC3bBgS', 'Admin'),
('Muhammad Khairulhafiz bin Azmi','khairulhafiz@mkrhartamas.com.my','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHpC3bBgS', 'Procurement_Staff'),
('Nurul Ain binti Roslan',        'ain@mkrhartamas.com.my',        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHpC3bBgS', 'Project_Manager'),
('Hazwan bin Othman',             'hazwan@mkrhartamas.com.my',     '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHpC3bBgS', 'Project_Manager'),
('Syafiqah binti Musa',           'syafiqah@mkrhartamas.com.my',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHpC3bBgS', 'Procurement_Staff');

-- Suppliers
INSERT INTO `suppliers` (`supplier_name`, `contact_person`, `email`, `phone_number`, `location`) VALUES
('Cytron Technologies Sdn. Bhd.',         'Lim Wei Keat',         'sales@cytron.io',                  '+603-5891-1666',  'Penang, Malaysia'),
('Riverdi Sdn. Bhd. (MY Distributor)',    'Azlan bin Hamid',      'azlan@riverdi-my.com',             '+603-7890-2210',  'Shah Alam, Selangor'),
('Seeedstudio Official MY',               'Tan Chin Huat',        'my-sales@seeed.io',                '+603-6200-1188',  'Kuala Lumpur, Malaysia'),
('Sensirion AG (Asia Pacific)',           'Kevin Ng',             'ap.sales@sensirion.com',           '+65-6272-3018',   'Singapore'),
('Agrilink Automation Sdn. Bhd.',         'Mohd Radzuan bin Said','radzuan@agrilink.com.my',          '+604-5551-2030',  'Alor Setar, Kedah'),
('Eltronis Sdn. Bhd.',                   'Faizal bin Ramli',     'faizal@eltronis.com.my',           '+603-5121-8899',  'Subang Jaya, Selangor'),
('Moxa Technologies (MY)',               'Raymond Cheah',        'raymond.cheah@moxa.com',           '+603-2771-8088',  'Kuala Lumpur, Malaysia'),
('AgroMech Engineering Sdn. Bhd.',       'Hafizuddin bin Yusof', 'hafizuddin@agromech.com.my',       '+609-7441-3322',  'Kota Bharu, Kelantan'),
('Digi-Farms Supplies Sdn. Bhd.',        'Salwani binti Idris',  'salwani@digifarms.my',             '+607-3552-7711',  'Johor Bahru, Johor'),
('Malaysian Agricultural Supplies Co.',  'Norzaidi bin Ahmad',   'norzaidi@mascosupplies.com.my',    '+603-9102-5566',  'Putrajaya, Malaysia');

-- Hardware Items
INSERT INTO `hardware_items` (`supplier_id`, `item_name`, `category`, `specifications`, `unit_cost`, `lead_time_days`) VALUES
(4,  'SHT40 Temperature & Humidity Sensor',        'Sensor',          'Accuracy: +-0.2C / +-1.8% RH; I2C; 3.3V; -40 to 125C range; IP40', 85.00,  14),
(3,  'Grove Soil Moisture Sensor v2',              'Sensor',          'Analog output 0-3.3V; capacitive; stainless steel probe; 3.3-5V supply', 42.50,  10),
(4,  'SCD41 CO2 & Climate Sensor Module',         'Sensor',          'CO2: 400-5000ppm +-50ppm; I2C; 3.3V; low-power PAS technology',  310.00, 21),
(1,  'pH Sensor Kit (Analog)',                    'Sensor',          'Range: 0-14 pH; analog BNC output; 3.3/5V; operating temp 0-60C', 120.00,  7),
(5,  'EC (Electrical Conductivity) Sensor Probe', 'Sensor',          'Range: 0-20 mS/cm; RS-485 Modbus RTU; 5-30V; IP68 rated',        285.00, 14),
(5,  'Water Flow Sensor YF-S201',                 'Sensor',          '1-30 L/min; pulse output 450 pulses/L; G1/2 thread; 5-18V DC',   38.00,   7),
(3,  'Grove Ultrasonic Level Sensor',             'Sensor',          'Range: 2-400 cm; single GPIO trigger; 5V; resolution 1cm',        55.00,  10),
(9,  'NPK Soil Nutrient Sensor (RS-485)',          'Sensor',          'Measures N/P/K simultaneously; RS-485 Modbus; IP68; 12-24V',     420.00, 21),
(1,  'Cytron Maker Pi RP2040',                    'Microcontroller', 'RP2040 dual-core 133MHz; 264KB SRAM; 2MB flash; Grove connectors; MicroPython/C++', 88.00,  5),
(1,  'ESP32-S3 DevKit (Cytron)',                  'Microcontroller', 'Xtensa LX7 dual-core 240MHz; 512KB SRAM; WiFi+BT5; 4MB flash; USB-OTG', 65.00,  5),
(3,  'Seeed XIAO ESP32C3',                        'Microcontroller', 'RISC-V 160MHz; WiFi+BLE; 400KB SRAM; 4MB flash; ultra-compact 21x17.5mm', 45.00,  7),
(3,  'Seeed Studio reTerminal DM',                'Microcontroller', 'CM4 4GB RAM; 5 inch capacitive touchscreen; PoE; RS-485; DIN-rail industrial HMI', 1850.00,28),
(2,  'Arduino Mega 2560 Rev3',                    'Microcontroller', 'ATmega2560 16MHz; 256KB flash; 54 digital I/O; 16 analog; UART/SPI/I2C', 130.00,  7),
(6,  'Raspberry Pi 4B 4GB RAM',                   'Microcontroller', 'Cortex-A72 1.5GHz quad-core; 4GB LPDDR4; 2xUSB3; HDMI; WiFi+BT5; PoE', 380.00, 14),
(5,  'Dosatron D14MZ2 Fertilizer Injector',       'Fertigation',     '14 L/hr; ratio 1:500-1:50; no electricity; food-grade polypropylene body',   680.00, 21),
(5,  'Peristaltic Dosing Pump 12V DC (x4 channel)','Fertigation',    '4-channel; 0-1000mL/min per channel; acid/base resistant tubing; PWM control', 320.00, 14),
(8,  'Solenoid Valve 3/4 inch 12V DC',            'Fertigation',     'Normally closed; 3/4 inch BSP; brass body; 10-100 PSI; IP65 rated',              75.00,   7),
(9,  'AgriDrip Smart Irrigation Controller',      'Fertigation',     '8-zone; WiFi+MQTT; 12V; mobile app; rain sensor bypass; IP66 enclosure',     1200.00,21),
(10, 'Fertilizer Storage Tank 500L (PE)',          'Fertigation',     'UV-stabilised HDPE; food-grade; bottom outlet 2 inch BSP; lid access port',      550.00, 14),
(7,  'Moxa AWK-1137C Industrial WiFi AP/Client',  'Network',         '802.11a/b/g/n; 300Mbps; -25 to 60C; IP30; DIN-rail; 2xSMA antenna', 2100.00, 21),
(7,  'Moxa NPort 5150A Serial-to-Ethernet',       'Network',         '1-port RS-232/422/485; 10/100BaseT; DIN-rail; -10 to 60C; IECEx',   950.00, 14),
(3,  'LoRa-E5 Module (868/915MHz)',               'Network',         'STM32WLE5JC; LoRaWAN Class A/B/C; -135dBm sensitivity; SMD; 3.3V', 85.00,  10),
(6,  '4G LTE Cat-4 Router (Industrial)',          'Network',         'Dual SIM failover; VPN; RS-232/485; WiFi AP; -20 to 70C; DIN-rail', 1450.00, 21),
(7,  'Managed PoE Switch 8-Port (Gigabit)',       'Network',         '8xRJ45 GbE PoE+; 2xSFP uplink; 130W budget; VLAN; SNMP; IP30',  3200.00, 28),
(8,  'Autonomous Field Survey Drone (Agri)',      'Machinery',       'Multispectral 5-band camera; 25min flight; RTK GPS; IP43; 2.3kg MTOW', 28500.00, 45),
(8,  'Variable Rate Sprayer Attachment (Tractor)','Machinery',       '600L tank; GPS-linked nozzle control; CAN-bus compatible; boom 12m', 18900.00, 35),
(10, 'Mini Tiller / Power Cultivator 7HP',        'Machinery',       'Honda GX210; tilling width 60cm; depth 20cm; weight 98kg; foldable handle', 4800.00, 21),
(9,  'Automated Greenhouse Ventilation Fan 36 inch','Machinery',     '36 inch blade; 3-phase 415V; airflow 15000 CFM; thermostat control; IP54', 2200.00, 14),
(10, 'Portable Water Pump 2 inch Centrifugal',    'Machinery',       'Honda WB20; 33m head; 650 L/min max; petrol; self-priming; 26kg',  2100.00, 10);

-- Contractors
INSERT INTO `contractors` (`company_name`, `registration_no`, `contact_person`, `email`, `phone_number`, `address`, `performance_rating`) VALUES
('TechFarm Solutions Sdn. Bhd.',        '1054321-K', 'Razali bin Mahfuz',     'razali@techfarmsolutions.com.my',   '+603-8921-4433', 'No. 12, Jalan P/7, Seksyen 13, 43650 Bandar Baru Bangi, Selangor', 4.7),
('AgriSmart Engineering Sdn. Bhd.',     '1128456-U', 'Dr. Suhaimi bin Yacob', 'suhaimi@agrismart.com.my',          '+604-7221-8810', 'Lot 22, Jalan Semarak, 05000 Alor Setar, Kedah',                   4.5),
('Green Nexus Technologies Sdn. Bhd.',  '987543-T',  'Lim Boon Heng',         'boonheng@greennexus.my',            '+603-5565-2200', 'Unit 9-1, Menara Avenue, Jalan Ampang, 50450 Kuala Lumpur',        4.2),
('Precision Agri Systems Sdn. Bhd.',    '1205678-W', 'Mohd Nadzri bin Hamid', 'nadzri@precisionagri.com.my',       '+609-5661-3388', 'No. 5, Lorong MADA 3, 17000 Pendang, Kedah',                      4.8),
('IoTerra Sdn. Bhd.',                   '1340912-A', 'Sarah Izzati binti Nor', 'sarah@ioterra.my',                 '+603-2301-7744', 'Level 18, Menara TH, Jalan Tun Razak, 50400 Kuala Lumpur',        3.9),
('BioField Automation Sdn. Bhd.',       '1089234-H', 'Khairizal bin Ghazali', 'khairizal@biofield.com.my',         '+607-2244-5566', 'PLO 211, Jalan Tanjung Puteri, 81300 Skudai, Johor',               4.6),
('MADA Tech Ventures Sdn. Bhd.',        '766123-P',  'Noorazman bin Salleh',  'noorazman@madatech.com.my',         '+604-7713-0066', 'Wisma MADA, Jalan Stadium, 05000 Alor Setar, Kedah',              4.9),
('SynAgro Systems Sdn. Bhd.',           '1415988-D', 'Wan Fadzillah bin Wan Ismail','wfadzillah@synagro.my',       '+605-3312-8877', 'No. 33, Jalan Raja Musa, 35000 Teluk Intan, Perak',               4.3),
('Integrated Farm Informatics Sdn.Bhd.','1502341-M', 'Johari bin Leman',       'johari@ifi.com.my',               '+603-8311-2244', 'No. 7A, Jalan Pertanian 3, 43400 UPM Serdang, Selangor',          4.1),
('NexSense Agritech Sdn. Bhd.',         '1621099-Z', 'Tengku Amirul bin Raja', 'amirul@nexsense.com.my',          '+609-4477-1133', 'No. 88, Jalan Taman Mardi, 26090 Kuantan, Pahang',                4.4);

-- Projects
INSERT INTO `projects` (`project_title`, `client_agency`, `start_date`, `end_date`, `budget`, `status`) VALUES
('MARDI Smart Greenhouse Phase 1 - Fertigation Automation',       'MARDI (Malaysian Agricultural Research and Development Institute)', '2025-01-15', '2025-09-30',  850000.00, 'Completed'),
('MADA Precision Rice Paddy Monitoring System',                    'MADA (Muda Agricultural Development Authority)',                    '2025-03-01', '2026-03-31',  1200000.00,'Active'),
('DOA e-Pertanian Sensor Network Deployment - Sabah',             'Department of Agriculture Malaysia (DOA)',                          '2025-06-01', '2026-06-30',  975000.00, 'Active'),
('FELDA Agritech IoT Integration - Jengka 7',                     'FELDA (Federal Land Development Authority)',                        '2025-09-15', '2026-09-14',  1650000.00,'Active'),
('MARDI Hydroponics NFT System Upgrade - Serdang',                'MARDI (Malaysian Agricultural Research and Development Institute)', '2025-11-01', '2026-04-30',  430000.00, 'Planning'),
('KADA Smart Water Management System',                             'KADA (Kemubu Agricultural Development Authority)',                  '2026-01-15', '2027-01-14',  1100000.00,'Planning'),
('LPP Agropreneurs Digital Farm Platform',                         'LPP (Lembaga Pertubuhan Peladang)',                                 '2024-07-01', '2025-06-30',  560000.00, 'Completed'),
('Agrobank Farm Connectivity Infrastructure - East Coast Cluster', 'Agrobank Malaysia Berhad',                                          '2026-03-01', '2027-03-01',  2200000.00,'Planning'),
('RISDA Smart Rubber Tapping Automation Pilot',                    'RISDA (Rubber Industry Smallholders Development Authority)',        '2025-04-01', '2025-12-31',  385000.00, 'Completed'),
('MADA Phase 2 - Drone Fleet & Variable Rate Application',        'MADA (Muda Agricultural Development Authority)',                    '2026-04-01', '2027-06-30',  3100000.00,'Planning');

-- Contractor-Project Assignments
INSERT INTO `contractor_projects` (`contractor_id`, `project_id`, `role_assigned`, `contract_value`, `remarks`) VALUES
(4,  1, 'Lead System Integrator & Fertigation Specialist', 510000.00, 'Delivered on schedule; excellent documentation'),
(1,  1, 'Hardware Procurement & Field Installation',       280000.00, 'Minor delays due to component shortage; resolved'),
(7,  2, 'Main Contractor - Sensor Network & SCADA',        780000.00, 'MADA preferred vendor; extensive paddy field experience'),
(2,  2, 'Sub-contractor - Agronomy Advisory & Calibration',185000.00, 'Provided technical validation for sensor placement'),
(3,  3, 'IoT Gateway & Network Infrastructure',            420000.00, 'Sabah DOA pilot; remote location logistics managed well'),
(5,  3, 'Cloud Dashboard & Data Analytics Platform',       310000.00, 'Delivered MVP; requested additional features pending'),
(6,  4, 'FELDA Site Coordinator & Installation Team Lead', 720000.00, 'Coordinating 12 sites across Jengka cluster'),
(8,  4, 'Mechanical & Irrigation Works Sub-contractor',    390000.00, 'On-site works proceeding to plan'),
(9,  5, 'Research & Development Partner - Hydroponics',    195000.00, 'UPM Serdang collaboration for NFT system design'),
(1,  5, 'Electronics Integration & Sensor Calibration',    140000.00, 'Early stage; awaiting component delivery'),
(7,  6, 'Prime Contractor - Smart Water System',           690000.00, 'Tender awarded Q1 2026; mobilisation in progress'),
(4,  6, 'Sub-contractor - Pump Automation & Controls',     290000.00, 'Scope includes 48 remote pump stations'),
(3,  7, 'Platform Development & Digital Integration',      320000.00, 'Project completed; handed over to LPP ICT team'),
(10, 8, 'IoT Infrastructure & LoRaWAN Network',            850000.00, 'Covering 3 states; network planning phase'),
(5,  8, 'System Architecture & Cloud Backend',             620000.00, 'AWS Malaysia region deployment'),
(2,  9, 'Rubber Tapping Sensor Integration & Training',    220000.00, 'Pilot at 3 RISDA schemes; satisfactory outcome'),
(6,  9, 'Mechanical Arm & Automation Hardware',            130000.00, 'Prototype fabrication completed'),
(7, 10, 'Prime Contractor - Drone Fleet Operations',      1800000.00, 'MADA Phase 2; fleet of 12 drones planned'),
(8, 10, 'Variable Rate Machinery & Tractor Attachments',   750000.00, 'Sourcing heavy agricultural machinery'),
(4, 10, 'Ground Sensor Network for Variable Rate Input',   380000.00, 'Complements drone data with soil telemetry');