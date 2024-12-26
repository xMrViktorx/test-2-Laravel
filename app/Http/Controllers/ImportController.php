<?php

namespace App\Http\Controllers;

use App\Models\Import;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the imports.
     * @param Request $request The HTTP request object.
     * @return Renderable
     */
    public function index(Request $request)
    {
        // Handle search functionality
        $search = $request->input('search');
        $query = Import::query();
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('import_type', 'like', '%' . $search . '%')
                  ->orWhere('file_name', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%'); 
            })
            ->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
    
        // Paginate the results
        $imports = $query->paginate(12);
        
        $importTypes = $imports->pluck('import_type')->unique();

        $permissions = [];
        foreach ($importTypes as $type) {
            $permissions[$type] = config("import.$type.permission_required");
        }

        return view('imports.index', compact('imports', 'search', 'permissions'));
    }

    /**
     * Delete a specific record from the imported data.
     * @param int $id The ID of the record to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $import = Import::findOrFail($id);

        // Check if the authenticated user has the required permission
        if (!auth()->user()->can(config("import.$import->import_type.permission_required"))) {
            abort(403);
        }

        $import->delete();

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }
}
