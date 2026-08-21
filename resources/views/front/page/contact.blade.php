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
      <h1 class="page-banner-title">Contact Us</h1>
      <p class="page-banner-sub">7th Asian Congress of Oral and Maxillofacial Surgery — PG Trainee Congress 2027, Kathmandu</p>
    </div>
  </div>
</section>


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="section-content">
  <div class="container">

    <!-- Get in touch -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Get in Touch</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text lead">
        For any queries regarding <strong>registration</strong> for the 7th ACOMS PG Trainee Congress 2027,
        please reach out to the Registration Committee using the contact details below.
      </p>

      <div class="row g-4 mt-1">
        <div class="col-lg-6">
          <div class="contact-card">
            <span class="cc-role">Registration Committee</span>
            <h3 class="cc-name">Dr. Vivek Singh</h3>
            <p class="cc-sub">Chair, Registration Committee</p>

            <ul class="contact-list">
              <li>
                <a href="tel:+9779851078686" class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-phone"></i></span>
                  <span>
                    <p class="ci-label">Phone</p>
                    <p class="ci-value">9851078686</p>
                  </span>
                </a>
              </li>
              <li>
                <div class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-envelope"></i></span>
                  <span>
                    <p class="ci-label">Email</p>
                    <p class="ci-value">
                      <a href="mailto:vivekomfs@gmail.com" class="multi" style="text-decoration:none;color:inherit;">vivekomfs@gmail.com</a>,
                      <a href="mailto:registration@7acomstrainee.com" class="multi" style="text-decoration:none;color:inherit;">registration@7acomstrainee.com</a>
                    </p>
                  </span>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection

