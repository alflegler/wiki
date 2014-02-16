
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- page
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `page`;

CREATE TABLE `page`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `parent_id` INTEGER,
    `parentPath` VARCHAR(255),
    `pageName` VARCHAR(255),
    `title` VARCHAR(255),
    `text` TEXT,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `pagePathIndex` (`parentPath`, `pageName`),
    INDEX `FI_ent` (`parent_id`),
    CONSTRAINT `parent`
        FOREIGN KEY (`parent_id`)
        REFERENCES `page` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_general_ci';

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
