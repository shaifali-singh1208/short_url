<x-app-layout>



<div class="container mt-2">

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if(Auth::user()->isAdmin() || Auth::user()->isMember())
    <div class="card mt-4 d-none" id="generateCard">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Generate Short URL</h5>

            <form action="{{ route('urls.store') }}" method="POST">
                @csrf
                <label class="form-label small fw-bold text-muted">Long URL</label>
                <div class="input-group">
                    <input type="url" name="long_url" class="form-control"
                           placeholder="https://example.com/very-long-url" required>
                    <button class="btn btn-primary px-4" type="submit">Generate</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- GENERATED URL TABLE --}}
    <div class="card mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Generated Short URLs</h5>

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
                    <button type="button" class="btn btn-primary btn-sm ms-2" onclick="toggleGenerateCard(this)">
                        <i class="bi bi-plus-circle"></i> Generate
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
                            <th>Long URL</th>
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
                            <td class="text-muted" style="max-width:300px;">
                                {{ $url->long_url }}
                            </td>
                            <td>{{ $url->hits }}</td>
                            <td>{{ $url->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No URLs generated yet.
                            </td>
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

    @if(Auth::user()->isAdmin())
    <div class="card mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Team Members</h5>
            <a href="{{ route('invitations.index') }}" class="btn btn-primary btn-sm">Invite</a>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email</th>
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
                        <td>{{ \App\Models\User::$role_type[$member->role] }}</td>
                        <td>{{ $member->short_urls_count }}</td>
                        <td>{{ $member->total_hits }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3 px-4 pb-3">
            {{ $teamMembers->links() }}
        </div>
    </div>
    @endif

</div>

<!-- Bootstrap JS -->

<!-- CUSTOM JS -->
<script>
    function toggleGenerateCard(btn) {
        const card = document.getElementById('generateCard');

        card.classList.toggle('d-none');

        if (!card.classList.contains('d-none')) {
            btn.innerHTML = '<i class="bi bi-x-circle"></i> Close';
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            btn.innerHTML = '<i class="bi bi-plus-circle"></i> Generate';
        }
    }
</script>

</x-app-layout>

