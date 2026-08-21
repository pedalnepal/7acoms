@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card border-3 border-top border-top-primary">
            <div class="card-body table-responsive">
                <div class="page-header">
                    <h2 class="pageheader-title">Abstract Submissions
                        <?php if(!isset($_GET['trashed'])){ ?>
                            <a class="btn btn-warning waves-effect waves-light float-end" href="{{route('abstract.index')}}?trashed">Show Trashed</a>
                        <?php }else{ ?>
                            <a class="btn btn-warning waves-effect waves-light float-end" href="{{route('abstract.index')}}">Show Active</a>
                        <?php } ?>
                    </h2>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Presenting Author</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>File</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($i = ($abstracts->currentPage()-1)*$abstracts->perPage())
                        @if($abstracts->count())
                        @foreach($abstracts as $abstract)
                        @php($i++)
                        <tr>
                            <th scope="row">{{$i}}</th>
                            <td>{{$abstract->title}}</td>
                            <td>{{$abstract->presenting_author}}</td>
                            <td>{{$abstract->category}}</td>
                            <td>{{ $abstract->pres_type ? ucfirst($abstract->pres_type) : '—' }}</td>
                            <td>
                                @if($abstract->file_path)
                                    <a href="{{route('abstract.download', $abstract->id)}}">Download</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{$abstract->created_at ? $abstract->created_at->format('d M Y, h:i A') : ''}}</td>
                            <td>
                                <a class="btn waves-effect waves-light btn-sm btn-info" href="{{route('abstract.show', $abstract->id)}}">View</a>
                                @if(isset($_GET['trashed']))
                                <form action="{{route('abstract.restore', $abstract->id)}}" method="post" style="display: inline-block;" class="m-l-10">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-success">Restore</button>
                                </form>
                                @endif
                                <form action="{{route('abstract.destroy', $abstract->id)}}" method="post" style="display: inline-block;" class="m-l-10" onsubmit="return confirm('Are you sure?');">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-danger"><?php if(isset($_GET['trashed'])){ ?>Delete Permanently<?php }else{ echo 'Trash'; } ?></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8"><center>No Abstract Submissions Found</center></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="footer text-center">
                {{$abstracts->render()}}
            </div>
        </div>
    </div>
</div>
@stop
