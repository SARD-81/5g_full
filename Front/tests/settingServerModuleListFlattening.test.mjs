import assert from "node:assert/strict";
import fs from "node:fs";

const settingServerSource = fs.readFileSync(
  new URL("../src/assets/scripts/settingServer.js", import.meta.url),
  "utf8"
);

const settingServerView = fs.readFileSync(
  new URL("../src/views/settingServer.html", import.meta.url),
  "utf8"
);

assert.match(
  settingServerSource,
  /const allModules = data\.data\.allModules \|\| \[\];/,
  "getModules should render using allModules"
);
assert.match(
  settingServerSource,
  /\.module-list-desktop/,
  "desktop flat module list container should be used"
);
assert.match(
  settingServerSource,
  /\.module-list-mobile/,
  "mobile flat module list container should be used"
);
assert.match(
  settingServerSource,
  /querySelectorAll\(["']\.module-button["']\)[\s\S]*?forEach/,
  "selection reset should be done across all module-button items"
);
assert.doesNotMatch(
  settingServerView,
  />EPC</,
  "settingServer sidebar should no longer render EPC grouping header"
);
assert.doesNotMatch(
  settingServerView,
  />5GC</,
  "settingServer sidebar should no longer render 5GC grouping header"
);

console.log("settingServer module list flattening tests passed");
