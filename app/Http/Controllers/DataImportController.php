<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Jobs\ImportJob;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataImportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'permission:data-import']);
    }

    /**
     * Display the data import page with a list of allowed imports.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $imports = config('import');
        $user = auth()->user();
    
        // Filter imports by user permissions
        $allowedImports = array_filter($imports, function ($import) use ($user) {
            return $user->can($import['permission_required']);
        });
    
        return view('data-import.index', compact('allowedImports'));
    }

    /**
     * Validate uploaded import files and queue import jobs.
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateImport(Request $request)
    {
        // Get the selected import type from the request
        $importType = $request->input('import_type');
        $imports = config('import');

        if (!$imports) {
            abort(404, 'File configuration not found.');
        }

        // Check if the selected import type is valid
        if (!isset($imports[$importType])) {
            return redirect()->back()->with('error', 'Invalid import type selected');
        }

        // Check if the user has the required permission for this import type
        $user = auth()->user();
        if (!$user->can($imports[$importType]['permission_required'])) {
            return redirect()->back()->with('error', 'You do not have permission to import this data');
        }

        // Validate uploaded files
        $files = $request->file('files');
        if (!$files || count($files) === 0) {
            return redirect()->back()->with('error', 'At least one file is required');
        }

        // Process each uploaded file
        foreach ($files as $fileType => $file) {
            // Validate file type
            if (!in_array($file->getClientOriginalExtension(), ['xlsx', 'csv'])) {
                return redirect()->back()->with('error', "Invalid file type for file {$file->getClientOriginalName()}. Only XLSX and CSV are allowed.");
            }

            // Read the first sheet of the file to get its headers
            $fileData = Excel::toArray([], $file)[0];
            
            // Get the required headers from the configuration
            $requiredHeaders = array_keys($imports[$importType]['files'][$fileType]['headers_to_db']);

            // Normalize the file's headers (replace spaces with underscores and convert to lowercase)
            $fileHeaders = array_map(function ($header) {
                return strtolower(str_replace(' ', '_', $header));
            }, $fileData[0]);

            // Check for missing required headers
            $missingHeaders = array_diff($requiredHeaders, $fileHeaders);
            if (!empty($missingHeaders)) {
                return redirect()->back()->with('error', "Missing required headers in file {$file->getClientOriginalName()}: " . implode(', ', $missingHeaders));
            }

            // All validations passed, create an Import record and dispatch a job
            $import = Import::create([
                'user_id' => $user->id,
                'import_type' => $importType,
                'file_name' => implode(', ', array_map(fn($file) => $file->getClientOriginalName(), $files)),
                'status' => 'pending',
            ]);

            // Get the specific file configuration for this file
            $fileConfig = $imports[$importType]['files'][$fileType];

            // Dispatch the import job to handle the file processing
            ImportJob::dispatch($file->store('imports'), $fileHeaders, $fileConfig, $import, $fileType);
        }

        return redirect()->back()->with('success', 'Import has been queued and will be processed shortly. You will be notified once the import is finished.');
    }
}
