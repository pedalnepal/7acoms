@extends('admin.common.layout')
@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="page-header">
      <h2 class="pageheader-title">Team Category List <a class="btn waves-effect waves-light btn-primary ml-4" href="{{route('team-category.create')}}"">Add Team Category</a>
                    <?php if(!isset($_GET['trashed'])){ ?>
                        <a class="btn btn-warning waves-effect waves-light pull-right" href="{{route('team-category.index')}}"?trashed">Show Trashed</a>
                    <?php }else{ ?>
                        <a class="btn btn-warning waves-effect waves-light pull-right" href="{{route('team-category.index')}}"">Show Active</a>
                    <?php } ?></h2>
    </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <?php if(!isset($_GET['trashed'])){ ?>
                            <th>Sort</th>
                            <?php } ?>
                            <th>Title</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="List_Sortable" class="ui-sortable" data-url="{{route('cteam.ajax')}}">
                        @php($i = ($cteams->currentPage()-1)*$cteams->perPage())
                        @if($cteams->count())
                        @foreach($cteams as $cteam)
                        @php($i++)
                        <tr>
                            <th scope="row">{{$i}}<span class="main_id" style="display: none;">{{$cteam->id}}</span></th>
                            <?php if(!isset($_GET['trashed'])){ ?>
                            <td><i class="fa fa-arrows-v handle" aria-hidden="true"></i></td>
                            <?php } ?>
                            <td>{{$cteam->title}}</td>
                            <td>
                                @if(!isset($_GET['trashed']))
                                {{-- no public route for a team category, so "View" only ever 404'd --}}
                                <a class="btn waves-effect waves-light btn-sm btn-success" href="{{route('team-category.edit', $cteam->id)}}">Edit</a>
                                @endif
                                @if(isset($_GET['trashed']))
                                <form action="{{route('team-category.restore', $cteam->id)}}" method="post" style="display: inline-block;" class="m-l-10">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{ $cteam->id }}">
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-success">Restore</button>
                                </form>
                                @endif
                                <form action="{{route('team-category.destroy', $cteam->id)}}" method="post" style="display: inline-block;" class="m-l-10">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <input type="hidden" name="id" value="{{ $cteam->id }}">
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-danger">
                                        <?php if(isset($_GET['trashed'])){ ?>
                                    Delete Permanently<?php }else{ echo 'Trash'; } ?></button>
                                </form>
                                
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8">
                                <center>No Team Category Found</center>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="footer text-center">
                {{$cteams->render()}}
            </div>
        </div>
    </div>
</div>
@stop