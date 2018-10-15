<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="<?=make_link("date", false);?>" class="tasks-switch__item <?php if (!isset($_GET["date"])): ?> tasks-switch__item--active<?php endif; ?>">Все задачи</a>
        <a href="<?=make_link("date", "today");?>" class="tasks-switch__item <?php if (isset($_GET["date"]) && $_GET["date"] === "today"): ?> tasks-switch__item--active<?php endif; ?>">Повестка дня</a>
        <a href="<?=make_link("date", "tomorrow");?>" class="tasks-switch__item<?php if (isset($_GET["date"]) && $_GET["date"] === "tomorrow"): ?> tasks-switch__item--active<?php endif; ?>">Завтра</a>
        <a href="<?=make_link("date", "overdue");?>" class="tasks-switch__item <?php if (isset($_GET["date"]) && $_GET["date"] === "overdue"): ?> tasks-switch__item--active<?php endif; ?>">Просроченные</a>
    </nav>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input class="checkbox__input visually-hidden show_completed"
               <?php if ($show_complete_tasks): ?>checked<?php endif; ?>
               type="checkbox">
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>


<!-- Таблица со списком задач -->
<table class="tasks">
    <?php foreach ($tasks as $task): ?> 
      <?php if (!$task["status"] || ($show_complete_tasks && $task["status"])): ?>

        <tr class="tasks__item task

            <?php
              if ($task["status"]): ?> task--completed
            <?php endif; ?>
            <?php
               if (is_important($task["deadline"])): ?> task--important
            <?php endif; ?>">

            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" <?php if ($task["status"]): ?>checked<?php endif; ?> value="<?=$task["id"];?>">
                    <span class="checkbox__text"><?=strip_tags($task["name"]); ?></span>
                </label>
            </td>

            <td class="task__file">
              <?php if (!empty($task["file"])): ?>
                <a class="download-link" href="<?=$task["file"]; ?>"></a>
              <?php endif; ?>
            </td>

            <td class="task__date"><?=$task["deadline"]; ?></td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>

</table>
