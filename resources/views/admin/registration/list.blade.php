@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')

@php
    $statusOptions = [
        ''        => 'All statuses',
        'paid'    => 'Paid',
        'pending' => 'Pending',
        'failed'  => 'Failed',
        'unpaid'  => 'Unpaid',
    ];
@endphp

<div class="card admin-panel-card">
    <div class="card-header admin-panel-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
        <h1 class="admin-page-title mb-0">
            Registrations
            <span class="badge bg-secondary fw-normal align-middle">{{ $registrations->total() }}</span>
        </h1>
        <div class="admin-toolbar">
            @if($trashed)
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('registration.index') }}">Show Active</a>
            @else
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('registration.index') }}?trashed">Show Trashed</a>
            @endif
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <form method="get" action="{{ route('registration.index') }}" class="row gy-2 gx-2 align-items-center">
            @if($trashed)
                <input type="hidden" name="trashed" value="1">
            @endif
            <div class="col-sm-6 col-md-4">
                <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm"
                       placeholder="Search name, email, phone or payment ref…">
            </div>
            <div class="col-sm-4 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-auto">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                @if($search !== '' || $status !== '')
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('registration.index') }}{{ $trashed ? '?trashed' : '' }}">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment Ref</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Category</th>
                        <th>NAOMS</th>
                        <th>Payment</th>
                        <th>Submitted</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Block form deliberately: Blade pairs raw php blocks with a
                        // non-greedy match, so the parenthesised inline form here would
                        // pair with the block terminator further down this file (the
                        // $badge assignment) and swallow every directive in between,
                        // including the loop.
                        $i = ($registrations->currentPage() - 1) * $registrations->perPage();
                    @endphp
                    @forelse($registrations as $registration)
                        @php $i++; @endphp
                        <tr>
                            <th scope="row">{{ $i }}</th>
                            <td class="text-nowrap">{{ $registration->paymentCode() }}</td>
                            <td>{{ $registration->full_name }}</td>
                            <td>{{ $registration->email }}</td>
                            <td>{{ $registration->phone }}</td>
                            <td>{{ $registration->category }}</td>
                            <td>{{ $registration->naoms_member }}</td>
                            <td>
                                @php
                                    $badge = [
                                        'paid'    => 'success',
                                        'pending' => 'warning',
                                        'failed'  => 'danger',
                                    ][$registration->payment_status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($registration->payment_status ?? 'unpaid') }}</span>
                                @if(!$trashed)
                                    <button type="button"
                                            class="btn btn-link btn-sm p-0 ms-1 align-baseline js-payment-status"
                                            data-action="{{ route('registration.payment_status', $registration->id) }}"
                                            data-code="{{ $registration->paymentCode() }}"
                                            data-name="{{ $registration->full_name }}"
                                            data-status="{{ $registration->payment_status ?? 'unpaid' }}"
                                            data-remarks="{{ $registration->payment_remarks }}">Change</button>
                                @endif
                                @if($registration->amount)
                                    <div class="small text-muted">
                                        {{ $registration->formattedAmount() }}
                                        @if($registration->isConverted())
                                            <span title="Charged in the currency the bank settles">({{ $registration->formattedChargeAmount() }})</span>
                                        @endif
                                    </div>
                                @endif
                                @if($registration->payment_remarks)
                                    <div class="small text-muted" title="{{ $registration->payment_remarks }}">
                                        {{ \Illuminate\Support\Str::limit($registration->payment_remarks, 40) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $registration->created_at ? $registration->created_at->format('d M Y, h:i A') : '' }}</td>
                            <td class="text-end text-nowrap">
                                <a class="btn waves-effect waves-light btn-sm btn-info" href="{{ route('registration.admin.show', $registration->id) }}">View</a>
                                @if($trashed)
                                    <form action="{{ route('registration.restore', $registration->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn waves-effect waves-light btn-sm btn-success">Restore</button>
                                    </form>
                                @else
                                    <form action="{{ route('registration.destroy', $registration->id) }}" method="post" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn waves-effect waves-light btn-sm btn-danger">Trash</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="admin-table-empty">
                                @if($search !== '' || $status !== '')
                                    No registrations match this search.
                                @else
                                    No registrations found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer text-center card-footer admin-pagination border-0">{{ $registrations->links() }}</div>
</div>

@unless($trashed)
<div class="modal fade" id="PaymentStatusModal" tabindex="-1" aria-labelledby="PaymentStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="" id="PaymentStatusForm">
            @csrf
            <input type="hidden" name="back" value="{{ request()->fullUrl() }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="PaymentStatusModalLabel">Update Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3 small text-muted" id="PaymentStatusFor"></p>

                    <div class="mb-3">
                        <label class="form-label" for="PaymentStatusSelect">Payment Status</label>
                        <select class="form-select" name="payment_status" id="PaymentStatusSelect" required>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="PaymentStatusRemarks">Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="payment_remarks" id="PaymentStatusRemarks" rows="3"
                                  maxlength="1000" required
                                  placeholder="Why is this being changed? e.g. bank transfer received on 05 Sep, ref 12345"></textarea>
                        <div class="form-text">Recorded against the registration with your name and the time.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endunless
@stop

@unless($trashed)
@push('scripts')
<script>
    (function () {
        var modalEl = document.getElementById('PaymentStatusModal');
        if (!modalEl) {
            return;
        }

        var form    = document.getElementById('PaymentStatusForm');
        var select  = document.getElementById('PaymentStatusSelect');
        var remarks = document.getElementById('PaymentStatusRemarks');
        var forLine = document.getElementById('PaymentStatusFor');
        var modal   = new bootstrap.Modal(modalEl);

        document.querySelectorAll('.js-payment-status').forEach(function (button) {
            button.addEventListener('click', function () {
                form.action  = button.dataset.action;
                forLine.textContent = button.dataset.code + ' — ' + button.dataset.name;
                remarks.value = button.dataset.remarks || '';

                // A gateway-owned status (pending, failed) is not one of the
                // two options, so default those to Paid rather than leave the
                // select showing something the row is not.
                //
                // script.js turns every <select> into a Select2 widget on
                // document-ready, which renders its own box over the native
                // element. Setting select.value directly leaves that box
                // showing whatever it started with — jQuery's val()+trigger
                // is what Select2 listens for to redraw it.
                var newValue = button.dataset.status === 'unpaid' ? 'unpaid' : 'paid';
                if (window.jQuery) {
                    jQuery(select).val(newValue).trigger('change');
                } else {
                    select.value = newValue;
                }

                modal.show();
            });
        });

        modalEl.addEventListener('shown.bs.modal', function () {
            remarks.focus();
        });
    })();
</script>
@endpush
@endunless
