<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

use Carbon\Carbon;

class PortalDeactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:deactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactive portals when expiration date.';

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
     * @return mixed
     */
    public function handle()
    {
        $portals = User::whereType('company')->where('is_active', 1)->get();
        
        foreach ($portals as $item) {
            $timezone = $item->timezone ?? config('app.timezone');
            $current = Carbon::now($timezone);
            if($item->expire_date) {
                $expire_date = Carbon::create($item->expire_date, $timezone);
                if($current->isAfter($expire_date)) {
                    $item->update(['is_active' => 0]);
                    $item->allMedia()->update(['is_active' => 0]);
                    $item->allTagged()->update(['is_active' => 0]);
                    if ($item->media) {
                        $item->media->update(['is_active' => 0]);
                    }  
                }
            }
        }
    }
}
