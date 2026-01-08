<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="navbar navbar-expand-xl fixed-top shadow-sm bg-white">
    <div class="container-fluid px-4">

        <!-- LOGO -->
        <a class="navbar-brand fw-bold d-flex align-items-center"
           href="{{ route('dashboard') }}">
            Nakkawin Decoration
        </a>

        <!-- TOGGLER (MOBILE) -->
        <button class="navbar-toggler d-xl-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- MENU -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">BERANDA</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}#tentangkami">TENTANG KAMI</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}#paket">PAKET</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}#layanan">LAYANAN</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-warning fw-semibold"
                       href="{{ route('pelanggan.pesanan') }}">
                        PESANAN
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}#portfolio">PORTFOLIO</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}#kontak">KONTAK</a>
                </li>

                <!-- PROFILE -->
                <li class="nav-item dropdown ms-xl-3">
                    <a class="nav-link dropdown-toggle p-0"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-4"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li class="dropdown-header fw-semibold">
                            @if(auth()->check())
                                {{ auth()->user()->name }}
                            @endif
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile') }}" wire:navigate>
                                <i class="bi bi-person me-2 text-warning"></i> Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button wire:click="logout" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
    <style>
    body {
        padding-top: 80px;
    }

    .nav-link {
        font-weight: 500;
        position: relative;
    }

    .nav-link:hover {
        color: #ff9800 !important;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        width: 0;
        height: 3px;
        background: #ff9800;
        transform: translateX(-50%);
        transition: width .3s;
    }

    .nav-link:hover::after {
        width: 60%;
    }

    @media (max-width: 1199px) {
        body {
            padding-top: 70px;
        }

        .nav-link::after {
            display: none;
        }
    }
</style>
</nav>


