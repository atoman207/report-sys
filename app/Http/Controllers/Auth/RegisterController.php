<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AvatarUploadService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    protected $avatarUploadService;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/form';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AvatarUploadService $avatarUploadService)
    {
        $this->middleware('guest');
        $this->avatarUploadService = $avatarUploadService;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,gif', 'max:2048'], // 2MB max
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        // Create user first to get the ID
        $user = User::create($userData);

        // Handle avatar upload if provided
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            try {
                $avatarPath = $this->avatarUploadService->uploadAvatar($data['avatar'], $user->id);
                $user->update(['avatar' => $avatarPath]);
                
                Log::info('User registered with avatar', [
                    'user_id' => $user->id,
                    'avatar_path' => $avatarPath
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to upload avatar during registration', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                
                // Continue with registration even if avatar upload fails
            }
        }

        return $user;
    }
}
