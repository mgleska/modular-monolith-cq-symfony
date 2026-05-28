<?php
declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

abstract class IntegrationTestCase extends KernelTestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->container = static::getContainer();
    }
}
