<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Fetch Recent Activity (Limit to 5)
        $activities = [];
        if (class_exists(ActivityLog::class)) {
            $activities = ActivityLog::where('user_id', $user->id)
                                     ->orderBy('created_at', 'desc')
                                     ->take(3)
                                     ->get();
        }

        // Fetch Browser Sessions
        $sessions = [];
        if (config('session.driver') === 'database') {
            $sessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) use ($request) {
                    $agent = $this->createAgent($session->user_agent);

                    return (object) [
                        'agent' => [
                            'is_desktop' => $agent->isDesktop(),
                            'platform' => $agent->platform(),
                            'browser' => $agent->browser(),
                        ],
                        'ip_address' => $session->ip_address,
                        'is_current_device' => $session->id === $request->session()->getId(),
                        'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    ];
                });
        }

        return view('profile.index', [
            'user' => $user,
            'sessions' => $sessions,
            'activities' => $activities,
        ]);
    }

    protected function createAgent($userAgent)
    {
        // Simple manual parsing
        $isDesktop = !preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
        
        $platform = 'Unknown';
        if (preg_match('/windows|win32/i', $userAgent)) { $platform = 'Windows'; }
        elseif (preg_match('/macintosh|mac os x/i', $userAgent)) { $platform = 'macOS'; }
        elseif (preg_match('/linux/i', $userAgent)) { $platform = 'Linux'; }
        elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) { $platform = 'iOS'; }
        elseif (preg_match('/android/i', $userAgent)) { $platform = 'Android'; }

        $browser = 'Unknown';
        if (preg_match('/MSIE|Trident/i', $userAgent)) { $browser = 'Internet Explorer'; }
        elseif (preg_match('/Edge/i', $userAgent)) { $browser = 'Edge'; }
        elseif (preg_match('/Firefox/i', $userAgent)) { $browser = 'Firefox'; }
        elseif (preg_match('/Chrome/i', $userAgent)) { $browser = 'Chrome'; }
        elseif (preg_match('/Safari/i', $userAgent)) { $browser = 'Safari'; }
        elseif (preg_match('/Opera|OPR/i', $userAgent)) { $browser = 'Opera'; }

        return new class($isDesktop, $platform, $browser) {
            public function __construct(private $desktop, private $plat, private $brows) {}
            public function isDesktop() { return $this->desktop; }
            public function platform() { return $this->plat; }
            public function browser() { return $this->brows; }
        };
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'photo' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');

            // Also save to database for persistence across Render deploys
            $photoFile = $request->file('photo');
            $user->profile_photo_data = base64_encode(file_get_contents($photoFile->getRealPath()));
            $user->profile_photo_mime = $photoFile->getMimeType();
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function destroyPhoto()
    {
        $user = auth()->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = null;
        $user->profile_photo_data = null;
        $user->profile_photo_mime = null;
        $user->save();

        return back()->with('success', 'Profile photo removed successfully.');
    }

    /**
     * Serve profile photo from the database (used when filesystem is wiped).
     */
    public function servePhoto()
    {
        $user = auth()->user();

        if ($user->profile_photo_data && $user->profile_photo_mime) {
            return response(base64_decode($user->profile_photo_data))
                ->header('Content-Type', $user->profile_photo_mime)
                ->header('Cache-Control', 'public, max-age=86400');
        }

        abort(404);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function logoutOtherBrowserSessions(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        if (config('session.driver') !== 'database') {
            return back()->withErrors(['password' => 'This feature requires the database session driver.']);
        }

        DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return back()->with('success', 'Other browser sessions have been logged out successfully.');
    }
}
