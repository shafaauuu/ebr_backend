<?php

namespace App\Models\FormA;

use Illuminate\Database\Eloquent\Model;

class FormABlister extends Model
{
    protected $table = 'form_a_blister'; // explicitly define the table name

    protected $primaryKey = 'id';

    public $timestamps = true; // created_at and updated_at will be managed

    protected $fillable = [
        'code_task',
        'tanggal',
        'sebelum_produk',
        'sebelum_bets',
        'bersih_lantai',
        'bersih_dinding',
        'bersih_grill',
        'bersih_alat',
        'sisa_produk',
        'sebelum_dokumen',
        'material_sesuai',
        'saat_dokumen',
        'suhu',
        'kelembapan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'bersih_lantai' => 'boolean',
        'bersih_dinding' => 'boolean',
        'bersih_grill' => 'boolean',
        'bersih_alat' => 'boolean',
        'sisa_produk' => 'boolean',
        'sebelum_dokumen' => 'boolean',
        'material_sesuai' => 'boolean',
        'saat_dokumen' => 'boolean',
        'suhu' => 'float',
        'kelembapan' => 'float',
    ];
}
