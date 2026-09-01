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
          <li>Keep a scanned copy of your <strong>ID card</strong> ready (JPG or PNG, max 4&nbsp;MB).</li>
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
              <input type="text" class="form-control" id="designation" name="designation"
                placeholder="e.g. Resident, OMFS" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="workplace">Working Place <span class="req">*</span></label>
              <input type="text" class="form-control" id="workplace" name="workplace"
                placeholder="Hospital / Institution" required>
            </div>

            <div class="col-12">
              <label class="form-label d-block">Upload ID Card <span class="req">*</span></label>
              <label class="upload-drop" id="idDrop" for="idFile">
                <i class="fa-solid fa-id-card"></i>
                <div class="ud-main">Drop your ID card here or <span>browse</span></div>
                <div class="ud-sub">JPG or PNG, maximum 4&nbsp;MB</div>
                <input type="file" id="idFile" name="idCard" accept=".jpg,.jpeg,.png" required>
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
                  <input type="radio" id="nat-saarc" name="nationality" value="SAARC">
                  <label for="nat-saarc">SAARC</label>
                </div>
                <div class="pill-opt">
                  <input type="radio" id="nat-nonsaarc" name="nationality" value="Non-SAARC">
                  <label for="nat-nonsaarc">Non-SAARC</label>
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

              <!-- conditional: membership ID -->
              <div class="conditional" id="memberIdWrap">
                <label class="form-label" for="memberId">Membership ID No.</label>
                <input type="text" class="form-control" id="memberId" name="memberId"
                  placeholder="Enter your NAOMS membership ID">
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
              <input type="radio" id="rf-course" name="regFor" value="Hands-on Course">
              <label for="rf-course">Hands-on Course</label>
            </div>
          </div>
        </div>

        <!-- ============ 4. ACCOMMODATION & ACCOMPANYING ============ -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-hotel"></i> Accommodation &amp; Accompanying Persons</p>
          <div class="row g-4">

            <div class="col-md-6">
              <label class="form-label d-block">Accommodation Required <span class="req">*</span></label>
              <div class="pill-group">
                <div class="pill-opt">
                  <input type="radio" id="acc-yes" name="accommodation" value="Yes" required>
                  <label for="acc-yes">Yes</label>
                </div>
                <div class="pill-opt">
                  <input type="radio" id="acc-no" name="accommodation" value="No">
                  <label for="acc-no">No</label>
                </div>
              </div>

              <!-- conditional: accommodation details -->
              <div class="conditional" id="accWrap">
                <div class="row g-3">
                  <div class="col-6">
                    <label class="form-label" for="accRooms">How Many Rooms</label>
                    <input type="number" min="1" class="form-control" id="accRooms" name="accRooms" placeholder="0">
                  </div>
                  <div class="col-6">
                    <label class="form-label" for="accType">Room Type</label>
                    <select class="form-select" id="accType" name="accType">
                      <option value="" selected disabled>Select type</option>
                      <option>Single</option>
                      <option>Double / Twin</option>
                      <option>Deluxe</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label d-block">Accompanying Person <span class="req">*</span></label>
              <div class="pill-group">
                <div class="pill-opt">
                  <input type="radio" id="acp-yes" name="accompanying" value="Yes" required>
                  <label for="acp-yes">Yes</label>
                </div>
                <div class="pill-opt">
                  <input type="radio" id="acp-no" name="accompanying" value="No">
                  <label for="acp-no">No</label>
                </div>
              </div>

              <!-- conditional: number of people -->
              <div class="conditional" id="acpWrap">
                <label class="form-label" for="acpCount">No. of People</label>
                <input type="number" min="1" class="form-control" id="acpCount" name="acpCount" placeholder="0">
              </div>
            </div>

          </div>
        </div>

        <!-- ============ 5. CATEGORY & PAYMENT ============ -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-money-check-dollar"></i> Registration Category &amp; Payment</p>

          @php
            // Pulled from config/registration.php at the tier applicable
            // today — see RegistrationFeeCalculator::currentCategoryFees().
            $catFee = fn (string $name) => $categoryFees[$name]['currency'] . ' ' . number_format($categoryFees[$name]['amount']);
          @endphp
          <label class="form-label d-block">Registration Category <span class="req">*</span></label>
          <div class="cat-group">
            <div class="cat-opt">
              <input type="radio" id="cat-1" name="category" value="NAOMS Member" required>
              <label for="cat-1">NAOMS Member <span class="cat-fee">{{ $catFee('NAOMS Member') }}</span></label>
            </div>
            <div class="cat-opt">
              <input type="radio" id="cat-2" name="category" value="Non-NAOMS Member (Nepalese)">
              <label for="cat-2">Non-NAOMS Member (Nepalese) <span class="cat-fee">{{ $catFee('Non-NAOMS Member (Nepalese)') }}</span></label>
            </div>
            <div class="cat-opt">
              <input type="radio" id="cat-3" name="category" value="International Delegate">
              <label for="cat-3">International Delegate <span class="cat-fee">{{ $catFee('International Delegate') }}</span></label>
            </div>
            <div class="cat-opt">
              <input type="radio" id="cat-4" name="category" value="Residents and Dental Surgeons (Nepalese)">
              <label for="cat-4">Residents and Dental Surgeons (Nepalese) <span class="cat-fee">{{ $catFee('Residents and Dental Surgeons (Nepalese)') }}</span></label>
            </div>
            <div class="cat-opt">
              <input type="radio" id="cat-5" name="category" value="Residents and Dental Surgeons (International)">
              <label for="cat-5">Residents and Dental Surgeons (International) <span class="cat-fee">{{ $catFee('Residents and Dental Surgeons (International)') }}</span></label>
            </div>
            <div class="cat-opt">
              <input type="radio" id="cat-6" name="category" value="Accompanying Person">
              <label for="cat-6">Accompanying Person <span class="cat-fee">{{ $catFee('Accompanying Person') }}</span></label>
            </div>
            <div class="cat-opt">
              <input type="radio" id="cat-7" name="category" value="Accompanying Person (International)">
              <label for="cat-7">Accompanying Person (International) <span class="cat-fee">{{ $catFee('Accompanying Person (International)') }}</span></label>
            </div>
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
              <div class="ud-sub">JPG or PNG, maximum 4&nbsp;MB</div>
              <input type="file" id="payFile" name="paymentReceipt" accept=".jpg,.jpeg,.png">
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
          alert('File is larger than 4 MB. Please choose a smaller image.');
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

  // ---- Conditional reveal panels ----
  // requiredFieldIds are only enforced while their panel is shown, so a
  // delegate who answers "No" is never blocked by a hidden required field.
  function wireConditional(name, showValue, wrapId, requiredFieldIds) {
    var wrap = document.getElementById(wrapId);
    var requiredFields = (requiredFieldIds || []).map(function (id) { return document.getElementById(id); });
    document.querySelectorAll('input[name="' + name + '"]').forEach(function (r) {
      r.addEventListener('change', function () {
        var visible = r.checked && r.value === showValue;
        wrap.classList.toggle('show', visible);
        requiredFields.forEach(function (field) {
          field.required = visible;
          // Clear the answer when the panel is hidden, so a value typed before
          // the delegate changed their mind is never submitted.
          if (!visible) field.value = '';
        });
      });
    });
  }
  wireConditional('naomsMember', 'Yes', 'memberIdWrap', ['memberId']);
  wireConditional('accommodation', 'Yes', 'accWrap', ['accRooms', 'accType']);
  wireConditional('accompanying', 'Yes', 'acpWrap', ['acpCount']);

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