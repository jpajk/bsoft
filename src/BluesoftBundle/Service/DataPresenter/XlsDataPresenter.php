<?php

namespace BluesoftBundle\Service\DataPresenter;

use BluesoftBundle\Entity\Contract;
use Symfony\Component\DependencyInjection\Container;
use stdClass;

class XlsDataPresenter
{
    /** @var Container $container */
    private $container;

    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }

    public function retrieveDataForPresentation()
    {
        $contracts = $this->getContainer()
                          ->get('doctrine')
                          ->getRepository('BluesoftBundle:Contract')
                          ->findContractsForPresentation();

        return $this->prepareData($contracts);
    }

    /**
     * @param array $input
     * @return string
     */
    protected function prepareData(array $input)
    {
        $return = [];
        $date_format = 'd/m/Y';

        foreach ($input as $contract) {
            /** @var Contract $c */
            $c = $contract;
            $r = [];

            $r[] = $c->getSystem()->getName();
            $r[] = $c->getRequest();
            $r[] = $c->getOrderNumber();
            $r[] = $c->getFromDate()->format($date_format);
            $r[] = $c->getToDate()->format($date_format);
            $r[] = $c->getAmount();
            $r[] = $c->getAmountType();
            $r[] = $c->getAmountPeriod();
            $r[] = $c->getAuthorizationPercent();
            $r[] = $c->getActive();

            $return[] = $r;
        }

        $m = new stdClass();
        $m->data = $return;

        return $this->encodeToJson($m);
    }

    /**
     * @param string $input
     * @return string
     */
    protected function encodeToJson($input='')
    {
        return json_encode($input);
    }

    /** Getters and setters */

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

}