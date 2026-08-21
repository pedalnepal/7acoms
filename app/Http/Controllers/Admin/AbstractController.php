<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AbstractSubmission;

class AbstractController extends Controller
{
    public function index()
    {
        if (isset($_GET['trashed'])) {
            $abstracts = AbstractSubmission::orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(50);
        } else {
            $abstracts = AbstractSubmission::orderBy('id', 'desc')->paginate(50);
        }
        return view('admin.abstract.list', ['abstracts' => $abstracts, 'title' => 'Abstract Submissions']);
    }

    public function show($id)
    {
        $abstract = AbstractSubmission::withTrashed()->findOrFail($id);
        return view('admin.abstract.show', ['abstract' => $abstract, 'title' => 'Abstract Detail']);
    }

    public function download($id)
    {
        $abstract = AbstractSubmission::withTrashed()->findOrFail($id);
        if (!$abstract->file_path || !file_exists(public_path($abstract->file_path))) {
            abort(404);
        }
        return response()->download(public_path($abstract->file_path), $abstract->file_name);
    }

    public function restore($id)
    {
        $abstract = AbstractSubmission::onlyTrashed()->findOrFail($id);
        $abstract->restore();
        \Session::flash('success_message', 'Abstract restored successfully.');
        return redirect(route('abstract.index') . '?trashed');
    }

    public function destroy($id)
    {
        $abstract = AbstractSubmission::withTrashed()->findOrFail($id);
        if ($abstract->trashed()) {
            if ($abstract->file_path && file_exists(public_path($abstract->file_path))) {
                @unlink(public_path($abstract->file_path));
            }
            $abstract->forceDelete();
            \Session::flash('success_message', 'Abstract permanently deleted.');
        } else {
            $abstract->delete();
            \Session::flash('success_message', 'Abstract successfully moved to trash.');
        }
        return redirect(route('abstract.index'));
    }
}
