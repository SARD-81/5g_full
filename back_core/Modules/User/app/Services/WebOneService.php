<?php

namespace Modules\User\Services;



use Illuminate\Support\Facades\Http;
class WebOneService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $fromNumber;

    public function __construct()
    {
        $this->baseUrl = env('WEB_ONE_BASE_URL');
        $this->username = env('WEB_ONE_USERNAME');
        $this->password = env('WEB_ONE_PASSWORD');
        $this->fromNumber = env('WEB_ONE_FORM_NUMBER');
    }



    public function sendSms(string $to, string $message)
    {
        try {

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/SMS/Send', [
                'UserName' => $this->username,
                'Password' => $this->password,
                'From' => $this->fromNumber,
                'To' => $to,
                'Message' => $message,
            ]);

                // success
            if ($response->successful())
                return response()->json(['success' => true, 'data' => $response->json()], 200);

                // faile
            return response()->json(['success' => false, 'data' => $response->body()], 422);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
