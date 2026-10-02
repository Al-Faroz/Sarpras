<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika user sudah login, langsung arahkan ke dashboard masing-masing role
        if (session()->get('logged_in')) {
            $role = session()->get('role');
            return redirect()->to($role === 'admin' ? '/admin/dashboard' : '/operator/dashboard');
        }
        
        // Menampilkan view halaman login
        return view('auth/login_view');
    }

    public function proses_login()
    {
        $session = session();
        $model = new UserModel();
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cari user berdasarkan username
        $dataUser = $model->where('username', $username)->first();

        if ($dataUser) {
            // Cek apakah akun aktif
            if ($dataUser['status_aktif'] == 1) {
                // Verifikasi password hash
                $verify_pass = password_verify($password, $dataUser['password']);
                
                if ($verify_pass) {
                    // Jika password cocok, daftarkan data ke Session
                    $ses_data = [
                        'id_user'       => $dataUser['id_user'],
                        'username'      => $dataUser['username'],
                        'role'          => $dataUser['role'],
                        'nama_lengkap'  => $dataUser['nama_lengkap'],
                        'logged_in'     => TRUE
                    ];
                    $session->set($ses_data);
                    
                    // Redirect sesuai Role
                    return redirect()->to($dataUser['role'] === 'admin' ? '/admin/dashboard' : '/operator/dashboard');
                } else {
                    $session->setFlashdata('error', 'Password yang Anda masukkan salah!');
                    return redirect()->to('/auth');
                }
            } else {
                $session->setFlashdata('error', 'Akun Anda dinonaktifkan. Hubungi Admin!');
                return redirect()->to('/auth');
            }
        } else {
            $session->setFlashdata('error', 'Username tidak ditemukan!');
            return redirect()->to('/auth');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/auth');
    }
}