@extends('home')

@section('subtitle', 'Users')

@section('content_body')

    <div class="row pt-3">
        <div class="col-12">
            <a href="{{ route('users.create') }}" class="btn btn-dark mb-3">Create new user</a>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users list</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created at</th>
                                <th>Updated at</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                    <td>{{ $user->updated_at->format('d.m.Y') }}</td>
                                    <td class="d-flex align-items-center">
                                        <a href="{{ route('users.edit', $user->id) }}"><i class="fas fa-pen text-dark"></i></a>
                                        <form action="{{ route('users.delete', $user->id) }}" method="post" onsubmit="return confirm('Are you sure want to delete this user?');">
                                            @method('DELETE')
                                            @csrf
                                            <button class="btn" type="submit"><i class="fas fa-trash text-dark"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
