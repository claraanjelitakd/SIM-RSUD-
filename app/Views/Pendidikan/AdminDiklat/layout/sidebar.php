<?php
$menu = $menu ?? 'dashboard';
$subMenu = $subMenu ?? '';
?>
<div class="sidebar" id="sidebarMenu">
    <div class="d-flex flex-column">
        <div class="px-4 mb-4 mt-2">
            <p class="text-uppercase small fw-bold text-muted mb-2">Menu Admin</p>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link <?= ($menu === 'dashboard') ? 'active' : '' ?>" href="<?= base_url('pendidikan/admin/diklat/dashboard') ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            
            <div class="px-4 mb-2 mt-4">
                <p class="text-uppercase small fw-bold text-muted mb-0">Manajemen</p>
            </div>
            
            <a class="nav-link <?= ($menu === 'institusi') ? 'active' : '' ?>" href="<?= base_url('pendidikan/admin/diklat/institusi') ?>">
                <i class="fas fa-university"></i> Institusi
            </a>
            
            <a class="nav-link <?= ($menu === 'ci') ? 'active' : '' ?>" href="<?= base_url('pendidikan/admin/diklat/ci') ?>">
                <i class="fas fa-user-md"></i> Clinical Instructor
            </a>
            
            <a class="nav-link <?= ($menu === 'stase') ? 'active' : '' ?>" href="<?= base_url('pendidikan/admin/diklat/stase') ?>">
                <i class="fas fa-route"></i> Stase
            </a>
            
            <a class="nav-link <?= ($menu === 'pengajuan') ? 'active' : '' ?>" href="<?= base_url('pendidikan/admin/diklat/pengajuan') ?>">
                <i class="fas fa-file-alt"></i> Pengajuan
            </a>

            <a class="nav-link <?= ($menu === 'user') ? 'active' : '' ?>" href="<?= base_url('pendidikan/admin/diklat/user') ?>">
                <i class="fas fa-users"></i> Akun
            </a>
            
            <div class="px-4 mb-2 mt-4">
                <p class="text-uppercase small fw-bold text-muted mb-0">Sistem</p>
            </div>

            <a class="nav-link" href="<?= base_url('pendidikan/logout') ?>">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </nav>
    </div>
</div>
<div class="main-content">
    <div class="container-fluid px-4">
