@extends('front.common.layout')

@section('content')
<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="page-banner">
  <img class="page-banner-img" src="images/kathmandu-radisson-hotel.jpg" alt="Radisson Hotel Kathmandu, Lazimpat">
  <div class="page-banner-overlay"></div>
  <div class="container">
    <div class="page-banner-body">
      <h1 class="page-banner-title">Venue Details</h1>
      <p class="page-banner-sub">Radisson Hotel Kathmandu, Lazimpat</p>
    </div>
  </div>
</section>


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="section-content">
  <div class="container">

    <!-- Intro -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Radisson Hotel Kathmandu</h2>
        <span class="sec-line"></span>
      </div>
      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <p class="content-text lead">
            The <strong>7th ACOMS Trainee Conference</strong> (Pre-Conference: <strong>26 February 2027</strong>;
            Conference: <strong>27–28 February 2027</strong>) will be held at the <strong>Radisson Hotel Kathmandu</strong>.
            This international-standard hotel is in Lazimpat, one of the city's most central and accessible neighbourhoods.
          </p>
        </div>
        <div class="col-lg-6">
          <figure class="venue-photo">
            <img src="images/kathmandu-radisson-hotel.jpg" alt="Radisson Hotel Kathmandu building and forecourt, Lazimpat" loading="lazy">
          </figure>
        </div>
      </div>
    </div>

    <!-- Venue at a glance -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Venue at a Glance</h2>
        <span class="sec-line"></span>
      </div>

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="contact-card">
            <span class="cc-role">Conference Venue</span>
            <h3 class="cc-name">Radisson Hotel Kathmandu</h3>
            <p class="cc-sub">Lazimpat, Kathmandu, Nepal</p>

            <ul class="contact-list">
              <li>
                <div class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-location-dot"></i></span>
                  <span>
                    <p class="ci-label">Address</p>
                    <p class="ci-value">P.O. Box 2269 Lazimpat, Kathmandu, Nepal</p>
                  </span>
                </div>
              </li>
              <li>
                <a href="tel:+97714511818" class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-phone"></i></span>
                  <span>
                    <p class="ci-label">Hotel Phone</p>
                    <p class="ci-value">+977 1 4511818</p>
                  </span>
                </a>
              </li>
              <li>
                <a href="mailto:reservation@radkat.com.np" class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-envelope"></i></span>
                  <span>
                    <p class="ci-label">Hotel Email</p>
                    <p class="ci-value">reservation@radkat.com.np</p>
                  </span>
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="contact-card">
            <span class="cc-role">Getting There</span>
            <h3 class="cc-name">Conference Logistics</h3>
            <p class="cc-sub">Pre-Conference 26 February · Conference 27–28 February 2027</p>

            <ul class="contact-list">
              <li>
                <div class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-plane-arrival"></i></span>
                  <span>
                    <p class="ci-label">Distance from Airport</p>
                    <p class="ci-value">About 4.7 km from Tribhuvan International Airport — usually around a 25-minute drive, depending on traffic</p>
                  </span>
                </div>
              </li>
              <li>
                <div class="contact-item">
                  <span class="ci-icon"><i class="fa-solid fa-door-open"></i></span>
                  <span>
                    <p class="ci-label">Conference Hall</p>
                    {{-- Update once the hall is confirmed (e.g. Nepa-Dhuku Hall). --}}
                    <p class="ci-value">To be confirmed</p>
                  </span>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- About the venue -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>About the Venue</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text">
        Radisson Hotel Kathmandu is a city-centre hotel suited to both business and leisure travellers, with easy access
        to Kathmandu's commercial hub and landmarks such as <strong>Thamel</strong>, the
        <strong>Narayanhiti Palace Museum</strong>, and <strong>Kathmandu Durbar Square</strong>. It sits near several
        embassies and consulates in the quiet Lazimpat area, and its event spaces include the rooftop
        <strong>Terrace Garden</strong> and <strong>Nepa-Dhuku Hall</strong>, along with seven flexible meeting rooms,
        free Wi-Fi, and on-site catering.
      </p>
    </div>

    <!-- Facilities -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Facilities for Delegates</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text">
        Rooms are air-conditioned and come with a flat-screen TV, free Wi-Fi, tea and coffee facilities, a minibar, and a
        work desk. The hotel also has:
      </p>
      <ul class="venue-tags">
        <li><i class="fa-solid fa-spa"></i> Spa</li>
        <li><i class="fa-solid fa-dumbbell"></i> Fitness centre</li>
        <li><i class="fa-solid fa-person-swimming"></i> Swimming pool</li>
        <li><i class="fa-solid fa-briefcase"></i> Business centre</li>
        <li><i class="fa-solid fa-bell-concierge"></i> Concierge</li>
        <li><i class="fa-solid fa-map-location-dot"></i> Travel &amp; tour desk</li>
        <li><i class="fa-solid fa-shirt"></i> Laundry services</li>
        <li><i class="fa-solid fa-suitcase-rolling"></i> Baggage storage</li>
      </ul>
      <p class="content-text mt-3">
        Baggage storage is especially useful for delegates arriving early or leaving after check-out.
      </p>
    </div>

    <!-- Dining -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Dining at the Venue</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text">
        <strong>The Fun Café</strong> and <strong>The Coffee Shop</strong> serve meals from breakfast through dinner, and
        the rooftop <strong>Terrace Garden</strong> offers a quieter dining option. There is also a
        <strong>Pastry Shop</strong>, a <strong>Lobby Bar</strong>, and <strong>24-hour room service</strong>.
        Breakfast options cater to gluten-free, halal, and vegetarian diets.
      </p>
    </div>

    <!-- Neighbourhood -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>The Neighbourhood</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text">
        Lazimpat is a calm, upscale diplomatic area with cafés, restaurants, and shops within walking distance.
        From the venue:
      </p>

      <ul class="venue-distances">
        <li>
          <span class="vd-name">Narayanhiti Palace Museum<span class="vd-note">The former royal residence</span></span>
          <span class="vd-km">~0.9 km</span>
        </li>
        <li>
          <span class="vd-name">Thamel<span class="vd-note">Kathmandu's main visitor district with restaurants, shops, and cafés</span></span>
          <span class="vd-km">~1.3 km</span>
        </li>
        <li>
          <span class="vd-name">Pashupatinath Temple</span>
          <span class="vd-km">~3 km</span>
        </li>
        <li>
          <span class="vd-name">Swayambhunath<span class="vd-note">The "Monkey Temple", a UNESCO World Heritage Site</span></span>
          <span class="vd-km">~3.5 km</span>
        </li>
        <li>
          <span class="vd-name">Boudhanath Stupa<span class="vd-note">A UNESCO World Heritage Site</span></span>
          <span class="vd-km">~4 km</span>
        </li>
      </ul>
    </div>

    <!-- Map -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Location Map</h2>
        <span class="sec-line"></span>
      </div>
      <div class="venue-map">
        <iframe
          src="https://maps.google.com/maps?q=Radisson%20Hotel%20Kathmandu%2C%20Lazimpat&z=15&output=embed"
          title="Map: Radisson Hotel Kathmandu, Lazimpat"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen></iframe>
      </div>
      <p class="content-text mt-2">
        <a href="https://www.google.com/maps/search/?api=1&query=Radisson%20Hotel%20Kathmandu%2C%20Lazimpat" target="_blank" rel="noopener" class="venue-map-link">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Open in Google Maps
        </a>
      </p>
    </div>

  </div>
</section>
@endsection
