<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Invitation;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationsController extends Controller
{
    /**
     * Show the invitation form (only for SuperAdmin and Admin).
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            abort(403);
        }

        $companies = Company::all();
        $roles = User::$role_type;

        return view('invitations.index', compact('companies', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'company_id' => 'nullable|exists:companies,id',
            'company_name' => 'nullable|string|max:255',
        ]);

        $aUser = Auth::user();
        $token = Str::random(32);

        $companyId = $request->company_id;

        $aData = $request->only(['name', 'email', 'role', 'company_id', 'company_name']);
        if ($aUser->isSuperAdmin()) {
            $aData['role'] = User::ADMIN;
            if (!$companyId && $request->company_name) {
                $company = Company::create(['name' => $request->company_name]);
                $aData['company_id'] = $company->id;
            }
        } elseif ($aUser->isAdmin()) {
            $aData['company_id'] = $aUser->company_id;
        }

        User::create([
            'name' => $aData['name'],
            'email' => $aData['email'],
            'role' => $aData['role'] ?? User::MEMBER,
            'company_id' => $aData['company_id'],
            'password' => Hash::make(Str::random(16)), // Temporary password
        ]);

        $invitation = Invitation::create([
            'email' => $request->email,
            'role' => $aData['role'],
            'company_id' => $aData['company_id'],
            'token' => $token,
        ]);

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()->with('status', 'invitation sent successfully.');
    }

    /**
     * Show set password page.
     */
    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->firstOrFail();
        return view('invitations.accept', compact('invitation'));
    }

    /**
     * Process registration (Update existing User).
     */
    public function process(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->firstOrFail();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $invitation->email)->firstOrFail();
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Password set successfully! Welcome to your dashboard.');
    }
}
