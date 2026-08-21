@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card admin-panel-card">
            <div class="card-header admin-panel-card-header py-3">
                <h1 class="admin-page-title mb-0">Site &amp; branding</h1>
            </div>
            <div class="card-body table-responsive">
                <form method="POST" action="{{route('setting.store')}}">
                    {{csrf_field()}}
                    <table class="table admin-table table-bordered table-striped mb-0">
                        <tbody>
                            @php($i = 0)
                            <tr>
                                <td>{{++$i}}</td>
                                <th>Logo</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                        <label for="">Light Logo</label>
                                        <div class="thumbnail-container clearfix">
                                        <button type="button" class="btn btn-xs btn-danger clear-media">&times;</button>
                                        <a class="media-open waves-effect waves-light btn btn-sm btn-primary" href="javascript:void(0)" data-for="logo" data-type="input">Choose Image</a>
                                        <div class="image-preview media-image-content" id="logo_preview">
                                            @if(config('setting.logo'))
                                            {!!get_attachment(config('setting.logo'))!!}
                                            @endif
                                        </div>
                                        </div>
                                        <input type="hidden" class="image image_id_content"  id="logo" name="logo" value="{{config('setting.logo')}}">
                                        </div>
                                        <div class="col-md-6">
                                        <label for="">Dark Logo</label>
                                        <div class="thumbnail-container clearfix">
                                          <button type="button" class="btn btn-xs btn-danger clear-media">&times;</button>
                                          <a class="media-open waves-effect waves-light btn btn-sm btn-primary" href="javascript:void(0)" data-for="dark_logo" data-type="input">Choose Image</a>
                                          <div class="image-preview media-image-content" id="dark_logo_preview">
                                            @if(config('setting.dark_logo'))
                                            {!!get_attachment(config('setting.dark_logo'))!!}
                                            @endif
                                          </div>
                                        </div>
                                        <input type="hidden" class="image image_id_content"  id="dark_logo" name="dark_logo" value="{{config('setting.dark_logo')}}">
                                        </div>
                                    </div>

                                </td>
                            </tr>

                            @php($configurations = array(
                                    'pemail'=>'Primary Email',
                                    'semail'=>'Secondary Email',
                                    'location'=>'Location',
                                    'contact'=>'Contact No.',
                                    'sphone'=>'Secondary Phone',
                                    'mobile'=>'Mobile No.',
                                    'facebook'=>'Facebook URL',
                                    'tiktok'=>'Tiktok URL',
                                    'linkedin'=>'Linkedin URL',
                                    'youtube' =>'Youtube',
                                    'instagram' =>'Instagram',
                                    'working_hours'=>'Working Hours',
                                )
                            )
                            @foreach($configurations as $configname => $configtitle)
                            @php($i++)
                            <tr>
                                <th scope="row">{{$i}}</th>
                                <td><label for="{{$configname}}">{{$configtitle}}</label></td>
                                <td>
                                    <input value="{{config('setting.'.$configname)}}" type="text" name="{{$configname}}" id="{{$configname}}" class="form-control">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                    <button class="btn btn-primary" type="submit">Save settings</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
