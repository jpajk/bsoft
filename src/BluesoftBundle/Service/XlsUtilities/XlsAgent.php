<?php

namespace BluesoftBundle\Service\XlsUtilities;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use PHPExcel_Shared_Date;
use BluesoftBundle\Entity\Contract;
use DateTime;

class XlsAgent
{
    private $container;
    /** @var XlsValidator $validator */
    private $validator;
    private $form;
    private $em;
    private $reader;
    private $errors = [];
    private $s_validator;

    /**
     * XlsAgent constructor.
     * @param Container $container
     * @param XlsValidator $validator
     */
    public function __construct(Container $container, $validator)
    {
        $this->setContainer($container);
        $this->setEm($this->getContainer()->get('doctrine')->getManager());
        $this->setValidator($validator);
        $this->setSValidator($this->getContainer()->get('validator'));
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

            $this->saveDataIntoDatabase($m);
        }

        $this->getEm()->flush();
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
        $has_errors = $validated->hasErrors();

        if ($has_errors) {
            $e = $validated->getErrors();

            foreach ($e as $item)
                $this->addToErrors($item);
        }

        return $has_errors;
    }

    public function addDuplicateError()
    {
        $e = new XlsError();
        $e->setMessage(XlsDataValidator::getErrorMessages('dup'));
        $this->addToErrors($e);
    }

    /**
     * Handles saving the information into the database
     * @param array $row
     */
    protected function saveDataIntoDatabase(array $row)
    {
        $index_columns = XlsDataValidator::_INDEX_COLUMNS_;
        /** @var EntityManager $em */
        $em = $this->getEm();
        $repo = $this->getContainer()
                     ->get('doctrine')
                     ->getRepository('BluesoftBundle:System');

        $c = new Contract();

        /** @todo refactor iterate */
        $c->setActive($this->retrieveRowData($row, $index_columns['active']))
          ->setAmount($this->retrieveRowData($row, $index_columns['amount']))
          ->setAmountPeriod($this->retrieveRowData($row, $index_columns['amount_period']))
          ->setAmountType($this->retrieveRowData($row, $index_columns['amount_type']))
          ->setAuthorizationPercent($this->retrieveRowData($row, $index_columns['authorization_percent']))
          ->setFromDate(
            $this->returnDateFromCellValue($this->retrieveRowData($row, $index_columns['from_date']))
            )
          ->setToDate(
            $this->returnDateFromCellValue($this->retrieveRowData($row, $index_columns['to_date']))
          )
          ->setOrderNumber($this->retrieveRowData($row, $index_columns['order_number']))
          ->setRequest($this->retrieveRowData($row, $index_columns['request']))
        ;

        $s = $repo->findSystemByName($this->retrieveRowData($row, $index_columns['system']));
        $c->setSystem($s);
        $em->persist($c);
    }

    /**
     * @param int $value
     * @return DateTime
     */
    protected function returnDateFromCellValue($value)
    {
        return new DateTime( date('d/m/Y', $value) );
    }

    /**
     * @param array $row
     * @param string $key
     * @return mixed
     */
    protected function retrieveRowData(array $row, $key='')
    {
        return $row[$key];
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
     * @return XlsValidator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param XlsValidator $validator
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

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * @return mixed
     */
    public function getSValidator()
    {
        return $this->s_validator;
    }

    /**
     * @param mixed $s_validator
     */
    public function setSValidator($s_validator)
    {
        $this->s_validator = $s_validator;
    }
}