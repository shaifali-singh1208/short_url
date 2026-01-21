@inject('UserObj', 'App\Models\User')

<x-guest-layout>

    <h4 class="text-center mb-2">Complete Your Registration</h4>

    <p class="text-center text-muted mb-4">
        Set your password to join as
        <strong>{{ $UserObj::$role_type[$invitation->role] }}</strong>
    </p>

    <form method="POST" action="{{ route('invitations.process', $invitation->token) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
            @error('password_confirmation')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Complete Registration
        </button>
    </form>

</x-guest-layout>
