<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =========================================================================
// LANDING PAGE — Publik
// =========================================================================
$routes->get('/', 'LandingPage::index');
$routes->get('landing/form_pinjam', 'LandingPage::form_pinjam');
$routes->post('landing/kirim_pinjam', 'LandingPage::kirim_pinjam');
$routes->get('landing/form_lapor', 'LandingPage::form_lapor');
$routes->post('landing/kirim_lapor', 'LandingPage::kirim_lapor');
$routes->get('scanner', 'LandingPage::scanner');

// =========================================================================
// AUTENTIKASI
// =========================================================================
$routes->get('auth', 'Auth::index');
$routes->post('auth/proses_login', 'Auth::proses_login');
$routes->get('auth/logout', 'Auth::logout');

// =========================================================================
// QR CODE SCAN — Publik
// =========================================================================
$routes->get('scan/(:num)', 'ScanController::index/$1');

// =========================================================================
// DASHBOARD
// =========================================================================
$routes->get('admin/dashboard',    'AdminDashboard::index', ['filter' => 'authFilter:admin']);
$routes->get('operator/dashboard', 'AdminDashboard::index', ['filter' => 'authFilter:operator']);

// =========================================================================
// MODUL USERS — admin only
// =========================================================================
$routes->get('admin/users',                'AdminUsers::index',     ['filter' => 'authFilter:admin']);
$routes->get('admin/users/tambah',         'AdminUsers::tambah',    ['filter' => 'authFilter:admin']);
$routes->post('admin/users/simpan',        'AdminUsers::simpan',    ['filter' => 'authFilter:admin']);
$routes->get('admin/users/edit/(:num)',    'AdminUsers::edit/$1',   ['filter' => 'authFilter:admin']);
$routes->post('admin/users/update/(:num)', 'AdminUsers::update/$1', ['filter' => 'authFilter:admin']);
$routes->get('admin/users/hapus/(:num)',   'AdminUsers::hapus/$1',  ['filter' => 'authFilter:admin']);

// =========================================================================
// MODUL INVENTARIS — admin & operator
// =========================================================================
$routes->get('admin/inventaris',                 'AdminInventaris::index',     ['filter' => 'authFilter:admin,operator']);
$routes->get('operator/inventaris',              'AdminInventaris::index',     ['filter' => 'authFilter:operator']);
$routes->get('admin/inventaris/tambah',          'AdminInventaris::tambah',    ['filter' => 'authFilter:admin,operator']);
$routes->post('admin/inventaris/simpan',         'AdminInventaris::simpan',    ['filter' => 'authFilter:admin,operator']);
$routes->get('admin/inventaris/edit/(:num)',     'AdminInventaris::edit/$1',   ['filter' => 'authFilter:admin,operator']);
$routes->post('admin/inventaris/update/(:num)',  'AdminInventaris::update/$1', ['filter' => 'authFilter:admin,operator']);
$routes->get('admin/inventaris/hapus/(:num)',    'AdminInventaris::hapus/$1',  ['filter' => 'authFilter:admin,operator']);
$routes->get('admin/inventaris/cetak_qr/(:num)','AdminInventaris::cetak_qr/$1',['filter' => 'authFilter:admin,operator']);

// Scan Aset
$routes->get('admin/scanner',    'AdminInventaris::scanner', ['filter' => 'authFilter:admin,operator']);
$routes->get('operator/scanner', 'AdminInventaris::scanner', ['filter' => 'authFilter:operator']);

// Print QR Batch
$routes->get('admin/inventaris/print_qr_batch',   'AdminInventaris::print_qr_batch',  ['filter' => 'authFilter:admin,operator']);
$routes->post('admin/inventaris/proses_print_qr', 'AdminInventaris::proses_print_qr', ['filter' => 'authFilter:admin,operator']);

// AJAX Inventaris
$routes->get('admin/inventaris/ajax_kode',           'AdminInventaris::ajax_kode',           ['filter' => 'authFilter:admin,operator']);
$routes->get('admin/inventaris/ajax_preview_no_inv', 'AdminInventaris::ajax_preview_no_inv', ['filter' => 'authFilter:admin,operator']);

// Import DIPA
$routes->get('admin/inventaris/import_dipa',          'AdminInventaris::import_dipa',        ['filter' => 'authFilter:admin,operator']);
$routes->post('admin/inventaris/proses_import_dipa',  'AdminInventaris::proses_import_dipa', ['filter' => 'authFilter:admin,operator']);
$routes->get('admin/inventaris/hasil_import_dipa',    'AdminInventaris::hasil_import_dipa',  ['filter' => 'authFilter:admin,operator']);
$routes->get('admin/inventaris/template_dipa',        'AdminInventaris::template_dipa',      ['filter' => 'authFilter:admin,operator']);

// =========================================================================
// MODUL PEMINJAMAN — admin & operator
// =========================================================================
$routes->get('admin/peminjaman',                 'AdminPeminjaman::index',     ['filter' => 'authFilter:admin,operator']);
$routes->get('operator/peminjaman',              'AdminPeminjaman::index',     ['filter' => 'authFilter:operator']);
$routes->get('admin/peminjaman/edit/(:num)',     'AdminPeminjaman::edit/$1',   ['filter' => 'authFilter:admin,operator']);
$routes->post('admin/peminjaman/update/(:num)',  'AdminPeminjaman::update/$1', ['filter' => 'authFilter:admin,operator']);
$routes->get('admin/peminjaman/hapus/(:num)',    'AdminPeminjaman::hapus/$1',  ['filter' => 'authFilter:admin,operator']);

// =========================================================================
// MODUL PEMELIHARAAN — admin only
// =========================================================================
$routes->get('admin/pemeliharaan',                'AdminPemeliharaan::index',    ['filter' => 'authFilter:admin']);
$routes->get('admin/pemeliharaan/tambah',         'AdminPemeliharaan::tambah',   ['filter' => 'authFilter:admin']);
$routes->post('admin/pemeliharaan/simpan',        'AdminPemeliharaan::simpan',   ['filter' => 'authFilter:admin']);
$routes->get('admin/pemeliharaan/edit/(:num)',    'AdminPemeliharaan::edit/$1',  ['filter' => 'authFilter:admin']);
$routes->post('admin/pemeliharaan/update/(:num)', 'AdminPemeliharaan::update/$1',['filter' => 'authFilter:admin']);
$routes->get('admin/pemeliharaan/hapus/(:num)',   'AdminPemeliharaan::hapus/$1', ['filter' => 'authFilter:admin']);

// =========================================================================
// MODUL LAPORAN KERUSAKAN — admin & operator
// =========================================================================
$routes->get('admin/laporan',                'AdminLaporan::index',     ['filter' => 'authFilter:admin,operator']);
$routes->get('operator/laporan',             'AdminLaporan::index',     ['filter' => 'authFilter:operator']);
$routes->get('admin/laporan/edit/(:num)',    'AdminLaporan::edit/$1',   ['filter' => 'authFilter:admin,operator']);
$routes->post('admin/laporan/update/(:num)', 'AdminLaporan::update/$1', ['filter' => 'authFilter:admin,operator']);
$routes->get('admin/laporan/hapus/(:num)',   'AdminLaporan::hapus/$1',  ['filter' => 'authFilter:admin,operator']);

// =========================================================================
// EXPORT PDF & EXCEL
// =========================================================================
$routes->get('export/inventaris_pdf',    'Export::inventaris_pdf',    ['filter' => 'authFilter:admin,operator']);
$routes->get('export/inventaris_excel',  'Export::inventaris_excel',  ['filter' => 'authFilter:admin,operator']);
$routes->get('export/peminjaman_pdf',    'Export::peminjaman_pdf',    ['filter' => 'authFilter:admin,operator']);
$routes->get('export/peminjaman_excel',  'Export::peminjaman_excel',  ['filter' => 'authFilter:admin,operator']);
$routes->get('export/pemeliharaan_pdf',  'Export::pemeliharaan_pdf',  ['filter' => 'authFilter:admin']);
$routes->get('export/pemeliharaan_excel','Export::pemeliharaan_excel',['filter' => 'authFilter:admin']);
$routes->get('export/laporan_pdf',       'Export::laporan_pdf',       ['filter' => 'authFilter:admin,operator']);
$routes->get('export/laporan_excel',     'Export::laporan_excel',     ['filter' => 'authFilter:admin,operator']);

// =========================================================================
// NOTIFIKASI AJAX — admin & operator
// =========================================================================
$routes->get('ajax/notifikasi', 'AdminDashboard::ajax_notifikasi', ['filter' => 'authFilter:admin,operator']);
