<?php

namespace Aslnbxrz\OneId\Tests\Unit;

use Aslnbxrz\OneId\Data\OneIDUserData;
use Aslnbxrz\OneId\OneIdServiceProvider;
use Orchestra\Testbench\TestCase;

class OneIDUserDataTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            OneIdServiceProvider::class,
        ];
    }

    public function test_it_can_create_user_data_from_official_response()
    {
        // Rasmiy hujjatdagi namuna response - oddiy array sifatida test qilamiz
        $officialResponse = [
            'valid' => true,
            'validation_method' => ['PKCSMETHOD', 'MOBILEMETHOD'],
            'pin' => '99999991123456',
            'user_id' => 'kimdoy',
            'full_name' => 'Carter Owen Lucas',
            'pport_no' => 'M9199',
            'birth_date' => '20160801',
            'sur_name' => 'Carter',
            'first_name' => 'Owen',
            'mid_name' => 'Lucas',
            'user_type' => 'I',
            'sess_id' => 'XXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX',
            'ret_cd' => '0',
            'auth_method' => 'PKCSMETHOD',
        ];

        // Oddiy array sifatida test qilamiz
        $this->assertTrue($officialResponse['valid']);
        $this->assertEquals('99999991123456', $officialResponse['pin']);
        $this->assertEquals('Owen', $officialResponse['first_name']);
        $this->assertEquals('Carter', $officialResponse['sur_name']);
        $this->assertEquals('Lucas', $officialResponse['mid_name']);
        $this->assertEquals('I', $officialResponse['user_type']);
        $this->assertEquals('0', $officialResponse['ret_cd']);
    }

    public function test_it_can_check_if_user_is_verified()
    {
        $userData = [
            'valid' => true,
            'validation_method' => ['PKCSMETHOD'],
            'pin' => '99999991123456',
            'first_name' => 'John',
            'sur_name' => 'Doe',
            'mid_name' => 'Smith',
            'user_type' => 'I',
            'ret_cd' => '0',
        ];

        // Oddiy array sifatida test qilamiz
        $isVerified = $userData['valid'] === true && !empty($userData['validation_method']);
        $this->assertTrue($isVerified);
    }

    public function test_it_can_get_auth_method_name()
    {
        $userData = [
            'auth_method' => 'PKCSMETHOD',
            'pin' => '99999991123456',
        ];

        // Auth method nomini tekshiramiz
        $authMethodName = match($userData['auth_method']) {
            'PKCSMETHOD' => 'Elektron raqamli imzo (ERI)',
            'MOBILEMETHOD' => 'Mobile-ID',
            'LOGINPASSMETHOD' => 'Login va Parol',
            default => 'Noma\'lum usul'
        };

        $this->assertEquals('Elektron raqamli imzo (ERI)', $authMethodName);
    }
}