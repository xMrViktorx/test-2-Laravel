<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\ImportLog;
use Illuminate\Support\Str;
use App\Events\ImportFailed;
use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $fileHeaders;
    protected $fileConfig;
    protected $import;
    protected $fileType;

    public function __construct($filePath, $fileHeaders, $fileConfig, $import, $fileType)
    {
        $this->filePath = $filePath;
        $this->fileHeaders = $fileHeaders;
        $this->fileConfig = $fileConfig;
        $this->import = $import;
        $this->fileType = $fileType;
    }

    public function handle()
    {
        try {
            // Load the Excel file into an array
            $data = Excel::toArray([], storage_path('app/private/' . $this->filePath))[0];
            $importStatus = 'successful';

            // Remove the first row which contains the headers
            foreach (array_slice($data, 1) as $rowIndex => $row) {
                $isValidRow = true;

                // Combine the headers with the row data
                $rowData = array_combine($this->fileHeaders, $row);
                // Iterate through the headers to database configuration
                foreach ($this->fileConfig['headers_to_db'] as $header => $details) {
                    $value = $rowData[$header] ?? null;

                    // Check if there are validation rules for the current header
                    if (isset($details['validation'])) {
                        foreach ($details['validation'] as $ruleName => $ruleParams) {

                            // If the rule name is an integer, it means the rule has no parameters
                            if (is_int($ruleName)) {
                                $ruleName = $ruleParams;
                                $ruleParams = null;
                            }

                            // Validate the column value based on the rule
                            $validationResult = $this->validateColumn($ruleName, $ruleParams, $value);

                            // If validation fails, mark the row as invalid and update the import status
                            if (!$validationResult['valid']) {
                                $isValidRow = false;
                                $importStatus = 'unsuccessful';

                                // Log the validation error
                                ImportLog::create([
                                    'import_id' => $this->import->id,
                                    'row' => $rowIndex + 2,
                                    'column' => $header,
                                    'value' => $value,
                                    'error_message' => $validationResult['message'],
                                ]);

                                // Skip to the next row
                                continue 2;
                            }
                        }
                    }
                }

                // If the row is invalid, skip to the next row
                if (!$isValidRow) {
                    continue;
                }

                // Prepare the data for database insertion
                $dbData = $this->prepareDbData($rowData);
                $filters = array_intersect_key($dbData, array_flip($this->fileConfig['update_or_create']));
                $this->updateOrCreateRecord($dbData, $filters, $rowIndex);
            }

            $this->import->update(['status' => $importStatus]);

            // Send success email
            if ($importStatus === 'successful') {
                \Mail::to($this->import->user->email)->send(new \App\Mail\ImportSuccessMail($this->import));
            }
        } catch (\Exception $e) {
            // Log the error and trigger the ImportFailed event
            \Log::error($e->getMessage());
            event(new ImportFailed($this->import->id, $this->import->user->email, $e->getMessage()));
            throw $e;
        }
    }

    // Method to validate a column value based on the rule
    protected function validateColumn($rule, $ruleParams, $value)
    {
        // Skip uniqueness validation if the column is part of the update_or_create fields
        if ($rule === 'unique' && in_array($ruleParams['column'], $this->fileConfig['update_or_create'])) {
            return ['valid' => true, 'message' => ''];
        }

        switch ($rule) {
            case 'required':
                return ['valid' => !empty($value), 'message' => 'This field is required.'];
            case 'unique':
                $modelClass = '\\App\\Models\\' . Str::singular(ucfirst($ruleParams['table']));
                $exists = $modelClass::where($ruleParams['column'], $value)->exists();
                return ['valid' => !$exists, 'message' => 'This value must be unique.'];
            case 'exists':
                $modelClass = '\\App\\Models\\' . Str::singular(ucfirst($ruleParams['table']));
                $exists = $modelClass::where($ruleParams['column'], $value)->exists();
                return ['valid' => $exists, 'message' => 'This value must exist in the database.'];
            case 'in':
                return ['valid' => in_array($value, $ruleParams), 'message' => 'Invalid value. Allowed values: ' . implode(', ', $ruleParams)];
            default:
                return ['valid' => true, 'message' => ''];
        }
    }

    // Method to convert the value to the specified type
    protected function convertType($value, $type)
    {
        switch ($type) {
            case 'integer': 
                return (int)$value;
            case 'double': 
            case 'float': 
                return (float)$value;
            case 'boolean': 
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'string': 
                return (string)$value;
            case 'date': 
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            default: 
                return $value;
        }
    }

    // Method to prepare the data for database insertion
    protected function prepareDbData($rowData)
    {
        $dbData = [];
        foreach ($this->fileConfig['headers_to_db'] as $header => $details) {
            $value = $rowData[$header] ?? null;
            // Convert the value to the specified type if needed
            if (isset($details['type'])) {
                $value = $this->convertType($value, $details['type']);
            }

            $dbData[$header] = $value;
        }
        return $dbData;
    }

    // Method to update or create a record in the database
    protected function updateOrCreateRecord($dbData, $filters, $rowIndex)
    {
        $modelClass = '\\App\\Models\\' . Str::singular(ucfirst($this->fileType));
        $model = $modelClass::where($filters)->first();

        if ($model) {
            // Update the existing record
            $model->update($dbData);

            // Log the changes in the audit log
            foreach ($dbData as $field => $newValue) {
                if ($model->$field != $newValue) {
                    AuditLog::create([
                        'import_id' => $this->import->id,
                        'auditable_id' => $model->id,
                        'auditable_type' => substr($modelClass, 1),
                        'row' => $rowIndex + 2,
                        'column' => $field,
                        'value' => "Changed from '{$model->$field}' to '{$newValue}'",
                    ]);
                }
            }
        } else {
            // Insert a new record
            $modelClass::create($dbData);
        }
    }
}