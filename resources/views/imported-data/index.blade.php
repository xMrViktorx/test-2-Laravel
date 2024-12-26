@extends('home')

@section('subtitle', 'Imported data')

@section('content_body')

    <div class="row pt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex">
                    <div>
                        <h3 class="card-title">{{ ucfirst($file) }} list</h3>
                    </div>
                </div>
                <!-- Search Form and Export button Section -->
                <div class="d-flex p-3">
                    <div>
                        <form action="{{ url()->current() }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search ?? '' }}" />
                            <button type="submit" class="btn btn-dark mx-3">Search</button>
                        </form>
                    </div>

                    <a href="{{ route('imported-data.export', $file) }}" class="btn btn-dark">Export</a>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                @foreach ($headers as $header)
                                    <th>{{ $header['label'] }}</th>
                                @endforeach
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($imports as $import)
                                <tr>
                                    @foreach ($headers as $key => $header)
                                        <td>{{ $import->$key }}</td>
                                    @endforeach     
                                    <td class="d-flex align-items-center">
                                        <!-- Button to view audit logs for the imported data -->
                                        <span type="button" data-toggle="modal" data-target="#auditLogsModal{{ $import->id }}">
                                            <i class="fas fa-eye text-dark"></i>
                                        </span>
    
                                        <!-- Modal to display audit logs -->
                                        <div class="modal fade" id="auditLogsModal{{ $import->id }}" tabindex="-1" role="dialog" aria-labelledby="auditLogsModal" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Audit logs</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if(!$import->auditLogs->count())
                                                            <b>No logs found!</b>
                                                        @endif
                                                        @foreach ($import->auditLogs as $log)
                                                            <b>Row:</b> {{ $log->row }} <br>
                                                            <b>Column:</b> {{ $log->column }} <br>
                                                            <b>Value:</b> {{ $log->value }} <br>
                                                            <hr>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Permission check to allow delete action -->
                                        @can($permission)               
                                            <form action="{{ route('imported-data.delete', $import->id) }}" method="post" onsubmit="return confirm('Are you sure want to delete this record?');">
                                                @method('DELETE')
                                                @csrf
                                                <input type="hidden" name="permission" value="{{ $permission }}">
                                                <input type="hidden" name="file" value="{{ $file }}">
                                                <button class="btn" type="submit"><i class="fas fa-trash text-dark"></i></button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for imported data list -->
                <div class="card-footer d-flex justify-content-center">
                    {{ $imports->appends(['search' => $search])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop
