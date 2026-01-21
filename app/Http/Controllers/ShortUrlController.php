<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Exports\ShortUrlExport;

class ShortUrlController extends Controller
{
    /**
     * fetch data.
     */

    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $urls = ShortUrl::with(['user', 'company'])->latest()->paginate(10, ['*'], 'urls_page');

            $teamMembers = User::where('role', '!=', User::SUPER_ADMIN)->with(['company'])->withCount('shortUrls')->withSum('shortUrls as total_hits', 'hits')->paginate(10, ['*'], 'members_page');


            return view('superadmin.dashboard', compact('urls', 'teamMembers'));
        }

        $teamMembers = collect();
        $query = ShortUrl::with(['user', 'company'])->latest();

        if ($user->isAdmin()) {
            $query->where('company_id', $user->company_id);
            $teamMembers = User::where('company_id', $user->company_id)
                ->withCount('shortUrls')
                ->withSum('shortUrls as total_hits', 'hits')
                ->paginate(10, ['*'], 'members_page');
        } else {
            $query->where('user_id', $user->id);
        }

        $urls = $query->paginate(10, ['*'], 'urls_page');

        return view('urls.index', compact('urls', 'teamMembers'));
    }

    /**
     * Public redirection.
     */
    public function redirect($code)
    {
        $url = ShortUrl::where('short_code', $code)->firstOrFail();
      
        $url->increment('hits');

        return redirect($url->long_url);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $aUser = Auth::user();

        if ($aUser->isSuperAdmin()) {
            return back()->withErrors(['error' => 'SuperAdmin is not authorized to create short URLs.']);
        }

        $request->validate([
            'long_url' => 'required|url',
        ]);

        do {
            $code = Str::random(6);
        } while (ShortUrl::where('short_code', $code)->exists());

         
        $aData = [
            'long_url' => $request->long_url,
            'short_code' => $code,
            'user_id' => $aUser->id,
            'company_id' => $aUser->company_id,
            'hits' => 0
        ];

        ShortUrl::create($aData);

        return redirect()->route('dashboard')->with('status', 'URL shortened successfully.');
    }


    /**
     * export short url data
     */
    public function export(Request $request)
    {
        return (new ShortUrlExport($request))->download();
    }
}
