<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function index()
    {
        if ($user = Auth::user()) {
            if ($user) {
                return redirect()->route('dashboard');
            }
        }
        return view('include/login');
    }

    public function prosesLogin(Request $request)
    {
        request()->validate(
            [
                'username' => 'required',
                'password' => 'required',
            ]);

            $kredensil = $request->only('username','password');
            if (Auth::attempt($kredensil)) {
                $user = Auth::user();
                if (!in_array($user->level, ['admin','adminmcu','adminhomecare','admintelemedis'])) {
                    $request->session()->flush();
                    Auth::logout();
                    return view('error.402');
                }
                if ($user) {
                    return redirect()->route('dashboard');
                }
                return redirect()->route('/');
            }

        return redirect()->route('login')
        ->withInput()
        ->withErrors(['login_gagal' => 'Username atau Password salah.']);
    }

    public function logout(Request $request)
    {
       $request->session()->flush();
       Auth::logout();
       return Redirect()->route('login');
    }
}
