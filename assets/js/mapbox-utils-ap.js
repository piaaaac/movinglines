// --------------------------------
// Mapbox line styles
// --------------------------------

// LABELS VIA FRAGA
// On foot · Air transport · Sea transport · Land transport

var lineTypes = {
  walk: { dashArray: [0, 3] }, // OK
  sea: { dashArray: [6, 5] }, // OK
  ground: { dashArray: [1] },
  air: { dashArray: [1] },
  other: { dashArray: [1] },
};

function getLineTypeById(id) {
  const defaultLineType = "other";
  if (!window.transports) return defaultLineType;
  const item = window.transports.find((t) => t.id === id);
  return item ? item.linetype : defaultLineType;
}

function kirbyTransportToDashArray(kirbyTransport) {
  var lineType = getLineTypeById(kirbyTransport);
  var dashArray = lineTypes[lineType].dashArray;
  return dashArray;
}

// --------------------------------
// Mapbox general utilities
// --------------------------------

function getEmptySource() {
  return {
    type: "geojson",
    data: {
      type: "Feature",
      properties: {},
      geometry: {
        type: "LineString",
        coordinates: [[76.993894, 31.781929]],
      },
    },
  };
}

function getBounds(coordinates) {
  // console.log("getBounds coordinates", coordinates);

  // Create a 'LngLatBounds' with both corners at the first coordinate.
  const bounds = new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]);

  // Extend the 'LngLatBounds' to include every coordinate in the bounds result.
  for (const coord of coordinates) {
    bounds.extend(coord);
  }

  return bounds;
}
