import assert from "node:assert/strict";
import {
  getBackendCommandError,
  mapServiceError,
  persistScopedServerCredentials,
  resolveValidatedServerCredentials,
} from "../src/assets/scripts/serverCredentials.js";

class FakeStorage {
  constructor(values = {}) {
    this.values = values;
  }

  getItem(key) {
    return this.values[key] ?? null;
  }

  setItem(key, value) {
    this.values[key] = value;
  }
}

const validStorage = new FakeStorage({
  userNameServer: "root",
  passwordServer: "secret",
  port: "22",
  serverCredentialServerId: "10",
});

const validResult = resolveValidatedServerCredentials(validStorage, "10");
assert.equal(validResult.valid, true);
assert.equal(validResult.credentials.port, 22);

const staleStorage = new FakeStorage({
  userNameServer: "root",
  passwordServer: "secret",
  port: "22",
  serverCredentialServerId: "99",
});
const staleResult = resolveValidatedServerCredentials(staleStorage, "10");
assert.equal(staleResult.valid, false);
assert.equal(staleResult.errorCode, "validation_failed");

const missingStorage = new FakeStorage({});
const missingResult = resolveValidatedServerCredentials(missingStorage, "10");
assert.equal(missingResult.valid, false);
assert.match(missingResult.reason, /missing/i);

assert.equal(
  mapServiceError("service_not_found", "fallback"),
  "The expected service unit was not found on the server."
);
assert.equal(
  mapServiceError("ssh_login_failed", "fallback"),
  "SSH login failed. Please verify username, password, or port."
);
assert.equal(mapServiceError("unknown", "fallback"), "fallback");
assert.equal(
  mapServiceError("ssh_connection_failed", "fallback"),
  "Could not establish SSH connection to the server."
);

const perServerStorage = new FakeStorage({
  serverCredentialsByServer: JSON.stringify({
    "10": { username: "srv-user", password: "srv-pass", port: 2022 },
  }),
});
const perServerResult = resolveValidatedServerCredentials(perServerStorage, "10");
assert.equal(perServerResult.valid, true);
assert.equal(perServerResult.credentials.username, "srv-user");
assert.equal(perServerResult.credentials.port, 2022);

const commandError = getBackendCommandError({
  success: false,
  error_code: "sudo_failed",
  message: "SSH connected, but sudo/systemctl execution failed.",
  details: { command: "systemctl status x" },
});
assert.equal(commandError.code, "sudo_failed");
assert.equal(commandError.details.command, "systemctl status x");

const persistedStorage = new FakeStorage({
  userNameServer: "old-user",
  passwordServer: "old-pass",
  port: "22",
});
persistScopedServerCredentials(persistedStorage, "77", {
  username: "new-user",
  password: "new-pass",
  port: 2022,
});
assert.equal(persistedStorage.getItem("serverCredentialServerId"), "77");
assert.equal(persistedStorage.getItem("userNameServer"), "new-user");
assert.equal(persistedStorage.getItem("passwordServer"), "new-pass");
assert.equal(persistedStorage.getItem("port"), "2022");
const persistedMap = JSON.parse(persistedStorage.getItem("serverCredentialsByServer"));
assert.equal(persistedMap["77"].username, "new-user");
assert.equal(persistedMap["77"].port, 2022);

const switchedServerStorage = new FakeStorage({
  serverCredentialServerId: "10",
  serverCredentialsByServer: JSON.stringify({
    "10": { username: "srv10", password: "pass10", port: 22 },
    "20": { username: "srv20", password: "pass20", port: 2022 },
  }),
});
const selectedServer20Result = resolveValidatedServerCredentials(
  switchedServerStorage,
  "20"
);
assert.equal(selectedServer20Result.valid, false);
assert.match(selectedServer20Result.reason, /different server/i);

persistScopedServerCredentials(switchedServerStorage, "20", {
  username: "srv20",
  password: "pass20",
  port: 2022,
});
const refreshedServer20Result = resolveValidatedServerCredentials(
  switchedServerStorage,
  "20"
);
assert.equal(refreshedServer20Result.valid, true);
assert.equal(refreshedServer20Result.credentials.username, "srv20");
assert.equal(refreshedServer20Result.credentials.port, 2022);

console.log("serverCredentials tests passed");
