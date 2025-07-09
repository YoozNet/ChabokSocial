<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

class AuthController extends Controller
{
    protected TwoFactorAuth $tfa;

    public function __construct()
    {
        $qrProvider = new EndroidQrCodeProvider();
        $this->tfa = new TwoFactorAuth(
            $qrProvider,
            'ChaBok', 
            6, 
            30
        );
    }

    public function showRegisterForm()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            if (!$request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'invalid request'], 400);
            }
    
            $validated = $request->validate([
                'name'     => 'required|string|max:100',
                'username' => 'required|string|max:50',
                'password' => 'required|string|min:8|max:32',
            ]);
    
            if (User::where('username', $validated['username'])->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'این نام کاربری قبلا ثبت شده است.'
                ], 422);
            }
    
            $masterKey = random_bytes(32);
            $salt = random_bytes(16);
    
            $derivedKey = sodium_crypto_pwhash(
                32,
                $validated['password'],
                $salt,
                SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
                SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
            );
    
            $iv = random_bytes(16);
    
            $masterKeyEncrypted = openssl_encrypt(
                $masterKey,
                'aes-256-cbc',
                $derivedKey,
                OPENSSL_RAW_DATA,
                $iv
            );
    
            $secret = $this->tfa->createSecret();
    
            $user = User::create([
                'name'                  => $validated['name'],
                'username'              => $validated['username'],
                'password'              => Hash::make($validated['password']),
                'secret'                => $secret,
                'is_active'             => 0,
                'master_key_encrypted'  => base64_encode($masterKeyEncrypted),
                'master_key_salt'       => base64_encode($salt),
                'master_key_iv'         => base64_encode($iv),
            ]);
    
            Session::put('verify_user_id', $user->id);
            Session::put('verify_user_password', $validated['password']);
            Session::put('session_expiration', now()->addDays(40));
    
            $qrCode = $this->tfa->getQRCodeImageAsDataUri($validated['username'], $secret);
    
            return response()->json([
                'status'   => 'ok',
                'qr'       => $qrCode,
                'secret'   => $secret,
                'username' => $validated['username']
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در ثبت‌نام: ' . $e->getMessage()
            ], 500);
        }

    }

    public function verify(Request $request)
    {
        try {
            $validated = $request->validate([
                'totp_code' => 'required|digits:6'
            ]);
    
            $userId   = Session::get('verify_user_id');
            $password = Session::get('verify_user_password');
    
            $user = User::find($userId);
    
            if (!$user || !$password) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'کاربر یا رمز عبور پیدا نشد.'
                ], 404);
            }
    
            $valid = $this->tfa->verifyCode($user->secret, $validated['totp_code']);
    
            if ($valid) {
                $user->is_active = 1;
                $user->save();
    
                auth()->login($user);
                event(new \Illuminate\Auth\Events\Login(auth()->guard(), $user, false));
                event(new \App\Events\UserLoggedIn($user));
    
                $derivedKey = sodium_crypto_pwhash(
                    32,
                    $password,
                    base64_decode($user->master_key_salt),
                    SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                    SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
                    SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
                );

                $masterKey = openssl_decrypt(
                    base64_decode($user->master_key_encrypted),
                    'aes-256-cbc',
                    $derivedKey,
                    OPENSSL_RAW_DATA,
                    base64_decode($user->master_key_iv)
                );

                if (!$masterKey) {
                    throw new \Exception('خطا در دیکریپت master key');
                }

                \Cache::put("user_{$user->id}_master_key", $masterKey, now()->addDays(40));
                Session::put('session_expiration', now()->addDays(40));
    
                return response()->json([
                    'status'  => 'ok',
                    'message' => 'ثبت نام با موفقیت تکمیل شد'
                ]);
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'کد وارد شده اشتباه است.'
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'عملیات تایید با خطا مواجه شد: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->json(['status'=>'error','message'=>'invalid request'],400);
        }

        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8|max:32'
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'نام کاربری یا رمز عبور نادرست است.'
            ], 422);
        }

        Session::put('login_verify_user_id', $user->id);
        Session::put('login_verify_password', $validated['password']);
        Session::put('session_expiration', now()->addDays(40));

        if ($user->is_active == 0) {
            $qr = $this->tfa->getQRCodeImageAsDataUri(
                $user->username,
                $user->secret
            );

            return response()->json([
                'status' => 'twofa_activation',
                'message' => 'حساب شما نیاز به فعالسازی دارد.',
                'qr' => $qr,
                'secret' => $user->secret
            ]);
        } else {
            return response()->json([
                'status' => 'twofa_login',
                'message' => 'کد ۶ رقمی را برای ورود وارد کنید.'
            ]);
        }
    }

    public function verifyLogin(Request $request)
    {
        try {
            $validated = $request->validate([
                'totp_code'   => 'nullable|digits:6',
                'backup_code' => 'nullable|string|max:8'
            ]);

            $userId            = Session::get('login_verify_user_id');
            $password          = Session::get('login_verify_password');
            $sessionExpiration = Session::get('session_expiration');

            if (!$userId || !$password) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'اطلاعات ورود در سشن پیدا نشد.'
                ], 404);
            }

            if ($sessionExpiration && now()->greaterThan($sessionExpiration)) {
                Session::forget('login_verify_user_id');
                Session::forget('login_verify_password');
                Session::forget('session_expiration');
                return response()->json([
                    'status'  => 'error',
                    'message' => 'نشست منقضی شده است، دوباره وارد شوید.'
                ], 401);
            }

            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'کاربر پیدا نشد.'
                ], 404);
            }

            $ok = false;

            if ($validated['totp_code']) {
                $ok = $this->tfa->verifyCode($user->secret, $validated['totp_code']);
            }

            if (!$ok && $validated['backup_code']) {
                $backupCode = $user->backupCodes()
                    ->where('plain_code', strtoupper($validated['backup_code']))
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->first();

                if ($backupCode) {
                    $ok = true;
                    $backupCode->update(['status' => 'expired']);
                }
            }

            if ($ok) {
                if (!$user->is_active) {
                    $user->is_active = 1;
                }
                $user->save();

                $derivedKey = sodium_crypto_pwhash(
                    32,
                    $password,
                    base64_decode($user->master_key_salt),
                    SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                    SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
                    SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
                );

                $masterKey = openssl_decrypt(
                    base64_decode($user->master_key_encrypted),
                    'aes-256-cbc',
                    $derivedKey,
                    OPENSSL_RAW_DATA,
                    base64_decode($user->master_key_iv)
                );

                if (!$masterKey) {
                    throw new \Exception('خطا در رمزگشایی master key');
                }

                \Cache::put("user_{$user->id}_master_key", $masterKey, now()->addDays(40));
                Session::put('session_expiration', now()->addDays(40));
                
                auth()->login($user);
                event(new \Illuminate\Auth\Events\Login(auth()->guard(), $user, false));
                event(new \App\Events\UserLoggedIn($user));

                return response()->json([
                    'status'  => 'ok',
                    'message' => 'ورود با موفقیت انجام شد.'
                ]);
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'کد وارد شده اشتباه است.'
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'بروز خطا در فرایند ورود: ' . $e->getMessage()
            ], 500);
        }
        
    }
}