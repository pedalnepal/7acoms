@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
        <h1 class="admin-page-title mb-0">Users</h1>
        <div class="admin-toolbar">
            @can('create users')
                <a class="btn btn-primary btn-sm" href="{{ route('user.create') }}">Add user</a>
            @endcan
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php($i = ($users->currentPage()-1)*$users->perPage())
                    @if($users->count())
                        @foreach($users as $user)
                            @php($i++)
                            <tr>
                                <th scope="row">{{ $i }}</th>
                                <td style="width: 120px;">
                                    @if(@$user->media)
                                        {!! @$user->media->get_attachment('thumb') !!}
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td class="text-end">
                                    @can('edit users')
                                        <a class="btn btn-sm btn-success" href="{{ route('user.edit', $user->id) }}">Edit</a>
                                    @endcan
                                    @can('delete users')
                                        @if($i != 1)
                                            <form action="{{ route('user.destroy', $user->id) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this user permanently?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $user->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="admin-table-empty">No users found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="footer text-center card-footer admin-pagination border-0">{{ $users->render() }}</div>
</div>
@stop
