<?php

namespace App\Http\Controllers\API;

/*----------  Helper  ----------*/
use Arr, DB, HTTP, Log, Str, Validator, Gate, Auth, Storage;

/*----------  Model  ----------*/
use App\Models\User;
use App\Models\Resource;
use App\Models\Preference;

/*----------  HTTP  ----------*/

// Base Controller
use App\Http\Controllers\Controller;

// Request
use Illuminate\Http\Request;

/**
 * @group User management
 *
 * APIs for managing User
 */
class MeController extends Controller
{
    /**
     * Get Me
     * @authenticated
     */
    public function me()
    {
        return response()->json([
            'status' => true,
            'data'   => Auth::user(),
            'message'=> 'Data Anda Berhasil Diambil',
        ]);
    }

    /**
     * Get notifikasi
     * @authenticated
     */
    public function notifikasi()
    {
        request()->merge(['user_id' => Auth::id()]);

        $dr = Preference::userid(Auth::id())->where('snooze', false)->groupby('jam')->groupby('direktori')->get(['direktori', 'jam'])->toArray();
        
        $notif      = [];
        foreach ($dr as $v) {
            $rs     = Resource::where('direktori', $v['direktori'])->inRandomOrder()->first();
            if($rs){
                $rs         = $rs->toArray();
                $rs['jam']  = $v['jam'];
                $notif[]    = $rs;
            }
        }

        return response()->json([
            'status' => true,
            'data'   => $notif,
            'message'=> 'Notifikasi Berhasil Diambil',
        ]);
    }

    /**
     * Get baca_notifikasi
     * @authenticated
     */
    public function baca_notifikasi()
    {
        $dr = Preference::userid(Auth::id())->where('jam', request()->jam)->where('direktori', request()->direktori)->where('snooze', false)->update(['snooze' => true]);

        return response()->json([
            'status' => true,
            'data'   => [],
            'message'=> 'Notifikasi Sudah Dibaca',
        ]);
    }
}