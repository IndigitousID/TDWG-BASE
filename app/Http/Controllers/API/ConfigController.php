<?php

namespace App\Http\Controllers\API;

/*----------  Helper  ----------*/
use Arr, DB, HTTP, Log, Str, Validator, Gate, Auth, Storage;

/*----------  Model  ----------*/
use App\Models\User;
use App\Models\Resource;

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
class ConfigController extends Controller
{
    /**
     * Get Direktori
     * @authenticated
     */
    public function direktori()
    {
        $config = Storage::get('config/direktori.json');

        return response()->json([
            'status' => true,
            'data'   => $config ? json_decode($config) : [],
            'message'=> 'Direktori Berhasil Diambil',
        ]);
    }

    /**
     * Get Resource
     * @authenticated
     */
    public function subdirektori()
    {
        request()->merge(['published' => true]);

        /*----------  Process  ----------*/
        $resource  = Resource::published(true);
        if(request()->direktori){
            $resource   = $resource->where('direktori', request()->direktori);
        }
        $resource  = $resource->distinct('subdirektori')->get(['subdirektori'])->toarray();

        return response()->json([
            'status' => true,
            'data'   => array_column($resource, 'subdirektori'),
            'message'=> 'Subdirektori Berhasil Diambil',
        ]);
    }

    /**
     * Get Resource
     * @authenticated
     */
    public function resource()
    {
        request()->merge(['published' => true]);

        /*----------  Process  ----------*/
        $resource  = Resource::filter(array_filter(request()->input()))->paginate(request()->has('per_page') ? request()->get('per_page') : 15);

        return response()->json([
            'status' => true,
            'data'   => $resource,
            'message'=> 'Resource Berhasil Diambil',
        ]);
    }
}