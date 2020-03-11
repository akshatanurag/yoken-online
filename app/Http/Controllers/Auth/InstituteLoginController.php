<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstituteLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:institute');
    }

    public function showLoginForm ()
    {
        return view('auth.institute-login');
    }
    public function login (Request $request)
    {
        //validate form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //attempt to log the user in
        if(\Auth::guard('institute')->attempt(['email'=>$request->email, 'password'=>$request->password], $request->remember))
        {
            return redirect()->intended(route('institute.dashboard'));
        }
        //if successful, redirect to the intended location

        //if unsuccessful, redirect back to the login with the form data
        $errors = ['error' => trans('auth.failed')];
        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors($errors);
    }
}
