<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('../data/diem_thi_thpt_2024.csv');

        if (!File::exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            $this->command->info("Please copy the CSV file from g-scores-backend-ts/data/ to g-scores-backend/data/");
            return;
        }

        $this->command->info("Starting to seed students from CSV...");

        // Clear existing data
        $this->command->info("Clearing existing student data...");
        Student::truncate();

        // Read and parse CSV
        $file = fopen($csvFile, 'r');
        $headers = fgetcsv($file); // Read header row

        if (!$headers) {
            $this->command->error("Failed to read CSV headers");
            return;
        }

        $batchSize = 1000;
        $batch = [];
        $count = 0;
        $total = 0;

        while (($row = fgetcsv($file)) !== false) {
            // Map CSV columns to database fields
            $data = array_combine($headers, $row);

            $student = [
                'sbd' => $data['sbd'] ?? null,
                'toan' => $this->parseScore($data['toan'] ?? null),
                'ngu_van' => $this->parseScore($data['ngu_van'] ?? null),
                'ngoai_ngu' => $this->parseScore($data['ngoai_ngu'] ?? null),
                'vat_li' => $this->parseScore($data['vat_li'] ?? null),
                'hoa_hoc' => $this->parseScore($data['hoa_hoc'] ?? null),
                'sinh_hoc' => $this->parseScore($data['sinh_hoc'] ?? null),
                'lich_su' => $this->parseScore($data['lich_su'] ?? null),
                'dia_li' => $this->parseScore($data['dia_li'] ?? null),
                'gdcd' => $this->parseScore($data['gdcd'] ?? null),
                'ma_ngoai_ngu' => $data['ma_ngoai_ngu'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Skip if SBD is empty
            if (empty($student['sbd'])) {
                continue;
            }

            $batch[] = $student;
            $count++;

            // Insert batch when it reaches the batch size
            if ($count >= $batchSize) {
                DB::table('students')->insert($batch);
                $total += $count;
                $this->command->info("Inserted {$total} students...");
                $batch = [];
                $count = 0;
            }
        }

        // Insert remaining records
        if (!empty($batch)) {
            DB::table('students')->insert($batch);
            $total += $count;
        }

        fclose($file);

        $this->command->info("Successfully seeded {$total} students!");
    }

    /**
     * Parse score value (handle empty strings and convert to float or null)
     */
    private function parseScore($value): ?float
    {
        if ($value === null || $value === '' || $value === 'NULL') {
            return null;
        }

        $score = floatval($value);
        
        // Validate score range
        if ($score < 0 || $score > 10) {
            return null;
        }

        return $score;
    }
}
