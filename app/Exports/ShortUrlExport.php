<?php

namespace App\Exports;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortUrlExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $user = Auth::user();
        $query = ShortUrl::with(['user', 'company']);

        if ($this->request->has('month') && $this->request->month != '') {
            $query->whereMonth('created_at', $this->request->month);
            $query->whereYear('created_at', date('Y'));
        }

        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin() || $user->isMember()) {
                $query->where('company_id', $user->company_id);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        $urls = $query->latest()->get();
        $fileName = 'short_urls_' . date('Y_m_d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Short URL', 'Long URL', 'Created By', 'Company', 'Created At'];

        $callback = function() use($urls, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($urls as $url) {
                fputcsv($file, [
                    url('/u/' . $url->short_code),
                    $url->long_url,
                    $url->user->name ?? '',
                    $url->company->name ?? '',
                    $url->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
