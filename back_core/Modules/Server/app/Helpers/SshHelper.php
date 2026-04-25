<?php

namespace Modules\Server\Helpers;

use Exception;
use Illuminate\Validation\ValidationException;
use phpseclib3\Net\SSH2;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;

class SshHelper
{
    protected $ssh;

    public function __construct(private $server_ip, private $username, private $password, private $port = 22, private $timeout = 1)
    {
        $this->ssh = new SSH2($server_ip, $this->port, $this->timeout);

        if (!$this->ssh->login($username, $password)) {
            $this->logActivity('failed-connection-server', 'constructor');
                throw ValidationException::withMessages(['server-login' => 'Your server login credentials are incorrect.']);
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
        $this->ssh->setTimeout(10);

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



    public function runCommandModule(string $command, string $typeCommand, string $method)
    {
        $fullCommand = sprintf(
            "TMP_OUT=$(mktemp) && TMP_ERR=$(mktemp) && echo %s | sudo -S -p '' bash -lc %s >\"\$TMP_OUT\" 2>\"\$TMP_ERR\"; __RC=$?; printf '__CMD_STDOUT_START__\\n'; cat \"\$TMP_OUT\"; printf '\\n__CMD_STDOUT_END__\\n__CMD_STDERR_START__\\n'; cat \"\$TMP_ERR\"; printf '\\n__CMD_STDERR_END__\\n__CMD_EXIT__:%%s\\n' \"\$__RC\"; rm -f \"\$TMP_OUT\" \"\$TMP_ERR\"",
            escapeshellarg($this->password),
            escapeshellarg($command)
        );

        $output = $this->ssh->exec($fullCommand);

        if (str_contains($output, 'FATAL') || str_contains($output, 'ERROR')) {
            $this->logActivity('module-error', $method,
                ['command' => $command, 'output' => $output]);
            throw new InvalidArgumentException($output);
        }
        else
            $this->logActivity($typeCommand, $method,
                ['command' => $command, 'output' => $output]);


        return $output;
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
