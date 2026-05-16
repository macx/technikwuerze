<?php
$formName = '';
$formEmail = '';
$pageLanguage = $kirby->language() ? $kirby->language()->code() : 'en';
$user = $kirby->user();

if (!is_null($user) && $user->isLoggedIn()) {
  $formName = $user->name();
  $formEmail = $user->email();
}
?>
<div class="card form form-komments">
  <h2><?= t('mauricerenck.komments.form.title') ?></h2>

  <form action="<?= $kirby->url('index') ?>/komments/send" method="post" id="kommentform">
    <div class="form-feedback">
      <div class="msg user-feedback"></div>
      <div class="msg loader" aria-live="polite">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3C16.9706 3 21 7.02944 21 12H19C19 8.13401 15.866 5 12 5V3Z"></path>
        </svg>
        <span><?= t('mauricerenck.komments.sending') ?></span>
      </div>
    </div>

    <span class="replyHandleDisplay"></span>

    <div class="form-row">
      <div class="form-column" style="--span: 6;">
        <div class="form-field">
          <label for="comment">
            <?= t('mauricerenck.komments.form.label.comment') ?>
            <span class="required" aria-hidden="true">*</span>
          </label>
          <textarea
            name="comment"
            id="comment"
            cols="30"
            rows="5"
            placeholder="<?= t('mauricerenck.komments.form.label.comment') ?>*"
            required
          ></textarea>
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-column" style="--span: 2;">
        <div class="form-field">
          <label for="email">
            <?= t('mauricerenck.komments.form.label.email') ?>
            <span class="required" aria-hidden="true">*</span>
          </label>
          <input
            type="email"
            name="email"
            id="email"
            placeholder="<?= t('mauricerenck.komments.form.label.email') ?>*"
            required
            value="<?= $formEmail ?>"
          >
        </div>
      </div>

      <div class="form-column" style="--span: 2;">
        <div class="form-field">
          <label for="author">
            <?= t('mauricerenck.komments.form.label.name') ?>
            <span class="required" aria-hidden="true">*</span>
          </label>
          <input
            type="text"
            name="author"
            id="author"
            placeholder="<?= t('mauricerenck.komments.form.label.name') ?>*"
            required
            value="<?= $formName ?>"
          >
        </div>
      </div>

      <div class="form-column" style="--span: 2;">
        <div class="form-field">
          <label for="author_url"><?= t('mauricerenck.komments.form.label.website') ?></label>
          <input
            type="url"
            name="author_url"
            id="author_url"
            placeholder="<?= t('mauricerenck.komments.form.label.website') ?>"
          >
        </div>
      </div>
    </div>

    <input type="text" name="url" id="url" placeholder="Leave empty" tabindex="-1">
    <input type="hidden" name="replyTo" value="">
    <input type="hidden" name="replyHandle" value="">
    <input type="hidden" name="language" value="<?= $pageLanguage ?>">
    <input type="hidden" name="pageUuid" value="<?= $page->uuid() ?>">

    <span class="komment-privacy"><?= t('mauricerenck.komments.form.privacy') ?></span>

    <button
      type="submit"
      class="<?= option(
        'mauricerenck.komments.form.submit.classNames',
        'button button-tiny button-primary',
      ) ?>"
    >
      <?= t('mauricerenck.komments.form.submit') ?>
    </button>
  </form>
</div>
<?= js(['/media/plugins/mauricerenck/komments/komments.js']) ?>
