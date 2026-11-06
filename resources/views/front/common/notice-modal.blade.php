<style>
    #noticeModal .modal-dialog {
        max-width: 700px;
        margin: 1rem auto;
        padding: 0 12px;
    }

    #noticeModal .modal-content {
        position: relative;
        overflow: hidden;
    }

    #noticeModal .btn-close {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 2;
        background-color: #fff;
        padding: 10px;
        border-radius: 50%;
        opacity: 1;
    }

    #noticeModal .modal-body {
        padding: 0;
        text-align: center;
    }

    #noticeModal .modal-body img {
        display: block;
        width: 100%;
        height: auto;
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
        object-fit: contain;
    }
</style>

<!-- Modal -->
<div class="modal fade"
     id="noticeModal"
     tabindex="-1"
     aria-label="Final call notice"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>

            <div class="modal-body">
                <img src="{{ asset('images/7th-acom-notice.jpeg') }}"
                     alt="Final call notice">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('noticeModal');
    if (el && window.bootstrap) {
      new bootstrap.Modal(el).show();
    }
  });
</script>
@endpush
