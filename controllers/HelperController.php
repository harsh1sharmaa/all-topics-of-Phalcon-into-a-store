<?php

// use Phalcon\Logger;
// use Phalcon\Logger\Adapter\Stream;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Firebase\JWT\JWT;
// use Phalcon\Escaper;

class HelperController extends Controller
{

    public function addproductAction($name = null, $price = null)
    {
        return 1;
    }

    public function addorderAction($name, $price, $address)
    {
    }
}
