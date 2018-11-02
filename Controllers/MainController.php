<?php
namespace Pickle;

class MainController {

    //You can add methods
    public function home(){

        $test = new \stdClass();
        $test->extension = 'jpg';

        $UserModel = new UserModel();
        $users = $UserModel->run();

        $data = new \stdClass();
        $data->user = $users[0];

        return [
            'users' => $users,
            'array' => [
                [
                    '1',
                    '2',
                    '3'
                ],
                [
                    "1",
                    "2",
                    '3'
                ]
            ],
            'test' => true,
            'data' => $data,
            'hello' => true
        ];

    }

}


?>