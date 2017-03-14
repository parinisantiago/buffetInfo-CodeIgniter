<?php

/**
 * Created by PhpStorm.
 * User: piturro
 * Date: 19/10/16
 * Time: 15:39
 */
class valException extends Exception
{
    function __construct($message)
    {
        parent::__construct($message);
    }
}