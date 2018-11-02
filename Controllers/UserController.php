<?php
namespace Pickle;
use Pickle\Engine\App;

class UserController{

    public function register(){//register a new user

        $errors = [];//initialize the errors array
        if ($_POST['pass'] != $_POST['pass_verify']) {//check if datas are good
            $errors[] = 'Passwords are not the same';//if not add an error
        }

        if (!empty($errors)) {//if there is/are error(s), go to the register page
            return view('home', compact('errors'));
        }
        $Model = new UserModel();
        $Model->create([
            "name" => $_POST['name'],
            'email' => $_POST['email'],
            'pass' => $_POST['pass']
        ]);

        App::save('flash', ['Your account has been created']);

        redirect(url('/'));

    }

    public function login(){//login

        $errors = [];//initialize the errors array

        $User = new UserModel();//initialize the User model

        $arg = [
            'name' => $_POST['name'],
            'pass' => $_POST['pass']
        ];

        $usr = $User->get_user($arg);//try to find a user with the same informations

        if ($usr == false) {//if not
            $arg = [
                'email' => $_POST['name'],
                'pass' => $_POST['pass']
            ];
            $usr = $User->get_user($arg);//try the same with the email instead of name
            if ($usr == false) {//if doesn't work
                $errors[] = "Username or password not valide";//add an error
            }
        }

        if (!empty($errors)) {//if there is/are error(s), return to the login page
            return view('home', compact('errors'));
        }
        //else
        App::connect($usr);//connect the website

        App::save('flash', ['You are connected']);

        redirect(url('/'));

    }

    public function logout(){
        App::logout();
        redirect(url('/'));
    }
    
}