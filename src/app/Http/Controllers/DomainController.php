<?php
namespace App\Http\Controllers;

use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'url'  => 'required|url|max:500',
            'method' => 'required|in:GET,HEAD',
            'timeout' => 'required|integer|min:1|max:60',
            'auto_check' => 'sometimes|boolean',
            'interval' => 'required|integer|min:1|max:1440',
            'notify_on_down' => 'sometimes|boolean',
        ]);

        $domain = auth()->user()->domains()->create($data);

        dispatch(new CheckDomainJob($domain));

        return response()->json(['domain' => $domain->load('latestCheck')]);
    }

    public function update(Request $request, Domain $domain)
    {
        abort_if($domain->user_id !== auth()->id(), 403);
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'url'  => 'required|url|max:500',
            'method' => 'required|in:GET,HEAD',
            'timeout' => 'required|integer|min:1|max:60',
            'auto_check' => 'sometimes|boolean',
            'interval' => 'required|integer|min:1|max:1440',
            'notify_on_down' => 'sometimes|boolean',
        ]);

        $domain->update($data);

        return response()->json([
            'domain' => $domain->fresh('latestCheck')
        ]);
    }
    public function destroy(Domain $domain)
    {
        abort_if($domain->user_id !== auth()->id(), 403);
        $domain->delete();
        return response()->json(['ok' => true]);
    }

    public function checkNow(Domain $domain)
    {
        abort_if($domain->user_id !== auth()->id(), 403);
        
        CheckDomainJob::dispatchSync($domain);
        
        return response()->json([
            'ok' => true,
            'domain' => $domain->fresh('latestCheck') 
        ]);
    }

    public function history(Domain $domain)
    {
        abort_if($domain->user_id !== auth()->id(), 403);

        return response()->json([
            'history' => $domain->checks()
                ->latest()
                ->take(10)
                ->get()
        ]);
    }
}