@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
        <h1 class="admin-page-title mb-0">Menu: {{ $menu->name }}</h1>
        <div class="admin-toolbar d-flex flex-wrap gap-2">
            <a class="btn btn-primary btn-sm" href="{{ route('menu.new', $menu->id) }}">Add item</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sort</th>
                        <th>Name</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="List_Sortable" class="ui-sortable" data-url="{{ route('menu.ajax') }}">
                    @if(count($menus)>0)
                        @php($i = 0)
                        @foreach($menus as $menu)
                            @php($i++)
                            <tr>
                                <th scope="row">{{ $i }}<span class="main_id" style="display: none;">{{ $menu->id }}</span></th>
                                <td><i class="handle" aria-hidden="true">&#8597;</i></td>
                                <td>{{ $menu->menu_title }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-success" href="{{ route('menu.edit', $menu->id) }}">Edit</a>
                                    <form action="{{ route('menu.destroy', $menu->id) }}" method="post" onsubmit="return confirm('Delete this item permanently?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $menu->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="admin-table-empty">No items yet.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
