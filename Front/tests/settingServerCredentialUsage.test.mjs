import assert from "node:assert/strict";
import fs from "node:fs";

const settingServerSource = fs.readFileSync(
  new URL("../src/assets/scripts/settingServer.js", import.meta.url),
  "utf8"
);

const dashboardSource = fs.readFileSync(
  new URL("../src/assets/scripts/Dashboard.js", import.meta.url),
  "utf8"
);

const requiredCredentialGuardedActions = [
  "StartModules",
  "StopModules",
  "RestartModules",
  "StatusModules",
  "ExportModules",
  "backToBefore",
  "returnToTheFirstState",
];

for (const actionName of requiredCredentialGuardedActions) {
  const actionBlockRegex = new RegExp(
    `async function ${actionName}\\([\\s\\S]*?await useApi\\([\\s\\S]*?\\n}`,
    "m"
  );
  const blockMatch = settingServerSource.match(actionBlockRegex);
  assert.ok(blockMatch, `${actionName} block was not found`);
  assert.match(
    blockMatch[0],
    /resolveServerCredentialsForAction\(/,
    `${actionName} must use resolveServerCredentialsForAction`
  );
}

assert.doesNotMatch(
  settingServerSource,
  /SSH was successful\./,
  "legacy transport-level SSH success wording should not be present"
);
assert.match(
  settingServerSource,
  /Module status retrieved successfully\./,
  "status action should use module-status-success wording"
);
assert.match(
  settingServerSource,
  /suppressDefaultErrorHandler:\s*true/,
  "command actions should suppress default generic error handler"
);

assert.match(
  dashboardSource,
  /persistScopedServerCredentials\(localStorage,\s*x,\s*\{/,
  "server selection flow should persist scoped credentials"
);
assert.match(
  dashboardSource,
  /persistScopedServerCredentials\(localStorage,\s*localStorage\.getItem\("server"\),\s*\{/,
  "backup password update should persist credential scope"
);

console.log("settingServer credential usage tests passed");
