<?php

// COLOR_DEFS_
// $green_fraga_lighter = "#0f521d";
// $green_fraga_darker = "#0f521d";
// $green_fraga_lighter = "#844DC8";
// $green_fraga_darker = "#844DC8";
$green_fraga_lighter = "#45419d";
$green_fraga_darker = "#45419d";

// $page_bg_color_lighter = "#eff6ff";
// $page_bg_color_darker = "#c0d4e3";
$page_bg_color_lighter = "#eff6ff";
$page_bg_color_darker = "#bfd9ec";
$page_bg_color_darker = "#CBE1F1";

// --- Prepare nav data

$siblings = $page->siblings()->listed();
$prevPage = $page->hasPrev($siblings) ? $page->prev($siblings) : $siblings->last();
$nextPage = $page->hasNext($siblings) ? $page->next($siblings) : $siblings->first();

// --- Prepare story data

$from = getFromPlace($page);
$fromCountry = getFromCountry($page);
$fromCountryCode = getFromCountryCode($page);
$to = getToPlace($page);
$toCountry = getToCountry($page);
$toCountryCode = getToCountryCode($page);
$subtitle = "$from, $fromCountry → $to, $toCountry";
$subtitleShort = "$from, $fromCountryCode → $to, $toCountryCode";

// --- Stats 

$trItem = "<span class='tr'></span>";
$ndItem = "<span class='tr-nodata'></span>";
$stItem = "<span class='st'></span>";
$legStats = [];
$totals = [
  "places" => $page->legs()->toStructure()->count() + 1,
  "countries" => [],
  "transports" => [],
  "travelDays" => 0,
  "stayDays" => 0,
  "totalDays" => 0,
  "daysSequence" => [],
];
foreach ($page->legs()->toStructure() as $leg) {
  $legStat = [];

  // countries
  $legCountryCode = $leg->country()->value();
  $legCountry = site()->countries()->toStructure()->findBy('code', $legCountryCode);
  $legCountryName = $legCountry ? $legCountry->name() : null;
  if ($legCountryName && !in_array($legCountryName, $totals["countries"])) {
    $totals["countries"][] = $legCountryName;
  }
  // transports
  $legTransport = $leg->transport()->value();
  if (!in_array($legTransport, $totals["transports"])) {
    $totals["transports"][] = $legTransport;
  }
  // days
  $legDays = (int)$leg->durationDays()->value();
  $legHours = (int)$leg->durationHours()->value();
  $stayDays = (int)$leg->stayDays()->value();
  $stayHours = (int)$leg->stayHours()->value();
  $totals["travelDays"] += $legDays + ceil($legHours / 24);
  $totals["stayDays"] += $stayDays + ceil($stayHours / 24);
  $totals["totalDays"] += $legDays + ceil($legHours / 24) + $stayDays + ceil($stayHours / 24);

  // sequence
  $tr = $legDays + ceil($legHours / 24);
  $st = $stayDays + ceil($stayHours / 24);
  if ($tr == 0) {
    $totals["daysSequence"] = array_merge($totals["daysSequence"], array_fill(0, 1, $ndItem));
    $legStat["noTripData"] = 1;
  } else {
    $totals["daysSequence"] = array_merge($totals["daysSequence"], array_fill(0, $tr, $trItem));
    $legStat["tripDays"] = $tr;
  }
  $totals["daysSequence"] = array_merge($totals["daysSequence"], array_fill(0, $st, $stItem));
  $legStat["stayDays"] = $st;
  $legStats[] = $legStat;
}
// kill($totals);
$tripDotsSize = "large";
if ((int)$totals["totalDays"] > 30) {
  $tripDotsSize = "medium";
}
if ((int)$totals["totalDays"] > 60) {
  $tripDotsSize = "small";
}
?>

<?php snippet("header", ["tallMenu" => true]) ?>

<?php snippet("menu", ["subtitle" => "$subtitle", "subtitleMobile" => "$subtitleShort", "showSwitch" => true, "showNav" => true]) ?>

<?php snippet('handlebars-templates') ?>

<section class="map-area">
  <div id="map-container"></div>
  <div class="info">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div id="box-container"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="map-legend">
    <img style="max-width: 100%;"
      src="<?= $kirby->url("assets") . '/images/legend-lines.svg?v=' . option('assets.version') ?>" />
  </div>
</section>

<?php /*  
<div class="debug" style="position: fixed; bottom: 20px; left: 20px; z-index: 10;">
  <a href="<?= $prevPage->url() ?>" class="button small grey-light one-of-two">Prev</a>
  <a href="<?= $nextPage->url() ?>" class="button small grey-light one-of-two">Next</a>
</div>
*/ ?>

<a class="scroll-down"></a>

<section id="about" class="mt-5">
  <div class="container-fluid texts">
    <div class="row">

      <div class="col-12">
        <!-- Small title -->
        <h5 class="mb-4"><?= $page->title() ?>’s trip</h5>
      </div>

      <?php if ($page->text()->isNotEmpty()): ?>
        <div class="col-lg-6">
          <!-- Citazione / testo -->
          <div class="mb-4 font-ser-m"><?= $page->text()->kt() ?></div>
        </div>
      <?php endif ?>

      <div class="col-lg-5<?= $page->text()->isNotEmpty() ? ' offset-lg-1' : '' ?>">

        <!-- Data recap paragraph -->
        <div class="font-sans-s">
          <?= $page->title() ?> travelled by <?= implode(", ", $totals["transports"]) ?> and passed through <?= count($totals["countries"]) ?> countries: <?= implode(", ", $totals["countries"]) ?>.
          The trip lasted in total <?= round($totals["totalDays"]) ?> days, <?= round($totals["travelDays"]) ?> of which spent traveling and <?= round($totals["stayDays"]) ?> of which spent staying in places.
        </div>

        <!-- Data pallini -->
        <div class="mt-4">
          <div>
            <img style="max-width: 100%;"
              src="<?= $kirby->url("assets") . '/images/legend-days-line.svg?v=' . option('assets.version') ?>" />
          </div>
          <div class="trip-symbols mt-2 mb-4" data-style="<?= $tripDotsSize ?>" style="letter-spacing: -0.1em;">
            <?= implode(" ", $totals["daysSequence"]) ?>
          </div>
        </div>
      </div>

      <?php
      // section for images, only if there are any

      /*  
      $images = $page->storyImages()->toFiles();
      $imgNum = $images->count();
      if ($imgNum > 0):
      ?>
        <div class="col-lg-5 offset-lg-1">
          <h5 class="mb-4"><?= "$imgNum image" . ($imgNum > 1 ? "s" : "") ?></h5>
          <div class="story-images" style="--count: <?= $imgNum ?>">
            <?php foreach ($images as $i => $image): ?>
              <img src="<?= $image->thumb(["height" => 400])->url() ?>" />
            <?php endforeach; ?>
          </div>
        </div>
      <?php 
      endif;
      */
      ?>

    </div>
  </div>

  <div class="blocks"><?= $page->blocks()->toBlocks() ?></div>

</section>

<section class="d-md-none">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <a href="<?= $prevPage->url() ?>" class="d-flex align-items-center no-u">
          <span class="px-2">&larr;</span>
          <span class="font-sans-xs">Previous<br />story</span>
        </a>
        <a href="<?= $nextPage->url() ?>" class="d-flex align-items-center no-u">
          <span class="font-sans-xs">Next<br />story</span>
          <span class="px-2">&rarr;</span>
        </a>
      </div>
    </div>
  </div>
</section>

<script>
  var countries = <?= json_encode(site()->countries()->toStructure()->toArray()) ?>;
  console.log("countries", countries);

  function countryCodeToName(code) {
    var country = countries.find(c => c.code == code);
    return country ? country.name : code;
  }

  function getColor1(bool) {
    return bool ? cd : cl;
  }

  // change root css variable --bg-color at the beginning, and then on scroll
  var root = document.documentElement;
  var cl = "<?= $page_bg_color_lighter ?>";
  var cd = "<?= $page_bg_color_darker ?>";
  root.style.setProperty('--bg-color', getColor1(localStorage.getItem("mapVisible") === "true"));
  window.addEventListener('scroll', () => {
    var scrollY = window.scrollY;
    var t = apMap(scrollY, 0, window.innerHeight * 0.6, 0, 1, true);
    var c1 = getColor1(localStorage.getItem("mapVisible") === "true");
    var c2 = cd;
    var newColor = blendHex(c1, c2, t);
    root.style.setProperty('--bg-color', newColor);
  });

  // --------------------------------
  // Kirby > JS data init
  // --------------------------------

  var kirbyData = <?= json_encode($page->content()->toArray()) ?>;
  kirbyData.legs = legs = <?= $page->legs()->toStructure()->toJson() ?>;
  console.log("kirbyData", kirbyData)

  var legStats = <?= json_encode($legStats) ?>;
  console.log("legStats", legStats);

  var lineColor = "<?= $green_fraga_darker ?>";

  var state = {
    loadCount: 0,
    storyPlaces: getStoryPlacesFromKirbyData(kirbyData),
    openPlaceId: null,
    currentMapStyle: null,
    activeLegIndex: null,
  }
  console.log("places", state.storyPlaces)

  // --------------------------------
  // Mapbox init
  // --------------------------------

  // AP
  const mbToken = "<?= option('mapbox.token') ?>";
  const mbStyleWithBg = "<?= option('mapbox.style.withBg') ?>";
  const mbStyleEmpty = "<?= option('mapbox.style.empty') ?>";

  var popupHover;
  var basicRouteDS = getBasicRouteDSFromState();
  var pointsDS = getPointsDSFromState();
  console.log("basicRouteDS", basicRouteDS)
  console.log("pointsDS", pointsDS)

  var recentlyClickedPoint = false;

  mapboxgl.accessToken = mbToken;
  const map = new mapboxgl.Map({
    container: 'map-container',
    style: mbStyleEmpty,
    center: [state.storyPlaces[0].lon, state.storyPlaces[0].lat],
    zoom: 7,
    attributionControl: false,
    logoPosition: 'bottom-right',
    scrollZoom: (localStorage.getItem("mapVisible") === "true"),
  });

  // --------------------------------
  // Add data
  // --------------------------------

  map.on('load', () => {

    // map.scrollZoom.disable();

    // --- Stiled attribution
    map.addControl(new mapboxgl.AttributionControl({
      compact: true,
    }), 'bottom-right');

    // --- Add data + layers
    addAdditionalSourceAndLayer();

    // --- fit to bounds
    fitFullRoute();

    // --- Create popup for later

    popupHover = new mapboxgl.Popup({
      closeButton: false,
      closeOnClick: true,
      offset: 8,
      className: "ck-map-popup",
    });

    // --- Handle map events

    // point events
    map.on('click', 'points', function(e) {

      console.log("event: ", e)
      e.originalEvent.stopPropagation();
      recentlyClickedPoint = true;
      setTimeout(() => {
        recentlyClickedPoint = false;
      }, 300);

      var data = e.features[0].properties;
      console.log("click point", data)
      highlightLeg(data.index);
    });
    map.on('mouseover', 'points', function(e) {
      map.getCanvas().style.cursor = 'pointer';
      mapPopup(e, popupHover);
    });
    map.on('mouseout', 'points', function() {
      map.getCanvas().style.cursor = '';
      popupHover.remove();
    });

    // route events
    map.on('click', 'routeSensi', function(e) {
      var data = e.features[0].properties;
      console.log("click on route", data)
      if (!recentlyClickedPoint) {
        highlightLeg(data.legIndex);
      }
    });
    map.on('mouseover', 'routeSensi', function(e) {
      map.getCanvas().style.cursor = 'pointer';
    });
    map.on('mouseout', 'routeSensi', function() {
      map.getCanvas().style.cursor = '';
    });

    // Add source and layer whenever base style is loaded
    map.on('style.load', () => {
      addAdditionalSourceAndLayer();
    });
  });


  function fitFullRoute() {
    var coordinates = [];
    basicRouteDS.data.features.forEach(feature => {
      feature.geometry.coordinates.forEach(coo => {
        coordinates.push(coo)
      })
    });
    var bounds = getBounds(coordinates)
    map.fitBounds(bounds, {
      padding: paddingValues(),
      duration: 1500,
    });
  }

  function addAdditionalSourceAndLayer() {
    state.loadCount++;

    // --- Add data sources
    map.addSource('routeDS', basicRouteDS);
    map.addSource('pointsDS', pointsDS);

    // --- Add layers

    var lineWidthRule = [
      "interpolate", ["linear"],
      ["zoom"],
      3, 1.5,
      6, 2,
      9, 2.5,
    ];

    // Only for when adding the layer
    var lineColorRule = [
      "case",
      ["==", ["get", "legIndex"], state.activeLegIndex],
      lineColor, // match → highlight
      "rgba(173, 173, 160, 0.8)" // otherwise → grey
    ];
    var lineOpacityRule = [
      "case",
      ["==", ["get", "tripTransport"], "plane"],
      0.25, // match → make opaque
      1 // otherwise → full opacity
    ];
    var circleOpacityRule = [
      "case", ["==", ["get", "legIndex"], state.activeLegIndex - 1], 1, 0
    ];
    if (state.activeLegIndex === null) {
      lineColorRule = lineColor; // highlight all
      circleOpacityRule = 1;
    }

    map.addLayer({
      id: "route",
      type: "line",
      source: "routeDS",
      layout: {
        "line-join": "round",
        "line-cap": "round",
      },
      paint: {
        "line-width": lineWidthRule,
        "line-dasharray": ["get", "dasharray"],
        "line-color": lineColorRule,
        "line-opacity": lineOpacityRule,
      }
    });

    map.addLayer({
      id: "routeSensi",
      type: "line",
      source: "routeDS",
      layout: {
        // "line-join": "round",
        // "line-cap": "round",
      },
      paint: {
        "line-width": 30,
        "line-color": "red",
        "line-opacity": 0,
      }
    });

    map.addLayer({
      id: "points",
      type: "circle",
      source: "pointsDS",
      paint: {
        "circle-radius": ["interpolate", ["linear"],
          ["zoom"], 3, 3, 6, 5, 9, 6,
        ],
        "circle-stroke-width": ["interpolate", ["linear"],
          ["zoom"], 3, 1, 6, 1.5, 9, 2,
        ],
        "circle-color": "#fff",
        "circle-stroke-color": "#222",
        "circle-opacity": circleOpacityRule,
      }
    });

    map.addLayer({
      id: "pointsDot",
      type: "circle",
      source: "pointsDS",
      paint: {
        "circle-radius": ["interpolate", ["linear"],
          ["zoom"], 3, 2, 6, 3, 9, 4,
        ],
        "circle-color": "#222",
        "circle-opacity": [
          "case", ["==", ["get", "legIndex"], state.activeLegIndex], 1, 0
        ],
      }
    });

    if (state.loadCount == 1) {
      highlightLeg(null, false);
    }
  }

  function highlightLeg(index, zoom = true) {
    if (state.activeLegIndex == index) {
      state.activeLegIndex = null;
    } else if (index >= state.storyPlaces.length) {
      state.activeLegIndex = null;
    } else if (index < 0) {
      state.activeLegIndex = null;
    } else {
      state.activeLegIndex = index;
    }

    if (state.activeLegIndex === null) {
      // All segments fully visible
      map.setPaintProperty('route', 'line-color', lineColor);
      map.setPaintProperty('points', 'circle-color', "#fff");
      map.setPaintProperty('points', 'circle-stroke-color', "#222");
      map.setPaintProperty('points', 'circle-opacity', 1);
      map.setPaintProperty('points', 'circle-stroke-opacity', 1);

      map.setPaintProperty('pointsDot', 'circle-opacity', 0);

      if (zoom) {
        fitFullRoute();
      }

      // Info box
      var markup = templateStoryInfoContents({
        "text": `<?= $page->title() ?> travelled through <?= $page->legs()->toStructure()->count() ?> places.`,
        "quote": `<?= $page->reasonDeparture()->isNotEmpty() ? $page->reasonDeparture()->kti() : '' ?>`,
        "name": "<?= $page->title() ?>",
      });
      document.querySelector("#box-container").innerHTML = markup;

    } else {

      // Highlight one segment
      map.setPaintProperty('route', "line-color", [
        "case", ["==", ["get", "legIndex"], state.activeLegIndex], lineColor, "rgba(173, 173, 160, 0.5)",
      ]);
      map.setPaintProperty('points', "circle-color", "#fff");
      map.setPaintProperty('points', "circle-stroke-color", "#222");
      map.setPaintProperty('points', 'circle-opacity', [
        "match", ["get", "legIndex"],
        [state.activeLegIndex, state.activeLegIndex - 1], 1, 0,
      ]);
      map.setPaintProperty('points', 'circle-stroke-opacity', [
        "match", ["get", "legIndex"],
        [state.activeLegIndex, state.activeLegIndex - 1], 1, 0,
      ]);
      map.setPaintProperty('pointsDot', 'circle-opacity', [
        "case", ["==", ["get", "legIndex"], state.activeLegIndex], 1, 0,
      ]);

      // Info box
      var e = state.storyPlaces[state.activeLegIndex];
      e.tripComments = kti(e.tripComments);
      console.log("highlightLeg", e);
      // merge object with defaults to 0 values for missing keys
      var stats = Object.assign({
        "tripDays": 0,
        "stayDays": 0,
        "noTripData": 0,
      }, legStats[state.activeLegIndex - 1] || {});
      var markup = templateLegInfoContents({
        "place": e,
        "bars": {
          "transport": 33,
          "trip": 80,
          "permanence": 40,
        },
        "stats": stats,
      });
      document.querySelector("#box-container").innerHTML = markup;

      // Zoom to segment
      if (zoom) {
        var bbox = null;
        if (e.geojsonUse && e.geojsonLeg) {
          bbox = turf.bbox(e.geojsonLeg);
        } else {
          var offset = 0.5;
          var west = Math.min(e.tripLonFrom, e.tripLonTo) || e.lon - offset;
          var south = Math.min(e.tripLatFrom, e.tripLatTo) || e.lat - offset;
          var east = Math.max(e.tripLonFrom, e.tripLonTo) || e.lon + offset;
          var north = Math.max(e.tripLatFrom, e.tripLatTo) || e.lat + offset;
          bbox = [west, south, east, north];
        }
        map.fitBounds(bbox, {
          padding: paddingValues(),
          duration: 1200,
        });
      }
    }
  }


  // --------------------------------
  // Debug properties metadata in routeDS features
  // --------------------------------

  // setTimeout(() => {
  //   console.log("~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ debug: features in routeDS");
  //   const features = map.querySourceFeatures('routeDS', {
  //     sourceLayer: 'route'
  //   });
  //   features.forEach(f => {
  //     console.log(f.properties);
  //   });
  // }, 3000);

  // --------------------------------
  // Handle map visibility
  // --------------------------------

  toggleMapStyle(currentMapVisibility);


  // --------------------------------
  // Functions
  // --------------------------------

  function getStoryPlacesFromKirbyData(kirbyData) {
    console.log("getStoryPlacesFromKirbyData", kirbyData)
    // --- data from kirby
    var startLon = +kirbyData.departurelon;
    var startLat = +kirbyData.departurelat;
    var startPlace = kirbyData.departureplace;
    var legs = kirbyData.legs;
    var startPlaceIsValid = (startLon && startLat) ? true : false;

    // --- fit into js format
    var firstPlace = {
      "name": startPlace,
      "lon": startLon,
      "lat": startLat,
      "index": 0,
      "isValidPlace": startPlaceIsValid,
      "tripCountryTo": countryCodeToName(kirbyData.departurecountry),
    }
    var places = [firstPlace];
    for (var i = 0; i < legs.length; i++) {
      var leg = legs[i];
      var lonFrom = (i > 0) ? (legs[i - 1].lon) : startLon
      var latFrom = (i > 0) ? (legs[i - 1].lat) : startLat
      var placeFrom = (i > 0) ? (legs[i - 1].place) : startPlace
      var lonTo = leg.lon
      var latTo = leg.lat

      var isValidPlace = false;
      var isValidTrip = false;
      if (leg.lon && leg.lat) {
        isValidPlace = true;
        if (lonFrom && latFrom) {
          isValidTrip = true;
        }
      }

      // console.log("DEBUG", leg.geojsonleg);

      var place = {
        "name": leg.place,
        "lon": leg.lon,
        "lat": leg.lat,
        "index": i + 1,
        "isValidPlace": isValidPlace,
        "isValidTrip": isValidTrip,
        "tripComments": leg.comments,
        "tripPlaceFrom": placeFrom,
        "tripPlaceTo": leg.place,

        "tripCountryTo": countryCodeToName(leg.country),

        "tripLonFrom": lonFrom,
        "tripLatFrom": latFrom,
        "tripLonTo": leg.lon,
        "tripLatTo": leg.lat,
        "tripTransport": leg.transport,
        "geojsonLeg": leg.geojsonleg ? JSON.parse(leg.geojsonleg) : null,
        "geojsonUse": leg.geojsonuse == "true",
      }
      places.push(place);
    }
    return places;
  }


  function getBasicRouteDSFromState() {

    // --- prepare data for mapbox
    var mbData = {
      'type': 'geojson',
      'data': {
        'type': 'FeatureCollection',
        'features': []
      }
    }

    var validTripPlaces = state.storyPlaces.filter(e => (e.isValidTrip === true));

    for (var i = 0; i < validTripPlaces.length; i++) {
      var place = validTripPlaces[i];
      var dashArray = kirbyTransportToDashArray(place.tripTransport)

      var geometry = {
        'type': 'LineString',
        'coordinates': [
          [place.tripLonFrom, place.tripLatFrom],
          [place.tripLonTo, place.tripLatTo],
        ],
      };
      if (legs[i].geojsonuse == "true" && legs[i].geojsonleg != "") {
        // console.log(legs[i], "using geojson for leg " + i); 
        try {
          var geojsonLeg = JSON.parse(legs[i].geojsonleg);
          // console.log("geojsonLeg", geojsonLeg) 
          geometry = geojsonLeg.features[0].geometry;
        } catch (e) {
          console.error("Error parsing geojson for leg " + i, e);
        }
      }

      var feature = {
        'type': 'Feature',
        'properties': {
          "legIndex": place.index,
          "dasharray": dashArray,
          "tripTransport": place.tripTransport,
        },
        'geometry': geometry,
      }
      mbData.data.features.push(feature);
    }

    return mbData;

  }

  function getPointsDSFromState() {

    var validPlaces = state.storyPlaces.filter(e => (e.isValidPlace === true));

    return {
      'type': 'geojson',
      'data': {
        "type": "FeatureCollection",
        "features": validPlaces.map(function(e, i) {
          var props = clone(e);
          props.isOpen = state.openStoryId == e.id;
          props.legIndex = e.index;
          return {
            "type": "Feature",
            "geometry": {
              "type": "Point",
              "coordinates": [e.lon, e.lat],
            },
            "properties": props,
          }
        }),
      }
    };
  }

  function paddingValues() {
    var margin = Math.min(window.innerWidth * 0.07, 100);
    var isMobile = breakpointIs("sm", "down");
    var startState = state.activeLegIndex === null;
    return {
      top: margin,
      bottom: margin,
      left: margin + (isMobile ? 0 : (startState ? 280 : 360)),
      right: margin,
    };

  }

  function mapPopup(e, popupObject) {
    var data = e.features[0].properties;
    console.log("mapPopup", data)

    var coordinates = e.features[0].geometry.coordinates.slice();
    while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
      coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
    }
    var markup = templatePopup({
      "place": data,
    });
    popupObject
      .setLngLat(coordinates)
      .setHTML(markup)
      .addTo(map);
  }

  function toggleMapStyle(bool) {
    if (typeof bool == 'undefined') {
      bool = true;
    }
    if (bool === true) {
      state.currentMapStyle = mbStyleWithBg;
      map.scrollZoom.enable();
    } else {
      state.currentMapStyle = mbStyleEmpty;
      map.scrollZoom.disable();
    }
    map.setStyle(state.currentMapStyle);
    localStorage.setItem("mapVisible", String(bool));
    window.dispatchEvent(new Event('scroll'));
  }

  // --- Utils

  function kti(text) {
    if (!text) return "";

    // Normalize Windows/Mac line endings
    const normalized = text.replace(/\r\n?/g, "\n");

    // Convert newlines to <br>
    const withBreaks = normalized.replace(/\n/g, "<br>");

    return withBreaks;
  }

  // --- Keyboard

  window.addEventListener('keydown', (e) => {
    switch (e.key) {
      // case 'ArrowUp':
      // case 'ArrowDown':
      case 'ArrowLeft':
        highlightLeg(state.activeLegIndex - 1);
        break;
      case 'ArrowRight':
      case 'Spacebar':
        highlightLeg(state.activeLegIndex + 1);
        break;
    }
  });

  function navigationAction(action) {
    console.log("navigation action", action);
    switch (action) {
      case "highlight-prev-leg":
        highlightLeg(state.activeLegIndex - 1);
        break;
      case "highlight-next-leg":
        highlightLeg(state.activeLegIndex + 1);
        break;
      case "close-leg":
        highlightLeg(null);
        break;
      case "start-story":
        highlightLeg(1);
        break;
    }
  }

  // --- Events

  document.querySelector('a.scroll-down').addEventListener('click', function(e) {
    e.preventDefault();
    const el = document.getElementById('map-container');
    const rect = el.getBoundingClientRect();
    const elementBottom = window.scrollY + rect.bottom;
    const targetScroll = elementBottom - 105;
    window.scrollTo({
      top: targetScroll,
      behavior: 'smooth'
    });
  });


  // --- Handlebars templates ---------
  // --- see https://tutorialzine.com/2015/01/learn-handlebars-in-10-minutes

  var templatePopup = Handlebars.compile($("#hb-popup").html());
  var templateStoryInfoContents = Handlebars.compile($("#hb-storyinfocontents").html());
  var templateLegInfoContents = Handlebars.compile($("#hb-leginfocontents").html());
</script>

<?php snippet("footer", ["markup" => false]) ?>