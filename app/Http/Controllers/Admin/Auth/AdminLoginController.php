<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.chat-rooms.index');
        }
        return view('admin.auth.login');
    }

    public function login(AdminLoginRequest $request)
    {
        $credentials = $this->buildCredentials(
            $request->string('login')->toString(),
            $request->string('password')->toString()
        );

        $remember = (bool) $request->boolean('remember');

        if (!Auth::guard('admin')->attempt($credentials, $remember)) {
            return back()
                ->withErrors(['login' => 'اطلاعات ورود نادرست است.'])
                ->withInput($request->only('login'));
        }

        $request->session()->regenerate();

        return redirect()->route('admin.chat-rooms.index');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
    
    private function buildCredentials(string $login, string $password)
    {
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field    => $login,
            'password' => $password,
        ];
    }
}