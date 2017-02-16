<?php

namespace BluesoftBundle\Service\XlsUtilities;


class XlsDataValidator
{
    public static function hasCorrectLength(array $row)
    {
        return 10 === count($row);
    }
}