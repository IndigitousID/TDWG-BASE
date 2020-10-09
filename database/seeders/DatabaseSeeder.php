<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        if (($handle = fopen(database_path('/seeders/csv/resource.csv'), 'r')) !== FALSE) 
		{
			$header         = null;

			while (($data = fgetcsv($handle, 500, ",")) !== FALSE) 
			{
				if ($header === null) 
				{
					$header = $data;
					continue;
				}
			
				$all_row    = array_combine($header, $data);
				try {
					\DB::beginTransaction();
						$user   = \App\Models\User::first();

						$konten = \App\Models\Resource::create([
							'user_id' 	=> $user->id, 
							'judul'		=> $all_row['judul'],
							'direktori' => $all_row['direktori'],
							'subdirektori'	=> $all_row['subdirektori'],
							'konten'		=> $all_row['konten'],
							'media_tipe'	=> $all_row['media_tipe'],
							'media_url' 	=> $all_row['media_url'],
							'published_at'	=> now()
						]);

					\DB::commit();
				}catch(\Exception $e){
					// dd($all_row);
					dd($e->getMessage());
				}
			}
		}
    }
}
