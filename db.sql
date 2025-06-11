CREATE TABLE `ticketsdb`.`users` (
    `id` INT(200) NOT NULL AUTO_INCREMENT , 
    `fname` VARCHAR(30) NOT NULL , 
    `lname` VARCHAR(30) NOT NULL , 
    `email` VARCHAR(100) NOT NULL , 
    `username` VARCHAR(30) NOT NULL , 
    `password` VARCHAR(100) NOT NULL , 
    `birthday` DATE NOT NULL , 
    `sex` VARCHAR, 
    `phonenumber` VARCHAR(15) NOT NULL , 
    `time_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `ticketsdb`.`bookings` (
    `id` INT(255) NOT NULL AUTO_INCREMENT ,
    `userid` INT(255) NOT NULL ,
    `flight_id` INT(255) NOT NULL ,
    `destination` VARCHAR(255) NOT NULL , 
    `date` DATE NOT NULL , 
    `time` DATETIME(6) NOT NULL , 
    `class` VARCHAR(255) NOT NULL , 
    `passengers` INT(255) NOT NULL , 
    `adults` INT(255) NOT NULL , 
    `children` INT(255) NOT NULL , 
    `infants` INT(255) NOT NULL , 
    `price` INT(255) NOT NULL , 
    `time_created` TIMESTAMP(6) NOT NULL , 
    PRIMARY KEY (`id`)) ENGINE = InnoDB;

    CREATE TABLE `ticketsdb`.`admin` (
        `id` INT(255) NOT NULL AUTO_INCREMENT ,
        `username` VARCHAR(255) NOT NULL ,
        `password` VARCHAR(255) NOT NULL , 
        PRIMARY KEY (`id`)) ENGINE = InnoDB;

    INSERT INTO `admin` (
        `id`,
        `username`, 
        `password`) 
        VALUES (
        '1', 
        'admin', 
        'admin123!');

    CREATE TABLE `ticketsdb`.`flights` (
        `id` INT(255) NOT NULL ,
        `destination` VARCHAR(255) NOT NULL ,
        `date` DATE NOT NULL ,
        `time` TIME(6) NOT NULL , 
        `first_class_seats` INT NOT NULL ,
        `business_seats` INT NOT NULL ,
        `economy_seats` INT NOT NULL , 
        PRIMARY KEY (`id`)) ENGINE = InnoDB;