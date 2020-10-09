<?php

namespace App\Http\Controllers\API;

/*----------  Helper  ----------*/
use Arr, DB, HTTP, Log, Str, Validator, Gate, Auth, Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

/*----------  Model  ----------*/
use App\Models\User;

/*----------  HTTP  ----------*/
// Base Controller
use App\Http\Controllers\Controller;

// Request
use Illuminate\Http\Request;

/**
 * @group Authentication
 *
 * APIs for Auth
 */
class LoginController extends Controller
{
    /**
     * Register
     *
     * @bodyParam name string required
     * @bodyParam username string required
     * @bodyParam password string required
     *
     * @apiResourceModel App\Http\Resources\UserResource
     */
    public function register(Request $request)
    {
        /*----------  Validate  ----------*/
        $request->validate([]);

        /*----------  Process  ----------*/
        $user = User::create($request->only('name', 'email', 'password'));

        /*----------  Return  ----------*/
        return response()->json([
            'status'    => true,
            'data'      => $user,
            'message'   => 'Email Anda Berhasil Didaftarkan. Silahkan Check Email Untuk Verifikasi',
        ]);
    }

    /**
     * Login
     *
     * @bodyParam username string required
     * @bodyParam password string required
     * @bodyParam device_name string optional (required for gatehering token)
     *
     * @apiResourceModel App\Http\Resources\UserResource
     */
    public function login(Request $request)
    {
        /*----------  Validate  ----------*/
        // $request->validated();

        /*----------  Process  ----------*/
        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()->json([
                'status'    => false,
                'data'      => [],
                'message'   => 'Email atau password Anda tidak terdaftar',
            ], 401);
        }
        else
        {
            // Create Token
            if (Auth::user() && $request->input('device_name'))
            {
                $token = Auth::user()->createToken($request->input('device_name') . '-' . $request->fingerprint())->plainTextToken;
            }

            return response()->json([
                'status' => true,
                'data'   => [
                    'token' => $token,
                    'user'  => Auth::user()->toArray()
                ],
                'message'   => 'Login Berhasil',
            ]);
        }
    }

    /**
     * Logout
     *
     *
     * @apiResourceModel App\Http\Resources\UserResource
     */
    public function logout()
    {
        $user = Auth::user();
        if ($user) $user->tokens()->delete();

        return response()->json([
            'status'    => true,
            'data'      => Auth::user()->toArray(),
            'message'   => 'Logout Berhasil',
        ]);
    }

    /**
     * Forget Password
     *
     * @bodyParam username string required
     *
     * @apiResourceModel App\Http\Resources\UserResource
     */
    public function forgetPassword(Request $request)
    {
        /*----------  Validate  ----------*/
        $request->validate([
            'email'  => ['required', 'string'],
        ]);

        /*----------  Process  ----------*/
        $status = Password::sendResetLink(
            $request->only('email')
        );

        /*----------  Return  ----------*/
        return response()->json([
            'status'    => true,
            'data'      => null,
            'message'   => 'Token Untuk Reset Password Telah Dikirimkan Ke Email Anda',
        ]);
    }

	/**
	 * Reset Password
	 *
	 * @bodyParam password string required
	 * @bodyParam token string required
	 *
	 * @apiResourceModel App\Http\Resources\UserResource
	 */
	public function resetPassword(Request $request)
	{
		/*----------  Validate  ----------*/
		$request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
            'password_confirmation' => ['required', 'string'],
		]);

		/*----------  Process  ----------*/
            $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => $password
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new PasswordReset($user));
            }
        );

		/*----------  Return  ----------*/
		return response()->json([
			'status'     => true,
			'data'       => [],
            'message'   => 'Reset Password Berhasil Dilakukan',
		]);
	}
}
