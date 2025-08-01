<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the post-login redirect path based on user role.
     */
    protected function redirectTo()
    {
        if (auth()->user()->role === 'admin') {
            return '/dashboard';
        }
        return '/user-dashboard';
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Log successful login
        Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_time' => now()->toDateTimeString()
        ]);

        // Update last login time
        $user->update(['last_login_at' => now()]);

        // Prepare welcome message based on user role
        $welcomeMessage = $this->getWelcomeMessage($user);
        
        // Redirect with success message
        return redirect()->intended($this->redirectPath())
            ->with('success', $welcomeMessage);
    }

    /**
     * Get welcome message based on user role and login time.
     */
    private function getWelcomeMessage($user)
    {
        $timeOfDay = $this->getTimeOfDay();
        $roleText = $user->role === 'admin' ? '管理者' : 'ユーザー';
        
        $messages = [
            'admin' => [
                'morning' => "おはようございます！{$user->name}さん、管理者としてログインしました。",
                'afternoon' => "こんにちは！{$user->name}さん、管理者としてログインしました。",
                'evening' => "こんばんは！{$user->name}さん、管理者としてログインしました。",
                'night' => "お疲れ様です！{$user->name}さん、管理者としてログインしました。"
            ],
            'user' => [
                'morning' => "おはようございます！{$user->name}さん、ログインしました。",
                'afternoon' => "こんにちは！{$user->name}さん、ログインしました。",
                'evening' => "こんばんは！{$user->name}さん、ログインしました。",
                'night' => "お疲れ様です！{$user->name}さん、ログインしました。"
            ]
        ];

        return $messages[$user->role][$timeOfDay];
    }

    /**
     * Get time of day for personalized greeting.
     */
    private function getTimeOfDay()
    {
        $hour = now()->hour;
        
        if ($hour >= 5 && $hour < 12) {
            return 'morning';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'afternoon';
        } elseif ($hour >= 17 && $hour < 22) {
            return 'evening';
        } else {
            return 'night';
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = auth()->user();
        
        // Log logout
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'logout_time' => now()->toDateTimeString()
            ]);
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'ログアウトしました。またお越しください。');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        Log::warning('Failed login attempt', [
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'attempt_time' => now()->toDateTimeString()
        ]);

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => trans('auth.failed'),
            ])
            ->with('error', 'ログインに失敗しました。メールアドレスとパスワードを確認してください。');
    }
}
