<?php
// licence proprietary

namespace App\Tests;

use Fidry\AliceDataFixtures\Loader\PurgerLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FixtureTest
 */
class FixtureTest extends WebTestCase
{
    /** @var PurgerLoader|object|null */
    protected $databaseTool;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = static::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
    }

    /**
     * Test pour remplir la base de données.
     */
    public function testRemplissage(): void
    {
        if ($this->databaseTool instanceof PurgerLoader) {
            $this->databaseTool->load([
              __DIR__.'/../src/DataFixtures/author.yml',
              __DIR__.'/../src/DataFixtures/editor.yml',
              __DIR__.'/../src/DataFixtures/tag.yml',
              __DIR__.'/../src/DataFixtures/book.yml',
            ]);
        }
    }
}
