@inject('UserObj', 'App\Models\User')
<x-app-layout>
<div class="container mt-4">
 
    <div class="card mb-4">
      
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">All Users</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Role</th>
                            <th>Total URLs</th>
                            <th>Total Hits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamMembers as $member)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $member->name }}</td>
                            <td class="text-muted">{{ $member->email }}</td>
                            <td>{{ $member->company->name ?? 'N/A' }}</td>
                            <td>{{ $UserObj::$role_type[$member->role] ?? $member->role }}</td>
                            <td>{{ $member->short_urls_count }}</td>
                            <td>{{ $member->total_hits ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3 px-4 pb-3">
            {{ $teamMembers->links() }}
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">All Generated Short URLs</h5>
            <div class="d-flex align-items-center">
                <form action="{{ route('urls.export') }}" method="GET" class="d-flex align-items-center">
                    <select name="month" class="form-select form-select-sm me-2" style="width: auto;">
                        <option value="">Full History</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-download me-1"></i> Download
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Short URL</th>
                            <th>Original URL</th>
                            <th>Created By</th>
                            <th>Company</th>
                            <th>Hits</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($urls as $url)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('urls.redirect', $url->short_code) }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                                    /u/{{ $url->short_code }} <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.8rem;"></i>
                                </a>
                            </td>
                            <td class="text-muted" style="max-width:250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $url->long_url }}
                            </td>
                            <td>{{ $url->user->name ?? 'N/A' }}</td>
                            <td>{{ $url->company->name ?? 'N/A' }}</td>
                            <td class="fw-bold text-success">{{ $url->hits }}</td>
                            <td>{{ $url->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No URLs generated yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3 px-4 pb-3">
            {{ $urls->links() }}
        </div>
    </div>
</div>
</x-app-layout>
