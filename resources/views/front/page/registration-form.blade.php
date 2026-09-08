@extends('front.common.layout')

@section('content')


<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="page-banner">
  <img class="page-banner-img" src="images/banner-1.jpg" alt="Oral and Maxillofacial Surgery congress">
  <div class="page-banner-overlay"></div>
  <div class="container">
    <div class="page-banner-body">
      <h1 class="page-banner-title">Registration Form</h1>
      <p class="page-banner-sub">7th ACOMS Trainee Conference 2027, Kathmandu — complete the form below to secure your place.</p>
    </div>
  </div>
</section>


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="section-content">
  <div class="container">

    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Delegate Registration</h2>
        <span class="sec-line"></span>
      </div>

      <p class="content-text lead">
        Please complete all required fields to register for the <strong>7th ACOMS Trainee Conference 2027</strong>.
        Your applicable fee is determined by the date payment is received — review the
        <a href="{{url('registration-details')}}" style="color:var(--red);font-weight:600;">registration fees and guidelines</a>
        before you begin.
      </p>

      <!-- Instructions -->
      <div class="instructions mt-3">
        <p class="ins-title"><i class="fa-solid fa-circle-info"></i> Before you fill in the form</p>
        <ul>
          <li>Complete every required field marked with an asterisk (<span style="color:var(--red)">*</span>).</li>
          <li>Choose the <strong>registration category</strong> that matches your professional and membership status.</li>
          <li>After you submit, you will be taken to a <strong>secure payment page</strong> to pay your registration fee by card.</li>
          <li>Your place is confirmed as soon as the payment goes through.</li>
        </ul>
      </div>

      @if(session('success'))
      <div class="alert alert-success mt-3" role="alert" style="border-left:4px solid #198754;background:#e8f6ee;color:#0f5132;padding:1rem 1.25rem;border-radius:6px;">
        <i class="fa-solid fa-circle-check me-1"></i>{{ session('success') }}
      </div>
      @endif

      @if($errors->any())
      <div class="alert alert-danger mt-3" role="alert" style="border-left:4px solid var(--red);background:#fdecec;color:#842029;padding:1rem 1.25rem;border-radius:6px;">
        <strong><i class="fa-solid fa-triangle-exclamation me-1"></i>Please correct the following:</strong>
        <ul class="mb-0 mt-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <!-- FORM -->
      <form class="form-card" id="regForm" method="POST" action="{{ route('registration.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- Shown by JS when the form is submitted with missing/invalid fields -->
        <div class="alert alert-danger" id="regFormAlert" role="alert" hidden
             style="border-left:4px solid var(--red);background:#fdecec;color:#842029;padding:1rem 1.25rem;border-radius:6px;margin-bottom:1.25rem;">
          <strong><i class="fa-solid fa-triangle-exclamation me-1"></i>Please fill in all required fields.</strong>
          <span id="regFormAlertDetail"></span>
        </div>

        <!-- ============ 1. PERSONAL INFORMATION ============ -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-user"></i> Personal Information</p>
          <div class="row g-3">

            <div class="col-md-4">
              <label class="form-label" for="regDate">Date</label>
              <input type="date" class="form-control" id="regDate" name="date">
            </div>

            <div class="col-md-8">
              <label class="form-label" for="fullName">Full Name <span class="req">*</span></label>
              <input type="text" class="form-control" id="fullName" name="fullName"
                placeholder="e.g. Dr. Aashish Sharma" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="email">Email <span class="req">*</span></label>
              <input type="email" class="form-control" id="email" name="email"
                placeholder="you@example.com" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="phone">Phone No. <span class="req">*</span></label>
              <input type="tel" class="form-control" id="phone" name="phone"
                placeholder="+977 98XXXXXXXX" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="designation">Designation <span class="req">*</span></label>
              <select class="form-select" id="designation" name="designation" required>
                <option value="" selected disabled>Select designation</option>
                <option value="Resident/Dental Surgeons/Students">Resident / Dental Surgeons / Students</option>
                <option value="Consultant/Faculty">Consultant / Faculty</option>
                <option value="Accompanying Person">Accompanying Person</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="workplace">Working Place <span class="req">*</span></label>
              <input type="text" class="form-control" id="workplace" name="workplace"
                placeholder="Hospital / Institution" required>
            </div>

            <div class="col-12" id="recLetterWrap" hidden>
              <label class="form-label d-block">Recommendation Letter from the Department Head <span class="req">*</span></label>
              <label class="upload-drop" id="idDrop" for="idFile">
                <i class="fa-solid fa-file-lines"></i>
                <div class="ud-main">Drop the recommendation letter here or <span>browse</span></div>
                <div class="ud-sub">JPG, PNG or PDF, maximum 4&nbsp;MB</div>
                <input type="file" id="idFile" name="recommendationLetter" accept=".jpg,.jpeg,.png,.pdf">
              </label>
              <div class="file-chosen" id="idChosen">
                <i class="fa-solid fa-file-image"></i>
                <span id="idFileName"></span>
                <button type="button" class="fc-remove" id="idRemove" aria-label="Remove file"><i class="fa-solid fa-xmark"></i></button>
              </div>
            </div>

          </div>
        </div>

        <!-- ============ 2. NATIONALITY & MEMBERSHIP ============ -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-passport"></i> Nationality &amp; Membership</p>
          <div class="row g-4">

            <div class="col-md-6">
              <label class="form-label d-block">Nationality <span class="req">*</span></label>
              <div class="pill-group">
                <div class="pill-opt">
                  <input type="radio" id="nat-nepali" name="nationality" value="Nepali" required>
                  <label for="nat-nepali">Nepali</label>
                </div>
                <div class="pill-opt">
                  <input type="radio" id="nat-international" name="nationality" value="International">
                  <label for="nat-international">International</label>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label d-block">NAOMS Member <span class="req">*</span></label>
              <div class="pill-group">
                <div class="pill-opt">
                  <input type="radio" id="mem-yes" name="naomsMember" value="Yes" required>
                  <label for="mem-yes">Yes</label>
                </div>
                <div class="pill-opt">
                  <input type="radio" id="mem-no" name="naomsMember" value="No">
                  <label for="mem-no">No</label>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- ============ 3. REGISTRATION TYPE ============ -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-clipboard-list"></i> Registration Type</p>
          <label class="form-label d-block">Registering For <span class="req">*</span></label>
          <div class="pill-group">
            <div class="pill-opt">
              <input type="radio" id="rf-conf" name="regFor" value="Conference" required>
              <label for="rf-conf">Conference</label>
            </div>
            <div class="pill-opt">
              <input type="radio" id="rf-conf-course" name="regFor" value="Conference + Hands-on Course">
              <label for="rf-conf-course">Conference + Hands-on Course</label>
            </div>
            <div class="pill-opt">
              <input type="radio" id="rf-conf-master" name="regFor" value="Conference + Master Class">
              <label for="rf-conf-master">Conference + Master Class</label>
            </div>
            <div class="pill-opt">
              <input type="radio" id="rf-conf-course-master" name="regFor" value="Conference + Hands-on Course + Master Class">
              <label for="rf-conf-course-master">Conference + Hands-on Course + Master Class</label>
            </div>
          </div>
        </div>

        <!-- ============ 4. CATEGORY & PAYMENT ============ -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-money-check-dollar"></i> Registration Category &amp; Payment</p>

          @php
            // Pulled from config/registration.php at the tier applicable
            // today — see RegistrationFeeCalculator::currentCategoryFees().
            $catFee = fn (string $name) => $categoryFees[$name]['currency'] . ' ' . number_format($categoryFees[$name]['amount']);
          @endphp
          <label class="form-label d-block">Registration Category <span class="req">*</span></label>

          {{-- Shown until the nationality and membership above are answered,
               since which categories apply — and what they cost — follows from
               those two answers. --}}
          <p class="field-hint" id="catPrompt" hidden>
            <i class="fa-solid fa-circle-info me-1"></i>
            Choose your <strong>nationality</strong> and <strong>NAOMS membership</strong> above to see the
            categories and fees that apply to you.
          </p>

          {{-- Rendered from the same config the fees come from, so a category
               added to config/registration.php appears here without an edit. --}}
          <div class="cat-group" id="catGroup">
            @foreach($categoryFees as $catName => $catRate)
              <div class="cat-opt" data-category="{{ $catName }}">
                <input type="radio" id="cat-{{ $loop->iteration }}" name="category" value="{{ $catName }}" required>
                <label for="cat-{{ $loop->iteration }}">{{ $catName }} <span class="cat-fee">{{ $catFee($catName) }}</span></label>
              </div>
            @endforeach
          </div>
          <p class="field-hint"><i class="fa-solid fa-circle-info me-1"></i>Fees rise at each deadline. See the full fee table on the <a href="{{url('registration-details')}}" style="color:var(--red);">registration details</a> page.</p>

          <div class="mt-4">
            <label class="form-label d-block">Upload Payment Receipt <span class="text-muted">(optional)</span></label>
            <p class="field-hint mb-2">
              <i class="fa-solid fa-circle-info me-1"></i>
              Only needed if you have already paid by bank transfer. Otherwise leave this empty — you will pay
              securely by card on the next step.
            </p>
            <label class="upload-drop" id="payDrop" for="payFile">
              <i class="fa-solid fa-cloud-arrow-up"></i>
              <div class="ud-main">Drop your payment receipt here or <span>browse</span></div>
              <div class="ud-sub">JPG, PNG or PDF, maximum 4&nbsp;MB</div>
              <input type="file" id="payFile" name="paymentReceipt" accept=".jpg,.jpeg,.png,.pdf">
            </label>
            <div class="file-chosen" id="payChosen">
              <i class="fa-solid fa-file-image"></i>
              <span id="payFileName"></span>
              <button type="button" class="fc-remove" id="payRemove" aria-label="Remove file"><i class="fa-solid fa-xmark"></i></button>
            </div>
          </div>

          <div class="mt-4">
            <label class="form-label" for="others">Others / Remarks</label>
            <textarea class="form-control" id="others" name="others" rows="3"
              placeholder="Any special requirements, dietary needs, or additional information"></textarea>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <span class="save-note"><i class="fa-solid fa-lock me-1"></i>Your details are stored securely. The next step is payment.</span>
          <button type="submit" class="btn btn-submit">
            <i class="fa-solid fa-lock me-1"></i>Continue to Payment
          </button>
        </div>

      </form>
    </div>

  </div>
</section>

@endsection


@push('scripts')
<script>
  // ---- Prefill today's date ----
  (function () {
    var d = document.getElementById('regDate');
    if (d && !d.value) d.value = new Date().toISOString().slice(0, 10);
  })();

  // ---- File upload display (reusable) ----
  function wireUpload(inputId, dropId, chosenId, nameId, removeId) {
    var input = document.getElementById(inputId);
    var drop = document.getElementById(dropId);
    var chosen = document.getElementById(chosenId);
    var nameEl = document.getElementById(nameId);
    var removeBtn = document.getElementById(removeId);
    var MAX = 4 * 1024 * 1024; // 4 MB

    input.addEventListener('change', function () {
      if (input.files.length) {
        if (input.files[0].size > MAX) {
          alert('File is larger than 4 MB. Please choose a smaller file.');
          input.value = '';
          chosen.classList.remove('show');
          return;
        }
        nameEl.textContent = input.files[0].name;
        chosen.classList.add('show');
      } else {
        chosen.classList.remove('show');
      }
    });
    removeBtn.addEventListener('click', function () {
      input.value = '';
      chosen.classList.remove('show');
    });
    ['dragover', 'dragenter'].forEach(function (evt) {
      drop.addEventListener(evt, function (e) { e.preventDefault(); drop.style.borderColor = 'var(--red)'; });
    });
    ['dragleave', 'drop'].forEach(function (evt) {
      drop.addEventListener(evt, function (e) { e.preventDefault(); drop.style.borderColor = ''; });
    });
    drop.addEventListener('drop', function (e) {
      if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
  }
  wireUpload('idFile', 'idDrop', 'idChosen', 'idFileName', 'idRemove');
  wireUpload('payFile', 'payDrop', 'payChosen', 'payFileName', 'payRemove');

  // ---- Recommendation letter: only for Residents / Dental Surgeons / Students ----
  // The upload card is hidden and not required for any other designation, so
  // the delegate is never blocked by a field they cannot see.
  (function () {
    var select = document.getElementById('designation');
    var wrap   = document.getElementById('recLetterWrap');
    var file   = document.getElementById('idFile');
    var chosen = document.getElementById('idChosen');
    function sync() {
      var show = select.value === 'Resident/Dental Surgeons/Students';
      wrap.hidden = !show;
      file.required = show;
      if (!show) {
        file.value = '';
        chosen.classList.remove('show');
      }
    }
    select.addEventListener('change', sync);
    sync();
  })();

  // ---- Registration category: only the ones the delegate is eligible for ----
  // Nationality picks the pricing group (the Nepalese rates are NPR, the
  // international ones USD); NAOMS membership only separates the two Nepalese
  // delegate rates. Ineligible options are disabled as well as hidden, so a
  // category the delegate was never shown cannot be submitted — the same map
  // is enforced again in registrationStore().
  //
  // Everything renders visible and enabled, and this narrows it on load, so
  // the form still works without JavaScript.
  (function () {
    var eligibility = @json($categoryEligibility);
    var group  = document.getElementById('catGroup');
    var prompt = document.getElementById('catPrompt');
    var opts   = Array.prototype.slice.call(group.querySelectorAll('.cat-opt'));

    function answer(name) {
      var checked = document.querySelector('input[name="' + name + '"]:checked');
      return checked ? checked.value : '';
    }

    function sync() {
      var allowed = (eligibility[answer('nationality')] || {})[answer('naomsMember')] || null;

      group.hidden  = !allowed;
      prompt.hidden = !!allowed;

      opts.forEach(function (opt) {
        var input = opt.querySelector('input[type="radio"]');
        var show  = !!allowed && allowed.indexOf(opt.dataset.category) !== -1;

        opt.hidden     = !show;
        input.disabled = !show;

        // A category that no longer applies must not stay selected: the fee
        // would no longer match what the delegate is telling us they are.
        if (!show) input.checked = false;
      });
    }

    ['nationality', 'naomsMember'].forEach(function (name) {
      document.querySelectorAll('input[name="' + name + '"]').forEach(function (radio) {
        radio.addEventListener('change', sync);
      });
    });

    sync();
  })();

  // ---- Submit handler: client-side validation, then submit to the server ----
  (function () {
    var form   = document.getElementById('regForm');
    var alertB = document.getElementById('regFormAlert');
    var detail = document.getElementById('regFormAlertDetail');

    // A readable label for a field, for the "missing: ..." summary.
    function labelFor(field) {
      // Radio/checkbox groups and file drop-zones wrap their control in a
      // label full of helper text — use the section's .form-label instead.
      var skipOwnLabel = field.type === 'radio' || field.type === 'checkbox' || field.type === 'file';
      if (!skipOwnLabel && field.labels && field.labels.length) {
        return field.labels[0].textContent.replace(/\*+/g, '').trim();
      }
      var group = field.closest('.col-md-6, .col-md-4, .col-md-8, .col-12, .col-6, .form-section');
      var l = group && group.querySelector('.form-label');
      return l ? l.textContent.replace(/\*+/g, '').trim() : (field.name || 'A required field');
    }

    // Drop the error state on a field as soon as the delegate corrects it.
    form.addEventListener('input', clearFieldError, true);
    form.addEventListener('change', clearFieldError, true);
    function clearFieldError(e) {
      var el = e.target;
      if (el.checkValidity && el.checkValidity()) {
        markTarget(el).classList.remove('is-invalid');
        // for a radio, the mark sits on the first control in the group
        if (el.type === 'radio') {
          form.querySelectorAll('input[name="' + el.name + '"]').forEach(function (r) {
            r.classList.remove('is-invalid');
          });
        }
        // hide the banner once nothing is invalid any more
        if (!form.querySelector('.is-invalid') && form.checkValidity()) {
          alertB.hidden = true;
        }
      }
    }

    // Put the red mark somewhere visible: the drop zone for file inputs,
    // the control itself otherwise.
    function markTarget(el) {
      return el.type === 'file' ? (el.closest('.upload-drop') || el) : el;
    }

    form.addEventListener('submit', function (e) {
      // Clear previous marks
      form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });

      if (form.checkValidity()) {
        alertB.hidden = true;
        return; // valid — let the native POST proceed
      }

      e.preventDefault();

      var invalid = Array.prototype.filter.call(form.elements, function (el) {
        return el.willValidate && !el.checkValidity();
      });

      // Mark each invalid control (for radio groups, mark the first in the group).
      var seenRadioGroups = {};
      var names = [];
      invalid.forEach(function (el) {
        if (el.type === 'radio') {
          if (seenRadioGroups[el.name]) return;
          seenRadioGroups[el.name] = true;
        }
        markTarget(el).classList.add('is-invalid');
        var name = labelFor(el);
        if (names.indexOf(name) === -1) names.push(name);
      });

      detail.textContent = names.length
        ? ' Missing or invalid: ' + names.join(', ') + '.'
        : '';

      alertB.hidden = false;
      alertB.scrollIntoView({ behavior: 'smooth', block: 'center' });

      var firstInvalid = invalid[0];
      if (firstInvalid && typeof firstInvalid.focus === 'function') firstInvalid.focus({ preventScroll: true });
    });
  })();
</script>
@endpush