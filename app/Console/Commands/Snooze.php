<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Preference;

class Snooze extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snooze';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Snooze';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hari   = strtolower(now()->format('l'));
        $jam    = now()->format('H:i');

        $trans  = [
            'sunday'    => ['Minggu', 'Setiap Hari'],
            'monday'    => ['Senin', 'Setiap Hari'],
            'tuesday'   => ['Selasa', 'Setiap Hari'],
            'wednesday' => ['Rabu', 'Setiap Hari'],
            'thursday'  => ['Kamis', 'Setiap Hari'],
            'friday'    => ['Jumat', 'Setiap Hari'],
            'saturday'  => ['Sabtu', 'Setiap Hari'],
        ];

        $pr     = Preference::whereIn('hari', $trans[$hari])->where('jam', $jam)->update(['snooze' => false]);

        return 1;
    }
}
