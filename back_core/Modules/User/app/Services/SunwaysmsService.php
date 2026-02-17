<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SunwaysmsService
{
    protected string $endpoint = 'https://sms.sunwaysms.com/MSWS/SOAP.asmx';


    public function __construct
    (
        protected string $username,
        protected string $password,
        protected string $specialNumber,
    )
    {}


    public function sendMessage (string $recipientNumber, string $massage) :?string
    {
        $xml = $this->buildGetCreditPayload();

        try {

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://tempuri.org/GetCredit',
                'Accept' => 'text/xml',
            ])
                ->withBody($xml, 'text/xml')
                ->post($this->endpoint);

            return $this->parseGetCreditResponse($response->body());

        } catch (\Exception $e) {
            throw ValidationException::withMessages(['errors' => [$e->getMessage()]]);
        }
    }

    public function buildGetCreditPayload () :string
    {
        return <<<XML
        <?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                       xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                       xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <GetCredit xmlns="http://tempuri.org/">
              <UserName>{$this->username}</UserName>
              <Password>{$this->password}</Password>
            </GetCredit>
          </soap:Body>
        </soap:Envelope>
        XML;
    }



    protected function parseGetCreditResponse(string $body): ?string
    {
        dd($body);
        try {

            $xml = simplexml_load_string($body);
            $namespaces = $xml->getNamespaces(true);
            $body = $xml->children($namespaces['soap'])->Body;
            $response = $body->children($namespaces[''])->GetCreditResponse;

            return (string) $response->GetCreditResult ?? null;

        } catch (\Exception $e) {
            throw ValidationException::withMessages(['errors' => [$e->getMessage()]]);
        }
    }

}
