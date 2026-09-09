@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="row clearfix">
    <div class="col-lg-10 col-md-12">
        <div class="card border-3 border-top border-top-primary">
            <div class="card-body">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <h2 class="pageheader-title mb-0">Registration Detail</h2>
                    <a class="btn btn-secondary btn-sm" href="{{route('registration.index')}}">&larr; Back to list</a>
                </div>

                <table class="table table-bordered mt-3">
                    <tbody>
                        <tr><th style="width:240px;">Date</th><td>{{$registration->reg_date}}</td></tr>
                        <tr><th>Full Name</th><td>{{$registration->full_name}}</td></tr>
                        <tr><th>Email</th><td>{{$registration->email}}</td></tr>
                        <tr><th>Phone</th><td>{{$registration->phone}}</td></tr>
                        <tr><th>Designation</th><td>{{$registration->designation}}</td></tr>
                        <tr><th>Working Place</th><td>{{$registration->workplace}}</td></tr>
                        <tr><th>Nationality</th><td>{{$registration->nationality}}</td></tr>
                        <tr><th>NAOMS Member</th><td>{{$registration->naoms_member}}</td></tr>
                        <tr><th>Registering For</th><td>{{$registration->reg_for}}</td></tr>
                        <tr><th>Registration Category</th><td>{{$registration->category}}</td></tr>
                        <tr>
                            <th>Recommendation Letter</th>
                            <td>
                                @if($registration->recommendation_letter_path)
                                    <a class="btn btn-sm btn-primary" href="{{route('registration.download', [$registration->id, 'recommendation_letter'])}}">Download {{$registration->recommendation_letter_name}}</a>
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Payment Receipt</th>
                            <td>
                                @if($registration->receipt_path)
                                    <a class="btn btn-sm btn-primary" href="{{route('registration.download', [$registration->id, 'receipt'])}}">Download {{$registration->receipt_name}}</a>
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Others / Remarks</th><td>{!! $registration->others ? nl2br(e($registration->others)) : '<span class="text-muted">—</span>' !!}</td></tr>
                        <tr><th>Status</th><td>{{ucfirst($registration->status)}}</td></tr>

                        <tr>
                            <th>Payment Status</th>
                            <td>
                                @php
                                    $badge = [
                                        'paid'    => 'success',
                                        'pending' => 'warning',
                                        'failed'  => 'danger',
                                    ][$registration->payment_status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{$badge}}">{{ucfirst($registration->payment_status ?? 'unpaid')}}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Fee</th>
                            <td>
                                @if($registration->amount)
                                    {{$registration->formattedAmount()}}
                                    @if($registration->fee_tier)
                                        <span class="text-muted">({{ucfirst($registration->fee_tier)}} rate)</span>
                                    @endif
                                    @if($registration->isConverted())
                                        <div class="small text-muted">
                                            Charged {{$registration->formattedChargeAmount()}} — {{$registration->fxRateLabel()}}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Paid At</th><td>{{$registration->paid_at ? $registration->paid_at->format('d M Y, h:i A') : '—'}}</td></tr>
                        <tr>
                            <th>Payment Remarks</th>
                            <td>
                                {!! $registration->payment_remarks ? nl2br(e($registration->payment_remarks)) : '<span class="text-muted">—</span>' !!}
                                @if($registration->payment_status_updated_at)
                                    <div class="small text-muted">
                                        Set by {{ optional($registration->paymentStatusUpdatedBy)->name ?? 'an administrator' }}
                                        on {{ $registration->payment_status_updated_at->format('d M Y, h:i A') }}
                                    </div>
                                @endif
                            </td>
                        </tr>

                        @php $transactions = $registration->transactions()->latest('id')->get(); @endphp
                        @if($transactions->isNotEmpty())
                        <tr>
                            <th>Payment Attempts</th>
                            <td>
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>When</th>
                                            <th>Reference</th>
                                            <th>Transaction ID</th>
                                            <th>Card</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($transactions as $txn)
                                        <tr>
                                            <td>{{$txn->created_at ? $txn->created_at->format('d M Y, h:i A') : '—'}}</td>
                                            <td>{{$txn->reference}}</td>
                                            <td>{{$txn->transaction_id ?: '—'}}</td>
                                            <td>{{$txn->card_masked ?: '—'}}</td>
                                            <td>
                                                {{$txn->currency}} {{number_format((float) $txn->amount, 2)}}
                                                @if($txn->wasConverted())
                                                    <div class="small text-muted">
                                                        for {{$txn->presentment_currency}} {{number_format((float) $txn->presentment_amount, 2)}}
                                                        @ {{rtrim(rtrim(number_format((float) $txn->fx_rate, 4), '0'), '.')}}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{$txn->isSuccessful() ? 'success' : 'danger'}}">{{$txn->status}}</span>
                                                @if($txn->message)
                                                    <div class="small text-muted">{{$txn->message}}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @endif
                        <tr><th>Submitted At</th><td>{{$registration->created_at ? $registration->created_at->format('d M Y, h:i A') : ''}}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
