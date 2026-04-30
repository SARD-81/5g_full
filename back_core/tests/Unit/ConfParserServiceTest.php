<?php

namespace Tests\Unit;

use Modules\Server\Service\Parser\ConfParserService;
use PHPUnit\Framework\TestCase;

class ConfParserServiceTest extends TestCase
{
    public function test_hss_conf_round_trip_preserves_quotes_loadextension_and_connectpeer(): void
    {
        $conf = <<<'CONF'
Identity = "hss.localdomain";
Realm = "localdomain";
ListenOn = "127.0.0.8";
TLS_CA = "/home/soheil/open5gs/install/etc/open5gs/tls/ca.crt";
TLS_Cred = "/home/soheil/open5gs/install/etc/open5gs/tls/hss.crt", "/home/soheil/open5gs/install/etc/open5gs/tls/hss.key";
NoRelay;
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dbg_msg_dumps.fdx" : "0x8888";
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_rfc5777.fdx";
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_mip6i.fdx";
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_nasreq.fdx";
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_nas_mipv6.fdx";
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_dcca.fdx";
LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_dcca_3gpp.fdx";
ConnectPeer = "mme.localdomain" { ConnectTo = "127.0.0.2"; No_TLS; };
ConnectPeer = "mme12.localdomain" { ConnectTo = "127.0.0.12"; No_TLS; };
CONF;

        $serialized = ConfParserService::serialize(ConfParserService::parseString($conf, 'conf'));

        $this->assertStringContainsString('Identity = "hss.localdomain";', $serialized);
        $this->assertStringContainsString('Realm = "localdomain";', $serialized);
        $this->assertStringContainsString('ListenOn = "127.0.0.8";', $serialized);
        $this->assertStringContainsString('TLS_CA = "/home/soheil/open5gs/install/etc/open5gs/tls/ca.crt";', $serialized);
        $this->assertStringContainsString('TLS_Cred = "/home/soheil/open5gs/install/etc/open5gs/tls/hss.crt", "/home/soheil/open5gs/install/etc/open5gs/tls/hss.key";', $serialized);
        $this->assertStringContainsString('LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dbg_msg_dumps.fdx" : "0x8888";', $serialized);
        $this->assertStringContainsString('LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_rfc5777.fdx";', $serialized);
        $this->assertStringContainsString('LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_mip6i.fdx";', $serialized);
        $this->assertStringContainsString('LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_nasreq.fdx";', $serialized);
        $this->assertStringContainsString('LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_nas_mipv6.fdx";', $serialized);
        $this->assertStringContainsString('LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_dcca.fdx";', $serialized);
        $this->assertStringContainsString('LoadExtension = "/home/soheil/open5gs/install/lib/x86_64-linux-gnu/freeDiameter/dict_dcca_3gpp.fdx";', $serialized);
        $this->assertStringContainsString('ConnectPeer = "mme.localdomain" { ConnectTo = "127.0.0.2"; No_TLS; };', $serialized);
        $this->assertStringContainsString('ConnectPeer = "mme12.localdomain" { ConnectTo = "127.0.0.12"; No_TLS; };', $serialized);

        $this->assertStringNotContainsString('Identity = hss.localdomain";', $serialized);
        $this->assertStringNotContainsString('Realm = localdomain";', $serialized);
        $this->assertStringNotContainsString('ListenOn = 127.0.0.8";', $serialized);
        $this->assertStringNotContainsString('LoadExtension = ;', $serialized);
        $this->assertStringNotContainsString('LoadExtension = ""/', $serialized);
        $this->assertStringNotContainsString('Identity = "hss.localdomain"', $serialized);
        $this->assertStringNotContainsString('Realm = "localdomain"', $serialized);
        $this->assertStringNotContainsString('_directives =', $serialized);
        $this->assertSame(2, substr_count($serialized, 'ConnectPeer = "'));
    }

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

    public function test_directives_are_not_duplicated_and_removed_directives_do_not_reappear(): void
    {
        $parsed = ConfParserService::parseString("NoRelay;\n", 'conf');
        $this->assertSame('NoRelay', $parsed['data']['_directives'][0]);

        $serialized = ConfParserService::serialize($parsed);
        $this->assertSame(1, substr_count($serialized, 'NoRelay;'));

        $parsed['data']['_directives'][] = 'NewDirective';
        $serializedWithNew = ConfParserService::serialize($parsed);
        $this->assertSame(1, substr_count($serializedWithNew, 'NoRelay;'));
        $this->assertSame(1, substr_count($serializedWithNew, 'NewDirective;'));

        $parsed['data']['_directives'] = ['No_TLS'];
        $serializedRemoved = ConfParserService::serialize($parsed);
        $this->assertSame(0, substr_count($serializedRemoved, 'NoRelay;'));
        $this->assertSame(1, substr_count($serializedRemoved, 'No_TLS;'));
    }
}
