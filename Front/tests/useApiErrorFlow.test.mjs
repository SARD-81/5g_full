import assert from "node:assert/strict";
import {
  shouldRunDefaultErrorHandler,
  shouldSkipDefaultErrorHandler,
} from "../src/assets/scripts/useApiErrorFlow.js";

assert.equal(shouldSkipDefaultErrorHandler(true), true);
assert.equal(shouldSkipDefaultErrorHandler({ handled: true }), true);
assert.equal(shouldSkipDefaultErrorHandler(undefined), false);

assert.equal(
  shouldRunDefaultErrorHandler({
    errorCallbackResult: { handled: true },
    suppressDefaultErrorHandler: false,
  }),
  false
);

assert.equal(
  shouldRunDefaultErrorHandler({
    errorCallbackResult: undefined,
    suppressDefaultErrorHandler: false,
  }),
  true
);

assert.equal(
  shouldRunDefaultErrorHandler({
    errorCallbackResult: undefined,
    suppressDefaultErrorHandler: true,
  }),
  false
);

console.log("useApi error flow tests passed");
