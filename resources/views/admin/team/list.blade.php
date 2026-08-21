@extends('admin.common.layout')
@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card border-3 border-top border-top-primary">
            <div class="card-body table-responsive">
                <div class="page-header">
    <h2 class="pageheader-title">Team Member List <a class="btn waves-effect waves-light btn-primary ml-4" href="{{route('team.create')}}">Add Team Member</a>
        <?php if(!isset($_GET['trashed'])){ ?>
                <a class="btn btn-warning waves-effect waves-light float-end" href="{{route('team.index')}}?trashed">Show Trashed</a>
            <?php }else{ ?>
                <a class="btn btn-warning waves-effect waves-light float-end" href="{{route('team.index')}}">Show Active</a>
            <?php } ?></h2>
    </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                             <?php if(!isset($_GET['trashed'])){ ?>
                            <th>Sort</th>
                            <?php } ?>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="List_Sortable" class="ui-sortable" data-url="{{route('team.ajax')}}">
                        @php($i = ($teams->currentPage()-1)*$teams->perPage())
                        @if($teams->count())
                        @foreach($teams as $team)
                        @php($i++)
                        <tr>
                            <th scope="row">{{$i}}<span class="main_id" style="display: none;">{{$team->id}}</span></th>
                            <?php if(!isset($_GET['trashed'])){ ?>
                            <td><i class="fa fa-arrows-v handle" aria-hidden="true"></i></td>
                            <?php } ?>
                            <td width="150">
                              @if(@$team->media)
                                {!!@$team->media->get_attachment('thumb')!!}
                              @endif
                            </td>
                            <td>{{$team->title}}</td>
                            <td>
                                @if(isset($_GET['trashed']))
                                <form action="{{route('team.restore', $team->id)}}" method="post" style="display: inline-block;" class="m-l-10">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{ $team->id }}">
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-success">Restore</button>
                                </form>
                                @else
                                <a class="btn waves-effect waves-light btn-sm btn-success" href="{{route('team.edit', $team->id)}}">Edit</a>
                                @endif
                                <form action="{{route('team.destroy', $team->id)}}" method="post" style="display: inline-block;" class="m-l-10">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <input type="hidden" name="id" value="{{ $team->id }}">
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-danger"> <?php if(isset($_GET['trashed'])){ ?> Delete Permanently<?php }else{ echo 'Trash'; } ?></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8">
                                <center>No Team Member Found</center>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="footer text-center">
                {{$teams->render()}}
            </div>
        </div>
    </div>
</div>
@stop
