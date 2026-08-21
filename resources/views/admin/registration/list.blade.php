@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card border-3 border-top border-top-primary">
            <div class="card-body table-responsive">
                <div class="page-header">
                    <h2 class="pageheader-title">Registrations
                        <?php if(!isset($_GET['trashed'])){ ?>
                            <a class="btn btn-warning waves-effect waves-light float-end" href="{{route('registration.index')}}?trashed">Show Trashed</a>
                        <?php }else{ ?>
                            <a class="btn btn-warning waves-effect waves-light float-end" href="{{route('registration.index')}}">Show Active</a>
                        <?php } ?>
                    </h2>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Category</th>
                            <th>NAOMS</th>
                            <th>Payment</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($i = ($registrations->currentPage()-1)*$registrations->perPage())
                        @if($registrations->count())
                        @foreach($registrations as $registration)
                        @php($i++)
                        <tr>
                            <th scope="row">{{$i}}</th>
                            <td>{{$registration->full_name}}</td>
                            <td>{{$registration->email}}</td>
                            <td>{{$registration->phone}}</td>
                            <td>{{$registration->category}}</td>
                            <td>{{$registration->naoms_member}}</td>
                            <td>
                                @php
                                    $badge = [
                                        'paid'    => 'success',
                                        'pending' => 'warning',
                                        'failed'  => 'danger',
                                    ][$registration->payment_status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{$badge}}">{{ucfirst($registration->payment_status ?? 'unpaid')}}</span>
                                @if($registration->amount)
                                    <div class="small text-muted">{{$registration->formattedAmount()}}</div>
                                @endif
                            </td>
                            <td>{{$registration->created_at ? $registration->created_at->format('d M Y, h:i A') : ''}}</td>
                            <td>
                                <a class="btn waves-effect waves-light btn-sm btn-info" href="{{route('registration.admin.show', $registration->id)}}">View</a>
                                @if(isset($_GET['trashed']))
                                <form action="{{route('registration.restore', $registration->id)}}" method="post" style="display: inline-block;" class="m-l-10">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-success">Restore</button>
                                </form>
                                @endif
                                <form action="{{route('registration.destroy', $registration->id)}}" method="post" style="display: inline-block;" class="m-l-10" onsubmit="return confirm('Are you sure?');">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn waves-effect waves-light btn-sm btn-danger"><?php if(isset($_GET['trashed'])){ ?>Delete Permanently<?php }else{ echo 'Trash'; } ?></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="9"><center>No Registrations Found</center></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="footer text-center">
                {{$registrations->render()}}
            </div>
        </div>
    </div>
</div>
@stop
