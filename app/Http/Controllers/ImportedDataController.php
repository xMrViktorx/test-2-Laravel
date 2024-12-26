<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ImportedDataController extends Controller
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
     * Display a listing of the imported data.
     * @param Request $request The HTTP request object.
     * @param string $file The name of the table/file being displayed.
     * @return Renderable
     */
    public function index(Request $request, $file)
    {
        $config = config("import");

        if (!$config) {
            abort(404, 'File configuration not found.');
        }

        $fileConfig = [];
        $permission = '';

        // Find the specific configuration for the given file
        foreach ($config as $key => $value) {
            foreach ($value['files'] as $fileKey => $fileValue) {
                if ($fileKey == $file) {
                    $fileConfig = $fileValue;
                    $permission = $value['permission_required'];
                }
            }
        }

        $headers = $fileConfig['headers_to_db'];

        // Handle search functionality
        $search = $request->input('search');
        
        $modelClass = '\\App\\Models\\' . Str::singular(ucfirst($file));

        if (!class_exists($modelClass)) {
            abort(404, 'Model not found.');
        }

        $query = $modelClass::query();

        if ($search) {
            $query->where(function ($q) use ($headers, $search) {
                foreach ($headers as $key => $header) {
                    $q->orWhere($key, 'like', '%' . $search . '%');
                }
            });
        }

        $imports = $query->paginate(12);

        return view('imported-data.index', compact('imports', 'headers', 'search', 'file', 'permission'));
    }

    /**
     * Export the imported data to an Excel file.
     * @param string $file The name of the table/file being exported.
     * @return Excel file
     */
    public function export($file)
    {
        $config = config("import");

        // Validate import configuration file
        if (!$config) {
            abort(404, 'File configuration not found.');
        }

        $fileConfig = [];

        // Find the specific configuration for the given file
        foreach ($config as $key => $value) {
            foreach ($value['files'] as $fileKey => $fileValue) {
                if ($fileKey == $file) {
                    $fileConfig = $fileValue;
                }
            }
        }

        // Extract labels from the headers for Excel column names
        $headers = array_map(fn($item) => $item['label'], $fileConfig['headers_to_db']);

        // Fetch data from the database, excluding specific fields
        $modelClass = '\\App\\Models\\' . Str::singular(ucfirst($file));

        if (!class_exists($modelClass)) {
            abort(404, 'Model not found.');
        }

        $data = $modelClass::select(array_diff(array_keys($fileConfig['headers_to_db']), ['id', 'created_at', 'updated_at']))->get();

        // Generate the Excel file and return it for download
        return Excel::download(new class($data, $headers) implements FromCollection, WithHeadings {
            protected $data;
            protected $headers;

            public function __construct($data, $headers)
            {
                $this->data = $data;
                $this->headers = $headers;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headers;
            }
        }, $file . '_data.xlsx');
    }

    /**
     * Delete a specific record from the imported data.
     * @param Request $request The HTTP request object.
     * @param int $id The ID of the record to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        // Get the file name and required permission from the request
        $file = $request->input('file');
        $permission = $request->input('permission');

        if (empty($file) || empty($permission)) {
            abort(400, 'Invalid parameters.');
        }

        $modelClass = '\\App\\Models\\' . Str::singular(ucfirst($file));

        if (!class_exists($modelClass)) {
            abort(404, 'Model not found.');
        }

        $model = $modelClass::findOrFail($id);

        // Check if the authenticated user has the required permission
        if (!auth()->user()->can($permission)) {
            abort(403);
        }

        $model->auditLogs()->delete();

        $model->delete();

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }
}
