<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;



class SecureController extends Controller
{




    public function BuildaclAction()
    {

        $aclFile = APP_PATH . '/security/acl.cache';

        if (true !== is_file($aclFile)) {
            $acl = new Memory();

            $acl->addRole('admin');


            $acl->addComponent(
                'acl',
                [
                    'index',
                ]
            );

            $acl->allow('admin', '*', '*');


            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        } else {
            $acl = unserialize(file_get_contents($aclFile));
        }


        // if (true == $acl->isallowed('manager', 'index', 'index')) {
        //     echo "Access granted";
        // } else {
        //     echo "Access denied";
        // }
        $id=$this->request->getQuery('redirect');

        if ($id == 1) {
            $this->response->redirect('login/admindash?role=' . $this->request->getQuery('role'));
        }

        return $acl;
    }
}
