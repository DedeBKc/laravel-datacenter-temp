<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SyncCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->syncData();
            \Log::info('Cron command executed at: ' . date('H:i:s'));
        } catch (\Exception $e) {
            \Log::error('Sync failed: ' . $e->getMessage());
        }
    }

    public function syncData()
    {
        $cacheKey = 'sync_last_id';
        $lastSyncedEmpNo = Cache::get($cacheKey, 0); // Ambil ID terakhir dari cache
        $hasMore = true;
        $baseUrl = env('APP_URL');

        \Log::info($baseUrl);
        // \Log::info('Starting sync from emp_no: ' . $lastSyncedEmpNo);

        // Menyimpan status apakah proses sync berhasil atau tidak
        $isSyncSuccessful = false;

        do {
            // Mengambil data dari API
            $response = Http::get("{$baseUrl}/employees/{$lastSyncedEmpNo}/50");

            if ($response->ok()) {
                $data = $response->json();

                // Jika tidak ada data baru, hentikan loop
                if (empty($data['data'])) {
                    $hasMore = false;
                    break;
                }

                // Simpan data ke database
                foreach ($data['data'] as $item) {
                    \DB::table('center_employees')->updateOrInsert(
                        ['emp_no' => $item['emp_no']], // Kondisi unik
                        [
                            'birth_date' => $item['birth_date'],
                            'first_name' => $item['first_name'],
                            'last_name' => $item['last_name'],
                            'gender' => $item['gender'],
                            'hire_date' => $item['hire_date'],
                        ]
                    );

                    // Perbarui ID terakhir
                    $lastSyncedEmpNo = $item['emp_no'];
                }

                // Simpan ID terakhir ke cache untuk sinkronisasi berikutnya
                Cache::put($cacheKey, $lastSyncedEmpNo);

                // Periksa apakah masih ada data lebih lanjut
                $hasMore = $data['has_more'] ?? false;

                // Tandai bahwa proses sync berhasil
                $isSyncSuccessful = true;

                \Log::info('Sync successful. Last emp_no synced: ' . $lastSyncedEmpNo);
            } else {
                // Log error jika API gagal
                \Log::error("Failed to fetch data: " . $response->body());
                $hasMore = false; // Hentikan loop jika API gagal
            }
        } while ($hasMore && !$isSyncSuccessful);

        // Jika gagal setelah beberapa percobaan, log error
        if (!$isSyncSuccessful) {
            \Log::error('Sync failed after multiple attempts.');
        }
    }
}
