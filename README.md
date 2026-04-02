# Moving Lines

01.02.24 --- Updated to Kirby 4

### TODO

- [ ] add 3 new stories to test the line editor
- [ ] add images
- [ ] add embeds (markdown block)

### Line types

UPDATE: Transports are now managed from the tab "Transports" in the "Site" section of the panel.

In `mapbox-utils-ap.js` the function `kirbyTransportToDashArray(kirbyTransport)` turns transport type to line type, and then to dash-array.

| transport | line name |
| --------- | --------- |
| plane     | air       |
| boat      | sea       |
| bus       | ground    |
| car       | ground    |
| truck     | ground    |
| train     | ground    |
| taxi      | ground    |
| walk      | walk      |
| \*        | other     |

Line types:

- walk: _Dots_
- sea: _Long Dashes_
- ground = air = other: _Solid line_

### Google drive photos & files

- https://drive.google.com/drive/folders/1EZy3B7_NPgspwWjcM4NL8HESSl0z9SX7

### LOG / BUGS

##### 31.03.2026

After updates online, the new legs didn't show up. I found that the content file had, for the old entries, 2 additional fields that are no longer present (`durationday` and `id`). Removing them seems fixing the issue. Still investigating how they got there.

### Datawrapper graphs

- Go to https://datawrapper.de/
- Login > Dashboard > Archive
- Left panel shows under Moving Lines: Grafici & Mappe

To embed

- Open the story in the panel > Find field `Content blocks`
- Prepare a `Markdown` block wrapped in a `Blocks wrapper` block (for margins)
- Copy the embed code named `Responsive iframe` from Datawrapper and paste it here

Silvia Costantini's profile

- https://medium.com/peopleonthemove/live-people-dead-and-missing-crossing-europe-since-january-2014-to-date-a28f2404fee0
- https://medium.com/peopleonthemove
