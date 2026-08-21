@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')

<div class="card admin-panel-card">
  <div class="card-header admin-panel-card-header py-3">
    <h1 class="admin-page-title mb-0">Page</h1>
  </div>
  <div class="card-body">
      @if(isset($page))
      <form method="post" action="{{route('page.update', $page->id)}}">
      <input name="_method" type="hidden" value="PUT">
      @else
        <form method="post" action="{{route('page.store')}}">
      @endif

      {{csrf_field()}}

          <div class="tab-regular">
            <ul class="nav nav-tabs">
              <li class="nav-item"><a class="nav-link active" href="#information" data-bs-toggle="tab" aria-expanded="true">Information</a></li>
              <li class="nav-item"><a class="nav-link" href="#meta_information" data-bs-toggle="tab" aria-expanded="true">Meta Information</a></li>
            </ul>
            <div class="tab-content pt-3">
                <div class="tab-pane active" id="information">
                <div class="row">
                  <div class="col-md-6 col-sm-6 col-xs-12">
                      <div class="form-group form-float">
                          <div class="form-line">
                              <label class="form-label">Title</label>
                              <input value="{{@$page->title}}" id="title" name="title" class="form-control" type="text">
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                      <div class="form-group form-float">
                          <div class="form-line">
                              <label class="form-label">Permalink</label>
                              <input id="permalink" value="{{@$page->permalink}}" name="permalink" class="form-control" type="text">
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                      <div class="form-group form-float">
                          <div class="form-line">
                              <label class="form-label">Caption</label>
                              <input value="{{@$page->caption}}" id="caption" name="caption" class="form-control" type="text">
                          </div>
                      </div>
                  </div>

              <div class="clearfix"></div>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                <label>Feature Image</label>
                      <div class="thumbnail-container clearfix">
                        <button type="button" class="btn btn-xs btn-danger clear-media">&times;</button>
                        <a  class="media-open waves-effect waves-light btn btn-sm btn-primary" href="javascript:void(0)" data-for="thumbnail" data-type="input" data-modal-caption="Featured Image">Choose Image</a>
                        <input id="thumbnail" name="image" value="<?php echo @$page->image;?>" type="hidden">
                      <div id="thumbnail_preview" class="media-image-content">
                        @if(@$page->media)
                          {!!@$page->media->get_attachment()!!}
                        @endif
                      </div>
                      </div>
                    </div>
                  <div class="col-md-12 col-sm-12 col-xs-12 mt-4">
                    <label for="description">Description</label><br>
                    <textarea id="description" name="detail" class="WYSWIYG large">{!!@$page->detail!!}</textarea>
                  </div>
                </div>
                </div>
                <div class="tab-pane" id="meta_information">
                  <div class="row">

                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <label for="meta_title">Meta Title</label>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea rows="3" class="form-control no-resize" name="meta_title" id="meta_title">{{@$page->meta_title}}</textarea>
                        </div>
                    </div>
                  </div>

                  <div class="col-md-6 col-sm-6 col-xs-12">
                     <label for="meta_description">Meta Description</label>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea rows="3" class="form-control no-resize" name="meta_description" id="meta_description">{{@$page->meta_description}}</textarea>
                        </div>
                    </div>
                  </div>

                  <div class="col-md-6 col-sm-6 col-xs-12">
                     <label for="meta_keyword">Meta Keyword</label>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea rows="3" class="form-control no-resize" name="meta_keyword" id="meta_keyword">{{@$page->meta_keyword}}</textarea>
                        </div>
                    </div>
                  </div>

                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <label for="meta_robot">Meta Robot</label><br>
                    <select name="meta_robot" id="meta_robot" class="form-control show-tick ms">
                      <?php $meta_robots = array('index, follow', 'index, nofollow', 'noindex, follow', 'noindex, nofollow');
                      foreach ($meta_robots as $key ) { ?>
                        <option value="<?php echo $key; ?>" <?php if($key==@$page->meta_robot) echo 'selected="selected"'; ?>><?php echo $key; ?></option>
                      <?php } ?>
                    </select>
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
