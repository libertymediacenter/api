<?php

namespace App\Observers;

use App\Models\Season;

class SeasonObserver
{
    /**
     * Handle the season "created" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function created(Season $season)
    {
        //
    }

    /**
     * Handle the season "updated" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function updated(Season $season)
    {
        //
    }

    /**
     * Handle the season "deleted" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function deleted(Season $season)
    {
        //
    }

    /**
     * Handle the season "restored" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function restored(Season $season)
    {
        //
    }

    /**
     * Handle the season "force deleted" event.
     *
     * @param  \App\Models\Season  $season
     * @return void
     */
    public function forceDeleted(Season $season)
    {
        //
    }
}
