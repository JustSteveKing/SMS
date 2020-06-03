<?php

declare(strict_types=1);

namespace JustSteveKing\Tests\SMS;

class CheckBalanceTest extends BaseApplication
{
    public function testBalanceCanBeRetrievedFromTheApi()
    {
        $response = $this->sender->balance();

        $this->assertTrue(
            ! empty($response)
        );

        $this->assertIsArray($response);

        $this->assertArrayHasKey('sms', $response);
        $this->assertArrayHasKey('mms', $response);
    }
}
