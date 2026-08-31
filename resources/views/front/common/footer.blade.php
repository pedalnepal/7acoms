

<!-- ============================================================
     FOOTER
============================================================ -->
<footer class="site-footer">
  <div class="container">
    <div class="row g-4">

      <!-- Find Us -->
      <div class="col-lg-4 col-md-6">
        <h6>Find us</h6>
        <p class="footer-org">Nepalese Association of Oral and<br>Maxillofacial Surgeons</p>
        @if(config('setting.location'))
        <div class="footer-contact-item">
          <i class="fa-solid fa-location-dot"></i>
          <span>{{ config('setting.location') }}</span>
        </div>
        @endif
        @if(config('setting.contact'))
        <a href="tel:{{ config('setting.contact') }}" class="footer-contact-item">
          <i class="fa-solid fa-phone"></i>
          <span>{{ config('setting.contact') }}</span>
        </a>
        @endif
        @if(config('setting.mobile'))
        <a href="tel:{{ config('setting.mobile') }}" class="footer-contact-item">
          <i class="fa-solid fa-phone"></i>
          <span>{{ config('setting.mobile') }}</span>
        </a>
        @endif
        @if(config('setting.sphone'))
        <a href="tel:{{ config('setting.sphone') }}" class="footer-contact-item">
          <i class="fa-solid fa-phone"></i>
          <span>{{ config('setting.sphone') }}</span>
        </a>
        @endif
        @if(config('setting.pemail'))
        <a href="mailto:{{ config('setting.pemail') }}" class="footer-contact-item">
          <i class="fa-solid fa-envelope"></i>
          <span>{{ config('setting.pemail') }}</span>
        </a>
        @endif
        @if(config('setting.semail'))
        <a href="mailto:{{ config('setting.semail') }}" class="footer-contact-item">
          <i class="fa-solid fa-envelope"></i>
          <span>{{ config('setting.semail') }}</span>
        </a>
        @endif
        <div class="footer-social">
          @if(config('setting.facebook'))
          <a href="{{ config('setting.facebook') }}" target="_blank" rel="noopener noreferrer" title="Facebook"><i class="fab fa-facebook-f"></i></a>
          @endif
          @if(config('setting.instagram'))
          <a href="{{ config('setting.instagram') }}" target="_blank" rel="noopener noreferrer" title="Instagram"><i class="fab fa-instagram"></i></a>
          @endif
          @if(config('setting.tiktok'))
          <a href="{{ config('setting.tiktok') }}" target="_blank" rel="noopener noreferrer" title="TikTok"><i class="fab fa-tiktok"></i></a>
          @endif
          @if(config('setting.youtube'))
          <a href="{{ config('setting.youtube') }}" target="_blank" rel="noopener noreferrer" title="YouTube"><i class="fab fa-youtube"></i></a>
          @endif
          @if(config('setting.linkedin'))
          <a href="{{ config('setting.linkedin') }}" target="_blank" rel="noopener noreferrer" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          @endif
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-4 col-md-6">
        <h6>Quick links</h6>
        <ul class="footer-links">
          {!!\App\Models\Menu::getMenu(2)!!}
        </ul>
      </div>

      <!-- Be a Part Of NAOMS -->
      <div class="col-lg-4 col-md-6">
        <h6>Be a Part Of NAOMS</h6>
        <ul class="footer-links">
          {!!\App\Models\Menu::getMenu(3)!!}
        </ul>
      </div>

    </div>
  </div>

  <div class="footer-bottom">
    Copyright &copy; {{ date('Y') }} NAOMS. All rights reserved. Powered by
    <a href="https://www.pedaladvertising.com/" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/pedal-logo-light.svg') }}" alt="Pedal Advertising"></a>
  </div>
</footer>
