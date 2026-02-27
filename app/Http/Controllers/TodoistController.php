<?php



namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class TodoistController extends Controller
{
    public function index()
    {
        return view('admin.todoist.index');
    }

    public function projects()
    {
        $token = config('services.todoist.token');

        $response = Http::withToken($token)
            ->get('https://api.todoist.com/api/v2/projects');

        return response()->json($response->json());
    }

    public function users($projectId)
    {
        $token = config('services.todoist.token');

        $response = Http::withToken($token)
            ->get("https://api.todoist.com/api/v2/projects/{$projectId}/collaborators");

        return response()->json($response->json());
    }
}
