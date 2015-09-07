<?php

namespace Spatie\Activitylog;

use Activity;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        foreach (static::getRecordActivityEvents() as $eventName) {
            static::$eventName(function (LogsActivityInterface $model) use ($eventName) {

                $message = $model->getActivityDescriptionForEvent($eventName);

                // Integration with lucadegasperi/oauth2-server-laravel
                $user_id = \Authorizer::getChecker()->getAccessToken() ? \Authorizer::getResourceOwnerId() : false;

                if ($message != '') {
                    Activity::log($message, $user_id);
                }
            });
        }
    }

    /**
     * Set the default events to be recorded if the $recordEvents
     * property does not exist on the model.
     *
     * @return array
     */
    protected static function getRecordActivityEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return [
            'created', 'updated', 'deleting', 'deleted',
        ];
    }
}
