<?php

return [

    /**
     * The Client Persistence
     * This is the class name (including namespace) for the class that
     * conforms to the ClientPersistenceInterface or extends the AbstractClientPersistence
     * that is used by the APIAuth Client objects to store and retrieve Client records.
     */
    'Client Persistence' => 'EloquentClientPersistence',

    /**
     * The Session Persistence
     * This is the class name (including namespace) for the class that
     * conforms to the SessionPersistenceInterface or extends the AbstractSessionPersistence
     * that is used by the APIAuth Session objects to store and retrieve Session records.
     */
    'Session Persistence' => 'EloquentSessionPersistence',
];