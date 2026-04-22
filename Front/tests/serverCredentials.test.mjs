import assert from "node:assert/strict";
import {
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
  "Service unit was not found on the server."
);
assert.equal(
  mapServiceError("ssh_login_failed", "fallback"),
  "SSH login failed. Username/password/port is invalid."
);
assert.equal(mapServiceError("unknown", "fallback"), "fallback");

console.log("serverCredentials tests passed");
