@extends('admin.common.layout')
@section('content')
<div class="card">
  <div class="card-header p-3">
    <h2 class="pageheader-title mb-0">Team Member Detail</h2>
  </div>
  <div class="card-body">
      @if(isset($team))
      <form method="post" action="{{route('team.update', $team->id)}}">
      <input name="_method" type="hidden" value="PUT">
      @else
        <form method="post" action="{{route('team.store')}}">
      @endif

      {{csrf_field()}}

          <div class="tab-regular">
            <ul class="nav nav-tabs">
              <li class="nav-item"><a class="nav-link active" href="#information" data-bs-toggle="tab" aria-expanded="true">Information</a></li>
            </ul>
            <div class="tab-content pt-3">
                <div class="tab-pane active" id="information">
                <div class="row">
                  <div class="col-md-6 col-sm-6 col-xs-12">
                      <div class="form-group form-float">
                          <div class="form-line">
                              <label class="form-label">Title</label>
                              <input value="{{@$team->title}}" id="title" name="title" class="form-control" type="text">
                          </div>
                      </div>
                  </div>
                  {{-- Permalink removed: team members have no front-end route.
                       The controller still generates one from the title to
                       satisfy the UNIQUE column. --}}
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label class="form-label">Designation</label>
                                <input value="{{@$team->designation}}" id="designation" name="designation" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label class="form-label">Email</label>
                                <input value="{{@$team->email}}" id="email" name="email" class="form-control" type="email">
                            </div>
                        </div>
                    </div>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                      <div class="form-group form-float">
                          <div class="form-line">
                              <label class="form-label">Team Type</label><br>
                              <select name="team_category_id" id="team_category_id" class="custom-select form-select">
                                @foreach($cats as $cat)
                                  <option @if($cat->id==@$team->team_category_id) selected @endif value="{{$cat->id}}">{{$cat->title}}</option>
                                @endforeach
                              </select>
                          </div>
                      </div>
                  </div>
              <div class="clearfix"></div>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                <label>Feature Image</label>
                      <div class="thumbnail-container clearfix">
                        <button type="button" class="btn btn-xs btn-danger clear-media">&times;</button>
                        <a  class="media-open waves-effect waves-light btn btn-sm btn-primary" href="javascript:void(0)" data-for="thumbnail" data-type="input" data-modal-caption="Featured Image">Choose Image</a>
                        <input id="thumbnail" name="image" value="<?php echo @$team->image;?>" type="hidden">
                      <div id="thumbnail_preview" class="media-image-content">
                        @if(@$team->media)
                          {!!@$team->media->get_attachment()!!}
                        @endif
                      </div>
                      </div>
                    </div>
                  <div class="col-md-12 col-sm-12 col-xs-12 mt-4">
                    <label for="description">Description</label><br>
                    <textarea id="description" name="detail" class="WYSWIYG large">{!!@$team->detail!!}</textarea>
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
