<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use BluesoftBundle\Entity\System;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = $this->getContainer()->get('davidbadura_faker.faker');

        $generate_sentences = function($n) use ($faker) {
            return $faker->sentences($n, true);
        };

        $systems = [
            [
                'description' => $generate_sentences(5),
                'name' => 'A100',
                'support_group' => $generate_sentences(1)
            ],
            [
                'description' => $generate_sentences(5),
                'name' => 'B020',
                'support_group' => $generate_sentences(1)
            ],
            [
                'description' => $generate_sentences(5),
                'name' => 'C333',
                'support_group' => $generate_sentences(1)
            ],
        ];

        foreach ($systems as $system) {
            $s = new System();
            $s->setName($system['name']);
            $s->setDescription($system['description']);
            $s->setSupportGroup($system['support_group']);
            $manager->persist($s);
        }

        $manager->flush();
    }
}