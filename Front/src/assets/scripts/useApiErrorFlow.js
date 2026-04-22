export function shouldSkipDefaultErrorHandler(errorCallbackResult) {
  return (
    errorCallbackResult === true ||
    (typeof errorCallbackResult === "object" &&
      errorCallbackResult !== null &&
      errorCallbackResult.handled === true)
  );
}

export function shouldRunDefaultErrorHandler({
  errorCallbackResult,
  suppressDefaultErrorHandler,
}) {
  if (suppressDefaultErrorHandler) return false;
  if (shouldSkipDefaultErrorHandler(errorCallbackResult)) return false;
  return true;
}
