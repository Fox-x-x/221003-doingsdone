/* Создаем пользователей */
INSERT INTO users
  SET reg_date = "2018-09-21",
      email = "test1@gmail.com",
      name = "Иван",
      password = "123456",
      contacts = "vk: vk.com/test1";

INSERT INTO users
  SET reg_date = "2018-09-23",
      email = "test2@yandex.ru",
      name = "Василий",
      password = "qwerty",
      contacts = "vk: vk.com/test2";


/* Заполняем таблицу проектов с привязкой к пользователям */
INSERT INTO projects
  SET name = "Входящие",
      created_by_user = 2;

INSERT INTO projects
  SET name = "Учеба",
      created_by_user = 2;

INSERT INTO projects
  SET name = "Работа",
      created_by_user = 3;

INSERT INTO projects
  SET name = "Домашние дела",
      created_by_user = 3;

INSERT INTO projects
  SET name = "Авто",
      created_by_user = 3;


/* Заполняем таблицу с задачами */
INSERT INTO tasks
  SET creation_date      = "2018-09-21 15:08:36",
      date_of_completion = null,
      status             = 0,
      name               = "Собеседование в IT компании",
      file               = "",
      deadline           = "2018-12-01 00:00:00",
      created_by_user    = 3,
      related_to_proj    = 3;

INSERT INTO tasks
  SET creation_date      = "2018-09-21 16:01:17",
      date_of_completion = null,
      status             = 0,
      name               = "Выполнить тестовое задание",
      file               = "",
      deadline           = "2018-09-29 00:00:00",
      created_by_user    = 3,
      related_to_proj    = 3;

INSERT INTO tasks
  SET creation_date      = "2018-09-21 10:06:01",
      date_of_completion = null,
      status             = 1,
      name               = "Сделать задание первого раздела",
      file               = "",
      deadline           = "2018-12-21 00:00:00",
      created_by_user    = 3,
      related_to_proj    = 2;

INSERT INTO tasks
  SET creation_date      = "2018-09-22 11:36:03",
      date_of_completion = null,
      status             = 0,
      name               = "Встреча с другом",
      file               = "",
      deadline           = "2018-09-20 00:00:00",
      created_by_user    = 2,
      related_to_proj    = 1;

INSERT INTO tasks
  SET creation_date      = "2018-09-23 17:22:59",
      date_of_completion = null,
      status             = 0,
      name               = "Купить корм для кота",
      file               = "",
      deadline           = null,
      created_by_user    = 2,
      related_to_proj    = 4;

INSERT INTO tasks
  SET creation_date      = "2018-09-24 20:26:43",
      date_of_completion = null,
      status             = 0,
      name               = "Заказать пиццу",
      file               = "",
      deadline           = null,
      created_by_user    = 2,
      related_to_proj    = 4;


/*************************************

Запросы на получение информации из БД:

 */


/*
получить список из всех проектов для одного пользователя
Для примера возьмем пользователя с id = 2
 */
SELECT name
FROM projects
WHERE created_by_user = 2;

/*
получить список из всех задач для одного проекта
Пусть будет проект с id = 3
 */
 SELECT name
 FROM tasks
 WHERE related_to_proj = 3;

 /*
 пометить задачу как выполненную
 */
 UPDATE tasks
 SET status = 1
 WHERE id = 5;

 /*
 получить все задачи для завтрашнего дня
 */
SELECT name
FROM tasks
WHERE deadline = CURDATE() + INTERVAL 1 DAY;

/*
обновить название задачи по её идентификатору
*/
UPDATE tasks
SET name = "новое имя"
WHERE id = 5;
