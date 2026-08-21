@extends('admin.common.layout')
@section('content')
<div class="card">
  <div class="card-header">
    <h2>Team Category Detail</h2>
  </div>
  <div class="card-body">
      @if(isset($cteam))
      <form method="post" action="{{route('team-category.update', $cteam->id)}}">
      <input name="_method" type="hidden" value="PUT">
      @else
        <form method="post" action="{{route('team-category.store')}}">
      @endif

      {{csrf_field()}}

          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group form-float">
                    <div class="form-line">
                        <label class="form-label">Title</label>
                        <input value="{{@$cteam->title}}" id="title" name="title" class="form-control" type="text" required>
                    </div>
                </div>
            </div>
          </div>

          <div class="box-footer clearfix mt-3">
            <ul class="list-inline">
              <li><button onclick="needToConfirm = false" type="submit" value="submit" name="SubmitBtn" class="btn btn-success">Submit</button></li>
            </ul>
          </div>
    </form>
  </div>
</div>
@stop
