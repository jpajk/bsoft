<?php

namespace BluesoftBundle\Service\XlsUtilities;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use PHPExcel_IOFactory;

class XlsAgent
{
    private $container;
    private $validator;
    private $form;
    private $em;

    public function __construct(Container $container, $validator)
    {
        $this->setContainer($container);
        $this->setValidator($validator);
        $this->setEm($this->getContainer()->get('doctrine')->getManager());
    }

    /**
     * @param Form $spreadsheet
     */
    public function validateAndParse($form)
    {
        $this->setForm($form);
        $this->doValidateAndParse();
    }

    protected function doValidateAndParse()
    {
        $spreadsheet = $this->getSpreadsheet();
        dump($this->getSpreadsheet());
        exit;

    }

    protected function validateRow()
    {

    }

    protected function saveData()
    {

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

    /**
     * @return mixed
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param mixed $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param mixed $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }

    /**
     * @return File
     */
    protected function getSpreadsheet()
    {
        $data = $this->getForm()->getData();
        return $data['spreadsheet'];
    }
}