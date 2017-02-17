<?php

namespace BluesoftBundle\Service\XlsUtilities;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use BluesoftBundle\Service\XlsUtilities\XlsError;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use PHPExcel_Shared_Date;

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
        foreach($sheet->getRowIterator() as $index => $row){
            /** Skip the first row in every case */
            if ($index === 1)
                continue;

            $m = [];

            foreach($row->getCellIterator() as $cell) {
                if(PHPExcel_Shared_Date::isDateTime($cell)) {
                    $m[] = PHPExcel_Shared_Date::ExcelToPHP($cell->getValue());
                } else {
                    $m[] = $cell->getValue();
                }
            }

            $has_errors = $this->dispatchRowValidation($m, $index);

            /** If the row has errors, abandon it completely and continue down the file */

            if ($has_errors)
                continue;

//            $this->saveDataIntoDatabase($row);

        }
    }

    /**
     * @param array $row
     * @param int $index
     * @return bool
     */
    protected function dispatchRowValidation(array $row, $index)
    {
        $data_validator = $this->getContainer()->get('xls.data.validator');
        /** @var XlsDataValidator $validated */
        $validated = $data_validator->validateRow($row, $index);
        dump($validated);
        $has_errors = $validated->hasErrors();

        if ($has_errors) {
            $e = $validated->getErrors();

            foreach ($e as $item)
                $this->addToErrors($item);
        }

        return $has_errors;
    }

    /**
     * Handles saving the information into the database
     * @param array $row
     */
    protected function saveDataIntoDatabase(array $row)
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