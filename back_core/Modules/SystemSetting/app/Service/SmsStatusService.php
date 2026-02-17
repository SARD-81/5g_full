<?php

namespace Modules\SystemSetting\Service;


final class SmsStatusService
{
    private const PREFIX = 'response sms panel sanway : ';

    /** @var array<string,string> */
    private const MESSAGES = [
        '1'   => 'No status has been received yet',
        '2'   => 'The SMS has been delivered to the recipient\'s mobile',
        '3'   => 'The SMS has not been delivered to the recipient\'s mobile',
        '4'   => 'The SMS has been delivered to the telecom center',
        '5'   => 'The SMS has not been delivered to the telecom center',
        '6'   => 'The recipient\'s mobile number is in the telecom inactive list',
        '7'   => 'The SMS is in the sending queue',
        '8'   => 'The server is currently sending the SMS',
        '9'   => 'Insufficient credit to send the SMS',
        '10'  => 'The SMS was not sent (connection error)',
        '11'  => 'The SMS has not yet been confirmed by the operator',
        '12'  => 'The SMS is in the canceled or filtered list',
        '50'  => 'The operation was successful',
        '51'  => 'Username or password is incorrect',
        '52'  => 'Username or password is empty',
        '53'  => 'RecipientNumber key exceeds the allowed limit (more than 1000 numbers)',
        '54'  => 'RecipientNumber key is empty',
        '55'  => 'RecipientNumber key is invalid (its value is Null)',
        '56'  => 'MessageID array exceeds the allowed limit (more than 1000 numbers)',
        '57'  => 'MessageID key is empty',
        '58'  => 'MessageID key is invalid (its value is Null)',
        '59'  => 'MessageBody key is empty',
        '60'  => 'The server is currently unable to respond due to high traffic...',
        '61'  => 'SpecialNumber key is invalid (the entered number does not exist or does not belong to this user)',
        '62'  => 'SpecialNumber key is empty',
        '77'  => 'You are not a web service user',
        '80'  => 'The web service is currently disabled by the admin (try again later)',
        '202' => 'The telecom operator of the RecipientNumber is unknown to the system',
        '203' => 'Due to insufficient credit, you cannot send SMS to this number (recharge your account and try again)',
        '205' => 'The format of the PersonNumber is incorrect',
        '206' => 'The operator number is invalid',
        '300' => 'Sending SMS containing links is not allowed (link agreement not signed)',
        '400' => 'The number of requests sent exceeds the allowed limit in a single call or time interval',
        '666' => 'The service is temporarily disabled',
        '777' => 'This IP is blocked',
        '888' => 'Sender number authentication has not been registered',
        '999' => 'Sending this SMS is not allowed',
    ];


    public static function getStatusMessages(array $statusCodes): array | bool
    {
        return array_map(function (string $code) {
            $raw = self::getMessageByCode($code);

            if (! $raw)
                return;

            $message = $raw ?? false;
            return self::PREFIX . $message;
        }, $statusCodes);
    }

    private static function getMessageByCode(string $code): ?string
    {
        return self::MESSAGES[$code] ?? null;
    }
}
