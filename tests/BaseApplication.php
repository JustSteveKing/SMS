<?php

declare(strict_types=1);

namespace JustSteveKing\Tests\SMS;

use Dotenv\Dotenv;
use JustSteveKing\SMS\Sender;
use PHPUnit\Framework\TestCase;

class BaseApplication extends TestCase
{
    protected Sender $sender;

    protected function setUp(): void
    {
        parent::setUp();

        try {
            $env = Dotenv::createImmutable(__DIR__ . '/../');
            $env->load();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->sender = new Sender(getenv('API_KEY'), false);
    }
}