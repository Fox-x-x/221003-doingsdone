CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;


CREATE TABLE projects (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  name            CHAR(128),
  created_by_user INT
);

CREATE TABLE users (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  reg_date  DATE,
  email     CHAR(128),
  name      CHAR(128),
  password  CHAR(32),
  contacts  CHAR
);

CREATE TABLE tasks (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  creation_date      DATETIME,
  date_of_completion DATETIME,
  status             TINYINT DEFAULT 0,
  name               CHAR,
  file               CHAR,
  deadline           DATETIME,
  created_by_user    INT,
  related_to_proj    INT
);

CREATE INDEX task_name_index ON tasks (name) USING BTREE;
