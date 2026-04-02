<?php
$stories = page("stories")->children()->listed();
$articles = page("articles")->children()->listed()->limit(3);
?>

<?php snippet("header", ["tallMenu" => true]) ?>

<?php snippet("menu", ["subtitle" => "Stories crossing borders", "transparentAtTop" => true]) ?>

<div class="container-fluid">
  <div class="row">
    <div class="col">
      <h2 class="text-center font-ser-xl font-weight-400 my-5">
        <span class="d-inline-block" style="max-width: 30em;">
          These lines represent the journeys of asylum seekers who for various reasons left their hometown in search for a better life.
        </span>
      </h2>
    </div>
  </div>
</div>

<div class="spacer py-4"></div>

<?php snippet("grid-test", ["stories" => $stories->limit(10)]) ?>

<?php /* 
<div class="spacer py-4"></div>
<?php snippet("stories-prev-2", ["stories" => $stories->limit(6)]) ?>
*/ ?>

<div class="full-w-btn">
  <a href="<?= page("stories")->url() ?>">
    <?php
    // $text = "    See all stories →    ";
    // echo $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text;
    $text = "    See all lines →    ";
    echo $text;
    ?>
  </a>
</div>

<div class="space-large"></div>

<?php /*  
<div class="container-fluid">
  <div class="row">
    <div class="col-12 mb-5">
      <h2>Articles & Resources</h2>
    </div>
  </div>
</div>

<?php snippet("articles-prev", ["articles" => $articles]) ?>

<div class="full-w-btn">
  <a href="<?= page("articles")->url() ?>">
    <?php
    $text = "    See all resources →    ";
    echo $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text . $text;
    ?>
  </a>
</div>

<div class="space-large"></div>
*/ ?>

<section id="about">
  <div class="container-fluid texts">
    <div class="row">
      <div class="col-lg-6 mb-5">
        <div class="block-font-sans-m">
          <?= $page->textAbout() ?>
        </div>
        <div class="mt-3">
          <a href="<?= page("about")->url() ?>" class="button small green-light px-3">
            READ MORE ABOUT THE PROJECT
          </a>
        </div>
      </div>
      <div class="col-lg-4 offset-lg-2 mb-5">
        <div class="block-font-sans-s">
          <?= $page->textAboutAuthors() ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php snippet("footer") ?>