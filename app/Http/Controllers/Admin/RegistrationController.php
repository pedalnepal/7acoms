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

    public function destroy($id)
    {
        $registration = Registration::withTrashed()->findOrFail($id);
        if ($registration->trashed()) {
            foreach ([$registration->recommendation_letter_path, $registration->receipt_path] as $p) {
                if ($p && file_exists(public_path($p))) {
                    @unlink(public_path($p));
                }
            }
            $registration->forceDelete();
            \Session::flash('success_message', 'Registration permanently deleted.');
        } else {
            $registration->delete();
            \Session::flash('success_message', 'Registration successfully moved to trash.');
        }
        return redirect(route('registration.index'));
    }
}
