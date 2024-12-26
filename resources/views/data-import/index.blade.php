@extends('home')

@section('subtitle', 'Data import')

@section('content_body')

    <div class="row pt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data import</h3>
                </div>
                <div class="card-body">
                    <!-- Form for importing data -->
                    <form action="{{ route('data-import.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Dropdown to select import type -->
                        <div class="form-group">
                            <label for="import_type">Select Import Type</label>
                            <select id="importType" name="import_type" id="import_type" class="form-control">
                                @foreach ($allowedImports as $key => $import)
                                    <option value="{{ $key }}">{{ $import['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Container for file input fields (will be dynamically populated) -->
                        <div id="fileInputs"></div>

                         <!-- Section to display required headers for the selected import type -->
                        <div id="requiredHeaders" class="mt-4"></div>

                        <button type="submit" class="btn btn-dark">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            // Generate the file inputs and required headers when an import type is selected.
            document.addEventListener('DOMContentLoaded', function () {
                const importTypeSelect = document.getElementById('importType');
                const importConfig = @json($allowedImports);

                // Function to update file inputs and headers
                function updateFileInputs(selectedType) {     
                    const fileInputsContainer = document.getElementById('fileInputs');
                    fileInputsContainer.innerHTML = ''; // Clear existing inputs

                    if (importConfig[selectedType] && importConfig[selectedType]['files']) {
                        const files = importConfig[selectedType]['files'];

                        // Iterate over the files object
                        Object.entries(files).forEach(([fileKey, fileDetails], index) => {
                            // Generate file input
                            const fileInput = `
                                <div class="form-group">
                                    <label for="file_${index}">${fileDetails.label}</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="files[${fileKey}]" id="file_${index}" class="custom-file-input">
                                            <label class="custom-file-label" for="file_${index}">Choose file</label>
                                        </div>
                                    </div>
                                    <div class="required-headers">
                                        <small>Required Headers:</small>
                                        <small id="headers_${index}">
                                            ${Object.entries(fileDetails.headers_to_db)
                                                .map(([header, details]) => `${details.label}`)
                                                .join(', ')}
                                        </small>
                                    </div>
                                </div>
                            `;
                            fileInputsContainer.insertAdjacentHTML('beforeend', fileInput);
                        });

                        // Add event listeners to update file input labels
                        fileInputsContainer.querySelectorAll('.custom-file-input').forEach(input => {
                            input.addEventListener('change', function () {
                                const fileName = this.files[0] ? this.files[0].name : 'Choose file';
                                this.nextElementSibling.textContent = fileName; // Update the label
                            });
                        });
                    } 
                }

                // Trigger update on dropdown change
                importTypeSelect.addEventListener('change', function () {
                    updateFileInputs(this.value);
                });

                // Trigger update on page load for the default selected option
                updateFileInputs(importTypeSelect.value);
            });
        </script>
    @endpush
@stop
