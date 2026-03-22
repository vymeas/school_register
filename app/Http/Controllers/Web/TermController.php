<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        return view('terms.index', [
            'terms' => Term::orderBy('start_date', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        Term::create($data);

        return redirect()->route('terms.index')->with('success', 'Term created successfully.');
    }

    public function update(Request $request, Term $term)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'status'     => 'required|in:active,inactive',
        ]);

        $term->update($data);

        return redirect()->route('terms.index')->with('success', 'Term updated successfully.');
    }
}
