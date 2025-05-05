<?php

namespace App\Models\FormA;

use Illuminate\Database\Eloquent\Model;

class FormAAssySyringe extends Model
{
    protected $table = 'form_a_assy_syringe';

    protected $fillable = [
        'code_task',
        'tanggal',
        'sebelum_produk',
        'sebelum_bets',
        'sebelum_needle',
        'bersih_palet',
        'bersih_lantai',
        'bersih_kolong',
        'bersih_mesin',
        'bersih_grill',
        'sisa_lantai',
        'sisa_barrel',
        'sisa_plunger',
        'sisa_needle',
        'sisa_gasket',
        'sisa_starwhell',
        'sisa_box',
        'sisa_reject',
        'sisa_rework',
        'sebelum_dokumen',
        'material_sesuai',
        'saat_dokumen',
        'suhu',
        'kelembapan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'bersih_palet' => 'boolean',
        'bersih_lantai' => 'boolean',
        'bersih_kolong' => 'boolean',
        'bersih_mesin' => 'boolean',
        'bersih_grill' => 'boolean',
        'sisa_lantai' => 'boolean',
        'sisa_barrel' => 'boolean',
        'sisa_plunger' => 'boolean',
        'sisa_needle' => 'boolean',
        'sisa_gasket' => 'boolean',
        'sisa_starwhell' => 'boolean',
        'sisa_box' => 'boolean',
        'sisa_reject' => 'boolean',
        'sisa_rework' => 'boolean',
        'sebelum_dokumen' => 'boolean',
        'material_sesuai' => 'boolean',
        'saat_dokumen' => 'boolean',
        'suhu' => 'float',
        'kelembapan' => 'float'
    ];
}
