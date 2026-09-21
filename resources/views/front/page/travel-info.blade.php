@extends('front.common.layout')

@section('content')
<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="page-banner">
  <img class="page-banner-img" src="images/banner-1.jpg" alt="7th ACOMS Trainee Conference, Kathmandu">
  <div class="page-banner-overlay"></div>
  <div class="container">
    <div class="page-banner-body">
      <h1 class="page-banner-title">Travel Info</h1>
      <p class="page-banner-sub">Getting to Kathmandu for the 7th ACOMS Trainee Conference 2027</p>
    </div>
  </div>
</section>


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="section-content">
  <div class="container">

    <!-- By air -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Getting to Kathmandu</h2>
        <span class="sec-line"></span>
      </div>
      <h3 class="travel-sub"><i class="fa-solid fa-plane-arrival"></i> By Air</h3>
      <p class="content-text">
        International delegates arrive at <strong>Tribhuvan International Airport (KTM)</strong>, Kathmandu. It is
        Nepal's only international airport for air arrivals. Kathmandu has direct connections to major hubs across Asia
        and the Middle East, including:
      </p>
      <ul class="venue-tags">
        <li>Delhi</li>
        <li>Doha</li>
        <li>Dubai</li>
        <li>Bangkok</li>
        <li>Kuala Lumpur</li>
        <li>Singapore</li>
        <li>Several cities in China</li>
      </ul>
    </div>

    <!-- Visa -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Visa Information</h2>
        <span class="sec-line"></span>
      </div>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-id-card"></i></span>
            <h3>Indian citizens</h3>
            <p>No visa is required. Carry a valid passport or Voter ID.</p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-earth-asia"></i></span>
            <h3>SAARC citizens</h3>
            <p>Citizens of SAARC countries (except Afghanistan) receive a free visa for up to 30 days on their first visit in a given visa year.</p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-passport"></i></span>
            <h3>All other nationalities</h3>
            <p>Most delegates can get a tourist visa on arrival. A small number of nationalities must get their visa in advance from a Nepali embassy instead, so please check your eligibility before travelling.</p>
          </div>
        </div>
      </div>

      <h3 class="travel-sub mt-5"><i class="fa-solid fa-money-bill-wave"></i> Tourist Visa Fees</h3>
      <div class="fee-table-wrap">
        <table class="fee-table travel-table">
          <thead>
            <tr>
              <th scope="col" class="col-cat">Duration</th>
              <th scope="col">Official Fee</th>
              <th scope="col">Entry</th>
            </tr>
          </thead>
          <tbody>
            <tr><th scope="row">15 days</th><td>USD 30</td><td>Multiple</td></tr>
            <tr><th scope="row">30 days</th><td>USD 50</td><td>Multiple</td></tr>
            <tr><th scope="row">90 days</th><td>USD 125</td><td>Multiple</td></tr>
          </tbody>
        </table>
      </div>
      <p class="content-text mt-2">A 15-day visa covers the conference comfortably.</p>

      <div class="stay-note mt-4">
        <i class="fa-solid fa-stopwatch"></i>
        <p>
          <strong>Save time at the airport:</strong> you can fill in the visa form online on the Department of
          Immigration website in the days before you travel. Save the barcode receipt on your phone or print it, and on
          arrival go straight to the bank counter to pay the visa fee.
        </p>
      </div>

      <h3 class="travel-sub mt-5"><i class="fa-solid fa-suitcase"></i> What to Carry</h3>
      <ul class="dot-list">
        <li>A passport valid for at least <strong>six more months</strong></li>
        <li>A recent passport-size photo</li>
        <li>The visa fee <strong>in cash</strong>. Nepalese and Indian rupees are not accepted for visa fees, and card payment at the counter is not always available.</li>
        <li>Your hotel booking and conference registration confirmation</li>
      </ul>

      <div class="travel-callout mt-4">
        <p>
          Visa rules can change. Please confirm current requirements on the official Nepal Department of Immigration
          website (<a href="https://www.immigration.gov.np" target="_blank" rel="noopener">immigration.gov.np</a>)
          before you travel.
        </p>
        <p>
          If you need an <strong>invitation letter</strong> for your visa application, email
          <a href="mailto:registration@7acomstrainee.com">registration@7acomstrainee.com</a>.
        </p>
      </div>
    </div>

    <!-- Airport to venue -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>From the Airport to the Venue</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text">
        The Radisson Hotel is about <strong>4.7 km</strong> from the airport. The drive usually takes
        <strong>20–30 minutes</strong>, and longer during morning and evening rush hours.
      </p>

      <div class="row g-4 mt-1">
        <div class="col-lg-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-taxi"></i></span>
            <h3>Pre-paid airport taxi</h3>
            <span class="travel-pill">Recommended for first-time visitors</span>
            <p>
              Pre-paid taxi counters are just past the baggage claim area. These government-regulated taxis charge a
              fixed fare and give you a receipt. Fares to central Kathmandu are typically around
              <strong>NPR 800–1,000</strong>. Expect a small extra charge of about NPR 100 after 9:00 pm.
            </p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-van-shuttle"></i></span>
            <h3>Hotel pickup</h3>
            <p>
              Most hotels in Lazimpat can arrange airport pickup if you request it when booking. This is the most
              convenient option after a long flight.
            </p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-mobile-screen"></i></span>
            <h3>Ride-hailing apps</h3>
            <p>
              Uber is not available in Nepal, but local apps such as <strong>Pathao</strong> and
              <strong>InDrive</strong> are widely used and reliable. You need a Nepali SIM card to book, and you can
              buy one at the airport. App taxis are not allowed inside the airport area, so you will need to walk out
              to the Ring Road for pickup.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Money -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Money &amp; Payments</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text">
        The local currency is the <strong>Nepalese Rupee (NPR)</strong>. ATMs and money exchange counters are available
        at the airport and throughout Lazimpat and Thamel. Major hotels accept international cards, but smaller shops
        and taxis usually need cash.
      </p>
      <ul class="dot-list">
        <li>
          Visitors can bring up to <strong>USD 5,000</strong> (or the equivalent in other currencies) into Nepal without
          a customs declaration. Larger amounts must be declared on arrival.
        </li>
        <li>
          <strong>For Indian delegates:</strong> Indian notes of ₹200 and ₹500 issued on or after 9 November 2016 can
          now be carried, up to ₹25,000 per person. Indian ₹2,000 notes remain banned.
        </li>
      </ul>
    </div>

    <!-- Weather -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Weather &amp; What to Pack</h2>
        <span class="sec-line"></span>
      </div>

      <div class="row g-4 align-items-stretch">
        <div class="col-lg-5">
          <div class="weather-card">
            <div class="wc-item">
              <i class="fa-solid fa-sun"></i>
              <span class="wc-val">~19°C</span>
              <span class="wc-label">Daytime</span>
            </div>
            <div class="wc-item">
              <i class="fa-solid fa-moon"></i>
              <span class="wc-val">~3°C</span>
              <span class="wc-label">Night</span>
            </div>
            <div class="wc-item">
              <i class="fa-solid fa-cloud-sun"></i>
              <span class="wc-val">Dry</span>
              <span class="wc-label">Rain is rare</span>
            </div>
          </div>
        </div>
        <div class="col-lg-7">
          <p class="content-text">
            Late February is a pleasant, dry time to be in Kathmandu. Days are sunny and mild, but nights are cold.
          </p>
          <p class="content-text mb-0">
            <strong>Pack:</strong> layers, a warm jacket for evenings, comfortable walking shoes, and smart attire for
            sessions and the conference dinner. Morning fog can occasionally delay early flights, so allow buffer time
            for onward connections.
          </p>
        </div>
      </div>
    </div>

    <!-- Good to know -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Good to Know</h2>
        <span class="sec-line"></span>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="contact-item info-row">
            <span class="ci-icon"><i class="fa-solid fa-clock"></i></span>
            <span>
              <p class="ci-label">Time Zone</p>
              <p class="ci-value">Nepal Standard Time is GMT +5:45</p>
            </span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="contact-item info-row">
            <span class="ci-icon"><i class="fa-solid fa-plug"></i></span>
            <span>
              <p class="ci-label">Electricity</p>
              <p class="ci-value">230V, 50Hz. Sockets are mostly Type C, D, and M, so bring a universal adapter.</p>
            </span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="contact-item info-row">
            <span class="ci-icon"><i class="fa-solid fa-sim-card"></i></span>
            <span>
              <p class="ci-label">Mobile &amp; Internet</p>
              <p class="ci-value">Tourist SIM cards from Ncell and Nepal Telecom are available at the airport with your passport. Hotels offer free Wi-Fi.</p>
            </span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="contact-item info-row">
            <span class="ci-icon"><i class="fa-solid fa-person-praying"></i></span>
            <span>
              <p class="ci-label">Etiquette</p>
              <p class="ci-value">Dress modestly at temples. Remove shoes where requested, and walk clockwise around stupas.</p>
            </span>
          </div>
        </div>
        <div class="col-md-6">
          <a href="tel:1144" class="contact-item info-row">
            <span class="ci-icon"><i class="fa-solid fa-shield-halved"></i></span>
            <span>
              <p class="ci-label">Tourist Police</p>
              <p class="ci-value">Dial 1144</p>
            </span>
          </a>
        </div>
      </div>
    </div>

    <!-- Explore -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Explore Kathmandu</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text">
        Plan an extra day or two to explore. <strong>Seven UNESCO World Heritage monument zones</strong> are within easy
        reach of the venue, including:
      </p>
      <ul class="venue-tags">
        <li><i class="fa-solid fa-landmark"></i> Swayambhunath</li>
        <li><i class="fa-solid fa-landmark"></i> Boudhanath</li>
        <li><i class="fa-solid fa-landmark"></i> Pashupatinath</li>
        <li><i class="fa-solid fa-landmark"></i> Kathmandu Durbar Square</li>
        <li><i class="fa-solid fa-landmark"></i> Patan Durbar Square</li>
        <li><i class="fa-solid fa-landmark"></i> Bhaktapur Durbar Square</li>
      </ul>
      <p class="content-text mt-3">
        Hotel tour desks can arrange guided sightseeing and short trips to Nagarkot or Pokhara.
      </p>
      {{-- Optional: add a link here to any NAOMS-organised tours. --}}
    </div>

    <!-- Help -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Need Help?</h2>
        <span class="sec-line"></span>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="contact-card">
            <span class="cc-role">Conference Secretariat</span>
            <h3 class="cc-name">NAOMS</h3>
            <p class="cc-sub">Nepalese Association of Oral and Maxillofacial Surgeons</p>

            <ul class="contact-list">
              <li>
                <a href="tel:+9779851078686" class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-phone"></i></span>
                  <span>
                    <p class="ci-label">Phone</p>
                    <p class="ci-value">+977 9851078686</p>
                  </span>
                </a>
              </li>
              <li>
                <div class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-envelope"></i></span>
                  <span>
                    <p class="ci-label">Email</p>
                    <p class="ci-value">
                      <a href="mailto:registration@7acomstrainee.com" style="text-decoration:none;color:inherit;">registration@7acomstrainee.com</a><br>
                      <a href="mailto:info@naoms.org.np" style="text-decoration:none;color:inherit;">info@naoms.org.np</a>
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
