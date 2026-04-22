export const SERVER_CREDENTIAL_SCOPE_KEY = "serverCredentialServerId";

export function resolveValidatedServerCredentials(localStorageRef, selectedServerId) {
  const username = localStorageRef.getItem("userNameServer");
  const password = localStorageRef.getItem("passwordServer");
  const portRaw = localStorageRef.getItem("port");
  const scopedServerId = localStorageRef.getItem(SERVER_CREDENTIAL_SCOPE_KEY);

  if (!selectedServerId) {
    return {
      valid: false,
      reason: "No server is selected.",
      errorCode: "validation_failed",
    };
  }

  if (!username || !password) {
    return {
      valid: false,
      reason: "SSH credentials are missing. Please re-enter server credentials.",
      errorCode: "validation_failed",
    };
  }

  if (scopedServerId && String(scopedServerId) !== String(selectedServerId)) {
    return {
      valid: false,
      reason: "Saved SSH credentials belong to a different server. Please re-enter credentials.",
      errorCode: "validation_failed",
    };
  }

  const parsedPort = Number(portRaw || 22);
  if (!Number.isInteger(parsedPort) || parsedPort < 1 || parsedPort > 65535) {
    return {
      valid: false,
      reason: "SSH port is invalid. Please re-enter server credentials.",
      errorCode: "validation_failed",
    };
  }

  return {
    valid: true,
    credentials: {
      username,
      password,
      port: parsedPort,
    },
  };
}

export function mapServiceError(errorCode, fallbackMessage = "Request failed.") {
  const messages = {
    server_off: "The selected server is offline.",
    ssh_login_failed: "SSH login failed. Username/password/port is invalid.",
    ssh_connection_failed: "SSH connection to server failed.",
    sudo_failed: "SSH connected, but sudo/systemctl execution failed.",
    service_not_found: "Service unit was not found on the server.",
    service_command_failed: "Service command failed on the remote server.",
    validation_failed: "The request is invalid. Please review credentials and server/module selection.",
  };

  return messages[errorCode] || fallbackMessage;
}
