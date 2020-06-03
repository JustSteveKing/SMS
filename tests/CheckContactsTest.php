<?php

declare(strict_types=1);

namespace JustSteveKing\Tests\SMS;

class CheckContactsTest extends BaseApplication
{
    public function testCanRetrieveGroupsFromApi()
    {
        $response = $this->sender->groups();

        $this->assertNotEmpty($response);

        $this->assertIsInt(
            $response->num_groups
        );

        $this->assertIsArray(
            $response->groups
        );

        $this->assertEquals(
            'success',
            $response->status
        );
    }
}
