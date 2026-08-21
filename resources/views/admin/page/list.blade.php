@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
        <h1 class="admin-page-title mb-0">Pages</h1>
        <div class="admin-toolbar d-flex flex-wrap gap-2 align-items-center">
            <a class="btn btn-primary btn-sm" href="{{ route('page.create') }}">Add page</a>
            @if(!isset($_GET['trashed']))
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('page.index') }}?trashed">Trashed</a>
            @else
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('page.index') }}">Active only</a>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php($i = ($pages->currentPage()-1)*$pages->perPage())
                    @if($pages->count())
                        @foreach($pages as $page)
                            @php($i++)
                            <tr>
                                <th scope="row">{{ $i }}</th>
                                <td>{{ $page->title }}</td>
                                <td class="text-end">
                                    @if(!isset($_GET['trashed']))
                                        <a target="_blank" class="btn btn-sm btn-outline-primary" href="{{ route('page.detail', $page->permalink) }}">View</a>
                                        <a class="btn btn-sm btn-success" href="{{ route('page.edit', $page->id) }}">Edit</a>
                                    @endif
                                    @if(isset($_GET['trashed']))
                                        <form action="{{ route('page.restore', $page->id) }}" method="post" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $page->id }}">
                                            <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('page.destroy', $page->id) }}" method="post" class="d-inline" onsubmit="return confirm('Continue?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $page->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            @if(isset($_GET['trashed']))Delete permanently @else Move to trash @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="admin-table-empty">No pages found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="footer text-center card-footer admin-pagination border-0">{{ $pages->render() }}</div>
</div>
@stop
