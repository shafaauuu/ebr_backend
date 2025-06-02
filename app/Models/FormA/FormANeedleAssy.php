<?php

namespace App\Models\FormA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormANeedleAssy extends Model
{
    use HasFactory;

    protected $table = 'form_a_needle_asy';
    protected $primaryKey = 'id';
    public $incrementing = true; // Keep this true because the table uses a sequence (auto-increment)
    protected $keyType = 'int';

    // Enable timestamps if using created_at and updated_at
    public $timestamps = true;

    protected $fillable = [
        'code_task',
        'tanggal',
        'sebelum_produk',
        'sebelum_bets',
        'sebelum_needle',
        'sebelum_cap',
        'bersih_palet',
        'bersih_lantai',
        'bersih_kolong',
        'bersih_mesin',
        'bersih_grill',
        'sisa_lantai',
        'sisa_hub',
        'sisa_cap',
        'sisa_canula',
        'sisa_box',
        'sisa_reject',
        'sisa_rework',
        'sebelum_dokumen',
        'material_sesuai',
        'saat_dokumen',
        'suhu',
        'kelembapan',
        'task_id'
    ];

    protected $casts = [
        'tanggal'         => 'date',
        'bersih_palet'    => 'boolean',
        'bersih_lantai'   => 'boolean',
        'bersih_kolong'   => 'boolean',
        'bersih_mesin'    => 'boolean',
        'bersih_grill'    => 'boolean',
        'sisa_lantai'     => 'boolean',
        'sisa_hub'        => 'boolean',
        'sisa_cap'        => 'boolean',
        'sisa_canula'     => 'boolean',
        'sisa_box'        => 'boolean',
        'sisa_reject'     => 'boolean',
        'sisa_rework'     => 'boolean',
        'sebelum_dokumen' => 'boolean',
        'material_sesuai' => 'boolean',
        'saat_dokumen'    => 'boolean',
        'suhu'            => 'float',
        'kelembapan'      => 'float',
    ];
}
