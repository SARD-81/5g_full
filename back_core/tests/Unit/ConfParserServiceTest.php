<?php

namespace Tests\Unit;

use Modules\Server\Service\Parser\ConfParserService;
use PHPUnit\Framework\TestCase;

class ConfParserServiceTest extends TestCase
{
    public function test_it_parses_and_serializes_loadextension_and_connectpeer_patterns(): void
    {
        $conf = <<<'CONF'
# sample
NoRelay;
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dbg_msg_dumps.fdx" : "0x8888";
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_rfc5777.fdx";

ConnectPeer = "mme.localdomain" { ConnectTo = "127.0.0.2"; No_TLS; };
ConnectPeer = "mme12.localdomain" { ConnectTo = "127.0.0.12"; No_TLS; };
CONF;

        $parsed = ConfParserService::parseString($conf, 'conf');
        $data = $parsed['data'];

        $this->assertIsArray($data['LoadExtension']);
        $this->assertSame('/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dbg_msg_dumps.fdx', $data['LoadExtension'][0]['value']);
        $this->assertSame('0x8888', $data['LoadExtension'][0]['argument']);
        $this->assertSame('/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_rfc5777.fdx', $data['LoadExtension'][1]['value']);

        $this->assertIsArray($data['ConnectPeer']);
        $this->assertSame('mme.localdomain', $data['ConnectPeer'][0]['value']);
        $this->assertSame('127.0.0.2', $data['ConnectPeer'][0]['ConnectTo']);
        $this->assertSame('No_TLS', $data['ConnectPeer'][0]['_directives'][0]);
        $this->assertSame('mme12.localdomain', $data['ConnectPeer'][1]['value']);

        $serialized = ConfParserService::serialize($parsed);
        $this->assertStringContainsString('LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dbg_msg_dumps.fdx" : "0x8888";', $serialized);
        $this->assertStringContainsString('ConnectPeer = "mme.localdomain"', $serialized);
        $this->assertStringNotContainsString('(copy)', $serialized);
        $this->assertStringNotContainsString("\nLoadExtension = :\n", $serialized);
    }
}

