<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek apakah user sudah login atau belum
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'Akses diblokir! Silakan login terlebih dahulu.');
            return redirect()->to('/auth');
        }

        // 2. Cek apakah role user diizinkan mengakses halaman tersebut (Bypass Enforcement)
        if ($arguments && !in_array(session()->get('role'), $arguments)) {
            // Sesuai bagan: Redirect ke dashboard masing-masing + pesan SweetAlert/Flashdata
            session()->setFlashdata('error', 'Akses Ditolak! Anda tidak memiliki otoritas untuk membuka halaman tersebut.');
            $role = session()->get('role');
            
            return redirect()->to($role === 'admin' ? '/admin/dashboard' : '/operator/dashboard');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah proses selesai
    }
}