<?php
namespace App\Middlewares;
use User;

class Participant
{
    function __construct()
    {
        // if(!session()->get('id') || session()->user()->user_level != 'participant')
        // {
        //     session()->destroy();
        //     redirect(base_url().'/login');
        //     return;
        // }
        
        if(!isset($_GET['token']))
        {
            redirect(base_url().'/login');
            return;
        }
        
        $user = User::where('auth_token',$_GET['token'])->first();
        if(empty($user))
        {
            redirect(base_url().'/login');
            return;
        }

        session()->setUser($user);

        return;
    }
}