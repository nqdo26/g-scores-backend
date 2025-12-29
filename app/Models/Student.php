<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'sbd',
        'toan',
        'ngu_van',
        'ngoai_ngu',
        'vat_li',
        'hoa_hoc',
        'sinh_hoc',
        'lich_su',
        'dia_li',
        'gdcd',
        'ma_ngoai_ngu',
    ];

    protected $casts = [
        'toan' => 'float',
        'ngu_van' => 'float',
        'ngoai_ngu' => 'float',
        'vat_li' => 'float',
        'hoa_hoc' => 'float',
        'sinh_hoc' => 'float',
        'lich_su' => 'float',
        'dia_li' => 'float',
        'gdcd' => 'float',
    ];

    public function getScoresAttribute(): array
    {
        return [
            'toan' => $this->toan,
            'ngu_van' => $this->ngu_van,
            'ngoai_ngu' => $this->ngoai_ngu,
            'vat_li' => $this->vat_li,
            'hoa_hoc' => $this->hoa_hoc,
            'sinh_hoc' => $this->sinh_hoc,
            'lich_su' => $this->lich_su,
            'dia_li' => $this->dia_li,
            'gdcd' => $this->gdcd,
            'ma_ngoai_ngu' => $this->ma_ngoai_ngu,
        ];
    }

    public function getGroupATotalAttribute(): ?float
    {
        if ($this->toan !== null && $this->vat_li !== null && $this->hoa_hoc !== null) {
            return round($this->toan + $this->vat_li + $this->hoa_hoc, 1);
        }
        return null;
    }

    public function hasGroupAScores(): bool
    {
        return $this->toan !== null && $this->vat_li !== null && $this->hoa_hoc !== null;
    }
}
