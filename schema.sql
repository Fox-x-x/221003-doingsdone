CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;


CREATE TABLE projects (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(128),
  created_by_user INT
);

CREATE TABLE users (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  reg_date  DATE,
  email     VARCHAR(128),
  name      VARCHAR(128),
  password  VARCHAR(32),
  contacts  VARCHAR(256)
);

CREATE TABLE tasks (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  creation_date      DATETIME,
  date_of_completion DATETIME,
  status             TINYINT DEFAULT 0,
  name               VARCHAR(256),
  file               VARCHAR(256),
  deadline           DATETIME,
  created_by_user    INT,
  related_to_proj    INT
);

CREATE INDEX task_name_index ON tasks (name) USING BTREE;
CREATE INDEX task_date_index ON tasks (deadline) USING BTREE;
