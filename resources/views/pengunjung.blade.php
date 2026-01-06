<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Wedding Organizer</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link rel="icon" href="{{ asset('images/android-chrome-512x512.png')}}">
  <link rel="icon" href="{{ asset('images/apple-touch-icon.png')}}">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('Dewi-1.0.0/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('Dewi-1.0.0/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('Dewi-1.0.0/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('Dewi-1.0.0/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('Dewi-1.0.0/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('Dewi-1.0.0/assets/css/main.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Dewi
  * Template URL: https://bootstrapmade.com/dewi-free-multi-purpose-html-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="{{ '/' }}" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">Nakkawin Decoration</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#beranda" class="active">Beranda</a></li>
          <li><a href="#tentangkami">Tentang Kami</a></li>
          <li><a href="#paket">Paket</a></li>
          <li><a href="#layanan">Layanan</a></li>
          <li><a href="#portfolio">Portfolio</a></li>
          <li><a href="#kontak">Kontak</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <form id="login-form" action="{{ route('login') }}" method="POST" class="d-none">
        @csrf
      </form>
      <a class="cta-btn" href="{{ route('login') }}">Login</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="beranda" class="hero section dark-background">

      <!-- Swiper Slider -->
      <div class="swiper-container hero-swiper">
        <div class="swiper-wrapper">
          @forelse($portfolios as $portfolio)
            @if($portfolio->images && is_array($portfolio->images))
              @foreach($portfolio->images as $image)
                <div class="swiper-slide">
                  <img src="{{ asset('storage/' . $image) }}" alt="Portfolio" class="hero-image" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
              @endforeach
            @endif
          @empty
            <div class="swiper-slide">
              <img src="{{ asset('Dewi-1.0.0/assets/img/hero-bg.jpg') }}" alt="" class="hero-image" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
          @endforelse
        </div>
      </div>

      <div class="container d-flex flex-column align-items-center" style="position: relative; z-index: 10;">
        <h2 data-aos="fade-up" data-aos-delay="100">PLAN. LAUNCH.</h2>
        <p data-aos="fade-up" data-aos-delay="200">We are team of Wedding Decoration</p>
        <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">
          <a href="{{ route('login') }}" class="btn-get-started">Pesan Sekarang</a>
          {{-- <a href="https://youtube.com/shorts/YfOudWrZk6A?si=_YZtAsg8VpK_BAn-" class="glightbox btn-watch-video d-flex align-items-center"><i class="bi bi-play-circle"></i><span>Watch Video</span></a> --}}
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="tentangkami" class="about section">
      <div class="container">

        <!-- Section Title -->
        <div class="row mb-3" data-aos="fade-up" data-aos-delay="100">
          <div class="col-12 text-center">
            <h2 style="font-size: 2.5rem; font-weight: bold;">
              {{ $aboutUs->title ?? 'About Us' }}
            </h2>
          </div>
        </div>

        <!-- Content Row -->
        <div class="row gy-4 align-items-center justify-content-center">

          <div class="col-lg-3 text-center" data-aos="fade-up" data-aos-delay="100">
            @if($aboutUs && $aboutUs->image)
              <img src="{{ asset('storage/' . $aboutUs->image) }}"
                  class="img-fluid rounded-4 mb-4"
                  alt="About Us">
            @else
              <img src="{{ asset('Dewi-1.0.0/assets/img/about.jpg') }}"
                  class="img-fluid rounded-4 mb-4"
                  alt="">
            @endif
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="250">
            <div class="content ps-0 ps-lg-5">
              <p class="fst-italic text-center text-lg-start">
                Kami adalah mitra terpercaya Anda dalam mewujudkan acara spesial yang tak terlupakan.
              </p>

              <ul class="list-unstyled text-center text-lg-start">
                <li><i class="bi bi-check-circle-fill"></i> Layanan dekorasi berkualitas tinggi</li>
                <li><i class="bi bi-check-circle-fill"></i> Tim profesional dan berpengalaman</li>
                <li><i class="bi bi-check-circle-fill"></i> Desain kreatif sesuai keinginan Anda</li>
              </ul>

              <p class="text-center text-lg-start">
                Dengan pengalaman bertahun-tahun, kami siap mengubah impian Anda menjadi kenyataan yang memukau.
              </p>

              <p class="text-center text-lg-start">
                {!! $aboutUs->description ?? 'Deskripsi tentang kami akan ditampilkan di sini.' !!}
              </p>
            </div>
          </div>

        </div>
      </div>
    </section>
    <!-- /About Section -->

    <!-- Packages Section -->
    <section id="paket" class="services section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Paket</h2>
        <p>Pilihan Paket<br></p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-5">

          @forelse($packages as $package)
          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
            <div class="service-item">
              <div class="img" style="width: 100%; height: 300px; overflow: hidden;">
                <img src="{{ asset('storage/' . $package->image) }}" class="img-fluid" alt="{{ $package->name }}" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
              <div class="details position-relative">
                <div class="icon">
                  <i class="bi bi-gift"></i>
                </div>
                <a href="#" class="stretched-link" onclick="openPackageModal({{ $package->id }}); return false;">
                  <h3 style="font-weight: bold; color: #000000;">{{ $package->name }}</h3>
                </a>
                <p class="price" style="font-weight: bold; color: #d43f8d; margin-top: 10px;">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
              </div>
            </div>
          </div><!-- End Service Item -->
          @empty
          <div class="col-12 text-center">
            <p>No packages available</p>
          </div>
          @endforelse

          <!-- Package Modal Data (Hidden) -->
          @foreach($packages as $package)
          <div id="packageModal{{ $package->id }}" style="display: none;">
            <div class="package-name fw-bold text-dark">{{ $package->name }}</div>
            <div class="package-image">{{ asset('storage/' . $package->image) }}</div>
            <div class="package-price">{{ number_format($package->price, 0, ',', '.') }}</div>
            <div class="package-services">
              @forelse($package->services as $service)
                <div class="service-item">{{ $service->name }} - Rp {{ number_format($service->pivot->value_price ?? $service->harga_layanan, 0, ',', '.') }}</div>
              @empty
                <div class="service-item">Tidak ada layanan</div>
              @endforelse
            </div>
          </div>
          @endforeach

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- Services Section -->
    <section id="layanan" class="services-2 section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up" data-aos-delay="100">
        <h2>Services</h2>
        <p>OUR AVAILABLE SERVICES</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4" id="servicesContainer">

          @forelse($services as $service)
          <div class="col-md-6 service-item-wrapper" data-service-index="{{ $loop->index }}" style="{{ $loop->index >= 10 ? 'display: none;' : '' }}">
              <div>
                <h4 class="title stretched-link">{{ $service->name }}</h4>
              </div>
            
          </div><!-- End Service Item -->
          @empty
          <div class="col-12 text-center">
            <p>No services available</p>
          </div>
          @endforelse

          @if($services->count() > 10)
          <div class="col-12 text-center" style="margin-top: 30px;" id="showMoreBtn">
            <button onclick="toggleServices()" style="background: none; border: none; display: inline-flex; flex-direction: column; align-items: center; gap: 10px; cursor: pointer; text-decoration: none; color: #e68900; font-weight: bold; font-size: 16px; padding: 0;">
              <span id="btnText">Lihat Selengkapnya</span>
              <i class="bi bi-chevron-down" id="chevronIcon" style="font-size: 24px; animation: bounce 2s infinite; transition: transform 0.3s;"></i>
            </button>
          </div>
          @endif

        </div>

      </div>

      <style>
        @keyframes bounce {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(10px); }
        }

        .service-item-wrapper .title a {
          color: #000000 !important;
          transition: color 0.3s ease;
        }

        .service-item-wrapper .title a:hover {
          color: #e68900 !important;
        }
      </style>

      <script>
        function toggleServices() {
          const container = document.getElementById('servicesContainer');
          const items = container.querySelectorAll('.service-item-wrapper');
          const chevron = document.getElementById('chevronIcon');
          const btnText = document.getElementById('btnText');
          let allVisible = false;

          items.forEach((item, index) => {
            if (index >= 4) {
              if (item.style.display === 'none') {
                item.style.display = '';
                allVisible = true;
              } else {
                item.style.display = 'none';
              }
            }
          });

          // Update button text and chevron direction
          if (allVisible) {
            btnText.textContent = 'Sembunyikan';
            chevron.style.transform = 'rotate(180deg)';
            chevron.style.animation = 'none';
          } else {
            btnText.textContent = 'Lihat Selengkapnya';
            chevron.style.transform = 'rotate(0deg)';
            chevron.style.animation = 'bounce 2s infinite';
          }
        }
      </script>

    </section><!-- /Services 2 Section -->

    <!-- Portfolio Section -->
    <section id="portfolio" class="portfolio section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Portfolio</h2>
        <p>CHECK OUR PORTFOLIO</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

          <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

            @forelse($portfolios as $portfolio)
              @if($portfolio->images && is_array($portfolio->images))
                @foreach($portfolio->images as $image)
                  <div class="col-lg-4 col-md-6 portfolio-item isotope-item">
                    <div class="portfolio-content h-100">
                      <img src="{{ asset('storage/' . $image) }}" class="img-fluid" alt="Portfolio Image">
                      <div class="portfolio-info">
                        <h4>Portfolio</h4>
                        <p>Karya Dekorasi Kami</p>
                        <a href="{{ asset('storage/' . $image) }}" title="Portfolio" data-gallery="portfolio-gallery" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                      </div>
                    </div>
                  </div><!-- End Portfolio Item -->
                @endforeach
              @endif
            @empty
              <div class="col-lg-4 col-md-6 portfolio-item isotope-item">
                <div class="portfolio-content h-100">
                  <img src="{{ asset('Dewi-1.0.0/assets/img/portfolio/app-1.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <h4>Portfolio Kosong</h4>
                    <p>Belum ada portfolio yang ditambahkan</p>
                  </div>
                </div>
              </div><!-- End Portfolio Item -->
            @endforelse

          </div><!-- End Portfolio Container -->

        </div>

      </div>

    </section><!-- /Portfolio Section -->

    <!-- Contact Section -->
    <section id="kontak" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>{{ $contactUs->subtitle ?? 'Hubungi Kami' }}</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">
          <div class="col-lg-6 ">
            <div class="row gy-4">

              <div class="col-lg-12">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
                  <i class="bi bi-geo-alt"></i>
                  <h3>Alamat</h3>
                  <p>{{ $contactUs->alamat}}</p>
                </div>
              </div><!-- End Info Item -->

              <div class="col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
                  <i class="bi bi-telephone"></i>
                  <h3>Kontak</h3>
                  <p>{{ $contactUs->nomor_hp ?? '08117411190' }}</p>
                </div>
              </div><!-- End Info Item -->

              <div class="col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
                  <i class="bi bi-envelope"></i>
                  <h3>Email</h3>
                  <p>{{ $contactUs->email ?? 'nakkawindecoration18@gmail.com' }}</p>
                </div>
              </div><!-- End Info Item -->

            </div>
          </div>

          <div class="col-lg-6">
            <iframe 
              src="{{ $contactUs->map_url 
                ?? 'https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3988.2053863622114!2d103.660432!3d-1.629236!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMcKwMzcnNDUuMyJTIDEwM8KwMzknMzcuNiJF!5e0!3m2!1sen!2sid!4v1766817015216!5m2!1sen!2sid' }}"
              style="border:0; width: 100%; height: 370px;"
              frameborder="0"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              data-aos="fade-up"
              data-aos-delay="500">
            </iframe>
          </div>
          <!-- End Contact Map -->

        </div>

      </div>

    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="{{ '/' }}" class="logo d-flex align-items-center">
            <span class="sitename">Nakkawin Decoration</span>
          </a>
          <div class="footer-contact pt-3">
            <p>{{ $contactUs->alamat}}</p>
            <p><strong>Telepon:</strong> <span>{{ $contactUs->nomor_hp ?? '08117411190' }}</span></p>
            <p><strong>Email:</strong> <span>{{ $contactUs->email ?? 'nakkawindecoration18@gmail.com' }}</span></p>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Menu</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="#beranda">Home</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#tentangkami">Tentang Kami</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#paket">Paket</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#layanan">Layanan</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#portfolio">Portofolio</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#kontak">Kontak</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Paket Kami</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="#paket">Standar</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#paket">Pre-Wedding Indoor</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#paket">Pre-Wedding Outdoor</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#paket">WO Only</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#paket">WO All-In</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#paket">Intimate Wedding</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12 footer-newsletter">
          <h4>Social Media</h4>
          <div class="social-links d-flex mt-4">
            <a href="https://www.facebook.com/puput.buffing/" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/nakkawin_decoration/" target="_blank"><i class="bi bi-instagram"></i></a>
            <a href="https://wa.me/6282372070011" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Chori Erola</strong>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a href=“https://themewagon.com>ThemeWagon
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('Dewi-1.0.0/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('Dewi-1.0.0/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('Dewi-1.0.0/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('Dewi-1.0.0/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('Dewi-1.0.0/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('Dewi-1.0.0/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('Dewi-1.0.0/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('Dewi-1.0.0/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('Dewi-1.0.0/assets/js/main.js') }}"></script>

  <!-- Package Modal HTML -->
  <div id="packageModalOverlay" class="package-modal-overlay" onclick="closePackageModal()"></div>
  <div id="packageModalContent" class="package-modal-content">
    <button class="package-modal-close" onclick="closePackageModal()">
      <i class="bi bi-x"></i>
    </button>
    <div class="package-modal-body">
      <img id="modalPackageImage" src="" alt="Package Image" class="modal-package-image">
      <h2 id="modalPackageName">Package Name</h2>
      <div class="modal-package-details">
        <div class="detail-section">
          <h4>Harga Paket</h4>
          <p id="modalPackagePrice" style="font-weight: bold; color: #d43f8d; font-size: 18px;">Rp 0</p>
        </div>
        <div class="detail-section">
          <h4>Layanan Included</h4>
          <ul id="modalPackageServices" style="text-align: left;">
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Hero Swiper Slider Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const heroSwiper = new Swiper('.hero-swiper', {
        loop: true,
        speed: 600,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
        },
        effect: 'fade',
        fadeEffect: {
          crossFade: true
        }
      });
    });
  </script>

  <!-- Hero Section Styles -->
  <style>
    .hero .swiper-container {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 1;
    }

    .hero .swiper-slide {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #000;
    }

    .hero .hero-image {
      width: 100%;
      height: 100%;
      object-fit: contain;
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 2;
    }

    .hero .container {
      position: relative;
      z-index: 3;
    }

    /* Package Modal Styles */
    .package-modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 999;
      cursor: pointer;
    }

    .package-modal-overlay.active {
      display: block;
    }

    .package-modal-content {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      border-radius: 10px;
      width: 90%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
      z-index: 1000;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .package-modal-content.active {
      display: block;
    }

    .package-modal-close {
      position: absolute;
      top: 15px;
      right: 15px;
      background: none;
      border: none;
      font-size: 28px;
      color: #333;
      cursor: pointer;
      z-index: 1001;
      padding: 0;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .package-modal-close:hover {
      color: #d43f8d;
    }

    .package-modal-body {
      padding: 30px;
      text-align: center;
    }

    .modal-package-image {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .package-modal-body h2 {
      color: #000;
      margin: 20px 0;
      font-size: 28px;
      font-weight: bold;
    }

    .modal-package-details {
      text-align: left;
      margin-top: 20px;
    }

    .detail-section {
      margin: 20px 0;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
    }

    .detail-section h4 {
      color: #333;
      margin: 0 0 10px 0;
      font-weight: bold;
    }

    .detail-section p {
      margin: 0;
      color: #666;
    }

    .detail-section ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .detail-section li {
      padding: 8px 0;
      border-bottom: 1px solid #e0e0e0;
      color: #666;
    }

    .detail-section li:last-child {
      border-bottom: none;
    }

    @media (max-width: 768px) {
      .package-modal-content {
        width: 95%;
        max-width: none;
      }

      .package-modal-body {
        padding: 20px;
      }

      .modal-package-image {
        height: 200px;
      }

      .package-modal-body h2 {
        font-size: 22px;
        font-weight: bold;
      }
    }
  </style>

  <script>
    function openPackageModal(packageId) {
      const modal = document.getElementById('packageModalOverlay');
      const modalContent = document.getElementById('packageModalContent');
      const hiddenData = document.getElementById('packageModal' + packageId);

      if (hiddenData) {
        // Get data from hidden div
        const packageName = hiddenData.querySelector('.package-name').textContent;
        const packageImage = hiddenData.querySelector('.package-image').textContent;
        const packagePrice = hiddenData.querySelector('.package-price').textContent;
        const servicesHTML = hiddenData.querySelector('.package-services').innerHTML;

        // Update modal content
        document.getElementById('modalPackageName').textContent = packageName;
        document.getElementById('modalPackageImage').src = packageImage;
        document.getElementById('modalPackagePrice').textContent = 'Rp ' + packagePrice;
        
        // Clear and populate services
        const servicesList = document.getElementById('modalPackageServices');
        servicesList.innerHTML = '';
        const servicesDiv = hiddenData.querySelector('.package-services');
        servicesDiv.querySelectorAll('.service-item').forEach(service => {
          const li = document.createElement('li');
          li.textContent = service.textContent;
          servicesList.appendChild(li);
        });

        // Show modal
        modal.classList.add('active');
        modalContent.classList.add('active');
      }
    }

    function closePackageModal() {
      const modal = document.getElementById('packageModalOverlay');
      const modalContent = document.getElementById('packageModalContent');
      modal.classList.remove('active');
      modalContent.classList.remove('active');
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
      const modal = document.getElementById('packageModalOverlay');
      if (event.target === modal) {
        closePackageModal();
      }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closePackageModal();
      }
    });
  </script>

</body>

</html>