<?php

namespace App\Observers;

use App\Models\Show;

class ShowObserver
{
    /**
     * Handle the show "created" event.
     *
     * @param  \App\Models\Show  $show
     * @return void
     */
    public function created(Show $show)
    {
        //
    }

    /**
     * Handle the show "updated" event.
     *
     * @param  \App\Models\Show  $show
     * @return void
     */
    public function updated(Show $show)
    {
        //
    }

    /**
     * Handle the show "deleted" event.
     *
     * @param  \App\Models\Show  $show
     * @return void
     */
    public function deleted(Show $show)
    {
        //
    }

    /**
     * Handle the show "restored" event.
     *
     * @param  \App\Models\Show  $show
     * @return void
     */
    public function restored(Show $show)
    {
        //
    }

    /**
     * Handle the show "force deleted" event.
     *
     * @param  \App\Models\Show  $show
     * @return void
     */
    public function forceDeleted(Show $show)
    {
        //
    }
}
