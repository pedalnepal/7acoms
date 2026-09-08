<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AbstractSubmission;

class AbstractController extends Controller
{
    public function index(Request $request)
    {
        $search  = trim((string) $request->query('q', ''));
        $type    = (string) $request->query('type', '');
        $trashed = $request->has('trashed');

        $query = $trashed
            ? AbstractSubmission::onlyTrashed()->orderBy('deleted_at', 'desc')
            : AbstractSubmission::orderBy('id', 'desc');

        if ($type !== '') {
            $query->where('pres_type', $type);
        }

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('presenting_author', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $abstracts = $query->paginate(10)->withQueryString();

        return view('admin.abstract.list', [
            'abstracts' => $abstracts,
            'title'     => 'Abstract Submissions',
            'search'    => $search,
            'type'      => $type,
            'trashed'   => $trashed,
        ]);
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
