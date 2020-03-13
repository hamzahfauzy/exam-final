<?php
namespace App\Controllers;
use User;

class AuthController
{

    function login()
    {
        if(session()->get('id'))
            redirect(history()->back());
        
        return;
    }

    function dologin()
    {
        if(session()->get('id'))
            redirect(history()->back());
            
        $request = request()->post();
        $password = md5($request->user_pass);
        $user = User::where('user_login',$request->user_login)->where('user_pass',$password)->where('user_status',1)->first();
        if(empty($user) || $user == null)
        {
            session()->set('error','Username atau Password salah');
            session()->set('old_email',$request->user_login);
            return route('login');
        }
        
        // if($user->user_level == 'participant' && $user->login_status == 1)
        // {
        //     session()->set('error','User ini sedang aktif');
        //     session()->set('old_email',$request->user_login);
        //     return route('login');
        // }

        if($user->user_level == 'admin' && !$user->customer())
        {
            session()->set('error','Akun anda sedang tidak aktif.');
            session()->set('old_email',$request->user_login);
            return route('login');
        }

        $token = $user->auth_token ? $user->auth_token : bin2hex(random_bytes(64));
        
        $user->save([
            'login_status' => 1,
            'auth_token'   => $token
        ]);

        if($user->user_level == 'participant')
        {
            header('location:'.base_url().'/participant?token='.$token);
            die;
        }
        session()->set('id',$user->id);
        return route('/');
        
    }

    function logout()
    {
        if(session()->get('id'))
        {
            $user = session()->user();
            $user->save([
                'login_status' => 0,
                'auth_token'   => ''
            ]);
            session()->destroy();
            return;
        }

        if(isset($_GET['token']))
        {
            $user = User::where('auth_token',$_GET['token'])->first();
            $user->save([
                'login_status' => 0,
                'auth_token'   => ''
            ]);

            return;
        }
        
        return;
    }

}