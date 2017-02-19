<?php

namespace BluesoftBundle\Service\XlsUtilities;

use BluesoftBundle\Repository\SystemRepository;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class XlsDataValidator
{
    private $errors = [];
    private $_current_column = '';
    private $_current_cell = null;

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
        'system'                => 'systemExists',
        'request'               => 'requestHasCorrectFormat',
        'order_number'          => 'orderNumberHasCorrectFormat',
        'from_date'             => 'dateHasCorrectFormat',
        'to_date'               => 'dateHasCorrectFormat',
        'amount'                => 'amountHasCorrectFormat',
        'amount_type'           => 'amountTypeHasCorrectFormat',
        'amount_period'         => 'amountPeriodHasCorrectFormat',
        'authorization_percent' => 'authorizationPercentHasCorrectFormat',
        'active'                => 'activeHasCorrectFormat'
    ];

    private $container;
    private $em;

    public function __construct(Container $container)
    {
        $this->setContainer($container);
        $this->setEm($this->getContainer()->get('doctrine')->getManager());
    }

    public function validateRow(array $row, $index=0)
    {
        foreach (self::_INDEX_COLUMNS_ as $column_name => $column_index) {
            $this->setCurrentColumn($column_name);
            $this->setCurrentCell($row[$column_index]);
            $action_name = self::_VALIDATION_ACTIONS_[$column_name];
            $this->{$action_name}($row[$column_index], $index);

            /** If at any point the instance has errors, stop validating and return */
            if ($this->hasErrors())
                break;
        }

        return $this;
    }

    /**
     * @param array $row
     * @return array
     */
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
     * @param $index
     */
    public function systemExists($system_name='', $index)
    {
        /** @var SystemRepository $repo */
        $repo = $this->getEm()->getRepository('BluesoftBundle:System');
        $exists = (bool) $repo->findSystemByName(trim( (string) $system_name ));

        if (!$exists)
            $this->dispatchErrorGeneration('sys', ['index' => $index]);
    }

    /**
     * @param string $request
     * @param $index
     */
    protected function requestHasCorrectFormat($request='', $index)
    {
        $test = $this->_validateRegex( (string) $request, "/^\d{0,12}$/" );

        if (empty($test))
            $this->dispatchErrorGeneration('req', ['index' => $index]);
    }

    /**
     * @param string $order_number
     * @param $index
     */
    protected function orderNumberHasCorrectFormat($order_number='', $index)
    {
        $test = $this->_validateRegex( (string) $order_number, "/^\d{2}\/\d{4}$/" );

        if (empty($test))
            $this->dispatchErrorGeneration('ord_num', ['index' => $index]);
    }

    /**
     * @todo check if $_current_cell will be needed
     *
     * @param int $date_int
     * @param $index
     */
    protected function dateHasCorrectFormat($date_int, $index)
    {
        $formatted = @date('d/m/Y', $date_int);

        if (!$formatted)
            $this->dispatchErrorGeneration('field_gen', ['index' => $index, 'field' => $this->getCurrentColumn()]);
    }

    /**
     * @param string $amount
     * @param $index
     */
    protected function amountHasCorrectFormat($amount='', $index)
    {
        $test = $this->_validateRegex( (string) $amount, "/\A\d{3}\.\d{2}\z/" );

        if (empty($test))
            $this->dispatchErrorGeneration('field_gen', ['index' => $index, 'field' => $this->getCurrentColumn()]);
    }

    /**
     * @param string $amount_type
     * @param $index
     */
    protected function amountTypeHasCorrectFormat($amount_type='', $index)
    {
        $test = $this->_validateRegex( (string) $amount_type, "/\A[a-zA-Z]{1,5}\z/" );

        if (empty($test))
            $this->dispatchErrorGeneration('field_gen', ['index' => $index, 'field' => $this->getCurrentColumn()]);
    }

    /**
     * @param string $amount_period
     * @param $index
     */
    protected function amountPeriodHasCorrectFormat($amount_period='', $index)
    {
        $test = $this->_validateRegex( (string) $amount_period, "/\A[a-zA-Z]{5}\z/" );

        if (empty($test))
            $this->dispatchErrorGeneration('field_gen', ['index' => $index, 'field' => $this->getCurrentColumn()]);
    }

    /**
     * @param string $authorization_percent
     * @param $index
     */
    protected function authorizationPercentHasCorrectFormat($authorization_percent='', $index)
    {
        $test = $this->_validateRegex( (string) $authorization_percent, "/\A\d{1,3}\z/" );

        if (empty($test))
            $this->dispatchErrorGeneration('field_gen', ['index' => $index, 'field' => $this->getCurrentColumn()]);
    }

    /**
     * @param string $active
     * @param $index
     */
    protected function activeHasCorrectFormat($active='', $index)
    {
        $test = $this->_validateRegex( (string) $active, "/\A[a-z]{1,5}\z/" );

        if (empty($test))
            $this->dispatchErrorGeneration('field_gen', ['index' => $index, 'field' => $this->getCurrentColumn()]);
    }

    /**
     * @param string $string
     * @param string $pattern
     * @return mixed
     */
    protected function _validateRegex($string='', $pattern='')
    {
        preg_match($pattern, $string, $test);

        return $test;
    }

    /**
     * @param $name
     * @return int
     */
    public static function getIndexByName($name)
    {
        return self::_INDEX_COLUMNS_[$name];
    }

    /**
     * @param string $type
     * @param array $args
     */
    protected function dispatchErrorGeneration($type='', $args=[])
    {
        $this->addToErrors($this->generateNewError(
            self::getErrorMessages($type, $args)
        ));
    }

    /**
     * @param string $error_message
     * @param array $args
     * @return XlsError
     */
    protected function generateNewError($error_message='', $args=[])
    {
        $e = new XlsError($error_message);
        $e->setAdditionalData($args);
        return $e;
    }

    /**
     * @param $type
     * @param array $args
     * @return string
     */
    public static function getErrorMessages($type, $args=[])
    {
        $errors = [
            'len_row' => function($args) {
                return "Nieprawidłowy rozmiar rzędu {$args['row_no']}: {$args['row_len']}";
            },
            'sys' => function($args) {
                return "System w rzędzie {$args['index']} nie istnieje";
            },
            'req' => function($args) {
                return "Pole \"request\" nie jest poprawne w rzędzie {$args['index']} ";
            },
            'ord_num' => function($args) {
                return "Pole \"order_number\" nie jest poprawne w rzędzie {$args['index']} ";
            },
            'field_gen' => function($args) {
                return "Pole \"{$args['field']}\" nie jest poprawne w rzędzie {$args['index']} ";
            },
            'dup' => function() {
                return "Dane w kolumnie request nie mogą ulec powtórzeniu. ";
            }
        ];

        return $errors[$type]($args);
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

    public function unsetErrors()
    {
        $this->errors = [];
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
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
     * @return string
     */
    public function getCurrentColumn()
    {
        return $this->_current_column;
    }

    /**
     * @param string $current_column
     */
    public function setCurrentColumn($current_column)
    {
        $this->_current_column = $current_column;
    }

    /**
     * @return null
     */
    public function getCurrentCell()
    {
        return $this->_current_cell;
    }

    /**
     * @param null $current_cell
     */
    public function setCurrentCell($current_cell)
    {
        $this->_current_cell = $current_cell;
    }
}