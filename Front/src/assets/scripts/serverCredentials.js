export const SERVER_CREDENTIAL_SCOPE_KEY = "serverCredentialServerId";
export const SERVER_CREDENTIALS_MAP_KEY = "serverCredentialsByServer";

function readCredentialMap(localStorageRef) {
  const raw = localStorageRef.getItem(SERVER_CREDENTIALS_MAP_KEY);
  if (!raw) return {};

  try {
    const parsed = JSON.parse(raw);
    if (parsed && typeof parsed === "object") {
      return parsed;
    }
  } catch (error) {
    return {};
  }

  return {};
}

export function getBackendCommandError(payload) {
  if (!payload || typeof payload !== "object") return null;
  if (payload.error_code) {
    return {
      code: payload.error_code,
      message: payload.message || "Request failed.",
      details: payload.details || {},
    };
  }

  if (payload.error && payload.error.code) {
    return {
      code: payload.error.code,
      message: payload.error.message || payload.message || "Request failed.",
      details: payload.error.details || payload.details || {},
    };
  }

  return null;
}

export function resolveValidatedServerCredentials(localStorageRef, selectedServerId) {
  const serverKey = String(selectedServerId || "");
  const byServer = readCredentialMap(localStorageRef);
  const scoped = byServer[serverKey] || null;
  const username = scoped?.username || localStorageRef.getItem("userNameServer");
  const password = scoped?.password || localStorageRef.getItem("passwordServer");
  const portRaw = scoped?.port || localStorageRef.getItem("port");
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
    ssh_login_failed: "SSH login failed. Please verify username, password, or port.",
    ssh_connection_failed: "Could not establish SSH connection to the server.",
    sudo_failed: "SSH connected, but sudo/systemctl execution failed.",
    service_not_found: "The expected service unit was not found on the server.",
    service_command_failed:
      "The service command reached the server but failed to execute successfully.",
    command_timeout: "The remote command timed out before completion.",
    unexpected_remote_error: "An unexpected remote command error occurred.",
    validation_failed: "The request is invalid. Please review credentials and server/module selection.",
  };

  return messages[errorCode] || fallbackMessage;
}
