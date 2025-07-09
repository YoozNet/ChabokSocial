<?php

namespace App\Http\Controllers;

use App\Models\BackupCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class BackupCodeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'کاربر احراز هویت نشده'
                ], 401);
            }
            $cacheKey = "backup_codes_{$user->id}";
    
            $codes = Cache::remember($cacheKey, now()->addMinutes(5), function() use ($user) {
                $result = [];
                foreach (range(1,10) as $slot) {
                    $history = $user->backupCodes()
                        ->where('slot_number', $slot)
                        ->orderBy('created_at','desc')
                        ->get()
                        ->map(fn($c)=>[
                            'code' => $c->plain_code,
                            'status' => $c->status,
                            'expires_at' => $c->expires_at
                        ]);
                    $result[$slot] = $history;
                }
                return $result;
            });
            return response()->json([
                'status' => 'ok',
                'slots'  => $codes
            ]);
        
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در دریافت کدهای پشتیبان'
            ], 500);
        }

    }

    public function generate(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'کاربر احراز هویت نشده'
                ], 401);
            }
            $hasAny = $user->backupCodes()->count();
            if($hasAny > 0){
                return response()->json([
                    'message' => 'already_exists'
                ], 409);
            }
    
            $now = now();
            $codes = [];
    
            foreach (range(1,10) as $slot) {
                $plain = strtoupper(Str::random(8));
                $user->backupCodes()->create([
                    'code' => bcrypt($plain),
                    'plain_code' => $plain,
                    'expires_at' => $now->addDays(30),
                    'slot_number' => $slot,
                    'status' => 'active'
                ]);
                $codes[$slot][] = [
                    'code'=>$plain,
                    'status'=>'active',
                    'expires_at' => $now->addDays(30),
                ];
            }
            Cache::forget("backup_codes_{$user->id}");
    
            return response()->json([
                'status' => 'ok',
                'slots'  => $codes
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در ایجاد کدهای پشتیبان'
            ], 500);
        }

    }

    public function regenerate(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'کاربر احراز هویت نشده'
                ], 401);
            }
            $now = now();
    
            $user->backupCodes()->where('status', 'expired')->delete();
            $user->backupCodes()->where('status','active')->update(['status'=>'expired']);
    
            $codes = [];
    
            foreach (range(1,10) as $slot) {
                $plain = strtoupper(Str::random(8));
                $user->backupCodes()->create([
                    'code' => bcrypt($plain),
                    'plain_code' => $plain,
                    'expires_at' => $now->addDays(30),
                    'slot_number' => $slot,
                    'status' => 'active'
                ]);
                $codes[$slot][] = [
                    'code'=>$plain,
                    'status'=>'active',
                    'expires_at' => $now->addDays(30),
                ];
            }
            Cache::forget("backup_codes_{$user->id}");
    
            return response()->json([
                'status' => 'ok',
                'slots'  => $codes
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در بازتولید کدهای پشتیبان'
            ], 500);
        }
    }
}
