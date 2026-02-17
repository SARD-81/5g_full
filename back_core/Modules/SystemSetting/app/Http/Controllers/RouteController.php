<?php

namespace Modules\SystemSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Server\Helpers\SshHelper;
use Modules\SystemSetting\Http\Requests\Route\AddRouteServerRequest;
use Modules\SystemSetting\Http\Requests\Route\DeleteRouteServerRequest;
use Modules\SystemSetting\Http\Requests\Route\RouteServerRequest;

class RouteController extends Controller
{
    public function __construct() {}
    public function cleanOutput($output)
    {
        // ۱. حذف کدهای رنگی و کدهای کنترلی ANSI
        $output = preg_replace('/\x1b[[0-9;?]*[a-zA-Z]/', '', $output);

        // ۲. تبدیل تمام خطوط به آرایه برای پردازش خط به خط
        $lines = explode("\n", str_replace("\r", "", $output));

        $result = [];
        foreach ($lines as $line) {
            $line = trim($line);

            // ۳. نادیده گرفتن خطوط خالی، خطوط شامل دستور اجرا شده، و خطوط مربوط به پرومپت شل
            if (empty($line) ||
                str_contains($line, 'ip route show') ||
                preg_match('/^\[.*@.*\][#$]/', $line)) {
                continue;
            }

            $result[] = $line;
        }

        return implode("\n", $result);
    }

    public function showRouteServer(RouteServerRequest $request)
    {
        $credentials = $request->validated();

        $server   = $request['server'];
        $username = $credentials['username'];
        $password = $credentials['password'];
        $port     = $credentials['port'] ?? 22;

        try {
            DB::beginTransaction();

            $command = 'ip route show';
            $ssh     = new SshHelper($server->ip, $username, $password, $port, 5);
            $output  = $ssh->runCommandModule($command, 'show-route', 'showRouteServer');
            $output = $this->cleanOutput($output);
            DB::commit();
                return response()->json(['success' => true, 'msg' => 'show route server successfully', 'output' => $output], 200);

        } catch (\Exception $e) {
            DB::rollback();
                throw $e;
        }
    }
    public function addRouteServer(AddRouteServerRequest $request)
    {
        $credentials = $request->validated();

        $server   = $request['server'];
        $username = $credentials['username'];
        $password = $credentials['password'];
        $port     = $credentials['port'] ?? 22;


        try {
            DB::beginTransaction();

            isset($credentials['interface'])
                ? $command = 'ip route add ' . $credentials['destination_ip'] . ' via ' . $credentials['geteway_ip'] . ' dev ' . $credentials['interface_route']
                : $command = 'ip route add ' . $credentials['destination_ip'] . ' via ' . $credentials['geteway_ip'];


                $ssh    = new SshHelper($server->ip, $username, $password, $port);
                $output = $ssh->runCommandModule($command, 'add-route', 'addRouteServer');



            DB::commit();
            return response()->json(['success' => true, 'msg' => 'add route server successfully', 'output' => $output], 200);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function deleteRouteServer(DeleteRouteServerRequest $request)
    {
        $credentials = $request->validated();

        $server   = $request['server'];
        $username = $credentials['username'];
        $password = $credentials['password'];
        $port     = $credentials['port'] ?? 22;

        try {
            DB::beginTransaction();

            isset($credentials['interface'])
                ? $command = 'ip route del ' . $credentials['destination_ip'] . ' via ' . $credentials['geteway_ip'] . ' dev ' . $credentials['interface_route']
                : $command = 'ip route del ' . $credentials['destination_ip'] . ' via ' . $credentials['geteway_ip'];


            $ssh    = new SshHelper($server->ip, $username, $password, $port);
            $output = $ssh->runCommandModule($command, 'delete-route', 'deleteRouteServer');


            DB::commit();
               return response()->json(['success' => true, 'msg' => 'show route server successfully', 'output' => $output], 200);

        } catch (\Exception $e) {
            DB::rollback();
                throw $e;
        }
    }
}
