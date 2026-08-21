@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header py-3">
        <h1 class="admin-page-title mb-0">Home page settings</h1>
    </div>
    <div class="card-body table-responsive">
        <form method="POST" action="{{ route('setting.home_store') }}">
            @csrf
            @php($i = 0)
            <table class="table admin-table table-bordered mb-0">
                <tbody>
                    <tr>
                        <th scope="row" class="text-muted" style="width:3rem;">{{ ++$i }}</th>
                        <td style="min-width: 180px;"><label class="mb-0" for="home_welcome">Home welcome text</label></td>
                        <td>
                            <textarea name="home_welcome" id="home_welcome" class="form-control WYSWIYG">{!! config('setting.home_welcome') !!}</textarea>
                        </td>
                    </tr>
                    @php($configurations = array('meta_title'=>'Meta title'))
                    @foreach($configurations as $configname => $configtitle)
                        @php($i++)
                        <tr>
                            <th scope="row" class="text-muted">{{ $i }}</th>
                            <td><label class="mb-0" for="{{ $configname }}">{{ $configtitle }}</label></td>
                            <td>
                                <input value="{{ config('setting.'.$configname) }}" type="text" name="{{ $configname }}" id="{{ $configname }}" class="form-control">
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th scope="row" class="text-muted">{{ ++$i }}</th>
                        <td><label class="mb-0" for="meta_description">Meta description</label></td>
                        <td>
                            <textarea name="meta_description" id="meta_description" class="form-control">{{ config('setting.meta_description') }}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="text-muted">{{ ++$i }}</th>
                        <td><label class="mb-0" for="meta_keyword">Meta keyword</label></td>
                        <td>
                            <textarea name="meta_keyword" id="meta_keyword" class="form-control">{{ config('setting.meta_keyword') }}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="text-muted">{{ ++$i }}</th>
                        <td><label class="mb-0" for="footer_text">Footer text</label></td>
                        <td>
                            <textarea name="footer_text" id="footer_text" class="form-control">{!! config('setting.footer_text') !!}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="text-muted">{{ ++$i }}</th>
                        <td><label class="mb-0" for="home_video">Home video embed URL</label></td>
                        <td>
                            <input value="{{ config('setting.home_video') }}" type="text" name="home_video" id="home_video" class="form-control">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="admin-form-actions">
                <button class="btn btn-primary" type="submit">Save settings</button>
            </div>
        </form>
    </div>
</div>
@stop
