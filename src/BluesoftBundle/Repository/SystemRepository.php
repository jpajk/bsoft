<?php

namespace BluesoftBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class SystemRepository
 * @package BluesoftBundle\Repository
 */
class SystemRepository extends EntityRepository
{
    /**
     * @param $name
     * @return mixed
     */
    public function findSystemByName($name)
    {

        return $this->findOneBy([
            'name' => (string) $name
        ]);
    }
}
