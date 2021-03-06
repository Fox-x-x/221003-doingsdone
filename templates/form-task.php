      <main class="content__main">
        <h2 class="content__main-heading">Добавление задачи</h2>

        <form class="form" enctype="multipart/form-data" action="/add.php" method="post">
          <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>
            <input class="form__input <?php if (isset($errors["name"])): ?>  form__input--error<?php endif; ?>" type="text" name="name" id="name" value="<?=strip_tags($added_task["name"]); ?>" placeholder="Введите название">

            <?php if (isset($errors["name"])): ?>
              <p class="form__message">
                <span class="error-message">
                  <?php echo $errors["name"]; ?>
                </span>
              </p>
            <?php endif; ?>

          </div>

          <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select<?php if (isset($errors["project"])): ?>  form__input--error<?php endif; ?>" name="project" id="project">
              <?php foreach ($projects as $project): ?> 
                <option value="<?php echo strip_tags($project["id"]); ?>"><?php echo strip_tags($project["name"]); ?></option>
              <?php endforeach; ?>
            </select>

            <?php if (isset($errors["project"])): ?>
              <p class="form__message">
                <span class="error-message">
                  <?php echo $errors["project"]; ?>
                </span>
              </p>
            <?php endif; ?>

          </div>

          <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date <?php if (isset($errors["date"])): ?>  form__input--error<?php endif; ?>" type="date" name="date" id="date" value="<?php echo strip_tags($added_task["date"]); ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">

            <?php if (isset($errors["date"])): ?>
              <p class="form__message">
                <span class="error-message">
                  <?php echo $errors["date"]; ?>
                </span>
              </p>
            <?php endif; ?>
          </div>

          <div class="form__row">
            <label class="form__label" for="preview">Файл</label>

            <div class="form__input-file">
              <input class="visually-hidden" type="file" name="preview" id="preview" value="">

              <label class="button button--transparent" for="preview">
                <span>Выберите файл</span>
              </label>
            </div>
          </div>

          <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
          </div>
        </form>
      </main>
    </div>
  </div>
</div>
