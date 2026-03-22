<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Turn;
use Illuminate\Http\Request;

class TurnController extends Controller
{
    public function index()
    {
        return view('turns.index', [
            'turns' => Turn::withCount('classrooms')->orderBy('start_time')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        Turn::create($data);

        return redirect()->route('turns.index')->with('success', 'Turn created successfully.');
    }

    public function update(Request $request, Turn $turn)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $turn->update($data);

        return redirect()->route('turns.index')->with('success', 'Turn updated successfully.');
    }

    public function destroy(Turn $turn)
    {
        if ($turn->classrooms()->count() > 0) {
            return back()->with('error', 'Cannot delete Turn that is assigned to classrooms.');
        }
        
        $turn->delete();
        return redirect()->route('turns.index')->with('success', 'Turn deleted successfully.');
    }
}
