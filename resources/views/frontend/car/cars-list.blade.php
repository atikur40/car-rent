@php
  $version = $basicInfo->theme_version;
@endphp
@extends("frontend.layouts.layout-v$version")
@section('pageHeading')
  {{ __('Cars') }}
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_cars }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_cars }}
  @endif
@endsection
@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => !empty($pageHeading) ? $pageHeading->car_page_title : __('Cars'),
  ])

  <!-- Listing-list-area start -->
  <div class="listing-list-area pt-100 pb-60">
    <div class="container">
      <div class="row gx-xl-5">
        @include('frontend.car.side-bar')
        <div class="col-lg-8 col-xl-9">
          <div class="product-sort-area" data-aos="fade-up">
            <div class="row align-items-center">
              <div class="col-lg-6">
                <h4 class="mb-20">{{ $total_cars }} {{ $total_cars > 1 ? __('Cars') : 'Car' }}
                  {{ __('Found') }}</h4>
              </div>
              <div class="col-4 d-lg-none">
                <button class="btn btn-sm btn-outline icon-end radius-sm mb-20" type="button" data-bs-toggle="offcanvas"
                  data-bs-target="#widgetOffcanvas" aria-controls="widgetOffcanvas">
                  Filter <i class="fal fa-filter"></i>
                </button>
              </div>
              <div class="col-8 col-lg-6">
                <ul class="product-sort-list list-unstyled mb-20">
                  <li class="item me-4">
                    <div class="sort-item d-flex align-items-center">
                      <label class="me-2 font-sm">{{ __('Sort By') }}:</label>
                      <form action="{{ route('frontend.cars') }}" method="get" id="SortForm">
                        @if (!empty(request()->input('category')))
                          <input type="hidden" name="category" value="{{ request()->input('category') }}">
                        @endif
                        @if (!empty(request()->input('title')))
                          <input type="hidden" name="title" value="{{ request()->input('title') }}">
                        @endif
                        @if (!empty(request()->input('location')))
                          <input type="hidden" name="location" value="{{ request()->input('location') }}">
                        @endif
                        @if (!empty(request()->input('brands')))
                          @if (is_array(request()->input('brands')))
                            @foreach (request()->input('brands') as $brand)
                              <input type="hidden" name="brands[]" value="{{ $brand }}">
                            @endforeach
                          @else
                            <input type="hidden" name="brands[]" value="{{ request()->input('brands') }}">
                          @endif

                        @endif
                        @if (request()->filled('models'))
                          @foreach ($models as $model)
                            <input type="hidden" name="models[]" value="{{ $model }}">
                          @endforeach
                        @endif
                        @if (!empty(request()->input('fuel_type')))
                          <input type="hidden" name="fuel_type" value="{{ request()->input('fuel_type') }}">
                        @endif
                        @if (!empty(request()->input('transmission')))
                          <input type="hidden" name="transmission" value="{{ request()->input('transmission') }}">
                        @endif
                        @if (!empty(request()->input('condition')))
                          <input type="hidden" name="condition" value="{{ request()->input('condition') }}">
                        @endif
                        @if (!empty(request()->input('min')))
                          <input type="hidden" name="min" value="{{ request()->input('min') }}">
                        @endif
                        @if (!empty(request()->input('max')))
                          <input type="hidden" name="max" value="{{ request()->input('max') }}">
                        @endif
                        <select name="sort" class="nice-select right color-dark" onchange="updateUrl2()">
                          <option {{ request()->input('sort') == 'new' ? 'selected' : '' }} value="new">
                            {{ __('Date : Newest on top') }}
                          </option>
                          <option {{ request()->input('sort') == 'old' ? 'selected' : '' }} value="old">
                            {{ __('Date : Oldest on top') }}
                          </option>
                          <option {{ request()->input('sort') == 'high-to-low' ? 'selected' : '' }} value="high-to-low">
                            {{ __('Price : High to Low') }}</option>
                          <option {{ request()->input('sort') == 'low-to-high' ? 'selected' : '' }} value="low-to-high">
                            {{ __('Price : Low to High') }}</option>
                        </select>
                      </form>
                    </div>
                  </li>
                  <li class="item">
                    <a href="" class="btn-icon view_type" data-tooltip="tooltip" data-type="grid"
                      data-bs-placement="top" title="{{ __('Grid View') }}">
                      <i class="fas fa-th-large"></i>
                    </a>
                  </li>
                  <li class="item">
                    <a href="" class="btn-icon active view_type" data-tooltip="tooltip" data-type='list'
                      data-bs-placement="top" title="{{ __('List View') }}">
                      <i class="fas fa-th-list"></i>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="row">
            @php
              $admin = App\Models\Admin::first();
            @endphp
            @foreach ($car_contents as $car_content)
              <div class="col-12" data-aos="fade-up">
                <div class="row g-0 product-default product-column border mb-30 align-items-center p-15">
                  <figure class="product-img col-xl-4 col-lg-5 col-md-6 col-sm-12">
                    <a href="{{ route('frontend.car.details', ['slug' => $car_content->slug, 'id' => $car_content->id]) }}"
                      class="lazy-container ratio ratio-2-3">
                      <img class="lazyload" data-src="{{ asset('assets/admin/img/car/' . $car_content->feature_image) }}"
                        alt="Product">
                    </a>
                  </figure>
                  <div class="product-details col-xl-5 col-lg-5 col-md-6 col-sm-12 border-lg-end pe-lg-2">
                    <span class="product-category font-sm">
                      {{ carBrand($car_content->brand_id) }}
                      {{ carModel($car_content->car_model_id) }}
                    </span>
                    <h5 class="product-title mb-10"><a
                        href="{{ route('frontend.car.details', ['slug' => $car_content->slug, 'id' => $car_content->id]) }}">{{ $car_content->title }}</a>
                    </h5>
                    <div class="author mb-10">
                      @if ($car_content->vendor_id != 0)
                        <a class="color-medium"
                          href="{{ route('frontend.vendor.details', ['username' => ($vendor = @$car_content->vendor->username)]) }}"
                          target="_self" title="{{ $vendor = @$car_content->vendor->username }}">
                          @if ($car_content->vendor->photo != null)
                            <img class="lazyload blur-up"
                              data-src="{{ asset('assets/admin/img/vendor-photo/' . optional($car_content->vendor)->photo) }}"
                              alt="Image">
                          @else
                            <img class="lazyload blur-up" data-src="{{ asset('assets/img/blank-user.jpg') }}"
                              alt="Image">
                          @endif
                          <span>{{ __('By') }} {{ $vendor = optional($car_content->vendor)->username }}</span>
                        </a>
                      @else
                        <img class="lazyload blur-up" data-src="{{ asset('assets/img/admins/' . $admin->image) }}"
                          alt="Image">
                        <span><a
                            href="{{ route('frontend.vendor.details', ['username' => $admin->username, 'admin' => 'true']) }}">{{ __('By') }}
                            {{ $admin->username }}</a></span>
                      @endif
                    </div>
                    <p class="text mb-0 lc-2">
                      {!! strlen(strip_tags($car_content->description)) > 100
                          ? mb_substr(strip_tags($car_content->description), 0, 100, 'utf-8') . '...'
                          : strip_tags($car_content->description) !!}
                    </p>
                    <ul class="product-icon-list mt-15 list-unstyled d-flex align-items-center">
                      <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                        title="{{ __('Model Year') }}">
                        <i class="fal fa-calendar-alt"></i>
                        <span>{{ $car_content->year }}</span>
                      </li>
                      <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                        title="{{ __('mileage') }}">
                        <i class="fal fa-road"></i>
                        <span>{{ $car_content->mileage }}</span>
                      </li>
                      <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                        title="{{ __('Top Speed') }}">
                        <i class="fal fa-tachometer-fast"></i>
                        <span>{{ $car_content->speed }}</span>
                      </li>
                    </ul>
                  </div>
                  <div class="product-action col-xl-3 col-lg-2 col-md-12 col-sm-12">
                    <div class="product-price">
                      <h6 class="new-price color-primary">
                        {{ symbolPrice($car_content->price) }}
                      </h6>
                      @if (!is_null($car_content->previous_price))
                        <span class="old-price font-sm">
                          {{ symbolPrice($car_content->previous_price) }}
                        </span>
                      @endif
                    </div>
                    <a href="{{ route('frontend.car.details', ['slug' => $car_content->slug, 'id' => $car_content->id]) }}"
                      class="btn btn-sm btn-primary" title="{{ __('View Details') }}">{{ __('View Details') }}</a>
                  </div>
                  @if (Auth::guard('web')->check())
                    @php
                      $user_id = Auth::guard('web')->user()->id;
                      $checkWishList = checkWishList($car_content->id, $user_id);
                    @endphp
                  @else
                    @php
                      $checkWishList = false;
                    @endphp
                  @endif
                  <a href="{{ $checkWishList == false ? route('addto.wishlist', $car_content->id) : route('remove.wishlist', $car_content->id) }}"
                    class="btn btn-icon {{ $checkWishList == false ? '' : 'wishlist-active' }}" data-tooltip="tooltip"
                    data-bs-placement="right"
                    title="{{ $checkWishList == false ? __('Save to Wishlist') : __('Saved') }}">
                    <i class="fal fa-heart"></i>
                  </a>
                </div><!-- product-default -->
              </div>
            @endforeach
          </div>
          <div class="pagination mb-40 justify-content-center" data-aos="fade-up">
            {{ $car_contents->appends([
                    'category' => request()->input('category'),
                    'location' => request()->input('location'),
                    'brands' => request()->input('brands'),
                    'models' => request()->input('models'),
                    'fuel_type' => request()->input('fuel_type'),
                    'transmission' => request()->input('transmission'),
                    'condition' => request()->input('condition'),
                    'min' => request()->input('min'),
                    'max' => request()->input('max'),
                    'sort' => request()->input('sort'),
                ])->links() }}
          </div>

          @if (!empty(showAd(3)))
            <div class="text-center mt-4 mb-40">
              {!! showAd(3) !!}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <!-- Listing-list-area end -->
  @include('frontend.partials.map-modal')
@endsection
@section('script')
  @if ($websiteInfo->google_map_api_key_status == 1)
    <script src="{{ asset('assets/front/js/api-search.js') }}"></script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key={{ $websiteInfo->google_map_api_key }}&libraries=places&callback=initMap"
      async defer></script>
  @endif
@endsection
