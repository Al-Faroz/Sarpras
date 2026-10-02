<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $allowedFields    = ['username', 'password', 'email', 'role', 'nama_lengkap', 'status_aktif'];
    
    // Nonaktifkan fitur auto-timestamp ci4 sementara karena kita pakai datetime default mysql
    protected $useTimestamps    = false; 
}