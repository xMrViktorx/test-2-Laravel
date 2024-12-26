@extends('home')

@section('subtitle', 'Imports')

@section('content_body')

    <div class="row pt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex">
                    <div>
                        <h3 class="card-title">Imports list</h3>
                    </div>
                </div>
                <!-- Search Form Section -->
                <div class="d-flex p-3">
                    <div>
                        <form action="{{ url()->current() }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search ?? '' }}" />
                            <button type="submit" class="btn btn-dark mx-3">Search</button>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>File name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($imports as $import)
                                <tr>
                                    <td>{{ $import->id }}</td>
                                    <td>{{ $import->user->name }}</td>
                                    <td>{{ $import->import_type }}</td>
                                    <td>{{ $import->file_name }}</td>
                                    <td>{{ $import->status }}</td>
                                    <td class="d-flex align-items-center">
                                        <!-- Button to view logs for the import -->
                                        <span type="button" data-toggle="modal" data-target="#importLogsModal{{ $import->id }}">
                                            <i class="fas fa-eye text-dark"></i>
                                        </span>

                                        <!-- Modal to display import logs -->
                                        <div class="modal fade" id="importLogsModal{{ $import->id }}" tabindex="-1" role="dialog" aria-labelledby="importLogsModal" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Validation logs</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-wrap">
                                                        @if(!$import->logs->count())
                                                            <b>No logs found!</b>
                                                        @endif
                                                        @foreach ($import->logs as $log)
                                                            <b>Row:</b> {{ $log->row }} <br>
                                                            <b>Column:</b> {{ $log->column }} <br>
                                                            <b>Value:</b> {{ $log->value }} <br>
                                                            <b>Message:</b> {{ $log->error_message }}<br>
                                                            <hr>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Permission check to allow delete action -->
                                        @can($permissions[$import->import_type])
                                            <form action="{{ route('imports.delete', $import->id) }}" method="post" onsubmit="return confirm('Are you sure want to delete this import?');">
                                                @method('DELETE')
                                                @csrf
                                                <button class="btn" type="submit"><i class="fas fa-trash text-dark"></i></button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination for imports list -->
                <div class="card-footer d-flex justify-content-center">
                    {{ $imports->appends(['search' => $search])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop
