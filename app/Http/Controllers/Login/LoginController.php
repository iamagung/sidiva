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
            if ($user->level == '1') { #admin
                return redirect()->route('dashboard');
            } else if($user->level == '2') {#Admin mcu
                return redirect()->route('dashboardMcu');
            } else if($user->level == '3') {#Admin homecare
                return redirect()->route('dashboardHc');
            } else if($user->level == '4') {#Admin psc
                return redirect()->route('dashboardPsc');
            }
        }
        return view('include/login');
    }

    public function proses_login(Request $request)
    {
        request()->validate(
            [
                'username' => 'required',
                'password' => 'required',
            ]);

            $kredensil = $request->only('username','password');
            if (Auth::attempt($kredensil)) {
                $user = Auth::user();
                if ($user->level == '1') { #admin
                    return redirect()->route('dashboard');
                } else if($user->level == '2') {#Admin mcu
                    return redirect()->route('dashboardMcu');
                } else if($user->level == '3') {#Admin homecare
                    return redirect()->route('dashboardHc');
                } else if($user->level == '4') {#Admin psc
                    return redirect()->route('dashboardPsc');
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
