<?php

/**
 * 
 * @param $stories – kirby pages collection
 * @param $style - "large" | "small" (default: "large")
 * 
 * */

?>

<div class="lines-grid">
  <?php foreach ($stories as $story):
    if ($story->legs()->toStructure()->isEmpty()) {
      continue;
    }
    $url = $story->url();
    $name = $story->title();
    $age = $story->age()->isNotEmpty() ? $story->age() : "";
    $title = "$name, $age y.o.";
    $from = getFromPlace($story);
    $fromCountry = getFromCountry($story);
    $to = getToPlace($story);
    $toCountry = getToCountry($story);
    $subtitle = "$from, $fromCountry → $to, $toCountry";
  ?>
    <div class="lines-grid-item">
      <a href="<?= $url ?>" class="d-block">
        <div class="svg-square-container p-5">
          <?php if ($story->cachedSvg()->isNotEmpty()): ?>
            <?= $story->cachedSvg()->value() ?>
          <?php endif ?>

          <?php /*  TOP-BOTTOM VERSION
              <div class="absolute-story-info">
                <div class="font-ser-l font-w-600 mb-1 outlined-page_bg_color"><?= $title ?></div>
                <div class="font-sans-m color-grey"><?= $subtitle ?></div>
              </div>
              */ ?>

          <?php /*  ALL BOTTOM VERSION */ ?>
          <div class="absolute-story-info">
            <div></div>
            <div>
              <div class="font-ser-l font-w-600 mb-1 outlined-page_bg_color"><?= $title ?></div>
              <div class="font-sans-s color-grey outlined-page_bg_color"><?= $subtitle ?></div>
            </div>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>