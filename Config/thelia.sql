
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- gallery
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gallery`;

CREATE TABLE `gallery`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `visible` TINYINT NOT NULL,
    `position` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET='utf8';

-- ---------------------------------------------------------------------
-- gallery_image
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_image`;

CREATE TABLE `gallery_image`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `gallery_id` INTEGER NOT NULL,
    `file` VARCHAR(255),
    `type` VARCHAR(255) DEFAULT '' NOT NULL,
    `type_id` INTEGER DEFAULT 0 NOT NULL,
    `subtype_id` INTEGER DEFAULT 0 NOT NULL,
    `url` VARCHAR(255),
    `position` INTEGER NOT NULL,
    `visible` TINYINT NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `gallery_image_FI_1` (`gallery_id`),
    CONSTRAINT `gallery_image_FK_1`
        FOREIGN KEY (`gallery_id`)
        REFERENCES `gallery` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET='utf8';

-- ---------------------------------------------------------------------
-- gallery_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_i18n`;

CREATE TABLE `gallery_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT NOT NULL,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `gallery_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `gallery` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- gallery_image_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_image_i18n`;

CREATE TABLE `gallery_image_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT NOT NULL,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `gallery_image_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `gallery_image` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
