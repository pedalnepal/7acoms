@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="row clearfix">
    <div class="col-lg-10 col-md-12">
        <div class="card border-3 border-top border-top-primary">
            <div class="card-body">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <h2 class="pageheader-title mb-0">Abstract Detail</h2>
                    <a class="btn btn-secondary btn-sm" href="{{route('abstract.index')}}">&larr; Back to list</a>
                </div>

                <table class="table table-bordered mt-3">
                    <tbody>
                        <tr><th style="width:220px;">Title</th><td>{{$abstract->title}}</td></tr>
                        <tr><th>Author &amp; Co-authors</th><td>{!! nl2br(e($abstract->authors)) !!}</td></tr>
                        <tr><th>Affiliation</th><td>{{$abstract->affiliation}}</td></tr>
                        <tr><th>Presenting Author</th><td>{{$abstract->presenting_author}}</td></tr>
                        <tr><th>Email</th><td>{{$abstract->email ?: '—'}}</td></tr>
                        <tr><th>Designation</th><td>{{ $abstract->designation ? ucfirst($abstract->designation) : '—' }}</td></tr>
                        <tr><th>Topic Category</th><td>{{$abstract->category}}</td></tr>
                        <tr><th>Presentation Type</th><td>{{ $abstract->pres_type ? ucfirst($abstract->pres_type) : '—' }}</td></tr>
                        <tr><th>Research Type</th><td>{{$abstract->research_type}}</td></tr>
                        <tr><th>Presentation Category</th><td>{{ $abstract->pres_category ? ucfirst($abstract->pres_category) : '—' }}</td></tr>
                        <tr><th>Abstract Body</th><td>{!! nl2br(e($abstract->abstract_body)) !!}</td></tr>
                        <tr><th>References</th><td>{!! $abstract->reference_list ? nl2br(e($abstract->reference_list)) : '<span class="text-muted">—</span>' !!}</td></tr>
                        <tr>
                            <th>Presentation File</th>
                            <td>
                                @if($abstract->file_path)
                                    <a class="btn btn-sm btn-primary" href="{{route('abstract.download', $abstract->id)}}">Download {{$abstract->file_name}}</a>
                                @else
                                    <span class="text-muted">No file uploaded</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Status</th><td>{{ucfirst($abstract->status)}}</td></tr>
                        <tr><th>Submitted At</th><td>{{$abstract->created_at ? $abstract->created_at->format('d M Y, h:i A') : ''}}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
