<?php

// Шаблонизатор
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require_once $name;

    $result = ob_get_clean();

return $result;
};


// Считаем кол-во задач для определенного проекта
function count_tasks($tasks_array, $project_id) {
  $tasksCounter = 0;
  foreach ($tasks_array as $task) {
    if ($task["related_to_proj"] == $project_id) {
      $tasksCounter++;
    }
  };

  return $tasksCounter;
};


// Определяем является ли задача важной (до дэдлайна осталось менее 24 часов)
function is_important($task) {
  date_default_timezone_set("Europe/Kaliningrad");

  $result = false;

  if (strtotime($task)) {
    $endDate = strtotime($task);
    $now = time();

    $diff = $endDate - $now;
    $hoursLeft = floor($diff / 3600);

    if ($hoursLeft <= 24) {
      $result = true;
    }
  }

  return $result;
};


// Отправка запроса в БД и получение ответа в виде массива
function sel_from_db_to_array($connect, $request) {

  $result_array = null;

  if ($connect == false) {
     print("Ошибка подключения: " . mysqli_connect_error());
  }
  else {
     mysqli_set_charset($connect, "utf8");

     // Выполняем запрос и получаем результат
     $result = mysqli_query($connect, $request);

     // запрос выполнен успешно
     if ($result) {
         // получаем все категории в виде двумерного массива
         $result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
     }
     else {
         // получить текст последней ошибки
         $error = mysqli_error($connect);
         print("Ошибка при выполнении запроса к БД: " . $error);
     }
  }

  return $result_array;
};

?>
