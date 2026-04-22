import assert from "node:assert/strict";
import { processTransportError } from "../src/assets/scripts/useApiTransport.js";

const structuredPayload = {
  success: false,
  error_code: "sudo_failed",
  message: "SSH connected, but sudo/systemctl execution failed.",
  details: { command: "systemctl status open5gs-amfd" },
};

let defaultHandlerCalls = 0;
let capturedPayload;

await processTransportError({
  error: {
    response: {
      status: 500,
      data: structuredPayload,
    },
  },
  errorCallback: (payload) => {
    capturedPayload = payload;
    return { handled: true };
  },
  suppressDefaultErrorHandler: false,
  defaultErrorHandler: () => {
    defaultHandlerCalls += 1;
  },
});

assert.equal(defaultHandlerCalls, 0);
assert.equal(capturedPayload.error_code, "sudo_failed");
assert.equal(capturedPayload.message, structuredPayload.message);

await processTransportError({
  error: {
    response: {
      status: 500,
      data: { message: "generic failure" },
    },
  },
  suppressDefaultErrorHandler: false,
  defaultErrorHandler: () => {
    defaultHandlerCalls += 1;
  },
});

assert.equal(defaultHandlerCalls, 1);

await processTransportError({
  error: {
    response: {
      status: 500,
      data: { message: "generic failure" },
    },
  },
  errorCallback: () => true,
  suppressDefaultErrorHandler: false,
  defaultErrorHandler: () => {
    defaultHandlerCalls += 1;
  },
});

assert.equal(defaultHandlerCalls, 1);

console.log("useApi transport tests passed");
