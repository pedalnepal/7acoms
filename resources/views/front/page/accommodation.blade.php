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
      <h1 class="page-banner-title">Accommodation</h1>
      <p class="page-banner-sub">Where to stay in Lazimpat, Kathmandu</p>
    </div>
  </div>
</section>


@php
  $hotels = [
      [
          'name'    => 'Radisson Hotel Kathmandu',
          'badge'   => 'Conference Venue',
          'bestFor' => 'Delegates who want to stay where the sessions are held.',
          'about'   => 'Room types include Superior, Premium, and Business Class rooms, Junior, Deluxe, and Premium Suites, and serviced Apartments. Check-in is at 3:00 pm and check-out at 12:00 noon. The hotel lists an airport shuttle among its services, which you can request when booking.',
          'address' => 'P.O. Box 2269 Lazimpat, Kathmandu',
          'phones'  => ['+977 1 4511818'],
          'email'   => 'reservation@radkat.com.np',
          'web'     => 'https://www.radissonhotels.com',
          'webText' => 'radissonhotels.com',
      ],
      [
          'name'    => 'Dusit Princess Kathmandu',
          'badge'   => null,
          'bestFor' => 'A newer upscale hotel within walking distance of the venue.',
          'about'   => 'It is in upmarket Lazimpat, with international restaurants and the Narayanhiti Palace Museum close by, and blends Thai and Nepalese hospitality. Its facilities include a spa, a fitness centre, and what it describes as the city\'s highest infinity pool. Soi Restaurant serves authentic Thai cuisine, and the hotel also runs momo-making sessions with its chefs, a fun cultural add-on.',
          'address' => 'Manaslu Road, Lazimpat, Kathmandu 44600',
          'phones'  => ['+977 1 5970265'],
          'email'   => null,
          'web'     => 'https://www.dusit.com/dusitprincess-kathmandu',
          'webText' => 'dusit.com/dusitprincess-kathmandu',
      ],
      [
          'name'    => 'Hotel Tibet',
          'badge'   => null,
          'bestFor' => 'Boutique character and warm, personal service.',
          'about'   => 'Hotel Tibet has welcomed guests since 1998. It was renovated after COVID to combine modern comforts with a traditional Tibetan aesthetic. It has a rooftop terrace, a café, laundry services, Shambala Spa, and two restaurants with garden views. The hotel is family-run, and its Yeti Terrace Bar is known for sunset views over the city.',
          'address' => 'Kumari Marg, Lazimpat, Kathmandu',
          'phones'  => ['+977-1-4529085'],
          'email'   => 'sales@hotel-tibet.com',
          'web'     => 'https://www.hotel-tibet.com',
          'webText' => 'hotel-tibet.com',
      ],
      [
          'name'    => 'Hotel Le Himalaya',
          'badge'   => null,
          'bestFor' => 'Comfortable, value-conscious stays, including longer trips.',
          'about'   => 'The hotel is on Lazimpat\'s main street, lined with embassies, corporate offices, restaurants, and shops, and is about 6 km from the international airport. It offers Deluxe and Suite rooms, an all-day restaurant with local and international food, and a rooftop with 360-degree views of Kathmandu. Its top-floor restaurant has a strong reputation with guests for both food and views.',
          'address' => 'Lazimpat Road, Kathmandu',
          'phones'  => ['+977-1-4520494', '01-4533400', '+977 9745941857'],
          'email'   => null,
          'web'     => 'https://www.hotellehimalaya.com',
          'webText' => 'hotellehimalaya.com',
      ],
  ];
@endphp


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="section-content">
  <div class="container">

    <!-- Where to stay -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Where to Stay</h2>
        <span class="sec-line"></span>
      </div>
      <p class="content-text lead">
        Delegates can stay at the conference venue itself or at one of several good hotels a short walk away in
        Lazimpat. All the options below are in the same neighbourhood, so you can walk to sessions without depending
        on traffic.
      </p>
      <div class="stay-note mt-3">
        <i class="fa-solid fa-calendar-check"></i>
        <p>Late February is a popular time to visit Kathmandu, so we recommend <strong>booking as early as possible</strong>.</p>
      </div>
    </div>

    <!-- Hotels -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Hotels</h2>
        <span class="sec-line"></span>
      </div>

      <div class="hotel-list">
        @foreach($hotels as $i => $hotel)
          <article class="hotel-card {{ $hotel['badge'] ? 'is-venue' : '' }}">
            <div class="hotel-head">
              <span class="hotel-num">{{ $i + 1 }}</span>
              <div>
                <h3 class="hotel-name">{{ $hotel['name'] }}</h3>
                @if($hotel['badge'])
                  <span class="hotel-badge">{{ $hotel['badge'] }}</span>
                @endif
              </div>
            </div>

            <p class="hotel-best"><strong>Best for:</strong> {{ $hotel['bestFor'] }}</p>
            <p class="content-text">{{ $hotel['about'] }}</p>

            <ul class="hotel-contact">
              <li>
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $hotel['address'] }}</span>
              </li>
              <li>
                <i class="fa-solid fa-phone"></i>
                <span>
                  @foreach($hotel['phones'] as $phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a>@if(!$loop->last) <span class="sep">/</span> @endif
                  @endforeach
                </span>
              </li>
              @if($hotel['email'])
                <li>
                  <i class="fa-solid fa-envelope"></i>
                  <a href="mailto:{{ $hotel['email'] }}">{{ $hotel['email'] }}</a>
                </li>
              @endif
              <li>
                <i class="fa-solid fa-globe"></i>
                <a href="{{ $hotel['web'] }}" target="_blank" rel="noopener">{{ $hotel['webText'] }}</a>
              </li>
            </ul>
          </article>
        @endforeach
      </div>
    </div>

    <!-- Booking tips -->
    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Booking Tips</h2>
        <span class="sec-line"></span>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-file-circle-check"></i></span>
            <h3>Keep your confirmation</h3>
            <p>Book directly with the hotel or through a trusted booking platform, and keep your confirmation handy. Immigration may ask for proof of accommodation on arrival.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-temperature-arrow-up"></i></span>
            <h3>Check for heating</h3>
            <p>Kathmandu nights are cold in late February, so check that your room has heating.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="tip-card">
            <span class="ci-icon"><i class="fa-solid fa-van-shuttle"></i></span>
            <h3>Arrange airport pickup</h3>
            <p>Ask your hotel about airport pickup when you book. It is the most stress-free way to arrive.</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection
