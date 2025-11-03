<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with('user')->paginate(10);

        return Inertia::render('Home', compact('tasks'));
    }
}
