<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;

class AaaInitTest extends UnitTestCase
{
    #[Test]
    public function initDb(): void
    {
        system(
            'php bin/console --env=test doctrine:database:drop --force --if-exists >/dev/null && \
            php bin/console --env=test doctrine:database:create > /dev/null && \
            php bin/console --env=test doctrine:schema:create -q',
            $result_code
        );

        $this->assertEquals(0, $result_code);
    }
}
