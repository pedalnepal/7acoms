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
      <h1 class="page-banner-title">Registration Details</h1>
      <p class="page-banner-sub">7th Asian Congress of Oral and Maxillofacial Surgery — PG Trainee Congress 2027, Kathmandu</p>
    </div>
  </div>
</section>


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="section-content">
  <div class="container">

    <!-- Registration Fees -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Registration Fees</h2>
        <span class="sec-line"></span>
      </div>

      <p class="content-text lead">
        Registration fees for the <strong>7th ACOMS PG Trainee Congress 2027</strong> are listed below by delegate
        category. Fees rise at each deadline, so registering early secures the lowest rate.
      </p>

      <!-- Deadline strip -->
      <div class="row g-3 deadline-row mt-2">
        <div class="col-lg-3 col-sm-6">
          <div class="deadline-card is-active">
            <div class="dl-label">Early Bird Registration</div>
            <p class="dl-date">Up to 15 November 2026</p>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="deadline-card">
            <div class="dl-label">Registration</div>
            <p class="dl-date">Up to 15 January 2027</p>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="deadline-card">
            <div class="dl-label">Late Registration</div>
            <p class="dl-date">After 15 January 2027</p>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="deadline-card">
            <div class="dl-label">Spot Registration</div>
            <p class="dl-date">At the venue</p>
          </div>
        </div>
      </div>

      <!-- Fee table -->
      <div class="fee-table-wrap">
        <table class="fee-table">
          <thead>
            <tr>
              <th scope="col" class="col-cat">Category</th>
              <th scope="col">Early Bird Registration<span class="th-sub">Up to 15 Nov 2026</span></th>
              <th scope="col">Registration<span class="th-sub">Up to 15 Jan 2027</span></th>
              <th scope="col">Late Registration<span class="th-sub">After 15 Jan 2027</span></th>
              <th scope="col">Spot Registration<span class="th-sub">At the venue</span></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">NAOMS Member</th>
              <td class="is-early"><span class="cur">NPR</span>18,000</td>
              <td><span class="cur">NPR</span>20,000</td>
              <td><span class="cur">NPR</span>22,000</td>
              <td><span class="cur">NPR</span>24,000</td>
            </tr>
            <tr>
              <th scope="row">Non-NAOMS Member (Nepalese)</th>
              <td class="is-early"><span class="cur">NPR</span>20,000</td>
              <td><span class="cur">NPR</span>22,000</td>
              <td><span class="cur">NPR</span>24,000</td>
              <td><span class="cur">NPR</span>26,000</td>
            </tr>
            <tr>
              <th scope="row">International Delegate</th>
              <td class="is-early"><span class="cur">USD</span>200</td>
              <td><span class="cur">USD</span>240</td>
              <td><span class="cur">USD</span>260</td>
              <td><span class="cur">USD</span>280</td>
            </tr>
            <tr>
              <th scope="row">Residents and Dental Surgeons (Nepalese)</th>
              <td class="is-early"><span class="cur">NPR</span>15,000</td>
              <td><span class="cur">NPR</span>17,000</td>
              <td><span class="cur">NPR</span>19,000</td>
              <td><span class="cur">NPR</span>20,000</td>
            </tr>
            <tr>
              <th scope="row">Residents and Dental Surgeons (International)</th>
              <td class="is-early"><span class="cur">USD</span>150</td>
              <td><span class="cur">USD</span>170</td>
              <td><span class="cur">USD</span>190</td>
              <td><span class="cur">USD</span>200</td>
            </tr>
            <tr>
              <th scope="row">Accompanying Person</th>
              <td class="is-early"><span class="cur">NPR</span>15,000</td>
              <td><span class="cur">NPR</span>15,000</td>
              <td><span class="cur">NPR</span>15,000</td>
              <td><span class="cur">NPR</span>16,000</td>
            </tr>
            <tr>
              <th scope="row">Accompanying Person (International)</th>
              <td class="is-early"><span class="cur">USD</span>100</td>
              <td><span class="cur">USD</span>100</td>
              <td><span class="cur">USD</span>100</td>
              <td><span class="cur">USD</span>120</td>
            </tr>
          </tbody>
        </table>
      </div>

      <p class="table-note">
        <i class="fa-solid fa-circle-info"></i>
        Nepalese categories are charged in Nepalese Rupees (NPR); international categories in US Dollars (USD).
        All fees are per person.
      </p>
    </div>

    <!-- What your registration includes -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>What Your Registration Includes</h2>
        <span class="sec-line"></span>
      </div>
      <ul class="dot-list">
        <li>Access to all <strong>scientific sessions</strong>, keynote lectures, and panel discussions.</li>
        <li>Entry to the <strong>trade and industry exhibition</strong>.</li>
        <li><strong>Congress kit</strong> and delegate materials.</li>
        <li><strong>Certificate of participation</strong>.</li>
        <li>Refreshments and lunch on all congress days.</li>
        <li>Invitation to the <strong>welcome reception</strong> and cultural programme.</li>
      </ul>
      <p class="content-text mt-3">
        Accompanying person registration covers social and cultural events only, and does not include entry to the
        scientific sessions.
      </p>
    </div>

    <!-- Registration guidelines -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Registration Guidelines</h2>
        <span class="sec-line"></span>
      </div>
      <ul class="dot-list">
        <li>The applicable fee is determined by the <strong>date payment is received</strong>, not the date the form is submitted.</li>
        <li><strong>Residents and dental surgeons</strong> must upload proof of current training or registration status to qualify for the concessional rate.</li>
        <li><strong>NAOMS members</strong> must hold valid membership for the current year to claim the member rate.</li>
        <li>Presenting authors of accepted abstracts must be registered for their work to be included in the scientific programme.</li>
        <li>Spot registration is subject to <strong>availability of seats</strong> at the venue.</li>
      </ul>
    </div>

    <!-- CTA -->
    <div class="content-block">
      <div class="reg-cta">
        <h3>Ready to join us in Kathmandu?</h3>
        <p>
          Secure your place at the 7th ACOMS PG Trainee Congress 2027 at the Early Bird rate.
          For any queries regarding registration, our team is happy to help.
        </p>
        <a href="{{url('registration-form')}}" class="btn-reg-lg">
          <i class="fa-solid fa-pen-to-square me-1"></i>Register Now
        </a>
      </div>
    </div>

  </div>
</section>
@endsection