<?php

/**
 * 
 * @param $stories – kirby pages collection
 * @param $style - "large" | "small" (default: "large")
 * 
 * */

$style = $style ?? "large";
?>

<div class="container-fluid <?= $style === "small" ? "texts-plus" : "" ?>">
  <div class="row">
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
      $fromCountryCode = getFromCountryCode($story);
      $to = getToPlace($story);
      $toCountry = getToCountry($story);
      $toCountryCode = getToCountryCode($story);
      $subtitle = "$from, $fromCountry → $to, $toCountry";
      $subtitleShort = "$from, $fromCountryCode → $to, $toCountryCode";
    ?>

      <?php if ($style === "large"): ?>

        <div class="col-sm-6 col-xl-4">
          <a href="<?= $url ?>" class="d-block">
            <div class="svg-square-container mb-3 mt-1">
              <div class="pad p-5">
                <?php if ($story->cachedSvg()->isNotEmpty()): ?>
                  <?= $story->cachedSvg()->value() ?>
                <?php endif ?>
                <div class="absolute-story-info">
                  <div></div>
                  <div>
                    <div class="font-ser-l font-w-600 mb-1 outlined-page_bg_color"><?= $title ?></div>
                    <div class="font-sans-m d-none d-md-block color-grey outlined-page_bg_color"><?= $subtitle ?></div>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>

      <?php elseif ($style === "small"): ?>

        <div class="col-md-6">
          <a href="<?= $url ?>" class="d-block no-u story-prev-small">
            <div class="row">
              <div class="col-4 col-sm-3 col-xl-4">
                <div class="svg-square-container mb-2 mb-md-3 mt-1">
                  <div class="pad padding-proportional-s">
                    <?php if ($story->cachedSvg()->isNotEmpty()): ?>
                      <?= $story->cachedSvg()->value() ?>
                    <?php endif ?>
                  </div>
                </div>
              </div>
              <div class="col-8 col-sm-9 col-xl-6 align-self-center">
                <div class="story-details color-black mb-4">
                  <div class="font-ser-l font-w-600 mb-1 outlined-page_bg_color"><?= $title ?></div>
                  <div class="font-sans-s color-grey outlined-page_bg_color d-none d-md-block"><?= $subtitle ?></div>
                  <div class="font-sans-s color-grey outlined-page_bg_color d-md-none"><?= $subtitleShort ?></div>
                </div>
              </div>
            </div>
          </a>
        </div>

      <?php endif ?>

    <?php endforeach ?>
  </div>
</div>