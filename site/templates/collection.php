<?php snippet("header", ["tallMenu" => true]) ?>

<?php snippet("menu", ["subtitle" => "Stories crossing borders"]) ?>

<div class="space-large"></div>

<div class="blocks"><?= $page->blocks()->toBlocks() ?></div>

<div class="my-5"></div>

<div class="container-fluid texts">
  <div class="row">
    <div class="col-12">
      <?php foreach ($page->children()->listed() as $child): ?>

        <div class="mb-2">
          <?php foreach ($child->tags()->split(",") as $tag) : ?>
            <a href="<?= page("stories")->url() . "?tag=" . trim($tag) ?>" class="badge badge-secondary temp-disabled"><?= trim($tag) ?></a>
          <?php endforeach ?>
          <?php foreach ($child->places()->split(",") as $tag) : ?>
            <a href="<?= page("stories")->url() . "?tag=" . trim($tag) ?>" class="badge badge-secondary temp-disabled"><?= trim($tag) ?></a>
          <?php endforeach ?>
        </div>

        <h2 class="font-w-400">
          <a href="<?= $child->url() ?>" class="color-black no-u hover-accent">
            <?= $child->title()->html() ?>
          </a>
        </h2>

        <hr class="mb-5" />
      <?php endforeach ?>
    </div>
  </div>
</div>

<script>

</script>

<?php snippet("footer") ?>