-- Buat Database
CREATE DATABASE IF NOT EXISTS digiternak_test;

-- Pilih Database
USE digiternak_test;

-- Tabel User
DROP TABLE IF EXISTS user;
CREATE TABLE IF NOT EXISTS user (
    id INT PRIMARY KEY NOT NULL,
    person_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    auth_key VARCHAR(32) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    password_reset_token VARCHAR(255),
    email VARCHAR(255),
    status SMALLINT(6),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    verification_token VARCHAR(255),
    role_id INT NOT NULL,
    token_jwt VARCHAR(512)
);

-- Tabel Person
DROP TABLE IF EXISTS person;

CREATE TABLE IF NOT EXISTS person (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nik VARCHAR(255) UNIQUE NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    middle_name VARCHAR(255),
    last_name VARCHAR(255),
    birthdate DATE,
    gender_id INT,
    address_id INT,
    contact_id INT,
    is_deleted TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Tabel Gender
DROP TABLE IF EXISTS gender;

CREATE TABLE IF NOT EXISTS gender (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Address
DROP TABLE IF EXISTS address;
CREATE TABLE IF NOT EXISTS address (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    person_id INT,
    province_id INT,
    city_id INT,
    district_id INT,
    sub_district_id INT,
    detail_address VARCHAR(500),
    landmark_info VARCHAR(255),
    post_code INT,
    type_of_home_id INT,
    is_deleted TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Tabel Province
DROP TABLE IF EXISTS province;
CREATE TABLE IF NOT EXISTS province (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel City
DROP TABLE IF EXISTS city;
CREATE TABLE IF NOT EXISTS city (
    id INT PRIMARY KEY NOT NULL,
    province_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel District
DROP TABLE IF EXISTS district;
CREATE TABLE IF NOT EXISTS district (
    id INT PRIMARY KEY NOT NULL,
    city_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Sub District
DROP TABLE IF EXISTS sub_district;
CREATE TABLE IF NOT EXISTS sub_district (
    id INT PRIMARY KEY NOT NULL,
    district_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Post Code
DROP TABLE IF EXISTS post_code;
CREATE TABLE IF NOT EXISTS post_code (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    province VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    district VARCHAR(255) NOT NULL,
    sub_district VARCHAR(255) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Tabel Type of Home
DROP TABLE IF EXISTS type_of_home;
CREATE TABLE IF NOT EXISTS type_of_home (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Contact
DROP TABLE IF EXISTS contact;
CREATE TABLE IF NOT EXISTS contact (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    person_id INT NOT NULL,
    phone_number VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Livestock
DROP TABLE IF EXISTS livestock;
CREATE TABLE IF NOT EXISTS livestock (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    eid VARCHAR(255) NOT NULL UNIQUE,
    vid VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    birthdate DATE,
    type_of_livestock_id INT,
    breed_of_livestock_id INT,
    maintenance_id INT,
    source_id INT,
    ownership_status_id INT,
    reproduction_id INT,
    gender VARCHAR(255),
    age VARCHAR(255),
    chest_size DECIMAL(18,1),
    body_weight DECIMAL(18,1),
    health VARCHAR(255),
    bcs INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Tabel Type of Livestock
DROP TABLE IF EXISTS type_of_livestock;
CREATE TABLE IF NOT EXISTS type_of_livestock (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Breed of Livestock
DROP TABLE IF EXISTS breed_of_livestock;
CREATE TABLE IF NOT EXISTS breed_of_livestock (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Maintenance
DROP TABLE IF EXISTS maintenance;
CREATE TABLE IF NOT EXISTS maintenance (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Source
DROP TABLE IF EXISTS source;
CREATE TABLE IF NOT EXISTS source (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Ownership Status
DROP TABLE IF EXISTS ownership_status;
CREATE TABLE IF NOT EXISTS ownership_status (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Child
DROP TABLE IF EXISTS child;
CREATE TABLE IF NOT EXISTS child (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    livestock_id INT,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Reproduction
DROP TABLE IF EXISTS reproduction;
CREATE TABLE IF NOT EXISTS reproduction (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Purpose
DROP TABLE IF EXISTS purpose;
CREATE TABLE IF NOT EXISTS purpose (
    id INT PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel User Role
CREATE TABLE IF NOT EXISTS user_role (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Tambahkan Foreign Key Constraint pada Tabel Address
ALTER TABLE address
ADD CONSTRAINT fk_person_address FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_province_address FOREIGN KEY (province_id) REFERENCES province(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_city_address FOREIGN KEY (city_id) REFERENCES city(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_district_address FOREIGN KEY (district_id) REFERENCES district(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_sub_district_address FOREIGN KEY (sub_district_id) REFERENCES sub_district(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_post_code_address FOREIGN KEY (post_code) REFERENCES post_code(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_type_of_home_address FOREIGN KEY (type_of_home_id) REFERENCES type_of_home(id) ON DELETE CASCADE;

-- Tambahkan Foreign Key Constraint pada Tabel City
ALTER TABLE city
ADD CONSTRAINT fk_province FOREIGN KEY (province_id) REFERENCES province(id) ON DELETE CASCADE;

-- Tambahkan Foreign Key Constraint pada Tabel District
ALTER TABLE district
ADD CONSTRAINT fk_city FOREIGN KEY (city_id) REFERENCES city(id) ON DELETE CASCADE;

-- Tambahkan Foreign Key Constraint pada Tabel Sub District
ALTER TABLE sub_district
ADD CONSTRAINT fk_district FOREIGN KEY (district_id) REFERENCES district(id) ON DELETE CASCADE;

-- Tambahkan Foreign Key Constraint pada Tabel Contact
ALTER TABLE contact
ADD CONSTRAINT fk_person_contact FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE CASCADE;

-- Tambahkan Foreign Key Constraint pada Tabel Livestock
ALTER TABLE livestock
ADD CONSTRAINT fk_type_of_livestock FOREIGN KEY (type_of_livestock_id) REFERENCES type_of_livestock(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_breed_of_livestock FOREIGN KEY (breed_of_livestock_id) REFERENCES breed_of_livestock(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_maintenance FOREIGN KEY (maintenance_id) REFERENCES maintenance(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_source FOREIGN KEY (source_id) REFERENCES source(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_ownership_status FOREIGN KEY (ownership_status_id) REFERENCES ownership_status(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_reproduction FOREIGN KEY (reproduction_id) REFERENCES reproduction(id) ON DELETE CASCADE;

-- Tambahkan Foreign Key Constraint pada Tabel Child
ALTER TABLE child
ADD CONSTRAINT fk_livestock_child FOREIGN KEY (livestock_id) REFERENCES livestock(id) ON DELETE CASCADE;

-- Tambahkan Foreign Key Constraint pada Tabel User
ALTER TABLE user
ADD CONSTRAINT fk_person_user FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_role_user FOREIGN KEY (role_id) REFERENCES user_role(id) ON DELETE CASCADE;