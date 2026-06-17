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
        $this->ssh->setTimeout(10);
    
        $fullCommand = sprintf(
            "TMP_OUT=\$(mktemp) && TMP_ERR=\$(mktemp) && " .
            "echo %s | sudo -S -p '' bash -lc %s >\"\$TMP_OUT\" 2>\"\$TMP_ERR\"; " .
            "RC=\$?; " .
            "echo '__START_OUT__'; cat \"\$TMP_OUT\"; echo '__END_OUT__'; " .
            "echo '__START_ERR__'; cat \"\$TMP_ERR\"; echo '__END_ERR__'; " .
            "echo '__RC:'\"\$RC\"; " .
            "rm -f \"\$TMP_OUT\" \"\$TMP_ERR\"",
            escapeshellarg($this->password),
            escapeshellarg($command)
        );
    
        $output = $this->ssh->exec($fullCommand);
    
        // --- Parse خروجی به صورت دقیق ---
        preg_match('/__START_OUT__(.*?)__END_OUT__/s', $output, $outMatch);
        preg_match('/__START_ERR__(.*?)__END_ERR__/s', $output, $errMatch);
        preg_match('/__RC:(\d+)/', $output, $rcMatch);
    
        $stdout = isset($outMatch[1]) ? trim($outMatch[1]) : '';
        $stderr = isset($errMatch[1]) ? trim($errMatch[1]) : '';
        $exitCode = isset($rcMatch[1]) ? (int)$rcMatch[1] : 1;
    
        // فقط errorهای واقعی
        $isHardError = in_array($exitCode, [126, 127], true);
    
        if ($isHardError) {
            $this->logActivity('module-error', $method, [
                'command' => $command,
                'stdout' => $stdout,
                'stderr' => $stderr,
                'exit_code' => $exitCode
            ]);
    
            throw new CommandExecutionException(
                'remote_command_failed',
                $stderr ?: $stdout,
                compact('command', 'exitCode'),
                422
            );
        }
    
        $this->logActivity($typeCommand, $method, [
            'command' => $command,
            'stdout' => $stdout,
            'stderr' => $stderr,
            'exit_code' => $exitCode
        ]);
    
        // 🔥 فقط stdout برگردون (کاملاً تمیز)
        return $stdout;
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
