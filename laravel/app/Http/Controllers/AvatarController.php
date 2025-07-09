<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function show($userId)
    {
        try {
            $user = User::findOrFail($userId);
    
            $avatarPath = $user->avatar;
            if (!$avatarPath) {
                abort(404, 'avatar not found');
            }
    
            $fullPath = storage_path("app/private/{$avatarPath}");
            if (!file_exists($fullPath)) {
                abort(404, 'avatar file missing');
            }
    
            $key = config('app.avatar_static_key');
            if (!$key) {
                abort(500, 'static avatar key missing');
            }
    
            $data = file_get_contents($fullPath);
            if ($data === false) {
                abort(500, 'cannot read avatar file');
            }
            $raw = base64_decode($data);
            $iv = substr($raw, 0, 16);
            $ciphertext = substr($raw, 16);
    
            $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
            if ($decrypted === false) {
                abort(500, 'cannot decrypt avatar');
            }
    
            return response($decrypted, 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="avatar.jpg"',
                'X-Content-Type-Options' => 'nosniff'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'user not found');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            abort(500, 'Internal Server Error');
        }
    }
}
