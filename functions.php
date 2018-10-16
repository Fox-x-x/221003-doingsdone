<?php


function get_connect_db() {
    return mysqli_connect("localhost", "root", "root", "doingsdone");
}

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




// Запись в БД
function insert_into_db($connect, $request) {

  $result = true;

  if ($connect == false) {
     print("Ошибка подключения: " . mysqli_connect_error());
     $result = false;
  }
  else {
     mysqli_set_charset($connect, "utf8");

     // Выполняем запрос и получаем результат
     $mysq_result = mysqli_query($connect, $request);

     // если запрос НЕ выполнился
     if (!$mysq_result) {

         $error = mysqli_error($connect);
         print("Ошибка при выполнении запроса к БД: " . $error);
         $result = false;
     }

  }

  return $result;
};


// проверка существоавания пользователя с заданным email в БД
function check_email_for_existence($email, $connect) {

  $result = false;

  if ($connect == false) {
          print("Ошибка подключения: " . mysqli_connect_error());
    }
    else {
          mysqli_set_charset($connect, "utf8");

          $request = "SELECT id, email FROM users WHERE email = '$email' ";
          // Выполняем запрос и получаем результат
          $mysq_result = mysqli_query($connect, $request);

          // если запрос НЕ выполнился
          if (!$mysq_result) {

              $error = mysqli_error($connect);
              print("Ошибка при выполнении запроса к БД: " . $error);
          }

          // если запрос выполнился и вернул ненулевое кол-во строк, то юзер существует
          if ( $mysq_result->num_rows > 0 ) {
            $result = true;
          }
    }

    return $result;

}



// валидация формы регистрации
function validate_reg_form($connect, $email, $required, $user) {

  $errors = [];

  // проверяем email на валидность и записываем ошибку + проверяем на существование такоего email в базе
  if ( (!filter_var($email, FILTER_VALIDATE_EMAIL)) && (!empty($email)) ) {

    $errors["email"] = "Неверный формат email адреса";

  } else if ((filter_var($email, FILTER_VALIDATE_EMAIL)) && (!empty($email))) {

    if (check_email_for_existence($email, $connect)) {
      $errors["email"] = "Юзер с таким email уже зарегистрирован";
    }
  }

  // проверяем все ли поля заполнены
  foreach ($required as $key) {
    if (empty($user[$key])) {
        $errors[$key] = "Это поле надо заполнить";
    }
  }

  return $errors;
}


// проверка пароля
function check_password($user, $connect) {

  $result = false;

  if ($connect == false) {
    print("Ошибка подключения: " . mysqli_connect_error());

  } else {
    mysqli_set_charset($connect, "utf8");

    $email = $user["email"];
    $request = "SELECT * FROM users WHERE email = '$email' ";

    // Выполняем запрос и получаем результат
    $mysq_result = mysqli_query($connect, $request);
    $result_array = mysqli_fetch_array($mysq_result, MYSQLI_ASSOC);


    // если запрос НЕ выполнился
    if (!$mysq_result) {

      $error = mysqli_error($connect);
      print("Ошибка при выполнении запроса к БД: " . $error);

    } else {

      if ( password_verify($user["password"], $result_array["password"]) ) {
        $result = true;
      }
    }

  }

  return $result;
}


// возвращает аутентифицированного юзера из БД
function get_auth_user($connect, $user) {

  $result = [];

  if ($connect == false) {
    print("Ошибка подключения: " . mysqli_connect_error());

  } else {
    mysqli_set_charset($connect, "utf8");

    $email = $user["email"];
    $request = "SELECT * FROM users WHERE email = '$email' ";

    // Выполняем запрос и получаем результат
    $mysq_result = mysqli_query($connect, $request);


    // если запрос НЕ выполнился
    if (!$mysq_result) {

      $error = mysqli_error($connect);
      print("Ошибка при выполнении запроса к БД: " . $error);

    } else {

      $result = mysqli_fetch_array($mysq_result, MYSQLI_ASSOC);

    }

  }

  return $result;
}


// валидация формы аутентификации
function validate_auth_form($connect, $required, $user) {

  $errors = [];

  // проверяем email на валидность и записываем ошибку + проверяем на существование такоего email в базе
  if ( (!empty($user["password"])) && (!empty($user["email"])) ) {

    if (!check_email_for_existence($user["email"], $connect)) {
      $errors["email"] = "Неверный email";
    }

    if ( !check_password($user, $connect)) {
      $errors["password"] = "Неверный пароль";
    }


  } else {

    // проверяем все ли поля заполнены
    foreach ($required as $key) {
      if (empty($user[$key])) {
          $errors[$key] = "Это поле надо заполнить";
      }
    }
  }

  return $errors;
}


// создаем ссылку с соответствующими параметрами для блока сортировки задач
function make_link($parameter, $value) {
    $url = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url_parts = parse_url($url);
    $result = 'http://'.$_SERVER['HTTP_HOST'].'/';
    if (isset($url_parts['query'])) {
        parse_str($url_parts['query'], $params);
        if ($value) {
            $params[$parameter] = $value;
        }
        else {
            unset($params[$parameter]);
        }
        $url_parts['query'] = http_build_query($params);
        if (!empty($url_parts['query'])) {
            $result = '?' . $url_parts['query'];
        }
    }
    elseif ($value) {
        $result = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?'.$parameter.'=' . $value;
    }
    return $result;
}


// проверяет существование проекта в БД (нужно для добавления нового проекта)
function check_project_for_existence($connect, $name, $user_id) {

  $result = false;

  if ($connect == false) {
    print("Ошибка подключения: " . mysqli_connect_error());

  } else {
    mysqli_set_charset($connect, "utf8");

    $request = "SELECT * FROM projects WHERE name = '$name' AND created_by_user = '$user_id' ";

    // Выполняем запрос и получаем результат
    $mysq_result = mysqli_query($connect, $request);


    // если запрос НЕ выполнился
    if (!$mysq_result) {

      $error = mysqli_error($connect);
      print("Ошибка при выполнении запроса к БД: " . $error);

    }

    // если запрос выполнился и вернул ненулевое кол-во строк, то проект существует
    if ( $mysq_result->num_rows > 0 ) {
      $result = true;
    }

  }


  return $result;
}


// валидация формы добавления нового проекта
function validate_project_form($connect, $project, $user_id) {

  $errors = [];

  if ( !empty($project["name"]) ) {
    if (check_project_for_existence($connect, $project["name"], $user_id)) {
      $errors["name"] = "Проект с таким именем уже существует";
    }
  } else {
    $errors["name"] = "Это поле надо заполнить";
  }

  return $errors;
}

?>
