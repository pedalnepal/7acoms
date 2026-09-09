<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Registration;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $search  = trim((string) $request->query('q', ''));
        $status  = (string) $request->query('status', '');
        $trashed = $request->has('trashed');

        $query = $trashed
            ? Registration::onlyTrashed()->orderBy('deleted_at', 'desc')
            : Registration::orderBy('id', 'desc');

        if ($status !== '') {
            $query->where('payment_status', $status);
        }

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");

                // The reference shown in the list (e.g. ACOMS-000123) is
                // derived from the id rather than stored, so a search for it
                // has to be turned back into an id to match anything.
                if (preg_match('/(\d+)/', $search, $matches)) {
                    $inner->orWhere('id', (int) $matches[1]);
                }
            });
        }

        $registrations = $query->paginate(10)->withQueryString();

        return view('admin.registration.list', [
            'registrations' => $registrations,
            'title'         => 'Registrations',
            'search'        => $search,
            'status'        => $status,
            'trashed'       => $trashed,
        ]);
    }

    public function show($id)
    {
        $registration = Registration::withTrashed()->findOrFail($id);
        return view('admin.registration.show', ['registration' => $registration, 'title' => 'Registration Detail']);
    }

    public function download($id, $type)
    {
        $registration = Registration::withTrashed()->findOrFail($id);
        $map = [
            'recommendation_letter' => ['path' => $registration->recommendation_letter_path, 'name' => $registration->recommendation_letter_name],
            'receipt' => ['path' => $registration->receipt_path, 'name' => $registration->receipt_name],
        ];
        if (!isset($map[$type]) || !$map[$type]['path'] || !file_exists(public_path($map[$type]['path']))) {
            abort(404);
        }
        return response()->download(public_path($map[$type]['path']), $map[$type]['name']);
    }

    public function restore($id)
    {
        $registration = Registration::onlyTrashed()->findOrFail($id);
        $registration->restore();
        \Session::flash('success_message', 'Registration restored successfully.');
        return redirect(route('registration.index') . '?trashed');
    }

    /**
     * Set the payment status by hand. Only the two settled outcomes are
     * offered: paid, for money taken outside the gateway (bank transfer, cash
     * at the desk), and unpaid, to undo one. Gateway-owned states — pending
     * and failed — are left to the gateway, and a remark is required so the
     * override is never anonymous.
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $data = $request->validate([
            'payment_status'  => 'required|in:' . Registration::PAYMENT_PAID . ',' . Registration::PAYMENT_UNPAID,
            'payment_remarks' => 'required|string|max:1000',
        ]);

        $registration = Registration::withTrashed()->findOrFail($id);

        if ($registration->trashed()) {
            \Session::flash('error_message', 'Payment status cannot be changed on a trashed registration.');
            return redirect($this->backTo($request));
        }

        $registration->payment_status = $data['payment_status'];
        $registration->payment_remarks = $data['payment_remarks'];
        $registration->payment_status_updated_by = \Auth::id();
        $registration->payment_status_updated_at = now();

        // paid_at is what the receipt and the reports read, so it has to follow
        // the status rather than keep a stale timestamp from a failed attempt.
        if ($data['payment_status'] === Registration::PAYMENT_PAID) {
            $registration->paid_at = $registration->paid_at ?: now();
        } else {
            $registration->paid_at = null;
        }

        $registration->save();

        \Session::flash('success_message', 'Payment status updated to ' . ucfirst($data['payment_status']) . '.');
        return redirect($this->backTo($request));
    }

    /**
     * Return to the list the admin was looking at, so the filter, search and
     * page they were on survive the update.
     */
    private function backTo(Request $request): string
    {
        $back = (string) $request->input('back', '');

        return $back !== '' && str_starts_with($back, route('registration.index'))
            ? $back
            : route('registration.index');
    }

    public function destroy($id)
    {
        $registration = Registration::withTrashed()->findOrFail($id);
        if ($registration->trashed()) {
            \Session::flash('error_message', 'Trashed registrations cannot be deleted permanently.');
            return redirect(route('registration.index') . '?trashed');
        }
        $registration->delete();
        \Session::flash('success_message', 'Registration successfully moved to trash.');
        return redirect(route('registration.index'));
    }
}
