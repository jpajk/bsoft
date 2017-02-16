<?php

namespace BluesoftBundle\Service\XlsUtilities;

use BluesoftBundle\Entity\System;

class XlsDataValidator
{
    private $errors = [];

    /** Column schema */
    const _INDEX_COLUMNS_ = [
        'system'                => 0,
        'request'               => 1,
        'order_number'          => 2,
        'from_date'             => 3,
        'to_date'               => 4,
        'amount'                => 5,
        'amount_type'           => 6,
        'amount_period'         => 7,
        'authorization_percent' => 8,
        'active'                => 9
    ];

    /** Stores validation actions */
    const _VALIDATION_ACTIONS_ = [
        'system' => 'systemExists'
    ];

    protected function validateRow(array $row, $index)
    {
        $length_result = self::hasCorrectLength($row);
        if (!$length_result['success']) {
            $e = new XlsError(
                $this->getErrorMessages('len_row'),
                ['row_no' => $index, 'row_len' => $length_result['length']]
            );

            $this->addToErrors($e);
            return $this;
        }

        foreach (self::_INDEX_COLUMNS_ as $column_name => $column_index) {
            $action_name = self::_VALIDATION_ACTIONS_[$column_name];
            $this->{$action_name}($row[$column_index]);

            /** If at any point the instance has errors, stop validating and return */
            if ($this->hasErrors())
                break;
        }

        return $this;
    }

    public function hasCorrectLength(array $row)
    {
        $len = count($row);

        $result = [
            'success' => 10 === $len,
            'length'  => $len,
        ];

        return $result;
    }

    /**
     * @param string $system_name
     * @return bool
     */
    public static function systemExists($system_name='')
    {

    }

    public static function getIndexByName($name)
    {
        return self::_INDEX_COLUMNS_[$name];
    }

    protected function getErrorMessages($type, $args=[])
    {
        $errors = [
            'len_row' => "Nieprawidłowy rozmiar rzędu {$args['row_no']}: {$args['row_len']}"
        ];

        return $errors[$type];
    }

    /** Getters and setters */

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }

    public function addToErrors(XlsError $error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}