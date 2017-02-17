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
                          ->findAll();

        dump($this->prepareData($contracts));

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
            $r = new stdClass();

            $r->active = $c->getActive();
            $r->amount = $c->getAmount();
            $r->amountPeriod = $c->getAmountPeriod();
            $r->amountType = $c->getAmountType();
            $r->authorizationPercent = $c->getAuthorizationPercent();
            $r->fromDate = $c->getFromDate()->format($date_format);
            $r->toDate = $c->getToDate()->format($date_format);
            $r->orderNumber = $c->getOrderNumber();
            $r->request = $c->getRequest();
            $r->system = $c->getSystem()->getName();

            $return[] = $r;
        }

        return $this->encodeToJson($return);
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