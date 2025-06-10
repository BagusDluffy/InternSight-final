<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-container">
  <!-- Brand Logo -->
  <a href="#" class="brand-link" style="pointer-events:none;">
  <img src="{{ asset('assets/logo1.png') }}" alt="InternSight Logo" class="brand-image elevation-3">
    <span class="" style="color:#2D336B">1</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar sidebar-content">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 text-center">
      <div class="image">
        <a href="{{ route('profile.edit') }}">
          <img src="{{ (Auth::user()->avatar && file_exists(public_path('storage/avatars/' . Auth::user()->avatar))) 
            ? asset('storage/avatars/' . Auth::user()->avatar) 
            : asset('AdminLTE/dist/img/user2-160x160.jpg') }}"
            class="img-circle elevation-2"
            style="width: 100px; height: 100px; object-fit: cover; margin-bottom: 10px;"
            alt="User Image">
        </a>
        <a href="{{ route('profile.edit') }}" class="d-block user-name" style="font-size: 16px; color: #fff;">
          {{ Auth::user()->name }}
        </a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
            <p>Dashboard</p>
          </a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item">
          <a href="{{ route('guru.index') }}" class="nav-link {{ request()->routeIs('guru.index') ? 'active' : '' }}">
            <p>List Guru</p>
          </a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item">
          <a href="{{ route('jurusan.index') }}" class="nav-link {{ request()->routeIs('jurusan.index') ? 'active' : '' }}">
            <p>List Jurusan</p>
          </a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item">
          <a href="{{ route('murid.index') }}" class="nav-link {{ request()->routeIs('murid.index') ? 'active' : '' }}">
            <p>List Murid</p>
          </a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item">
          <a href="{{ route('dudika.index') }}" class="nav-link {{ request()->routeIs('dudika.index') ? 'active' : '' }}">
            <p>List Dudika</p>
          </a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item">
          <a href="{{ route('magang.index') }}" class="nav-link {{ request()->routeIs('magang.index') ? 'active' : '' }}">
            <p>List Magang</p>
          </a>
        </li>
        <hr class="sidebar-divider">
      </ul>
    </nav>
  </div>
</aside>

<!-- Custom CSS -->
<style>
/* Styling Sidebar */
.nav-sidebar > .nav-item > .nav-link {
  color: #c2c7d0;
  transition: all 0.3s ease;
  border-radius: 4px;
  margin: 8px 15px;
  /* padding: 12px 15px; */
  font-size: 14px;
  display: flex;
  align-items: center;
}

.nav-sidebar > .nav-item > .nav-link i.nav-icon {
  margin-right: 0px;
  font-size: 18px;
}

.nav-sidebar > .nav-item > .nav-link:hover {
  background-color: #2e2e38;
  /* color: #42b983; */
}

.nav-sidebar > .nav-item > .nav-link.active {
  background-color: #1e5dd1; /* Warna latar aktif */
  color: white;
}


.nav-sidebar > .nav-item > .nav-link.active i.nav-icon {
  color: white;
}

.user-panel .user-name {
  font-weight: bold;
  font-size: 16px !important;
  color: #c2c7d0 !important;
}

/* Ukuran Font Untuk Layar Besar */
@media (min-width: 1920px) {
  .nav-sidebar > .nav-item > .nav-link {
    font-size: 18px; /* Perbesar font */
  }
  .user-panel .user-name {
    font-size: 20px !important; /* Perbesar nama user */
  }
}

.main-sidebar {
  overflow-x: hidden;
  width: 230px;
  background: #2D336B;
}

.sidebar-mini.sidebar-collapse .main-sidebar {
  transform: translateX(-100%);
  opacity: 0;
  pointer-events: none;
}

.sidebar-mini.sidebar-collapse .nav-sidebar .nav-link {
  text-align: center;
  padding: 10px 5px;
}

.sidebar-mini.sidebar-collapse .nav-sidebar .nav-link p {
  display: none;
}

.content-wrapper {
  transition: all 0.4s ease-in-out;
  margin-left: 230px;
  width: calc(100% - 230px);
  overflow-x: hidden; /* Mencegah scroll horizontal */
}

.sidebar-mini.sidebar-collapse .content-wrapper {
  margin-left: 0 !important;
  width: 100% !important;
}

.brand-link {
    display: flex;
    align-items: center;
    justify-content: center; /* Tengahkan logo */
    height: auto; /* Pastikan tidak ada batasan tinggi */
    /* padding: 10px 0; Sesuaikan padding */
}

.brand-link img {
    width: 50px !important; /* Sesuaikan ukuran logo */
    height: auto !important;
    max-height: 100px !important; /* Pastikan tidak terpotong */
    object-fit: contain; /* Hindari distorsi gambar */
}

.sidebar-divider {
    border: none;
    height: 1px;
    background-color: rgba(255, 255, 255, 0.2); /* Warna garis */
    margin: 10px 15px;
}


</style>
