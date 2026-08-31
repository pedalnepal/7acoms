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
      <h1 class="page-banner-title">Abstract Submission</h1>
      <p class="page-banner-sub">7th ACOMS Trainee Conference 2027 — share your research with the frontier surgeons of tomorrow.</p>
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
        <h2>Submit Your Abstract</h2>
        <span class="sec-line"></span>
      </div>

      <p class="content-text lead">
        Welcome to the abstract submission portal for the <strong>7th ACOMS Trainee Conference</strong>. The scientific
        committee truly believes that your input is a step towards sharing knowledge and enlightening the empowered
        frontier surgeons of tomorrow.
      </p>

      <!-- Instructions -->
      <div class="instructions mt-3">
        <p class="ins-title"><i class="fa-solid fa-circle-info"></i> Please read all instructions before submitting</p>
        <ul>
          <li>Complete every required field marked with an asterisk (<span style="color:var(--red)">*</span>).</li>
          <li>The abstract body should not exceed <strong>300 words</strong>, excluding the title and references.</li>
          <li>Presentation files may be uploaded once your abstract is accepted (PDF, PPT, or PPTX).</li>
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
      <form class="form-card" id="abstractForm" method="POST" action="{{ route('abstract.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- Shown by JS when the form is submitted with missing/invalid fields -->
        <div class="alert alert-danger" id="abstractFormAlert" role="alert" hidden
             style="border-left:4px solid var(--red);background:#fdecec;color:#842029;padding:1rem 1.25rem;border-radius:6px;margin-bottom:1.25rem;">
          <strong><i class="fa-solid fa-triangle-exclamation me-1"></i>Please fill in all required fields.</strong>
          <span id="abstractFormAlertDetail"></span>
        </div>

        <!-- Author details -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-user-pen"></i> Author &amp; Affiliation</p>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="title">Title of Abstract <span class="req">*</span></label>
              <input type="text" class="form-control" id="title" name="title"
                     placeholder="Enter the full title of your abstract" value="{{ old('title') }}" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="authors">Author &amp; Co-authors <span class="req">*</span></label>
              <textarea class="form-control" id="authors" name="authors" rows="2"
                        placeholder="e.g. Rai S, Shrestha P, Gurung M" required>{{ old('authors') }}</textarea>
              <div class="field-hint">List all authors in presentation order, separated by commas.</div>
            </div>

            <div class="col-12">
              <label class="form-label" for="affiliation">Affiliation <span class="req">*</span></label>
              <input type="text" class="form-control" id="affiliation" name="affiliation"
                     placeholder="Institution / Department, City, Country" value="{{ old('affiliation') }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="presentingAuthor">Presenting Author <span class="req">*</span></label>
              <input type="text" class="form-control" id="presentingAuthor" name="presentingAuthor"
                     placeholder="Full name of the presenting author" value="{{ old('presentingAuthor') }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="email">Email <span class="req">*</span></label>
              <input type="email" class="form-control" id="email" name="email"
                     placeholder="you@example.com" value="{{ old('email') }}" required>
              <div class="field-hint">We will send submission updates to this address.</div>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="designation">Designation <span class="req">*</span></label>
              <select class="form-select" id="designation" name="designation" required>
                <option value="" disabled {{ old('designation') ? '' : 'selected' }}>Select designation</option>
                <option value="consultant" {{ old('designation')=='consultant'?'selected':'' }}>Consultant</option>
                <option value="trainee" {{ old('designation')=='trainee'?'selected':'' }}>Trainee</option>
                <option value="dental-surgeon" {{ old('designation')=='dental-surgeon'?'selected':'' }}>Dental Surgeon</option>
                <option value="student" {{ old('designation')=='student'?'selected':'' }}>Student</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Presentation details -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-list-check"></i> Presentation Details</p>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="category">Category for Topic <span class="req">*</span></label>
              <select class="form-select" id="category" name="category" required>
                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Select a topic category</option>
                @foreach([
                  'Craniofacial Trauma',
                  'Craniofacial Deformity — Cleft Lip Palate; Distraction; Orthognathic Surgery',
                  'Dentoalveolar Surgery',
                  'Dental Implantology',
                  'Facial Esthetic Surgery',
                  'Head and Neck Oncology',
                  'Oral and Maxillofacial Pathology & Infection',
                  'Orofacial Pain & TMJ',
                  'Patient Safety, Ethics & Outcomes',
                  'Practice Management',
                  'Research & Clinical Studies',
                  'Others',
                ] as $topic)
                  <option value="{{ $topic }}" {{ old('category')===$topic ? 'selected' : '' }}>{{ $topic }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="presType">Type of Presentation <span class="req">*</span></label>
              <select class="form-select" id="presType" name="presType" required>
                <option value="" disabled {{ old('presType') ? '' : 'selected' }}>Select type</option>
                <option value="paper" {{ old('presType')=='paper'?'selected':'' }}>Paper (Oral)</option>
                <option value="eposter" {{ old('presType')=='eposter'?'selected':'' }}>e-Poster</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="researchType">Type of Research <span class="req">*</span></label>
              <select class="form-select" id="researchType" name="researchType" required>
                <option value="" disabled {{ old('researchType') ? '' : 'selected' }}>Select research type</option>
                @foreach(['Original Research','Review','Case Report','Case Series'] as $rt)
                  <option value="{{ $rt }}" {{ old('researchType')===$rt ? 'selected' : '' }}>{{ $rt }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label d-block">Category of Presentation <span class="req">*</span></label>
              <div class="pill-group">
                <div class="pill-opt">
                  <input type="radio" name="presCategory" id="catPrize" value="prize" {{ old('presCategory')=='prize'?'checked':'' }} required>
                  <label for="catPrize"><i class="fa-solid fa-trophy me-1"></i>Prize</label>
                </div>
                <div class="pill-opt">
                  <input type="radio" name="presCategory" id="catFree" value="free" {{ old('presCategory')=='free'?'checked':'' }}>
                  <label for="catFree">Free (Non-prize)</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Abstract body -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-align-left"></i> Abstract</p>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="abstractBody">Abstract Body <span class="req">*</span></label>
              <textarea class="form-control" id="abstractBody" name="abstractBody" rows="9"
                        placeholder="Structure your abstract under headings such as Background, Methods, Results, and Conclusion."
                        required>{{ old('abstractBody') }}</textarea>
              <div class="char-count" id="wordCount">0 / 300 words</div>
            </div>

            <div class="col-12">
              <label class="form-label" for="references">References</label>
              <textarea class="form-control" id="references" name="references" rows="3"
                        placeholder="List any references cited in your abstract (optional).">{{ old('references') }}</textarea>
            </div>
          </div>
        </div>

        <!-- Upload -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Presentation</p>

          <label class="upload-drop" for="presFile" id="uploadDrop">
            <i class="fa-solid fa-cloud-arrow-up d-block"></i>
            <div class="ud-main"><span>Click to upload</span> or drag and drop your file</div>
            <div class="ud-sub">For oral presentation / e-poster — PDF, PPT, or PPTX (max 50 MB)</div>
            <input type="file" id="presFile" name="presFile" accept=".pdf,.ppt,.pptx">
          </label>
          <div class="file-chosen" id="fileChosen">
            <i class="fa-solid fa-file-lines"></i>
            <span id="fileName">file.pdf</span>
            <button type="button" class="fc-remove" id="fileRemove" aria-label="Remove file">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <span class="save-note"><i class="fa-solid fa-lock me-1"></i>Your details are stored securely and reviewed by the scientific committee.</span>
          <button type="submit" class="btn btn-submit">
            <i class="fa-solid fa-paper-plane me-1"></i>Submit Abstract
          </button>
        </div>

      </form>
    </div>

  </div>
</section>

@endsection

@push('scripts')
<script>
  // ---- Live word counter for the abstract body (limit 300) ----
  (function () {
    var body = document.getElementById('abstractBody');
    var counter = document.getElementById('wordCount');
    if (!body || !counter) return;
    function update() {
      var words = body.value.trim() === '' ? 0 : body.value.trim().split(/\s+/).length;
      counter.textContent = words + ' / 300 words';
      counter.style.color = words > 300 ? 'var(--red, #c0392b)' : '';
    }
    body.addEventListener('input', update);
    update();
  })();

  // ---- Upload field: show chosen file name, allow removal ----
  (function () {
    var input   = document.getElementById('presFile');
    var chosen  = document.getElementById('fileChosen');
    var nameEl  = document.getElementById('fileName');
    var removeB = document.getElementById('fileRemove');
    if (!input || !chosen) return;
    var MAX = 50 * 1024 * 1024; // 50 MB
    input.addEventListener('change', function () {
      if (input.files.length) {
        if (input.files[0].size > MAX) {
          alert('File is larger than 50 MB. Please choose a smaller file.');
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
    if (removeB) {
      removeB.addEventListener('click', function () {
        input.value = '';
        chosen.classList.remove('show');
      });
    }
  })();

  // ---- Submit handler: show a summary message for empty/invalid fields ----
  (function () {
    var form   = document.getElementById('abstractForm');
    var alertB = document.getElementById('abstractFormAlert');
    var detail = document.getElementById('abstractFormAlertDetail');
    if (!form || !alertB) return;

    // A readable label for a field, for the "missing: ..." summary.
    function labelFor(field) {
      var skipOwnLabel = field.type === 'radio' || field.type === 'checkbox' || field.type === 'file';
      if (!skipOwnLabel && field.labels && field.labels.length) {
        return field.labels[0].textContent.replace(/\*+/g, '').trim();
      }
      var group = field.closest('.col-md-6, .col-md-4, .col-md-8, .col-12, .col-6, .form-section');
      var l = group && group.querySelector('.form-label');
      return l ? l.textContent.replace(/\*+/g, '').trim() : (field.name || 'A required field');
    }

    // Put the red mark on the drop zone for file inputs, the control otherwise.
    function markTarget(el) {
      return el.type === 'file' ? (el.closest('.upload-drop') || el) : el;
    }

    // Drop the error state on a field as soon as the author corrects it.
    form.addEventListener('input', clearFieldError, true);
    form.addEventListener('change', clearFieldError, true);
    function clearFieldError(e) {
      var el = e.target;
      if (el.checkValidity && el.checkValidity()) {
        markTarget(el).classList.remove('is-invalid');
        if (el.type === 'radio') {
          form.querySelectorAll('input[name="' + el.name + '"]').forEach(function (r) {
            r.classList.remove('is-invalid');
          });
        }
        if (!form.querySelector('.is-invalid') && form.checkValidity()) {
          alertB.hidden = true;
        }
      }
    }

    form.addEventListener('submit', function (e) {
      form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });

      if (form.checkValidity()) {
        alertB.hidden = true;
        return; // valid — let the native POST proceed
      }

      e.preventDefault();

      var invalid = Array.prototype.filter.call(form.elements, function (el) {
        return el.willValidate && !el.checkValidity();
      });

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

      detail.textContent = names.length ? ' Missing or invalid: ' + names.join(', ') + '.' : '';

      alertB.hidden = false;
      alertB.scrollIntoView({ behavior: 'smooth', block: 'center' });

      var firstInvalid = invalid[0];
      if (firstInvalid && typeof firstInvalid.focus === 'function') firstInvalid.focus({ preventScroll: true });
    });
  })();
</script>
@endpush