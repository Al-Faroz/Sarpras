<?php

namespace App\Controllers;
use App\Models\UserModel;

class AdminUsers extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel->findAll();
        // Memanggil view dengan nama yang spesifik
        return view('admin/users/user_display', $data); 
    }

    public function tambah()
    {
        return view('admin/users/user_tambah');
    }

    public function simpan()
    {
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'role'         => $this->request->getPost('role'),
            'password'     => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT)
        ];

        $this->userModel->insert($data);
        session()->setFlashdata('pesan', 'Data Pengguna berhasil ditambahkan.');
        return redirect()->to('/admin/users');
    }

    public function edit($id)
    {
        $data['user'] = $this->userModel->find($id);
        return view('admin/users/user_edit', $data);
    }

    public function update($id)
    {
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'role'         => $this->request->getPost('role')
        ];

        $password_baru = $this->request->getPost('password');
        if (!empty($password_baru)) {
            $data['password'] = password_hash((string) $password_baru, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);
        session()->setFlashdata('pesan', 'Data Pengguna berhasil diubah.');
        return redirect()->to('/admin/users');
    }

    public function hapus($id)
    {
        $this->userModel->delete($id);
        session()->setFlashdata('pesan', 'Data Pengguna berhasil dihapus.');
        return redirect()->to('/admin/users');
    }
}