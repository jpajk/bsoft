<?php

namespace BluesoftBundle\Service\XlsUtilities;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use BluesoftBundle\Service\XlsUtilities\XlsError;
use PHPExcel_IOFactory;
use PHPExcel_Settings;

class XlsAgent
{
    private $container;
    private $validator;
    private $form;
    private $em;
    private $reader;
    private $errors = [];

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
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        $php_excel = PHPExcel_IOFactory::load($this->getSpreadsheet()->getRealPath());
        $this->setReader($php_excel);
        $this->iterateOverSheets();
    }

    protected function iterateOverSheets()
    {
        $sheets = $this->getReader()->getAllSheets();

        foreach ($sheets as $sheet)
            $this->iterateOverRowsAndColumns($sheet);

        exit;
    }

    /**
     * @param \PHPExcel_CachedObjectStorage_CacheBase $sheet
     */
    protected function iterateOverRowsAndColumns($sheet)
    {
        foreach( $sheet->getRowIterator() as $index => $row ){

            if ($index === 1)
                continue;

            $m = [];

            foreach( $row->getCellIterator() as $cell )
                $m[] = $cell->getCalculatedValue();

            dump($m);
        }

//        dump($sheet->rangeToArray('A1:J' . $highest_row));
//
////        for ($a=1; $a >= $highest_row; $a++) {
////
////        }
//
//        $row_iterator = $sheet->getRowIterator();
//
//
//
//        dump($row_iterator);
//        dump($sheet->getRowIterator(), $sheet->getColumnIterator());


    }

    /**
     * @param array $row
     */
    protected function validateRow(array $row)
    {

    }

    protected function saveData($row)
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

    /**
     * @return mixed
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @param mixed $reader
     */
    public function setReader($reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param XlsError $error_message
     */
    public function addToErrors(XlsError $error_message)
    {
        $this->errors[] = $error_message;
    }
}