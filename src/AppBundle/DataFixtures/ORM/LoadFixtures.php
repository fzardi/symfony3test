<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadFixtures implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(
            __DIR__.'/fixtures.yml',
            $manager,
            ['providers' => [ $this ] ]
        );
    }

    public function genus()
    {
        $genera = [
            'Genus1',
            'Genus2',
            'Genus3',
            'Genus4',
            'Genus5',
            'Genus6',
            'Genus7',
            'Genus8',
            'Genus9'
        ];

        $key = array_rand($genera);
        return $genera[$key];
    }
}