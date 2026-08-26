<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Mail;

class FrontController extends Controller
{
	public function xml_sitemap()
	{
			$content = view('front.page.sitemap',
			[
				'pages'=> \App\Models\Page::where('deleted_at', null)->get(),

			]);
			return \Response::make($content, '200')->header('Content-Type', 'text/xml');
	}

	public function pageDetail($permalink)
	{
		$page = \App\Models\Page::where('permalink', $permalink)->firstOrFail();

        $title = $page->meta_title ?? $page->title;
        $meta_description = $page->meta_description ?? '';
        $meta_keyword = $page->meta_keyword ?? '';
        $meta_robot = $page->meta_robot ?? '';
        $image = $page->media ? $page->media->get_attachment_url() : '';

        $data = compact('page', 'title', 'meta_description', 'meta_keyword', 'meta_robot', 'image');

        // Detect by permalink first, fall back to legacy ID matching
        if (in_array($page->permalink, ['contact', 'contact-us']) || $page->id == 38) {
            return view('front.page.contact', $data);
        }


        $viewPath = 'front.page.page-' . $page->id;
        if (!view()->exists($viewPath)) {
            $viewPath = 'front.page.detail';
        }

        return view($viewPath, $data);
	}

    public function aboutACOMS()
    {
        $page = \App\Models\Page::whereIn('permalink', ['about-acoms'])
                    ->orWhere('id', 37)
                    ->firstOrFail();

        $data = [
            'page'             => $page,
            'title'            => $page->meta_title ?? $page->title,
            'meta_description' => $page->meta_description ?? '',
            'meta_keyword'     => $page->meta_keyword ?? '',
            'meta_robot'       => $page->meta_robot ?? '',
            'image'            => $page->media ? $page->media->get_attachment_url() : '',
        ];

        return view('front.page.about-acoms', $data);
    }

    public function aboutNAOMS()
    {
        $page = \App\Models\Page::whereIn('permalink', ['about-naoms'])
                    ->orWhere('id', 38)
                    ->firstOrFail();

        $data = [
            'page'             => $page,
            'title'            => $page->meta_title ?? $page->title,
            'meta_description' => $page->meta_description ?? '',
            'meta_keyword'     => $page->meta_keyword ?? '',
            'meta_robot'       => $page->meta_robot ?? '',
            'image'            => $page->media ? $page->media->get_attachment_url() : '',
        ];

        return view('front.page.about-naoms', $data);
    }

    public function contactUs()
    {
        $page = \App\Models\Page::whereIn('permalink', ['contact-us'])
                    ->orWhere('id', 39)
                    ->firstOrFail();

        $data = [
            'page'             => $page,
            'title'            => $page->meta_title ?? $page->title,
            'meta_description' => $page->meta_description ?? '',
            'meta_keyword'     => $page->meta_keyword ?? '',
            'meta_robot'       => $page->meta_robot ?? '',
            'image'            => $page->media ? $page->media->get_attachment_url() : '',
        ];

        return view('front.page.contact', $data);
    }

    public function registrationDetails()
    {
        $page = \App\Models\Page::whereIn('permalink', ['registration-details'])
                    ->orWhere('id', 40)
                    ->firstOrFail();

        $data = [
            'page'             => $page,
            'title'            => $page->meta_title ?? $page->title,
            'meta_description' => $page->meta_description ?? '',
            'meta_keyword'     => $page->meta_keyword ?? '',
            'meta_robot'       => $page->meta_robot ?? '',
            'image'            => $page->media ? $page->media->get_attachment_url() : '',
        ];

        return view('front.page.registration-details', $data);
    }
    public function abstractSubmit()
    {
        $page = \App\Models\Page::whereIn('permalink', ['abstract-submission'])
                    ->orWhere('id', 41)
                    ->firstOrFail();

        $data = [
            'page'             => $page,
            'title'            => $page->meta_title ?? $page->title,
            'meta_description' => $page->meta_description ?? '',
            'meta_keyword'     => $page->meta_keyword ?? '',
            'meta_robot'       => $page->meta_robot ?? '',
            'image'            => $page->media ? $page->media->get_attachment_url() : '',
        ];

        return view('front.page.abstract-submission', $data);
    }

    public function abstractStore(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'authors'          => 'required|string',
            'affiliation'      => 'required|string|max:255',
            'presentingAuthor' => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'designation'      => 'required|string|max:100',
            'category'         => 'required|string|max:255',
            'presType'         => 'required|string|max:100',
            'researchType'     => 'required|string|max:100',
            'presCategory'     => 'required|string|max:50',
            'abstractBody'     => 'required|string',
            'references'       => 'nullable|string',
            'presFile'         => 'nullable|file|mimes:pdf,ppt,pptx|max:51200',
        ], [
            'presFile.mimes' => 'The presentation must be a PDF, PPT, or PPTX file.',
            'presFile.max'   => 'The presentation may not be larger than 50 MB.',
        ]);

        $abstract = new \App\Models\AbstractSubmission;
        $abstract->title             = $request->title;
        $abstract->authors           = $request->authors;
        $abstract->affiliation       = $request->affiliation;
        $abstract->presenting_author = $request->presentingAuthor;
        $abstract->email             = $request->email;
        $abstract->designation       = $request->designation;
        $abstract->category          = $request->category;
        $abstract->pres_type         = $request->presType;
        $abstract->research_type     = $request->researchType;
        $abstract->pres_category     = $request->presCategory;
        $abstract->abstract_body     = $request->abstractBody;
        $abstract->reference_list    = $request->references;
        $abstract->status            = 'submitted';

        if ($request->hasFile('presFile')) {
            $file      = $request->file('presFile');
            $original  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename  = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($original, PATHINFO_FILENAME)) . '.' . $extension;
            $dir       = 'uploads/abstracts';
            $file->move(public_path($dir), $filename);
            $abstract->file_name = $original;
            $abstract->file_path = $dir . '/' . $filename;
        }

        $abstract->save();

        // Notify the scientific committee, and confirm to the author. A mail
        // failure must never break the submission — the data is already stored.
        try {
            \Illuminate\Support\Facades\Mail::to(config('mail.admin_address'))
                ->send(new \App\Mail\AbstractSubmitted($abstract));
            if ($abstract->email) {
                \Illuminate\Support\Facades\Mail::to($abstract->email)
                    ->send(new \App\Mail\AbstractConfirmation($abstract));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Abstract email failed: ' . $e->getMessage());
        }

        return redirect()->route('abstract.submission')
            ->with('success', 'Thank you! Your abstract has been submitted successfully and is now under review by the scientific committee.');
    }

    public function organizingCommittee()
    {
        $page = \App\Models\Page::whereIn('permalink', ['organizing-committee'])
                    ->orWhere('id', 42)
                    ->firstOrFail();

        $data = [
            'page'             => $page,
            'title'            => $page->meta_title ?? $page->title,
            'meta_description' => $page->meta_description ?? '',
            'meta_keyword'     => $page->meta_keyword ?? '',
            'meta_robot'       => $page->meta_robot ?? '',
            'image'            => $page->media ? $page->media->get_attachment_url() : '',
        ];

        return view('front.page.organizing-committee', $data);
    }


    public function registrationForm()
    {
        $page = \App\Models\Page::whereIn('permalink', ['registration-form'])
                    ->orWhere('id', 43)
                    ->firstOrFail();

        $data = [
            'page'             => $page,
            'title'            => $page->meta_title ?? $page->title,
            'meta_description' => $page->meta_description ?? '',
            'meta_keyword'     => $page->meta_keyword ?? '',
            'meta_robot'       => $page->meta_robot ?? '',
            'image'            => $page->media ? $page->media->get_attachment_url() : '',
        ];

        return view('front.page.registration-form', $data);
    }

    public function registrationStore(Request $request)
    {
        $request->validate([
            'date'          => 'nullable|date',
            'fullName'      => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:50',
            'designation'   => 'required|string|max:255',
            'workplace'     => 'required|string|max:255',
            'idCard'        => 'required|file|mimes:jpg,jpeg,png|max:4096',
            'nationality'   => 'required|string|max:50',
            'naomsMember'   => 'required|string|max:10',
            'memberId'      => 'nullable|required_if:naomsMember,Yes|string|max:100',
            'regFor'        => 'required|string|max:100',
            'accommodation' => 'required|string|max:10',
            'accRooms'      => 'nullable|required_if:accommodation,Yes|integer|min:1',
            'accType'       => 'nullable|required_if:accommodation,Yes|string|max:50',
            'accompanying'  => 'required|string|max:10',
            'acpCount'      => 'nullable|required_if:accompanying,Yes|integer|min:1',
            'category'      => 'required|string|max:255',
            'paymentReceipt'=> 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'others'        => 'nullable|string',
        ], [
            'idCard.mimes'         => 'The ID card must be a JPG or PNG image.',
            'idCard.max'           => 'The ID card image may not be larger than 4 MB.',
            'paymentReceipt.mimes' => 'The payment receipt must be a JPG or PNG image.',
            'paymentReceipt.max'   => 'The payment receipt may not be larger than 4 MB.',
            'category.required'    => 'Please choose the registration category that applies to you.',
            'memberId.required_if' => 'Please enter your NAOMS membership ID.',
            'accRooms.required_if' => 'Please specify how many rooms you need.',
            'accType.required_if'  => 'Please select a room type.',
            'acpCount.required_if' => 'Please specify how many accompanying people you are bringing.',
        ]);

        $reg = new \App\Models\Registration;
        $reg->reg_date      = $request->date;
        $reg->full_name     = $request->fullName;
        $reg->email         = $request->email;
        $reg->phone         = $request->phone;
        $reg->designation   = $request->designation;
        $reg->workplace     = $request->workplace;
        $reg->nationality   = $request->nationality;
        $reg->naoms_member  = $request->naomsMember;
        $reg->member_id     = $request->memberId;
        $reg->reg_for       = $request->regFor;
        $reg->accommodation = $request->accommodation;
        $reg->acc_rooms     = $request->accRooms;
        $reg->acc_type      = $request->accType;
        $reg->accompanying  = $request->accompanying;
        $reg->acp_count     = $request->acpCount;
        $reg->category      = $request->category;
        $reg->others        = $request->others;
        $reg->status        = 'pending';

        // Handle for the payment page — unguessable, so the link can be emailed
        // and revisited without the delegate signing in.
        $reg->payment_reference = (string) \Illuminate\Support\Str::uuid();
        $reg->payment_status    = \App\Models\Registration::PAYMENT_UNPAID;

        if ($request->hasFile('idCard')) {
            $stored = $this->storeUpload($request->file('idCard'), 'uploads/registrations');
            $reg->id_card_name = $stored['name'];
            $reg->id_card_path = $stored['path'];
        }
        if ($request->hasFile('paymentReceipt')) {
            $stored = $this->storeUpload($request->file('paymentReceipt'), 'uploads/registrations');
            $reg->receipt_name = $stored['name'];
            $reg->receipt_path = $stored['path'];
        }

        $reg->save();

        // Price the registration so the payment page has an amount to charge.
        // A category with no configured fee must not silently become a free
        // registration, so fall the delegate back to the manual process.
        try {
            $quote = app(\App\Services\RegistrationFeeCalculator::class)->calculate($reg);

            $reg->amount        = $quote['total'];
            $reg->currency      = $quote['currency'];
            $reg->fee_tier      = $quote['tier'];
            $reg->fee_breakdown = $quote['lines'];
            $reg->save();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Registration fee calculation failed: ' . $e->getMessage());

            $this->notifyRegistration($reg);

            return redirect()->route('registration.form')
                ->with('success', 'Thank you! Your registration has been submitted. The organising committee will contact you with payment instructions.');
        }

        $this->notifyRegistration($reg);

        return redirect()->route('registration.payment', $reg->payment_reference);
    }

    /**
     * Notify the committee, and confirm to the registrant. Never let a mail
     * failure break the submission — the data is already safely stored.
     */
    private function notifyRegistration(\App\Models\Registration $reg): void
    {
        try {
            \Illuminate\Support\Facades\Mail::to(config('mail.admin_address'))
                ->send(new \App\Mail\RegistrationSubmitted($reg));
            if ($reg->email) {
                \Illuminate\Support\Facades\Mail::to($reg->email)
                    ->send(new \App\Mail\RegistrationConfirmation($reg));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Registration email failed: ' . $e->getMessage());
        }
    }

    private function storeUpload($file, $dir)
    {
        $original  = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename  = time() . '_' . \Illuminate\Support\Str::random(6) . '_' . \Illuminate\Support\Str::slug(pathinfo($original, PATHINFO_FILENAME)) . '.' . $extension;
        $file->move(public_path($dir), $filename);
        return ['name' => $original, 'path' => $dir . '/' . $filename];
    }

}
