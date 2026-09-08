@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')

@php
    $typeOptions = [
        ''        => 'All types',
        'paper'   => 'Paper (Oral)',
        'eposter' => 'e-Poster',
    ];
@endphp

<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
        <h1 class="admin-page-title mb-0">
            Abstract Submissions
            <span class="badge bg-secondary fw-normal align-middle">{{ $abstracts->total() }}</span>
        </h1>
        <div class="admin-toolbar">
            @if($trashed)
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('abstract.index') }}">Show Active</a>
            @else
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('abstract.index') }}?trashed">Show Trashed</a>
            @endif
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <form method="get" action="{{ route('abstract.index') }}" class="row gy-2 gx-2 align-items-center">
            @if($trashed)
                <input type="hidden" name="trashed" value="1">
            @endif
            <div class="col-sm-6 col-md-4">
                <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm"
                       placeholder="Search title, presenting author or email…">
            </div>
            <div class="col-sm-4 col-md-3">
                <select name="type" class="form-select form-select-sm">
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-auto">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                @if($search !== '' || $type !== '')
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('abstract.index') }}{{ $trashed ? '?trashed' : '' }}">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Presenting Author</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>File</th>
                        <th>Submitted</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Block form deliberately: Blade pairs raw php blocks with a
                        // non-greedy match, so the parenthesised inline form here would
                        // pair with a later block terminator further down the file and
                        // swallow every directive in between, including the loop.
                        $i = ($abstracts->currentPage() - 1) * $abstracts->perPage();
                    @endphp
                    @forelse($abstracts as $abstract)
                        @php $i++; @endphp
                        <tr>
                            <th scope="row">{{ $i }}</th>
                            <td>{{ $abstract->title }}</td>
                            <td>{{ $abstract->presenting_author }}</td>
                            <td>{{ $abstract->category }}</td>
                            <td>{{ $abstract->pres_type ? ucfirst($abstract->pres_type) : '—' }}</td>
                            <td>
                                @if($abstract->file_path)
                                    <a href="{{ route('abstract.download', $abstract->id) }}">Download</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $abstract->created_at ? $abstract->created_at->format('d M Y, h:i A') : '' }}</td>
                            <td class="text-end text-nowrap">
                                <a class="btn waves-effect waves-light btn-sm btn-info" href="{{ route('abstract.show', $abstract->id) }}">View</a>
                                @if($trashed)
                                    <form action="{{ route('abstract.restore', $abstract->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn waves-effect waves-light btn-sm btn-success">Restore</button>
                                    </form>
                                @endif
                                <form action="{{ route('abstract.destroy', $abstract->id) }}" method="post" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-danger">{{ $trashed ? 'Delete Permanently' : 'Trash' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="admin-table-empty">
                                @if($search !== '' || $type !== '')
                                    No abstract submissions match this search.
                                @else
                                    No abstract submissions found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer text-center card-footer admin-pagination border-0">{{ $abstracts->links() }}</div>
</div>
@stop
