@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
        <h1 class="admin-page-title mb-0">Media</h1>
        <div class="admin-toolbar">
            @if(!isset($_GET['trashed']))
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('media.index') }}?trashed">Trashed</a>
            @else
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('media.index') }}">Active only</a>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Preview</th>
                        <th>Title / alt</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php($i = ($medias->currentPage()-1)*$medias->perPage())
                    @if($medias->count())
                        @foreach($medias as $media)
                            @php($i++)
                            <tr>
                                <th scope="row">{{ $i }}</th>
                                <td style="width: 90px;">
                                    <div style="width: 75px;">{!! $media->get_attachment('thumb') !!}</div>
                                </td>
                                <td>{{ $media->alt }}</td>
                                <td class="text-end">
                                    @if(isset($_GET['trashed']))
                                        <form action="{{ route('media.restore', $media->id) }}" method="post" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $media->id }}">
                                            <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('media.destroy', $media->id) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $media->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            @if(isset($_GET['trashed']))Delete permanently @else Trash @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="admin-table-empty">No media found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="footer text-center card-footer admin-pagination border-0">{{ $medias->render() }}</div>
</div>
@stop
