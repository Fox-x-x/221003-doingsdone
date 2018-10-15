<h2 class="content__main-heading">Добавление проекта</h2>

<form class="form"  action="add-project.php" method="post">
  <div class="form__row">
    <label class="form__label" for="project_name">Название <sup>*</sup></label>

    <input class="form__input <?php if (isset($errors["name"])): ?>  form__input--error<?php endif; ?>" type="text" name="name" id="project_name" value="<?=strip_tags($added_project["name"]); ?>" placeholder="Введите название проекта">

    <?php if (isset($errors["name"])): ?>
      <p class="form__message">
        <span class="error-message">
          <?php echo $errors["name"]; ?>
        </span>
      </p>
    <?php endif; ?>
    
  </div>

  <div class="form__row form__row--controls">
    <input class="button" type="submit" name="" value="Добавить">
  </div>
</form>
