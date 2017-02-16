<?php

namespace BluesoftBundle\Service\XlsUtilities;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;

class XlsValidator
{
    const _ALLOWED_EXTENSIONS_ = [ 'xls', 'xlsx' ];
    const _VALIDATION_ACTIONS_ = [ 'validateExtension' ];

    /** @var Form */
    private $form;

    /**
     * @param Form $form
     */
    public function validate(Form $form)
    {
        $this->setForm($form);

        foreach (self::_VALIDATION_ACTIONS_ as $action)
            $this->{$action}();

        return $this->getForm();
    }

    /**
     * @return $this
     */
    protected function validateExtension()
    {
        $ext = $this->getSpreadsheet()->guessExtension();

        if (!$ext || !in_array($ext, self::_ALLOWED_EXTENSIONS_)) {
            $error = new FormError($this->getErrors('ext'));
            /** @var Form $ef */
            $ef = $this->getForm()->get('spreadsheet')->addError($error);
            $this->setForm($ef);
        }

        return $this;
    }

    protected function validateColumns()
    {

    }

    /**
     * @param $type
     * @param array $args
     * @return mixed
     */
    protected function getErrors($type, $args=[])
    {
        $errors = [
            'ext' => 'Plik musi mieć rozszerzenie xls lub xlsx'
        ];

        return $errors[$type];
    }

    /** Getters and setters */

    /**
     * @return File
     */
    protected function getSpreadsheet()
    {
        $data = $this->getForm()->getData();
        return $data['spreadsheet'];
    }

    /**
     * @return Form
     */
    protected function getForm()
    {
        return $this->form;
    }

    /**
     * @param Form $form
     */
    protected function setForm($form)
    {
        $this->form = $form;
    }
}