<?php

namespace App\Providers;

use App\Events\ComplaintCreated;
use App\Events\ComplaintStatusChanged;
use App\Events\AnnouncementCreated;
use App\Listeners\SendComplaintNotificationToAdmin;
use App\Listeners\SendStatusChangeNotificationToUser;
use App\Listeners\SendAnnouncementNotificationToAll;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ComplaintCreated::class => [
            SendComplaintNotificationToAdmin::class,
        ],
        ComplaintStatusChanged::class => [
            SendStatusChangeNotificationToUser::class,
        ],
        AnnouncementCreated::class => [
            SendAnnouncementNotificationToAll::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
