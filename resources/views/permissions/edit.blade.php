@extends('home')

@section('subtitle', 'Welcome')

@section('content_body')
    <div class="row pt-3">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit permission</h3>
                </div>

                <form method="POST" action="{{ route('permissions.update', $permission->id) }}">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') border-danger @enderror" id="name" name="name" placeholder="Enter name" value="{{ $permission->name }}">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-dark">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
