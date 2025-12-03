<?php
// Mevcut sayfa ismini al (Active class eklemek için)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* --- YENİ TAŞIYICI (WRAPPER) --- */
    /* Konumlama işlemlerini artık bu sınıf yapıyor */
    .nav-wrapper {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        /* Alt alta diz */
        align-items: center;
        /* Ortala */
        gap: 12px;
        /* Logo ile Menü arasındaki boşluk */
    }

    /* --- LOGO STİLİ --- */
    .nav-logo {
        height: 40px;
        /* Logo yüksekliğini buradan ayarlayabilirsin */
        width: auto;
        /* İsteğe bağlı: Logo arkasına hafif gölge veya filtre */
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    /* --- NAVİGASYON STİLİ (GÜNCELLENDİ) --- */
    .floating-nav {
        /* position, top, left sildik çünkü artık wrapper yönetiyor */
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 6px;
        border-radius: 50px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.8);
        display: flex;
        gap: 5px;
        width: auto;
        white-space: nowrap;
    }

    .nav-item {
        text-decoration: none;
        color: #64748b;
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 40px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .nav-item:hover {
        background-color: #f1f5f9;
        color: #334155;
    }

    /* Aktif Sayfa Stili */
    .nav-item.active {
        background-color: #1e293b;
        color: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Mobil Uyumluluk */
    @media (max-width: 1000px) {
        .nav-wrapper {
            top: auto;
            bottom: 30px;
            /* Mobilde tüm yapıyı aşağı al */
            width: 90%;
            /* Mobilde genişlik ayarı */
        }

        .floating-nav {
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
        }
    }
</style>

<div class="nav-wrapper" style="margin-top: 1.5em;">

    <img src="logo.svg" alt="Logo" class="nav-logo">

    <div class="floating-nav">

        <a href="TransactionsCount.php" class="nav-item <?= $current_page == 'TransactionsCount.php' ? 'active' : '' ?>">
            Satış Analizi
        </a>

        <a href="TransactionsAverage.php" class="nav-item <?= $current_page == 'TransactionsAverage.php' ? 'active' : '' ?>">
            Ort. Fiyat Analizi
        </a>

        <a href="TransactionsSqm.php" class="nav-item <?= $current_page == 'TransactionsSqm.php' ? 'active' : '' ?>">
            m² Fiyatı Analizi
        </a>

        <div style="width:1px; background:#cbd5e1; margin:0 5px;"></div>

        <a href="ProjectsCount.php" class="nav-item <?= $current_page == 'ProjectsCount.php' ? 'active' : '' ?>">
            Proje Adedi
        </a>

        <a href="ProjectAverage.php" class="nav-item <?= $current_page == 'ProjectAverage.php' ? 'active' : '' ?>">
            Proje Değerleri
        </a>

        <a href="ProjectDeveloper.php" class="nav-item <?= $current_page == 'ProjectDeveloper.php' ? 'active' : '' ?>">
            Geliştiriciler
        </a>
    </div>
</div>