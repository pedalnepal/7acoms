@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header py-3">
        <h1 class="admin-page-title mb-0">Upload media</h1>
        <p class="text-muted small mb-0 mt-1">Files are added to your media library.</p>
    </div>
    <div class="card-body">
        <div id="AllMediaContainer">
            <input type="file" multiple="multiple" name="files[]" id="media_uploader">
        </div>
    </div>
</div>
@stop
