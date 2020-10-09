<?php

namespace App\Http\Controllers\API;

/*----------  Helper  ----------*/
use Arr, DB, HTTP, Log, Str, Validator, Gate, Auth;

/*----------  Model  ----------*/
use App\Models\Preference;

/*----------  HTTP  ----------*/

// Base Controller
use App\Http\Controllers\Controller;

// Request
use Illuminate\Http\Request;

// Preference

/*----------  Aggregate  ----------*/

/**
 * @group Preference management
 *
 * APIs for managing Preference
 */
class PreferenceController extends Controller
{
    public function index()
    {
        request()->merge(['userid' => Auth::id()]);

        /*----------  Process  ----------*/
        $preference  = Preference::filter(array_filter(request()->input()))->paginate(request()->has('per_page') ? request()->get('per_page') : 15);

        return response()->json([
            'status' => true,
            'data'   => $preference,
            'message'=> 'Preferensi Berhasil Diambil',
        ]);
    }

    /**
     * Show Preference
     *
     * @authenticated
     * @urlParam id string required
     *
     * @apiPreferenceModel App\Http\Preferences\PreferencePreference
     */
    public function show(String $id)
    {
        /*----------  ACL  ----------*/

        /*----------  Process  ----------*/
        $preference = Preference::findorfail($id);
        if ($preference->user_id !== Auth::id()) abort(404);

        /*----------  Return  ----------*/
        return response()->json([
            'status' => true,
            'data'   => $preference,
            'message'=> 'Preferensi Berhasil Diambil',
        ]);
    }

    /**
     * Create Preference
     *
     * @authenticated
     * @urlParam id string required
     *
     * @apiPreferenceModel App\Http\Preferences\PreferencePreference
     */
    public function store()
    {
        request()->merge(['user_id' => Auth::id()]);
        $preference = Preference::create(request()->only('hari', 'jam', 'direktori', 'snooze', 'user_id'));

        /*----------  Return  ----------*/
        return response()->json([
            'status' => true,
            'data'   => $preference,
            'message'=> 'Preferensi Berhasil Disimpan',
        ]);
    }

    /**
     * Update Preference
     *
     * @authenticated
     * @urlParam id string required
     *
     * @apiPreferenceModel App\Http\Preferences\PreferencePreference
     */
    public function update(String $id)
    {
        try {
            /*----------  Process  ----------*/
            $preference = Preference::userId(Auth::id())->findorfail($id);
            $preference->fill(request()->only('hari', 'jam', 'direktori', 'snooze'));
            $preference->save();
            $preference = Preference::userId(Auth::id())->findorfail($id);

            return response()->json([
                'status' => true,
                'data'   => $preference,
                'message'=> 'Preferensi Berhasil Di update',
            ]);
            
        } catch (\Exception $e) {
            /*----------  Return  ----------*/
            return response()->json([
                'status' => false,
                'data'   => request()->input(),
                'message'=> $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete Preference
     *
     * @authenticated
     * @urlParam id string required
     *
     * @apiPreferenceModel App\Http\Preferences\PreferencePreference
     */
    public function destroy(String $id)
    {
        try {
            /*----------  Process  ----------*/
            $preference = Preference::userId(Auth::id())->findorfail($id);
            $preference->delete();

            return response()->json([
                'status' => true,
                'data'   => [],
                'message'=> 'Preferensi Berhasil Di Hapus',
            ]);
            
        } catch (\Exception $e) {
            /*----------  Return  ----------*/
            return response()->json([
                'status' => false,
                'data'   => [],
                'message'=> $e->getMessage(),
            ]);
        }
    }
}