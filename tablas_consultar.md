Tablas sql consultar para poder crear inserts, updates o delete, ect
CREATE TABLE `sector` (
    `id_sector` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) DEFAULT NULL,
    `superficie` decimal(10, 2) DEFAULT NULL,
    `geometria` geometry NOT NULL,
    `geometria_kml` longtext NOT NULL,
    `foto_url` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `estat_productiu` enum(
        'Repos',
        'Plantat',
        'Productiu',
        'Reconvertit',
        'Abandonat'
    ) DEFAULT 'Plantat',
    PRIMARY KEY (`id_sector`),
    SPATIAL KEY `geometria` (`geometria`)
) ENGINE = InnoDB AUTO_INCREMENT = 13 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci

CREATE TABLE `sector_parcela` (
    `id_sector` int(11) NOT NULL,
    `id_parcela` int(11) NOT NULL,
    PRIMARY KEY (`id_sector`, `id_parcela`),
    KEY `idx_sp_parcela` (`id_parcela`),
    CONSTRAINT `sp_fk_parcela` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`),
    CONSTRAINT `sp_fk_sector` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci

CREATE TABLE `sector_varietat` (
    `id_sector` int(11) NOT NULL,
    `id_varietat` int(11) NOT NULL,
    `id_data` int(11) NOT NULL,
    PRIMARY KEY (
        `id_sector`,
        `id_varietat`,
        `id_data`
    ),
    KEY `id_varietat` (`id_varietat`),
    KEY `id_data` (`id_data`),
    CONSTRAINT `sector_varietat_ibfk_1` FOREIGN KEY (`id_sector`) REFERENCES `sector` (`id_sector`),
    CONSTRAINT `sector_varietat_ibfk_2` FOREIGN KEY (`id_varietat`) REFERENCES `varietat` (`id_varietat`),
    CONSTRAINT `sector_varietat_ibfk_3` FOREIGN KEY (`id_data`) REFERENCES `data` (`id_data`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci

