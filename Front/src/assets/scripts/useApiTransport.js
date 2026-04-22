import { shouldRunDefaultErrorHandler } from "./useApiErrorFlow.js";

export function resolveShouldRunDefaultErrorHandler({
  errorCallbackResult,
  suppressDefaultErrorHandler,
}) {
  return shouldRunDefaultErrorHandler({
    errorCallbackResult,
    suppressDefaultErrorHandler,
  });
}

export async function processTransportError({
  error,
  errorCallback,
  suppressDefaultErrorHandler,
  defaultErrorHandler,
}) {
  let errorCallbackResult;

  if (errorCallback && typeof errorCallback === "function") {
    errorCallbackResult = await errorCallback(error.response?.data, error.response);
  }

  const shouldRunDefault = resolveShouldRunDefaultErrorHandler({
    errorCallbackResult,
    suppressDefaultErrorHandler,
  });

  if (shouldRunDefault && typeof defaultErrorHandler === "function") {
    defaultErrorHandler(error);
  }

  return {
    shouldRunDefault,
    errorCallbackResult,
  };
}
