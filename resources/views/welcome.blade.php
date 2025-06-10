<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Sight</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/js/bootstrap.min.js">
    <link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700&display=swap" rel="stylesheet">


    <style>
/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    scroll-behavior: smooth;
}

body {
    font-family: "Plus Jakarta Sans";
    color: #fff;
    background-color: #000000;
    background-size: 100vh;
    line-height: 1.6;
    font-family: "Plus Jakarta Sans" !important;
}

/* Hero Section */
.hero {
    height: 100vh;
    background-image: url('{{ asset("assets/background.png") }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    margin-bottom:16rem;
}

.hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2); 
    z-index: 1; /* Overlay berada di atas background */
}

.hero-content {
    position: absolute;
    bottom: 20px; /* Sesuaikan posisi ke kiri bawah */
    left: 20px;
    z-index: 2; /* Pastikan konten berada di atas overlay */
    text-align: left;
}

/* Header */
header {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(0, 0, 0, 0); /* Transparansi background header */
    padding: 1rem 2rem;
}

header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

header .logo {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

header .logo img {
    height: 40px;
}

header .left-nav, header .right-nav, header .logo {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    color: #000000;
}

header nav a {
    color: #fff;
    text-decoration: none;
    font-size: 1rem;
    position: relative;
    overflow: hidden;
    transition: color 0.3s ease;
    z-index: 1;
    padding: 0.5rem 1rem; /* Menambahkan padding */
    margin: 0 0.5rem; /* Menambahkan margin */
}

header nav a::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #444; /* Warna background saat hover */
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    z-index: -1; /* Mengatur z-index agar background di bawah teks */
}

header nav a:hover::before {
    transform: scaleX(1);
}

header nav a:hover {
    color: #fff; /* Warna teks tetap putih saat hover */
}

header .login-btn {
    border: none; /* Menghapus border */
    background-color: transparent; /* Tidak ada background */
    color: #fff; /* Warna teks putih */
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: color 0.3s ease;
    position: relative;
    overflow: hidden;
    z-index: 1;
    font-size: 1rem; /* Meningkatkan ukuran teks */
}

header .login-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #444; /* Warna background saat hover */
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    z-index: -1; /* Mengatur z-index agar background di bawah teks */
}

header .login-btn:hover::before {
    transform: scaleX(1);
}

header .login-btn:hover {
    color: #fff; /* Warna teks tetap putih saat hover */
}

@media (min-width: 1024px) {
    header nav a, header .login-btn {
        font-size: 1.2rem;
    }

    header .logo img {
        height: 50px;
    }
}

@media (max-width: 1023px) {
    header nav a, header .login-btn {
        font-size: 1rem;
    }

    header .logo img {
        height: 40px;
    }
}

/* Hero Content */
.hero-content {
    position: absolute;
    bottom: 2rem; /* Jarak dari bawah */
    left: 2rem; /* Jarak dari kiri */
    text-align: left; /* Teks rata kiri */
    color:rgb(255, 255, 255);
}

.hero-content h1 {
    font-size: 5rem; /* Ukuran lebih besar untuk h1 */
    font-weight: bold;
    text-transform: uppercase; /* Huruf besar semua */
    margin-bottom: 1rem;
}

.hero-content p {
    font-size: 1.1rem; /* Ukuran teks */
    max-width: 900px; /* Lebar maksimum untuk mengontrol panjang teks */
    margin: 0 auto; /* Untuk memusatkan */
    text-align: left; /* Menyelaraskan teks ke kiri */
    line-height: 1.5; /* Jarak antar baris */
    font-weight: bold;
}

/* section 1 */
.about {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start; /* Mengatur align items ke atas */
    gap: 2rem;
    margin-bottom: 8rem;
}

.cards-container {
    display: flex;
    gap: 1.5rem;
    margin: 0 2rem;
    align-items: flex-start; /* Mengatur align items ke atas */
}

.cards {
    display: flex;
    gap: 1.5rem;
}

.card {
    min-width: 400px;
    min-height: 550px;
    max-width: 250px;
    max-height: 250px;
}

.card img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.teks {
    font-size: 1.05rem;
    line-height: 1.4;
    max-width: 100%;
    max-height: 100%;
    text-overflow: ellipsis;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: justify;
    /* border: 1px solid red; */
    height: 600px; /* Menyesuaikan tinggi dengan card */
}

@media(min-width:1920px){
    .teks{
        font-size: 1.7rem;
    }
}

.teks p:first-child {
    margin-top: 0; /* Memastikan teks pertama sejajar dengan bagian atas */
}

.teksb {
    align-self: flex-start; /* Memastikan teks berada di bagian bawah */
    font-size: 1rem;
    margin-bottom: 0; /* Memastikan teks sejajar dengan bagian bawah */
}

/* Improved Gallery Section */
.gallery {
  position: relative;
  overflow: hidden;
  padding: 20px;
  margin-top: 20rem;
  width: 100%;
}

.gallery-container {
  padding: 1rem;
  display: flex;
  overflow-x: auto;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
  gap: 15px; /* Space between cards */
  scrollbar-width: none; /* Firefox */
}

/* Hide scrollbar for Chrome, Safari and Opera */
.gallery-container::-webkit-scrollbar {
  display: none;
}

.gallery-card {
  flex: 0 0 auto;
  width: 400px;
  height: 550px;
  transition: transform 0.3s ease;
}

/* Add hover effect */
.gallery-card:hover {
  transform: scale(1.02);
}

.gallery-image {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.gallery-thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.gallery-thumb:hover {
  transform: scale(1.05);
}

.gallery-category {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1rem;
  margin-bottom: 1rem;
}

.gallery-left,
.gallery-right {
  font-weight: bold;
  font-size: 2.2rem;
}

.gallery-left {
  text-align: left;
  flex: 1;
}

.gallery-center {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    color: white;
    font-weight: bold;
    font-size: 24px;
    /* margin-bottom: 16rem; */
}

.gallery-center::before,
.gallery-center::after {
    content: "";
    flex: 1;
    height: 2px; /* Ketebalan garis */
    background-color: white; /* Warna garis */
    margin: 0 30px; /* Jarak garis ke teks, sesuaikan dengan elemen lain */
}

.gallery-right {
  text-align: right;
  flex: 1;
}

/* Gallery navigation buttons */
.gallery-navigation {
  display: flex;
  justify-content: center;
  margin-top: 20px;
  gap: 20px;
}

.gallery-nav-btn {
  background-color: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.gallery-nav-btn:hover {
  background-color: rgba(255, 255, 255, 0.3);
}

/* Responsive adjustments */
@media (max-width: 1200px) {
  .gallery-card {
    width: 350px;
    height: 500px;
  }
}

@media (max-width: 768px) {
  .gallery-card {
    width: 300px;
    height: 400px;
  }
  
  .gallery-left,
  .gallery-right {
    font-size: 1.8rem;
  }
}

@media (max-width: 576px) {
  .gallery-card {
    width: 250px;
    height: 350px;
  }
  
  .gallery-left,
  .gallery-right {
    font-size: 1.5rem;
  }
}

/* Call-to-Action Section */
.cta {
    text-align: center;
    padding: 3rem 2rem;
}

.cta h2 {
    margin-top:8rem;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.cta p {
    font-size: 1.1rem;
    margin-bottom: 3rem;
}

.cta .cta-image {
    background-color: #444;
    width: 100%;
    height: 600px;
    margin: 0 auto;
}

.cta .cta-image img{
    width:100%;
    height:100%;
}

.cta .contact-btn {
    background-color: transparent; /* Membuat background transparan */
    color: #fff; /* Teks tetap terlihat (pilih warna yang kontras dengan background) */
    padding: 0.5rem 1rem;
    /* border: 0.5px solid #fff; Tambahkan border agar tombol terlihat */
    cursor: pointer;
    margin-top: 1rem;
    transition: all 0.3s ease; /* Tambahkan efek transisi */
}

/* Tambahkan efek hover untuk tombol */
.cta .contact-btn:hover {
    background-color: rgba(255, 255, 255, 0.2); /* Efek transparan saat hover */
    color: #fff; /* Warna teks tetap */
}

/* Animasi Pulse */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
    }
    70% {
        box-shadow: 0 0 0 20px rgba(255, 255, 255, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
    }
}

/* Tombol dengan Animasi Border Gradient dan Pulse */
.contact-btn {
    background-color: transparent;
    color: #fff;
    padding: 1rem 3rem;
    font-size: 1.5rem;
    cursor: pointer;
    margin-top: 1rem;
    border: 2px solid rgba(100, 100, 100, 1);
    border-radius: 3px 20px;
    position: relative;
    background: linear-gradient(to right, transparent 50%, rgba(195, 195, 195, 0.83) 50%);
    background-size: 200% 100%;
    background-position: left bottom;
    transition: all 0.3s ease;
}

/* Efek Hover */
.contact-btn:hover {
    background-position: right bottom; /* Animasi Border Gradient */
    animation: pulse 1.5s infinite; /* Animasi Pulse */
    color: #000000;
}

/* Popup Header Buttons */
.popup-header {
    display: flex;
    justify-content: flex-end;
    position: absolute;
    top: 10px;
    right: 10px;
}

.minimize-btn, .close-btn {
    background-color: #fff;
    border: none;
    color: #333;
    font-size: 1.5rem;
    margin-left: 10px;
    cursor: pointer;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.minimize-btn:hover, .close-btn:hover {
    background-color: #f1f1f1;
}

/* Popup Modal Style */
.popup-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    display: none;
    z-index: 999;
    overflow: auto; /* Mengizinkan scrolling jika konten terlalu panjang */
}

.popup-content {
    position: relative;
    background-color: #fff;
    width: 70%; /* Mengatur lebar popup */
    max-width: 900px; /* Lebar maksimum */
    margin: 100px auto; /* Mengatur margin atas dan bawah */
    border-radius: 10px;
    padding: 20px;
    display: flex;
    overflow: hidden;
}

.popup-left img {
    width: 100%;
    border-radius: 10px 0 0 10px;
}

.popup-right {
    padding: 20px;
    width: 50%;
}

.popup-left {
    width: 50%; /* Set width agar proporsional */
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 8px 0 0 8px; /* Membuat sudut kiri atas dan bawah membulat */
    background-color: #f0f0f0; /* Warna latar belakang default */
}

.popup-img {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 8px 0 0 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Tambahkan bayangan agar lebih elegan */
}

.popup-left img:hover {
    transform: scale(1.05); /* Efek zoom saat hover */
    transition: transform 0.3s ease;
}

/* Hidden State for Minimized Popup */
.popup-content.minimized {
    height: 50px;
    overflow: hidden;
}

/* form griup styling */
.popup-right form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: #333;
    text-align: left; /* Memastikan teks label rata kiri */
    width: 100%; /* Mengisi seluruh lebar form-group */
}

.form-group input,
.form-group textarea {
    padding: 0.75rem 1rem;
    font-size: 1rem;
    border: 2px solid #ccc;
    border-radius: 8px;
    outline: none;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

textarea {
    height: 150px;
    resize: none;
}

/* Styling Button */
.submit-btn {
    background-color: black;
    color: #fff;
    padding: 0.75rem 1.5rem;
    border: 2px solid #fff;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    margin-top: 10rem;
    /* transition: all 1s ease */
}

.submit-btn:hover {
    background-color: grey;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .popup-content {
        width: 80%; /* Mengatur lebar popup */
        max-width: 700px; /* Lebar maksimum */
        margin: 70px auto; /* Mengatur margin atas dan bawah */
    }
}

@media (max-width: 768px) {
    .popup-content {
        width: 90%; /* Mengatur lebar popup */
        max-width: 500px; /* Lebar maksimum */
        margin: 50px auto; /* Mengatur margin atas dan bawah */
        flex-direction: column; /* Mengatur flex-direction menjadi column */
    }

    .popup-left, .popup-right {
        width: 100%; /* Mengatur lebar popup-left dan popup-right */
    }

    .popup-left {
        border-radius: 10px 10px 0 0;
    }

    .popup-img {
        border-radius: 10px 10px 0 0;
    }
}

@media (max-width: 576px) {
    .popup-content {
        width: 95%; /* Mengatur lebar popup */
        max-width: 400px; /* Lebar maksimum */
        margin: 30px auto; /* Mengatur margin atas dan bawah */
    }

    .popup-left, .popup-right {
        width: 100%; /* Mengatur lebar popup-left dan popup-right */
    }

    .popup-left {
        border-radius: 10px 10px 0 0;
    }

    .popup-img {
        border-radius: 10px 10px 0 0;
    }
}

/* footer */
.footer {
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.logo img {
    height: auto;
    width: auto;
    max-height: 50px; /* Sesuaikan ukuran maksimal */
    max-width: 100%;
}

.logo-img {
    width: 60px;
    height: auto; /* Membuat ukuran gambar responsif */
}

.social-media {
    text-align: right;
}

.social-media span {
    display: block;
    font-size: 14px;
    margin-bottom: 8px;
    color: #fff;
}

.icons a {
    margin-left: 10px;
    color: #fff;
    font-size: 24px;
    text-decoration: none;
    transition: color 0.3s;
}

.icons a:hover {
    color: #1da1f2; /* Mengubah warna ikon saat hover */
}

/* Animasi memudar saat membuka */
.fade-in {
    animation: fadeIn 0.3s forwards;
}

/* Animasi memudar saat menutup */
.fade-out {
    animation: fadeOut 0.3s forwards;
}

/* Keyframes animasi */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

/* Additional responsive styles for other sections */
@media (max-width: 1200px) {
    .cards-container {
        flex-direction: column;
    }
    
    .cards {
        width: 100%;
    }
    
    .teks {
        width: 100%;
        height: auto;
        margin-top: 2rem;
    }
    
    .hero-content h1 {
        font-size: 4rem;
    }
}

@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 3rem;
    }
    
    .hero-content p {
        font-size: 1rem;
    }
    
    .card {
        min-width: 300px;
        min-height: 400px;
    }
    
    .popup-content {
        flex-direction: column;
        width: 90%;
    }
    
    .popup-left, .popup-right {
        width: 100%;
    }
    
    .popup-left {
        border-radius: 8px 8px 0 0;
    }
    
    .popup-img {
        border-radius: 8px 8px 0 0;
    }
    
    .submit-btn {
        margin-top: 2rem;
    }
}

@media (max-width: 576px) {
    header .container {
        flex-direction: column;
        align-items: center;
    }
    
    header .logo {
        position: relative;
        left: 0;
        transform: none;
        margin: 1rem 0;
    }
    
    header .left-nav, header .right-nav {
        margin-top: 0.5rem;
    }
    
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .hero-content {
        bottom: 1rem;
        left: 1rem;
    }
    
    .card {
        min-width: 250px;
        min-height: 350px;
    }
    
    .teks {
        font-size: 1.2rem;
    }
    
    .contact-btn {
        padding: 0.8rem 2rem;
        font-size: 1.2rem;
    }
}

    </style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Sight</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="hero">
    <header>
    <div class="container">
        <nav class="left-nav">
            <a href="#contact">Contact</a>
            <a href="#secabt">About</a>
        </nav>
        <div class="logo">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="logo-img">
        </div>
        <nav class="right-nav">
        @if (Auth::check())
                    <a href="{{ route('home') }}" class="login-btn">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="login-btn">Login</a>
                    @endif
        </nav>
    </div>
</header>

        <div class="hero-content">
    <h1><span>mempermudah</span><br><span>monitoring</span></h1>
    <p>Internship Sight menghadirkan solusi online yang dirancang untuk membantu</p> 
    <p>guru memantau perkembangan siswa magang secara real-time.</p>
</div>
</div>

<!-- section 1 -->

<section class="about" id="secabt">
    <div class="cards-container">
        <div class="cards">
            <div class="card"><img src="{{ asset('assets/foto2.jpg') }}" alt=""></div>
            <div class="card"><img src="{{ asset('assets/foto1.jpg') }}" alt=""></div>
        </div>
        <div class="teks">
            <p>
            InternSight adalah aplikasi monitoring PKL yang dirancang untuk mempermudah pengelolaan dan pemantauan kegiatan praktik kerja lapangan di sekolah. Dengan fitur pencatatan laporan, pemantauan progres, serta evaluasi berbasis data, InternSight membantu guru dalam mengawasi aktivitas murid, memastikan mereka menjalankan PKL sesuai rencana.

            <br><br>

            Aplikasi ini juga memudahkan murid dalam melaporkan kegiatan mereka secara real-time dan memungkinkan dudika (dunia usaha dan industri) memberikan umpan balik yang langsung terdokumentasi. Dengan sistem yang terstruktur, InternSight meningkatkan efisiensi pelaksanaan PKL, memastikan transparansi, serta mendukung integrasi digital dalam dunia pendidikan. 
            </p>
            <p class="teksb">Teks yang ingin ditampilkan di dalam kotak biru.</p>
        </div>
    </div>
</section>


<!-- section 2 -->
<section class="gallery"> 
    <h2 class="gallery-category">
        <span class="gallery-left">GALLERY</span>
        <span class="gallery-right">INTERNSHIP</span>
    </h2>

    <div class="gallery-container">
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/foto1.jpg') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/foto2.jpg') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/fotoo1.png') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/fotoo2.png') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/fotoo3.png') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/foto6.jpeg') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/foto7.jpeg') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/foto8.jpeg') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/foto9.jpeg') }}" class="gallery-thumb">
            </div>
        </div>
        <div class="gallery-card">
            <div class="gallery-image">
                <img src="{{ asset('assets/foto10.png') }}" class="gallery-thumb">
            </div>
        </div>
    </div>

    <div class="gallery-navigation">
        <button class="gallery-nav-btn gallery-prev-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="gallery-nav-btn gallery-next-btn"><i class="fas fa-chevron-right"></i></button>
    </div>
</section>
<h2 class="gallery-center">LEFT / RIGHT</h2>

<!-- section 3 -->
    <section class="cta">
    <h2 >Kenapa Tidak Bergabung?</h2>
    <p>Dengan kami, mempermudah guru untuk menjadi penghubung anak murid dengan dunia pekerjaan.</p>
    <div class="cta-image">
        <img src="{{ asset('assets/background.png') }}" alt="Card 2" class="card-img">
    </div>
    <button class="contact-btn" id="contact" onclick="openPopup()">Contact Us</button>

    <!-- Popup Modal -->
    <div class="popup-modal" id="contactPopup">
    <div class="popup-content">
        <div class="popup-header">
            <button class="close-btn" onclick="closePopup()">×</button>
        </div>

        <div class="popup-left">
            <img src="{{ asset('assets/contact us.jpeg') }}" alt="Contact Image" class="popup-img">
        </div>

        <div class="popup-right">
            <form action="https://api.web3forms.com/submit" method="POST">
                <input type="hidden" name="access_key" value="11439bd8-5392-4444-be53-a1aed7cfbcf5">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Your message" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send</button>
            </form>
        </div>
    </div>
</div>
</section>
    <footer class="footer">
        <div class="footer-content">
            <!-- Logo Section -->
            <div class="logo">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="logo-img">
            </div>
            <!-- Social Media Section -->
            <div class="social-media">
                <span>SOCIAL MEDIA</span>
                <div class="icons">
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const galleryContainer = document.querySelector('.gallery-container');
    const prevBtn = document.querySelector('.gallery-prev-btn');
    const nextBtn = document.querySelector('.gallery-next-btn');
    
    // Define scroll amount based on card width
    const scrollAmount = 420; // Card width + gap
    let autoScrollInterval;
    let isAutoScrolling = false;

    // Function to scroll gallery automatically
    function autoScroll() {
        galleryContainer.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
        // Reset position if gallery reaches the end
        if (galleryContainer.scrollLeft >= galleryContainer.scrollWidth - galleryContainer.clientWidth) {
            galleryContainer.scrollLeft = 0;
        }
    }

    // Function to start auto-scrolling
    function startAutoScroll() {
        if (!isAutoScrolling) {
            autoScrollInterval = setInterval(autoScroll, 3000); // Adjust interval time as needed
            isAutoScrolling = true;
        }
    }

    // Function to stop auto-scrolling
    function stopAutoScroll() {
        if (isAutoScrolling) {
            clearInterval(autoScrollInterval);
            isAutoScrolling = false;
        }
    }

    // Next button click event
    nextBtn.addEventListener('click', function() {
        stopAutoScroll();
        galleryContainer.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
        startAutoScroll();
    });
    
    // Previous button click event
    prevBtn.addEventListener('click', function() {
        stopAutoScroll();
        galleryContainer.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
        startAutoScroll();
    });
    
    // Enable keyboard navigation
    document.addEventListener('keydown', function(e) {
        stopAutoScroll();
        if (e.key === 'ArrowRight') {
            galleryContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        } else if (e.key === 'ArrowLeft') {
            galleryContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        }
        startAutoScroll();
    });
    
    // Enable touch swipe for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    galleryContainer.addEventListener('touchstart', function(e) {
        stopAutoScroll();
        touchStartX = e.changedTouches[0].screenX;
    });
    
    galleryContainer.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        startAutoScroll();
    });
    
    function handleSwipe() {
        if (touchStartX - touchEndX > 50) {
            // Swipe left, go next
            galleryContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        } else if (touchEndX - touchStartX > 50) {
            // Swipe right, go previous
            galleryContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        }
    }

    // Start auto-scrolling on page load
    startAutoScroll();
});

function openPopup() {
    const popup = document.getElementById('contactPopup');
    popup.style.display = 'block';  // Pastikan popup terlihat
    popup.classList.add('fade-in'); // Tambahkan animasi fade-in

    document.body.style.overflow = 'hidden';
}

function closePopup() {
    const popup = document.getElementById('contactPopup');
    popup.classList.remove('fade-in'); // Hapus animasi fade-in
    popup.classList.add('fade-out');   // Tambahkan animasi fade-out

    document.body.style.overflow = 'auto';

    // Tunggu hingga animasi selesai sebelum menyembunyikan popup
    setTimeout(() => {
        popup.style.display = 'none';
        popup.classList.remove('fade-out');
    }, 300);
}

window.addEventListener('click', (e) => {
    const popupModal = document.getElementById('contactPopup');
    const popupContent = document.querySelector('.popup-content');

    if (e.target === popupModal && !popupContent.contains(e.target)) {
        closePopup();
    }
});


// // section 2
const productContainers = [...document.querySelectorAll('.product-container')];
const nxtBtn = [...document.querySelectorAll('.nxt-btn')];
const preBtn = [...document.querySelectorAll('.pre-btn')];

productContainers.forEach((item, i) => {
    let containerDimensions = item.getBoundingClientRect();
    let containerWidth = containerDimensions.width;

    nxtBtn[i].addEventListener('click', () => {
        item.scrollLeft += containerWidth;
    })

    preBtn[i].addEventListener('click', () => {
        item.scrollLeft -= containerWidth;
    })
})

preButton.addEventListener('click', () => {
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
            updateSlider();
        });

        nxtButton.addEventListener('click', () => {
            currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
            updateSlider();
        });
</script>

<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>

<script>
    $(document).ready(function () {
        // Toggle sidebar ketika tombol minimizer diklik
        $('[data-widget="pushmenu"]').PushMenu();
    });
</script>


</html>