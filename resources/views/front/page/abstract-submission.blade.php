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
        <h2>Call for Abstracts</h2>
        <span class="sec-line"></span>
      </div>

      <p class="content-text lead">
        Welcome to the abstract submission portal for the <strong>7th ACOMS Trainee Conference</strong>. The scientific
        committee truly believes that your input is a step towards sharing knowledge and enlightening the empowered
        frontier surgeons of tomorrow.
      </p>
      <p class="content-text">
        Please read the key dates and submission guidelines below before you submit.
        <a href="#submit-abstract" class="abs-jump">Go to the submission form <i class="fa-solid fa-arrow-down"></i></a>
      </p>

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
        <a href="#submit-abstract" class="abs-jump mt-2 d-inline-block">Go to the form <i class="fa-solid fa-arrow-down"></i></a>
      </div>
      @endif
    </div>

    <!-- Key dates -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Key Dates</h2>
        <span class="sec-line"></span>
      </div>

      <div class="fee-table-wrap compact-wrap">
        <table class="fee-table compact-table">
          <thead>
            <tr>
              <th scope="col" class="col-cat">Milestone</th>
              <th scope="col"><i class="fa-solid fa-trophy me-1"></i>Prize Paper / e-Poster</th>
              <th scope="col">Free Paper / e-Poster</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">Last date for abstract submission</th>
              <td class="is-early">25 October 2026</td>
              <td>8 November 2026</td>
            </tr>
            <tr>
              <th scope="row">Notification of acceptance</th>
              <td>After 15 November 2026</td>
              <td>After 25 November 2026</td>
            </tr>
            <tr>
              <th scope="row">Last date for presentation file upload</th>
              <td>15 February 2027</td>
              <td>10 February 2027</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Guidelines -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Submission Guidelines</h2>
        <span class="sec-line"></span>
      </div>

      <div class="accordion abs-guide" id="absGuide">

        <!-- 1 -->
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#g1" aria-expanded="true" aria-controls="g1">
              <span class="ag-num">1</span> General Submission Guidelines
            </button>
          </h3>
          <div id="g1" class="accordion-collapse collapse show" data-bs-parent="#absGuide">
            <div class="accordion-body">
              <ul class="dot-list mt-0">
                <li><strong>Eligibility:</strong> Submissions are welcomed from all qualified oral and maxillofacial surgeons / postgraduate surgical trainees in Oral and Maxillofacial Surgery. Abstracts must be original.</li>
                <li><strong>Language:</strong> All abstracts must be submitted in English.</li>
                <li><strong>Abstract format:</strong> Must follow the guidelines suggested by the Scientific Committee.</li>
                <li><strong>In-person presentation is mandatory</strong> for all accepted abstracts. Virtual presentations will not be considered.</li>
                <li>All presenting authors must <a href="{{ route('registration.form') }}">register for the 7th ACOMS</a>. Abstracts will not be included in the programme unless the presenter is registered.</li>
                <li><strong>Conflict of interest:</strong> All presenters must disclose any potential conflicts of interest.</li>
                <li>Presenters may select their presentation preference (Oral / e-Poster) and category (Prize / Free). However, the Scientific Committee reserves the right to allocate the preferences.</li>
                <li>Abstracts not selected for oral presentation may be eligible for e-poster.</li>
                <li>All abstracts must be submitted through this abstract submission portal. <strong>Submissions by email will not be considered</strong> for review.</li>
                <li>Figures, tables, and graphs are not permitted in submissions but may be included in presentations.</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- 2 -->
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#g2" aria-expanded="false" aria-controls="g2">
              <span class="ag-num">2</span> Abstract Structure
            </button>
          </h3>
          <div id="g2" class="accordion-collapse collapse" data-bs-parent="#absGuide">
            <div class="accordion-body">
              <p class="content-text">All abstracts must be submitted following the instructions below.</p>
              <div class="row g-4">
                <div class="col-lg-6">
                  <div class="ag-card">
                    <h4>For Research Papers / Reviews</h4>
                    <ul class="dot-list">
                      <li><strong>Title:</strong> concise and descriptive (limit: 15 words).</li>
                      <li><strong>Authors and affiliations:</strong> list all authors and their institutions. The name and designation of the presenting author must be entered in the submission portal.</li>
                      <li>IMRD format, <strong>maximum 300 words</strong> excluding references, font Times New Roman 14.</li>
                      <li>Structured as: Introduction, Aims &amp; Objectives, Materials &amp; Methods, Results, Discussion, Conclusion.</li>
                      <li>A maximum of <strong>four references</strong> is permitted.</li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="ag-card">
                    <h4>For Case Reports / Series</h4>
                    <ul class="dot-list">
                      <li><strong>Title:</strong> accurate and concise (not exceeding 15 words).</li>
                      <li><strong>Maximum 300 words</strong>, excluding references.</li>
                      <li>Font: Times New Roman 14.</li>
                      <li>Structured as: Introduction, Case Description, Discussion, Conclusion.</li>
                      <li>A maximum of <strong>four references</strong> is permitted.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 3 -->
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#g3" aria-expanded="false" aria-controls="g3">
              <span class="ag-num">3</span> Categories for Abstract Submission
            </button>
          </h3>
          <div id="g3" class="accordion-collapse collapse" data-bs-parent="#absGuide">
            <div class="accordion-body">
              <p class="content-text">Abstracts should be submitted under one of the following categories in the submission form:</p>
              <ul class="venue-tags">
                <li>Craniofacial Trauma</li>
                <li>Craniofacial Deformity — Cleft Lip Palate; Distraction; Orthognathic Surgery</li>
                <li>Dentoalveolar Surgery</li>
                <li>Dental Implantology</li>
                <li>Facial Esthetic Surgery</li>
                <li>Head and Neck Oncology</li>
                <li>Oral and Maxillofacial Pathology &amp; Infection</li>
                <li>Orofacial Pain &amp; TMJ</li>
                <li>Patient Safety, Ethics &amp; Outcomes</li>
                <li>Practice Management</li>
                <li>Research &amp; Clinical Studies</li>
                <li>Others</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- 4 -->
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#g4" aria-expanded="false" aria-controls="g4">
              <span class="ag-num">4</span> Prize Paper (Oral) / e-Poster Category
            </button>
          </h3>
          <div id="g4" class="accordion-collapse collapse" data-bs-parent="#absGuide">
            <div class="accordion-body">
              <ul class="dot-list mt-0">
                <li>Only <strong>postgraduates / trainees</strong> are eligible to submit abstracts in this category.</li>
                <li>Last date for submission of abstracts: <strong>25 October 2026</strong>.</li>
                <li>Submitted abstracts will be reviewed and shortlisted by the Scientific Committee. Only a few papers / e-posters from all the abstracts submitted in this category will be selected for presentation.</li>
                <li>Selected presenters will be informed by email after <strong>15 November 2026</strong>.</li>
                <li>The final presentation file (oral / e-poster) must be uploaded in its respective format before <strong>15 February 2027</strong>.</li>
                <li>Registration for the conference is mandatory before submitting the presentation file.</li>
                <li>Abstracts not selected in this category will be permitted to be presented as a free paper / e-poster during the conference.</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- 5 -->
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#g5" aria-expanded="false" aria-controls="g5">
              <span class="ag-num">5</span> Free Paper (Oral) / e-Poster Category
            </button>
          </h3>
          <div id="g5" class="accordion-collapse collapse" data-bs-parent="#absGuide">
            <div class="accordion-body">
              <ul class="dot-list mt-0">
                <li>Last date for submission of abstracts: <strong>8 November 2026</strong>.</li>
                <li>Notification of acceptance will be sent after <strong>25 November 2026</strong>.</li>
                <li>The final presentation (paper / e-poster) must be uploaded in its respective format before <strong>10 February 2027</strong>.</li>
                <li>Registration for the conference is mandatory before submitting the presentation file.</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- 6 -->
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#g6" aria-expanded="false" aria-controls="g6">
              <span class="ag-num">6</span> Paper (Oral) Presentation Guidelines
            </button>
          </h3>
          <div id="g6" class="accordion-collapse collapse" data-bs-parent="#absGuide">
            <div class="accordion-body">
              <ul class="dot-list mt-0">
                <li>Oral presentation duration is <strong>8 minutes only</strong>.</li>
                <li>The chairpersons may allow questions and clarifications from delegates in an additional <strong>2 minutes</strong> allocated for discussion.</li>
                <li>The presentation must be made in PowerPoint and saved in <strong>.pptx</strong> format.</li>
                <li>Last date for file upload: <strong>15 February 2027</strong> for the Prize category; <strong>10 February 2027</strong> for the Free (Non-prize) category.</li>
                <li>Presentations must be uploaded before the last date. Files uploaded after that will not be accepted for presentation.</li>
                <li>Registration for the conference is mandatory before submitting the presentation file.</li>
                <li>The certificate of presentation will be given to the presenting author only.</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- 7 -->
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#g7" aria-expanded="false" aria-controls="g7">
              <span class="ag-num">7</span> e-Poster Guidelines
            </button>
          </h3>
          <div id="g7" class="accordion-collapse collapse" data-bs-parent="#absGuide">
            <div class="accordion-body">
              <ul class="dot-list mt-0">
                <li>Upload your e-poster according to the theme as a <strong>single-page PDF, JPEG, or PowerPoint file</strong> in landscape orientation (<strong>16:9</strong> page setup).</li>
                <li>Last date for file upload: <strong>15 February 2027</strong> for the Prize category; <strong>10 February 2027</strong> for the Free (Non-prize) category.</li>
                <li>Presentations must be uploaded before the last date. Files uploaded after that will not be accepted for presentation.</li>
                <li>Registration for the conference is mandatory before submitting the presentation file.</li>
                <li>e-Posters will be displayed on LED screens in auto-play mode.</li>
                <li>Slide shows and multiple slides are not allowed.</li>
                <li>Include your e-poster number when uploading your file (e-poster numbers will be allotted).</li>
                <li>Pictures, tables, or graphs can be included.</li>
                <li>Music, video, and GIF files are not allowed.</li>
                <li>For prize e-posters, the time of your evaluation by the judges will be communicated to you, and your presence at that time is mandatory for evaluation and scoring.</li>
                <li>Only presenting authors will be awarded e-poster presentation certificates.</li>
              </ul>
            </div>
          </div>
        </div>

      </div>

      <!-- 8 -->
      <div class="stay-note mt-4">
        <i class="fa-solid fa-headset"></i>
        <p>
          <strong>Technical support and inquiries:</strong> if you run into technical issues while submitting your
          abstract, or have any questions, please contact the Scientific Committee at
          <a href="mailto:scientificacoms2027@gmail.com">scientificacoms2027@gmail.com</a>.
        </p>
      </div>
    </div>

    <!-- Form -->
    <div class="content-block" id="submit-abstract">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Submit Your Abstract</h2>
        <span class="sec-line"></span>
      </div>

      <!-- Instructions -->
      <div class="instructions">
        <p class="ins-title"><i class="fa-solid fa-circle-info"></i> Before you submit</p>
        <ul>
          <li>Complete every required field marked with an asterisk (<span style="color:var(--red)">*</span>).</li>
          <li>The abstract body should not exceed <strong>300 words</strong>, excluding the title and references, with a maximum of <strong>four references</strong>.</li>
          <li>Presentation files are collected later, once your abstract is accepted: <strong>.pptx</strong> for oral presentations; a single-page <strong>PDF, JPEG, or PowerPoint</strong> file for e-posters.</li>
        </ul>
      </div>

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
              <label class="form-label" for="phone">Phone Number <span class="req">*</span></label>
              <input type="tel" class="form-control" id="phone" name="phone"
                     placeholder="+977 98XXXXXXXX" value="{{ old('phone') }}" autocomplete="tel" required>
              <div class="field-hint">Include your country code.</div>
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
                        placeholder="Research / Review: Introduction, Aims &amp; Objectives, Materials &amp; Methods, Results, Discussion, Conclusion. Case Report / Series: Introduction, Case Description, Discussion, Conclusion."
                        required>{{ old('abstractBody') }}</textarea>
              <div class="char-count" id="wordCount">0 / 300 words</div>
            </div>

            <div class="col-12">
              <label class="form-label" for="references">References</label>
              <textarea class="form-control" id="references" name="references" rows="3"
                        placeholder="List any references cited in your abstract (optional, maximum four).">{{ old('references') }}</textarea>
            </div>
          </div>
        </div>

        {{-- Upload Presentation is hidden for now: presentation files are collected
             after acceptance. The controller still accepts an optional presFile,
             so restoring this block re-enables uploads.
        <!-- Upload -->
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Presentation</p>

          <label class="upload-drop" for="presFile" id="uploadDrop">
            <i class="fa-solid fa-cloud-arrow-up d-block"></i>
            <div class="ud-main"><span>Click to upload</span> or drag and drop your file</div>
            <div class="ud-sub">Oral presentation: PPTX · e-Poster: single-page PDF, JPEG, or PowerPoint (max 50 MB)</div>
            <input type="file" id="presFile" name="presFile" accept=".pdf,.ppt,.pptx,.jpg,.jpeg">
          </label>
          <div class="file-chosen" id="fileChosen">
            <i class="fa-solid fa-file-lines"></i>
            <span id="fileName">file.pdf</span>
            <button type="button" class="fc-remove" id="fileRemove" aria-label="Remove file">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>
        --}}

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