<?php

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


function countTasks($tasksArray, $projectName) {
  $tasksCounter = 0;
  foreach ($tasksArray as $task) {
    if ($task["project"] === $projectName) {
      $tasksCounter++;
    }
  };

  return $tasksCounter;
};

?>
