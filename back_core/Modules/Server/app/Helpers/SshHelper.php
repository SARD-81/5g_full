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
        $this->ssh->setTimeout(5);
        $fullCommand = sprintf(
            "echo %s | sudo -S -p '' bash -lc %s 2>&1; echo '__CMD_EXIT__:'$?",
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

        $this->logActivity($typeCommand, $method, [
            'command' => $command,
            'output' => $output
        ]);

        return $output;

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
