@inject('UserObj', 'App\Models\User')

<x-app-layout>

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-7">
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">
                        {{ Auth::user()->isSuperAdmin() ? 'Invite New Client' : 'Invite New Team Member' }}
                    </h5>
                    
                    <form action="{{ route('invitations.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Client Name..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                            </div>

                            @if(Auth::user()->isSuperAdmin())
                                <div class="col-md-6">
                                    <label class="form-label">Existing Company (Optional)</label>
                                    <select name="company_id" class="form-select">
                                        <option value="">-- Create New --</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">New Company Name</label>
                                    <input type="text" name="company_name" class="form-control" placeholder="Acme Corp">
                                </div>
                            @else
                                <div class="col-md-12">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="{{ $UserObj::MEMBER }}">Member</option>
                                        <option value="{{ $UserObj::ADMIN }}">Admin</option>
                                    </select>
                                </div>
                            @endif

                            <div class="col-12 mt-4 text-center">
                                <button type="submit" class="btn btn-primary px-5">Send Invitation</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('dashboard') }}" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
</x-app-layout>

