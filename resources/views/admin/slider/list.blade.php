@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
        <h1 class="admin-page-title mb-0">Sliders</h1>
        <div class="admin-toolbar d-flex flex-wrap gap-2 align-items-center">
            <a class="btn btn-primary btn-sm" href="{{ route('slider.create') }}">Add slider</a>
            @if(!isset($_GET['trashed']))
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('slider.index') }}?trashed">Trashed</a>
            @else
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('slider.index') }}">Active only</a>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        @if(!isset($_GET['trashed']))
                            <th>Sort</th>
                        @endif
                        <th>Image</th>
                        <th>Title</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Theme</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="List_Sortable" class="ui-sortable" data-url="{{ route('slider.ajax') }}">
                    @php($i = ($sliders->currentPage()-1)*$sliders->perPage())
                    @if($sliders->count())
                        @foreach($sliders as $slider)
                            @php($i++)
                            <tr>
                                <th scope="row">{{ $i }}<span class="main_id" style="display: none;">{{ $slider->id }}</span></th>
                                @if(!isset($_GET['trashed']))
                                    <td><i class="handle" aria-hidden="true">&udhar;</i></td>
                                @endif
                                <td style="width: 150px;">
                                    @if(@$slider->media)
                                        {!! @$slider->media->get_attachment('thumb') !!}
                                    @endif
                                </td>
                                <td>{!! $slider->title !!}</td>
                                <td class="text-center">
                                    @if($slider->status)
                                        <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fa-solid fa-circle-xmark me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(($slider->theme ?? 'light') === 'dark')
                                        <span class="badge bg-dark"><i class="fa-solid fa-moon me-1"></i>Dark</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-sun me-1"></i>Light</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(isset($_GET['trashed']))
                                        <form action="{{ route('slider.restore', $slider->id) }}" method="post" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $slider->id }}">
                                            <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                        </form>
                                    @else
                                        <a class="btn btn-sm btn-success" href="{{ route('slider.edit', $slider->id) }}">Edit</a>
                                    @endif
                                    <form action="{{ route('slider.destroy', $slider->id) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $slider->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            @if(isset($_GET['trashed']))Delete permanently @else Trash @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="admin-table-empty">No sliders found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="footer text-center card-footer admin-pagination border-0">{{ $sliders->render() }}</div>
</div>
@stop
