@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header py-3">
        <h1 class="admin-page-title mb-0">Menus</h1>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!$menus->isEmpty())
                        @php($i = ($menus->currentPage()-1)*$menus->perPage())
                        @foreach($menus as $menu)
                            @php($i++)
                            <tr>
                                <th scope="row">{{ $i }}</th>
                                <td>{{ $menu->name }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-success" href="{{ route('menu.show', $menu->id) }}">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="admin-table-empty">No menus found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="footer text-center card-footer admin-pagination border-0">{{ $menus->render() }}</div>
</div>
@stop
