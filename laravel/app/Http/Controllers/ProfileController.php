<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();
        $cacheKey = "user_profile_{$user->id}";

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'password' => 'nullable|string|min:8|confirmed',
                'totp_code' => 'required|digits:6',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024|dimensions:max_width=1024,max_height=1024',
            ]);
            $qrProvider = new EndroidQrCodeProvider();
            $tfa = new TwoFactorAuth($qrProvider, 'ChaBok', 6, 30);
            if (!$tfa->verifyCode($user->secret, $validated['totp_code'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'کد دومرحله‌ای اشتباه است.'
                ], 422);
            }

            $changes = [];
            if ($validated['name'] !== $user->name) {
                $changes['name'] = $validated['name'];
            }

            if (!empty($validated['password'])) {
                $changes['password'] = bcrypt($validated['password']);
            }

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                $rawImage = file_get_contents($file->getRealPath());
                if (!$rawImage) {
                    abort(500, 'read error');
                }
                $key = config('app.avatar_static_key');
                if (!$key) {
                    abort(500, 'static avatar key missing');
                }

                $iv = random_bytes(16);

                $encrypted = openssl_encrypt($rawImage, 'aes-256-cbc', $key, 0, $iv);
                if ($encrypted === false) {
                    abort(500, 'encryption failed');
                }
                $data = base64_encode($iv . $encrypted);

                $filename = uniqid().'_'.time().'.enc';
                $path = storage_path("app/private/avatars/{$filename}");

                if (!file_put_contents($path, $data)) {
                    abort(500, 'failed to save avatar');
                }
                $changes['avatar'] = 'avatars/'.$filename;
            }

            if (empty($changes)) {
                $profile = Cache::remember($cacheKey, now()->addMinutes(5), function() use ($user) {
                    return [
                        'name' => $user->name,
                        'avatar' => $user->avatar_url,
                    ];
                });
                return response()->json([
                    'status' => 'ok',
                    'message' => 'بدون تغییر، پروفایل از کش بارگذاری شد.',
                    'avatar' => $profile['avatar'],
                    'name' => $profile['name']
                ]);
            }

            foreach ($changes as $field => $value) {
                $user->{$field} = $value;
            }
            $user->save();

            Cache::forget($cacheKey);

            Cache::put($cacheKey, [
                'name' => $user->name,
                'avatar' => $user->avatar_url
            ], now()->addMinutes(5));

            return response()->json([
                'status' => 'ok',
                'message' => 'پروفایل با موفقیت بروزرسانی شد.',
                'avatar' => $user->avatar_url,
                'name' => $user->name
            ]);

        } catch (\Throwable $e) {
            return response()->json(['status'=>'error','message'=>'مشکلی در پردازش رخ داد'], 500);
        }
    }

    public function downloadAvatar()
    {
        try {
            $user = auth()->user();
            if (! $user) {
                abort(401, 'Unauthenticated');
            }
            $avatarPath = $user->avatar;
            if (empty($avatarPath)) {
                abort(404, 'Avatar not found');
            }
    
            $fullPath = storage_path("app/private/{$avatarPath}");
            if (! file_exists($fullPath)) {
                abort(404, 'Avatar file missing');
            }
    
            $key = config('app.avatar_static_key');
            if (empty($key)) {
                abort(500, 'Static avatar key missing');
            }
    
            $data = file_get_contents($fullPath);
            if ($data === false) {
                abort(500, 'Cannot read avatar file');
            }
            $raw = base64_decode($data);
            $iv = substr($raw, 0, 16);
            $ciphertext = substr($raw, 16);
    
            $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
            if ($decrypted === false) {
                abort(500, 'Cannot decrypt avatar');
            }
            return response($decrypted, 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="avatar.jpg"',
                'X-Content-Type-Options' => 'nosniff'
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            abort(500, 'Internal Server Error');
        }

    }


}
