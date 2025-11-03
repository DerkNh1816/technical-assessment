<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user()->tasks()->create([
            'title' => $request->title,
        ]);

        return redirect()->back();
    }
}
