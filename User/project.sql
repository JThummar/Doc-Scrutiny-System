-- first create the database named `project` into the server and run the following queries
CREATE TABLE `project`.`user_information` ( `id` INT NOT NULL AUTO_INCREMENT , `first_name` VARCHAR(40) NOT NULL , `middle_name` VARCHAR(40) NOT NULL , `last_name` VARCHAR(40) NOT NULL , `birth_date` DATE NOT NULL , `phone` VARCHAR(10) NOT NULL , `email` VARCHAR(255) NOT NULL UNIQUE , `password` VARCHAR(255) NOT NULL , `token` VARCHAR(255) NOT NULL , `email_verification` BOOLEAN NOT NULL , `mobile_verification` BOOLEAN NOT NULL , `photo_name` VARCHAR(255) , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `project`.`office_employee_information` ( `id` INT NOT NULL AUTO_INCREMENT , `first_name` VARCHAR(40) NOT NULL , `middle_name` VARCHAR(40) NOT NULL , `last_name` VARCHAR(40) NOT NULL , `birth_date` DATE NOT NULL , `phone` VARCHAR(10) NOT NULL , `email` VARCHAR(255) NOT NULL UNIQUE , `password` VARCHAR(255) NOT NULL , `token` VARCHAR(255) NOT NULL , `email_verification` BOOLEAN NOT NULL , `mobile_verification` BOOLEAN NOT NULL , `photo_name` VARCHAR(255) , `verified` BOOLEAN NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `project`.`documents` ( `id` INT NOT NULL AUTO_INCREMENT , `document_name` VARCHAR(40) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `documents` ADD `document_description` VARCHAR(255) NULL AFTER `document_name`;

CREATE TABLE `project`.`proofs` ( `id` INT NOT NULL AUTO_INCREMENT , `proof_name` VARCHAR(40) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `proofs` ADD `proof_description` VARCHAR(255) NULL AFTER `proof_name`;

-- at  13 -11 
CREATE TABLE `project`.`requests` ( `request_id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `document_id` INT NOT NULL , `time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `status` VARCHAR(255) NOT NULL , `officer_id` INT NOT NULL , PRIMARY KEY (`request_id`)) ENGINE = InnoDB;
ALTER TABLE `requests` CHANGE `officer_id` `officer_id` INT(11) NULL;
ALTER TABLE `office_employee_information` ADD `master_user` TINYINT NOT NULL DEFAULT '0' AFTER `verified`;

ALTER TABLE `documents` ADD `active` VARCHAR(255) NOT NULL DEFAULT 'enable' AFTER `document_description`;
ALTER TABLE `office_employee_information` CHANGE `verified` `verified` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `office_employee_information` CHANGE `email_verification` `email_verification` TINYINT(1) NOT NULL DEFAULT '0';
CREATE TABLE `project`.`password_recover_hash` ( `name` VARCHAR(255) NOT NULL , `hash` VARCHAR(32) NOT NULL , PRIMARY KEY (`name`)) ENGINE = InnoDB;
