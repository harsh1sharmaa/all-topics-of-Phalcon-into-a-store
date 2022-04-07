<?php

use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Firebase\JWT\JWT;
// use Phalcon\Escaper;

class LoginController extends Controller
{
    public function loginuserAction()
    {

        // echo "hello";
        // die();
        $data = $this->request->getPost();

        if (isset($data['submit'])) {

            print_r($data);
            // die();
            $email = $data['email'];
            $obj = Users::find(
                [
                    'conditions' => 'email = :email:',
                    'bind'       => [
                        'email' => $email,
                    ],
                ]
            );
            // echo $obj[0]->token;
            // die();
            $this->response->redirect('/login/admindash?role='.$obj[0]->token);
        }
    }
    public function admindashAction()
    {

        // echo "hello";
        // die();
        // $data = $this->request->getPost();

        // if (isset($data['submit'])) {

        //     print_r($data);
        //     die();
        // }
    }
    public function addorderAction()
    {
        $data = $this->request->getPost();
        if (isset($data['submit'])) {

            // print_r($data);
            // die();
            // $obj = new Helper();
            $success = $this->addorderhelperAction($data['name'], $data['price'], $data['address']);
            if ($success) {
                $this->view->message = " order successfully";
            } else {
                $this->view->message = "order failed";
            }
        }
    }
    public function orderlistAction()
    {
        $order = Orders::find();
        $this->view->message = $order;
    }
    public function addproductAction()
    {
        $data = $this->request->getPost();
        if (isset($data['submit'])) {

            // print_r($data);
            // die();
            // $obj = new Helper();
            $success = $this->addproducthelperAction($data['name'], $data['price']);
            if ($success) {
                $this->view->message = "t added successfully";
            } else {
                $this->view->message = "add product failed";
            }
        }
    }
    public function productlistAction()
    {
        $order = Products::find();
        $this->view->message = $order;
    }

    public function AccountantAction()
    {

        $aclFile = APP_PATH . '/security/acl.cache';


        $secure = new SecureController();
        $acl = $secure->BuildaclAction();

        $acl->addComponent(
            "login",
            [

                "addorder",
                "orderlist",
                "admindash",

            ]
        );
        $acl->addRole("accountant");
        $acl->allow("accountant", "login", "addorder");
        $acl->allow("accountant", "login", "orderlist");
        $acl->allow("accountant", "login", "admindash");

        file_put_contents(
            $aclFile,
            serialize($acl)
        );
    }
    public function ManagerAction()
    {

        $aclFile = APP_PATH . '/security/acl.cache';


        $secure = new SecureController();
        $acl = $secure->BuildaclAction();

        $acl->addComponent(
            "login",
            [

                "addproduct",
                "productlist",
                "admindash",

            ]
        );
        $acl->addRole("manager");
        $acl->allow("manager", "login", "addproduct");
        $acl->allow("manager", "login", "productlist");   
        $acl->allow("manager", "login", "admindash");

        file_put_contents(
            $aclFile,
            serialize($acl)
        );
    }


    public function addUserAction()
    {

        $data = $this->request->getPost();

        if (isset($data['submit'])) {

            $name = $data['name'];
            $email = $data['email'];
            $role = $data['role'];

            $key = "example_key";
            $payload = array(
                "iss" => "/",
                "aud" => "/",
                "iat" => 1356999524,
                "nbf" => 1357000000,
                "name" => $name,
                "email" => $email,
                "role" => $role
            );
            $jwt = JWT::encode($payload, $key, 'HS256');

            $user = new Users();
            $user->name = $name;
            $user->email = $email;
            $user->role = $role;
            $user->token = $jwt;

            if ($role == "accountant") {

                $this->AccountantAction();
            }
            if ($role == "manager") {

                $this->ManagerAction();
            }

            $success = $user->save();
            if ($success) {
                $this->view->message = "add";
                $this->response->redirect("login/admindash?role=" . $this->request->getQuery('role'));
            } else {
                $this->view->message = "not add";
            }
        }
    }

    public function addproducthelperAction($name = null, $price = null)
    {
        if ($name != null && $price != null) {
            $obj = new Products();
            $obj->name = $name;
            $obj->price = $price;

            $success = $obj->save();

            if ($success) {
                $eventsManager = $this->di->get('EventsManager');
                $eventsManager->fire('notification:afterAddproduct', $this);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function addorderhelperAction($name, $price, $address)
    {

        if ($name != null && $price != null && $address != null) {
            $obj = new Orders();
            $obj->name = $name;
            $obj->price = $price;
            $obj->address = $address;

            $success = $obj->save();

            if ($success) {
                // echo "rte";
                // die();
                $eventsManager = $this->di->get('EventsManager');
                // print_r($eventsManager);
                // die;
                $eventsManager->fire('notification:afterorderAdd', $this);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function settingAction()
    {

        // echo "hello world";
        // die();


        $data = $this->request->getPost();
        // print_r($data);
        // die;
        if(isset($data['submit'])){

            // print_r($data);
            // die;
            // $obj = new Settings();
            $obj=Settings::findFirst(1);
        //    echo $obj->Default_Price;
            // die;

            $obj->Default_Price = $data['default_price'];
            $success=$obj->update();
            $this->response->redirect("login/admindash?role=" . $this->request->getQuery('role'));
            // echo $success;
            // die();

        }

    }
}
