@extends('admin.common.layout')
@section('content')
@include('admin.common.flash')
<div class="card admin-panel-card">
  <div class="card-header admin-panel-card-header py-3">
      <h1 class="admin-page-title mb-0">{{ isset($slider) ? 'Edit Slider' : 'Add Slider' }}</h1>
  </div>
  <div class="card-body">

    @if(isset($slider))
      <form method="post" action="{{ route('slider.update', $slider->id) }}">
      <input name="_method" type="hidden" value="PUT">
    @else
      <form method="post" action="{{ route('slider.store') }}">
    @endif
    {{ csrf_field() }}

      <div class="row g-4">

        {{-- Left column: title + caption --}}
        <div class="col-md-6">

          <div class="mb-3">
            <label class="form-label fw-semibold">Title</label>
            <textarea id="title" name="title" class="WYSWIYG large form-control">{!! @$slider->title !!}</textarea>
          </div>

          <div class="mb-3">
            <label for="caption" class="form-label fw-semibold">Caption</label>
            <textarea id="caption" name="caption" class="form-control" rows="3">{{ @$slider->caption }}</textarea>
          </div>

          <div class="mb-3">
            <label for="link_title" class="form-label fw-semibold">Button label</label>
            <input id="link_title" name="link_title" class="form-control" value="{{ @$slider->link_title }}">
          </div>

          <div class="mb-3">
            <label for="links" class="form-label fw-semibold">Button URL</label>
            <input id="links" name="links" class="form-control" value="{{ @$slider->links }}">
          </div>

          {{-- ── Status ── --}}
          <div class="mb-3">
            <label class="form-label fw-semibold d-block">Status</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="statusActive" value="1"
                  {{ old('status', isset($slider) ? ($slider->status ? '1' : '0') : '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label text-success fw-semibold" for="statusActive">
                  <i class="fa-solid fa-circle-check me-1"></i> Active
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="statusInactive" value="0"
                  {{ old('status', isset($slider) ? ($slider->status ? '1' : '0') : '1') == '0' ? 'checked' : '' }}>
                <label class="form-check-label text-secondary fw-semibold" for="statusInactive">
                  <i class="fa-solid fa-circle-xmark me-1"></i> Inactive
                </label>
              </div>
            </div>
            <small class="text-muted">Inactive sliders are hidden from the homepage.</small>
          </div>

          {{-- ── Theme ── --}}
          <div class="mb-3">
            <label class="form-label fw-semibold d-block">Text theme</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="theme" id="themeLight" value="light"
                  {{ old('theme', @$slider->theme ?? 'light') === 'light' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="themeLight">
                  <i class="fa-solid fa-sun me-1 text-warning"></i> Light bg
                  <small class="text-muted d-block fw-normal">Dark text — use when bg image is light/pale</small>
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="theme" id="themeDark" value="dark"
                  {{ old('theme', @$slider->theme ?? 'light') === 'dark' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="themeDark">
                  <i class="fa-solid fa-moon me-1 text-primary"></i> Dark bg
                  <small class="text-muted d-block fw-normal">White text — use when bg image is dark</small>
                </label>
              </div>
            </div>
          </div>

        </div>

        {{-- Right column: image --}}
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-semibold">Feature Image <small class="text-muted">(1920 × 1080 recommended)</small></label>
            <div class="thumbnail-container clearfix">
              <button type="button" class="btn btn-xs btn-danger clear-media">&times;</button>
              <a class="media-open waves-effect waves-light btn btn-sm btn-primary"
                 href="javascript:void(0)"
                 data-for="thumbnail"
                 data-type="input"
                 data-modal-caption="Featured Image">Choose Image</a>
              <input id="thumbnail" name="image" value="{{ @$slider->image }}" type="hidden">
              <div id="thumbnail_preview" class="media-image-content mt-2">
                @if(@$slider->media)
                  {!! @$slider->media->get_attachment() !!}
                @endif
              </div>
            </div>
          </div>
        </div>

      </div>{{-- /row --}}

      <div class="box-footer clearfix mt-4">
        <button onclick="needToConfirm = false" type="submit" class="btn btn-success">
          <i class="fa-solid fa-floppy-disk me-1"></i> Save
        </button>
        <a href="{{ route('slider.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
      </div>

    </form>
  </div>
</div>
@stop
