<?php
// licence proprietary

namespace App\Tests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FixtureTest
 */
class FixtureTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    /**
     * Test pour remplir la base de données.
     */
    public function testRemplissage(): void
    {
        $this->databaseTool->loadFixtures([
            __DIR__.'/../src/DataFixtures/author.yml',
            __DIR__.'/../src/DataFixtures/editor.yml',
            __DIR__.'/../src/DataFixtures/tag.yml',
            __DIR__.'/../src/DataFixtures/book.yml',
        ]);
    }
}
