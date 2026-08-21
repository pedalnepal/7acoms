@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
  <div class="card-header admin-panel-card-header py-3">
    <h1 class="admin-page-title mb-0">User</h1>
  </div>
  <div class="card-body">
      @if(isset($user))
      <form method="post" action="{{route('user.update', $user->id)}}">
      <input name="_method" type="hidden" value="PUT">
      @else
        <form method="post" action="{{route('user.store')}}">
      @endif

      {{csrf_field()}}

          <div class="nav-tabs-custom">
            <div class="tab-content m-t-10">
                <div class="tab-pane active" id="information">
                <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group form-float">
                        <div class="form-line">
                            <label class="form-label">Name</label>
                            <input value="{{@$user->name}}" id="name" name="name" class="form-control" type="text" required="">
                        </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12 top-20">
                      <div class="form-group form-float">
                          <div class="form-line">
                              <label class="form-label">Email</label>

                              <input value="{{@$user->email}}" id="email" name="email" class="form-control" type="email" required="">
                            </div>
                      </div>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12 top-20">
                      <div class="form-group form-float">
                          <div class="form-line">
                              <label class="form-label">Password</label>
                              <input id="password" name="password" class="form-control" type="password">
                          </div>
                      </div>
                  </div>

                  <div class="clearfix"></div>
                  <div class="col-md-12 col-sm-12 col-xs-12 top-20">

                     <label>Image</label>
                      <div class="thumbnail-container clearfix">
                        <button type="button" class="btn btn-xs btn-danger clear-media">&times;</button>
                        <a  class="media-open waves-effect waves-light btn btn-sm btn-primary" href="javascript:void(0)" data-for="thumbnail" data-type="input" data-modal-caption="Featured Image">Choose Image</a>
                        <input id="thumbnail" name="image" value="<?php echo @$user->image;?>" type="hidden">
                      <div id="thumbnail_preview" class="media-image-content">
                        @if(@$user->media)
                          {!!@$user->media->get_attachment()!!}
                        @endif
                      </div>
                      </div>
                  </div>

                </div>
                </div>
            </div>
            <!-- /.tab-content -->
            <div class="box-footer clearfix mt-3">
              <ul class="list-inline">
                <li><button onclick="needToConfirm = false" type="submit" value="submit" name="SubmitBtn" class="btn btn-success">Submit</button></li>
              </ul>
            </div>
            </div>
    </form>
  </div>
</div>
@stop
