<?php


namespace Modules\User\Services;

use GuzzleHttp\Client;
use Illuminate\Validation\ValidationException;
use Modules\SystemSetting\Service\SmsStatusService;
use mysql_xdevapi\SqlStatementResult;

class SMSService
{

    /**
     * send message to sanway panel sms
     */
    public function sendMessageAsync(
        ?string $username,
        ?string $password,
        ?array $recipientNumbers,
        ?string $message,
        ?string $sender,
        ?bool $isFlash,
        ?array $messageIds
    ): mixed {
        $params = [
            'service'       => 'SendArray',
            'UserName'      => $username,
            'Password'      => $password,
            'To'            => implode(',', $recipientNumbers),
            'Message'       => $message,
            'From'          => $sender,
            'Flash'         => $isFlash ? 'true' : 'false',
            'chkMessageId'  => implode(',', $messageIds),
        ];

        $query = http_build_query($params);
        $url = env('SUN_WAY_SMS_ADDRESS') . '?' . $query;

        exec("curl -s \"$url\"", $output, $returnCode);


//        return error messange as panel sms sanway
        if (! empty(array_filter(SmsStatusService::getStatusMessages($output))))
            throw ValidationException::withMessages(['sms_panel_error' => SmsStatusService::getStatusMessages($output)]);


        return $output;
    }

    /**
     * get user info panel sms sanway
     */
    public function getUserInfo (string $username, string $password)
    {
        $params = [
            'service'       => 'GetUserInfo',
            'UserName'      => $username,
            'Password'      => $password,
        ];

        $query = http_build_query($params);
        $url = env('SUN_WAY_SMS_ADDRESS') . '?' . $query;

        exec("curl -s \"$url\"", $output, $returnCode);


        $output = json_decode($output[0], true);

//        return error messange as panel sms sanway
        if (! empty(array_filter(SmsStatusService::getStatusMessages(array($output['Status'])))))
            throw ValidationException::withMessages(['sms_panel_error' => SmsStatusService::getStatusMessages(array($output['Status']))]);


        return $output;
    }
}
