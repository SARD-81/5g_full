import assert from "node:assert/strict";
import {
  getBackendCommandError,
  mapServiceError,
  resolveValidatedServerCredentials,
} from "../src/assets/scripts/serverCredentials.js";

class FakeStorage {
  constructor(values = {}) {
    this.values = values;
  }

  getItem(key) {
    return this.values[key] ?? null;
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

console.log("serverCredentials tests passed");
