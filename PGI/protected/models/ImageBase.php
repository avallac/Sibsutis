<?php

/**
 * Created by PhpStorm.
 * User: avallac
 * Date: 09.01.16
 * Time: 4:40
 */
abstract class ImageBase
{
    protected $header;
    protected $image;

    public function getHeader()
    {
        return $this->header;
    }

    public function getImage()
    {
        return $this->image;
    }
}