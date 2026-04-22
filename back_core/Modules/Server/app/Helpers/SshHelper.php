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

    public function __construct(private $server_ip, private $username, private $password, private $port = 22, private $timeout = 5)
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
        // تایم‌اوت را می‌توانید نگه دارید، اما با exec معمولا فقط در صورت طول کشیدن خود دستور اعمال می‌شود
        $this->ssh->setTimeout(5);

        // ترکیب تمام مراحل در یک دستور خطی
        // پسورد از طریق pipe به sudo داده می‌شود و دستور داخل bash اجرا می‌گردد
        $fullCommand = sprintf(
            "echo %s | sudo -S -p '' bash -c %s",
            escapeshellarg($this->password),
            escapeshellarg($command)
        );

        // اجرای یکباره دستور (جایگزین ۳ مرحله write و read)
        $output = $this->ssh->exec($fullCommand);

        $this->logActivity($typeCommand, $method, [
            'command' => $command,
            'output' => $output
        ]);

        return $output;

    } catch (Exception $e) {
        $this->logActivity('module-error', $method, [
            'command' => $command,
            'error' => $e->getMessage()
        ]);

        throw $e;
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
