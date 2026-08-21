<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Registration;

class RegistrationController extends Controller
{
    public function index()
    {
        if (isset($_GET['trashed'])) {
            $registrations = Registration::orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(50);
        } else {
            $registrations = Registration::orderBy('id', 'desc')->paginate(50);
        }
        return view('admin.registration.list', ['registrations' => $registrations, 'title' => 'Registrations']);
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
            'id_card' => ['path' => $registration->id_card_path, 'name' => $registration->id_card_name],
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
            foreach ([$registration->id_card_path, $registration->receipt_path] as $p) {
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
