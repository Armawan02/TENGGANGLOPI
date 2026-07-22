<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Node;
use App\Models\Telemetry;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Fetch the latest data for the dashboard via polling.
     */
    public function data()
    {
        // Get the latest telemetry for each active node
        // For simplicity, we just grab the single most recent node & its data for the prototype
        $latestNode = Node::latest()->first();
        
        $telemetry = null;
        if ($latestNode) {
            $telemetry = Telemetry::where('node_id', $latestNode->id)->latest()->first();
        }

        // Get latest unresolved alerts
        $alerts = Alert::with('node')->where('is_resolved', false)->latest()->take(5)->get();

        return response()->json([
            'node' => $latestNode,
            'telemetry' => $telemetry,
            'alerts' => $alerts,
        ]);
    }
}
