<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Phalcon\Security\JWT\Signer\Hmac;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class NotificationListeners extends Injectable
{


    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {
        $controller = $this->router->getControllerName();

        $action = $this->router->getActionName();
        // if ($controller == 'login' && $action == 'loginuser') {
        //     // echo "frrg";
        //     // die();
        //     $this->response->redirect('/login/loginuser');
        // }

        $aclFile = APP_PATH . '/security/acl.cache';
        if (true === is_readable($aclFile)) {

            $acl = unserialize(
                file_get_contents($aclFile)
            );

            $role = $this->request->getQuery('role');

            // echo $role;
            // die();
            if ($role == '') {
                // echo $this->locale->_("access denied");
                // echo "access denied";
                // die;
            } else {

                $key = "example_key";
                try {
                    $decoded = JWT::decode($role, new Key($key, 'HS256'));
                } catch (\Exception $e) {

                    // echo $this->locale->_("access denied");
                    echo "access denied";
                    die;
                }

                // $decoded = JWT::decode($role, new Key($key, 'HS256'));


                $decoded_array = (array) $decoded;
                // print_r($decoded_array);
                // die();
                $role = $decoded_array['role'];



                // if ($role != 'admin') {
                //     echo $this->locale->_("access denied");
                //     die;
                // }
                // if($role ==""){

                // }
                if (true !== $acl->isAllowed($role, $this->router->getControllerName() ?? "acl", $this->router->getActionName() ?? "index")) {

                    // echo $this->locale->_("access denied");
                    echo "access denied";
                    die;
                }
            }
        } else {
            // echo "donr find acl";
            // die();
            // $response = new response();

            $this->response->redirect('secure/buildacl?redirect=1&role=' . $this->request->getQuery('role'));
        }
    }



    public function afterAddproduct()
    {
        // echo $product->price;




        $logger = $this->di->get('logger');
        $logger->info('product saved');
    }
    public function afterorderAdd()
    {
        // echo $product->price;
        // echo "retr";
        // die;

        $logger = $this->di->get('logger');
        // print_r($logger);
        // die();
        $logger->info('order saved');
    }
}
