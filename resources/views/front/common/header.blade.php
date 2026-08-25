
<!-- ============================================================
     NAVBAR
============================================================ -->
<nav class="navbar navbar-expand-lg">
  <div class="container">

    <!-- LOGO -->
    <a class="navbar-brand" href="/">
      <img src="{{ asset('images/logo/7th-acoms-mnemonic.svg') }}" alt="7th ACOMS Trainee Conference">
    </a>

    <!-- Offcanvas toggle (mobile) -->
    <button class="navbar-toggler border-0 shadow-none" type="button"
      data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Desktop nav (lg+) -->
    <div class="justify-content-end d-none d-lg-flex" id="mainNav">
      <ul class="navbar-nav align-items-lg-center">
        {!!\App\Models\Menu::getMenu(1)!!}
        <li class="nav-item">
          <a class="nav-link nav-btn-reg" href="{{ route('registration.details') }}">
            <i class="fa-solid fa-pen-to-square me-1"></i>Registration
          </a>
        </li>

      </ul>
    </div>

  </div>
</nav>

<!-- ============================================================
     OFFCANVAS MOBILE MENU
============================================================ -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
  <div class="offcanvas-header border-bottom">
    <a href="#" class="navbar-brand">
      <img src="{{ asset('images/logo/7th-acoms-mnemonic.svg') }}" alt="7th ACOMS" style="height:50px;width:auto;">
    </a>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0">
    <ul class="navbar-nav flex-column">

      {!!\App\Models\Menu::getMenu(1)!!}

      <li class="nav-item p-3">
        <a href="{{ route('registration.details') }}" class="btn w-100 text-white fw-600"
          style="background:var(--red);border-radius:4px;font-size:.88rem;padding:.6rem 1rem;">
          <i class="fa-solid fa-pen-to-square me-1"></i>Registration
        </a>
      </li>

    </ul>
  </div>
</div>

{{-- Mobile menu: replace the flickery Bootstrap dropdown with a smooth, stable
     Collapse (same mechanism the original static menu used). --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  var oc = document.getElementById('mobileMenu');
  if (!oc) return;
  oc.querySelectorAll('.nav-item.dropdown > .dropdown-toggle').forEach(function (toggle) {
    var li = toggle.closest('.nav-item.dropdown');
    var menu = li.querySelector(':scope > .dropdown-menu');
    if (!menu) return;
    toggle.removeAttribute('data-bs-toggle');            // stop dropdown auto-open/close
    menu.classList.add('collapse');                      // turn submenu into a collapse target
    var collapse = new bootstrap.Collapse(menu, { toggle: false });
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      collapse.toggle();
      li.classList.toggle('open');                       // drives the caret rotation
    });
  });
});
</script>

