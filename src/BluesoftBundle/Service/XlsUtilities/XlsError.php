<?php

namespace BluesoftBundle\Service\XlsUtilities;

class XlsError
{
    private $message='';
    private $additional_data = [
        'cell' => 0,
        'row' => 0,
        'contents' => ''
    ];

    function __construct($message='Fatal error')
    {
        $this->setMessage($message);
    }

    public function setAdditionalDatum($key, $content)
    {
        $this->additional_data[$key] = $content;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}