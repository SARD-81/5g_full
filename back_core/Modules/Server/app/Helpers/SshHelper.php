<?php

namespace Modules\Server\Helpers;

use Exception;
use Modules\Server\Exceptions\CommandExecutionException;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Auth;

class SshHelper
{
    protected $ssh;

    public function __construct(private $server_ip, private $username, private $password, private $port = 22, private $timeout = 5)
    {
        try {
            $this->ssh = new SSH2($server_ip, $this->port, $this->timeout);
        } catch (\Throwable $exception) {
            throw new CommandExecutionException(
                'ssh_connection_failed',
                'Unable to establish SSH connection to remote server.',
                ['exception' => $exception->getMessage()],
                502
            );
        }

        if (!$this->ssh->login($username, $password)) {
            $this->logActivity('failed-connection-server', 'constructor');
            throw new CommandExecutionException(
                'ssh_login_failed',
                'SSH login failed. Username/password/port is invalid.',
                ['host' => $this->server_ip, 'port' => $this->port],
                401
            );
        }
    }

        // log
    protected function logActivity($event, $method, $extra = [])
    {
        activity($event)
            ->causedBy(Auth::user())
            ->event($event)
            ->withProperties(array_merge([
                'type-log' => 'server',
                'route' => request()->fullUrl(),
                'method' => $method,
                'user' =>  Auth::user()?->makeHidden(['roles', 'permissions'])->toArray(),
                'user_role' =>Auth::user()?->roles()->pluck('name')->first(),
                'ssh_connection_details' => [
                    'host' => $this->server_ip,
                    'username' => $this->username,
                    'port' => $this->port,
                ],
//                'server' => $this->server
            ], $extra))
            ->log($event);
    }

  public function runCommand($command)
{
    try {
        $this->ssh->setTimeout(5);

        $this->ssh->write("sudo -S su\n");
        $this->ssh->write("{$this->password}\n");
        $this->ssh->read();

        $this->ssh->write("$command\n");
        $output = $this->ssh->read();

        return $output;
    } catch (Exception $e) {
        $this->logActivity('failed-command', 'runCommand', [
            'Error' => $e
        ]);


        throw new Exception('Server error! Please try again');
    }
}
    public function getFileContent($command)
    {
        $fileContent = $this->ssh->exec($command);

        $this->logActivity('export-file', 'getFileContent', ['command' => $command]);

        return $fileContent;
    }
    public function testConnection()
    {
        return true;
    }


public function runCommandModule(string $command, string $typeCommand, string $method): string
{
    try {
        $this->ssh->setTimeout($this->timeout);
        $fullCommand = sprintf(
            "TMP_OUT=$(mktemp) && TMP_ERR=$(mktemp) && echo %s | sudo -S -p '' bash -lc %s >\"$TMP_OUT\" 2>\"$TMP_ERR\"; __RC=$?; printf '__CMD_STDOUT_START__\\n'; cat \"$TMP_OUT\"; printf '\\n__CMD_STDOUT_END__\\n__CMD_STDERR_START__\\n'; cat \"$TMP_ERR\"; printf '\\n__CMD_STDERR_END__\\n__CMD_EXIT__:%s\\n' \"$__RC\"; rm -f \"$TMP_OUT\" \"$TMP_ERR\"",
            escapeshellarg($this->password),
            escapeshellarg($command)
        );

        $output = $this->ssh->exec($fullCommand);
        if ($output === false) {
            throw new CommandExecutionException(
                'ssh_connection_failed',
                'SSH command execution failed because the remote channel was interrupted.',
                ['command' => $command],
                502
            );
        }

        if (method_exists($this->ssh, 'isTimeout') && $this->ssh->isTimeout()) {
            throw new CommandExecutionException(
                'command_timeout',
                'Remote command timed out before completion.',
                ['command' => $command],
                504
            );
        }

        $result = $this->parseCommandOutput((string) $output);
        $combinedOutput = trim($result['stdout'] . "\n" . $result['stderr']);
        $lowerOutput = mb_strtolower($combinedOutput);

        if ($combinedOutput === '') {
            throw new CommandExecutionException(
                'unexpected_remote_error',
                'Remote server returned an empty response.',
                ['command' => $command, 'exit_code' => $result['exit_code']],
                502
            );
        }

        if (preg_match('/(permission denied|a terminal is required|no tty present|sudoers|incorrect password|authentication failure)/i', $combinedOutput)) {
            throw new CommandExecutionException(
                'sudo_failed',
                'SSH connected, but sudo/systemctl execution failed.',
                ['command' => $command, 'output' => $combinedOutput, 'exit_code' => $result['exit_code']]
            );
        }

        if (preg_match('/(unit\s+.+\s+could\s+not\s+be\s+found|loaded:\s+not-found|not-found)/i', $combinedOutput)) {
            throw new CommandExecutionException(
                'service_not_found',
                'The expected service unit was not found on the server.',
                ['command' => $command, 'output' => $combinedOutput, 'exit_code' => $result['exit_code']]
            );
        }

        if (preg_match('/(connection reset|connection closed|broken pipe|transport endpoint|unable to open channel)/i', $lowerOutput)) {
            throw new CommandExecutionException(
                'ssh_connection_failed',
                'Could not establish SSH command channel to remote server.',
                ['command' => $command, 'output' => $combinedOutput],
                502
            );
        }

        if ($result['exit_code'] !== 0) {
            $errorCode = str_contains($command, 'systemctl') ? 'service_command_failed' : 'unexpected_remote_error';
            throw new CommandExecutionException(
                $errorCode,
                $errorCode === 'service_command_failed'
                    ? 'The service command reached the server but failed to execute successfully.'
                    : 'Remote command execution failed.',
                ['command' => $command, 'output' => $combinedOutput, 'exit_code' => $result['exit_code']]
            );
        }

        $this->logActivity($typeCommand, $method, [
            'command' => $command,
            'output' => $combinedOutput,
            'stdout' => $result['stdout'],
            'stderr' => $result['stderr'],
            'exit_code' => $result['exit_code'],
        ]);

        return $combinedOutput;

    } catch (CommandExecutionException $exception) {
        throw $exception;
    } catch (Exception $e) {
        $this->logActivity('module-error', $method, [
            'command' => $command,
            'error' => $e->getMessage()
        ]);

        throw new CommandExecutionException(
            'ssh_connection_failed',
            'SSH command execution failed unexpectedly.',
            ['command' => $command, 'exception' => $e->getMessage()],
            502
        );
    }
}

    private function parseCommandOutput(string $output): array
    {
        $stdout = $output;
        $stderr = '';
        $exitCode = 0;

        if (preg_match('/__CMD_STDOUT_START__\n(?P<stdout>.*)\n__CMD_STDOUT_END__\n__CMD_STDERR_START__\n(?P<stderr>.*)\n__CMD_STDERR_END__\n__CMD_EXIT__:(?P<code>\d+)/s', $output, $matches)) {
            $stdout = trim($matches['stdout']);
            $stderr = trim($matches['stderr']);
            $exitCode = (int) $matches['code'];
        } elseif (preg_match('/__CMD_EXIT__:(\d+)/', $output, $matches)) {
            $exitCode = (int) $matches[1];
            $stdout = trim((string) preg_replace('/__CMD_EXIT__:\d+\s*$/', '', $output));
        }

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'exit_code' => $exitCode,
        ];
    }


    public function pingRunCommand ($command)
    {
        try {
            $this->ssh->setTimeout(5);
            $this->ssh->write("sudo -S su\n");
            $this->ssh->write("{$this->password}\n");
            $this->ssh->read();

            $output = $this->ssh->exec($command);

            $this->logActivity('ping', 'ping');

            return $output;
        } catch (Exception $e) {
            $this->logActivity('failed-command', 'runCommand', ['Error' => $e]);
            throw $e;
        }
    }

}
