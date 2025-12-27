<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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

    /**
     * Get all scores as an associative array
     */
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

    /**
     * Calculate Group A total score (Math + Physics + Chemistry)
     */
    public function getGroupATotalAttribute(): ?float
    {
        if ($this->toan !== null && $this->vat_li !== null && $this->hoa_hoc !== null) {
            return round($this->toan + $this->vat_li + $this->hoa_hoc, 1);
        }
        return null;
    }

    /**
     * Check if student has all Group A subjects
     */
    public function hasGroupAScores(): bool
    {
        return $this->toan !== null && $this->vat_li !== null && $this->hoa_hoc !== null;
    }
}
