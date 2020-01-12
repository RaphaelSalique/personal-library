<?php
// licence proprietary

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FixtureTest
 */
class FixtureTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * test pour remplir la base de données.
     */
    public function testRemplissage()
    {
        $this->markTestSkipped(
            'Ne sert que pour remplir la BDD'
        );
        $this->loadFixtureFiles(array(
            __DIR__.'/../src/DataFixtures/author.yml',
            __DIR__.'/../src/DataFixtures/editor.yml',
            __DIR__.'/../src/DataFixtures/tag.yml',
            __DIR__.'/../src/DataFixtures/book.yml',
        ));
    }

}
