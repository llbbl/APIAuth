<?php

return [

    /*
     * The Client Persistence
     * This is the class name (including namespace) for the class that
     * conforms to the ClientPersistenceInterface or extends the AbstractClientPersistence
     * that is used by the APIAuth Client objects to store and retrieve Client records.
     */
    'Client Persistence' => 'EloquentClientPersistence',

    /*
     * The Session Persistence
     * This is the class name (including namespace) for the class that
     * conforms to the SessionPersistenceInterface or extends the AbstractSessionPersistence
     * that is used by the APIAuth Session objects to store and retrieve Session records.
     */
    'Session Persistence' => 'EloquentSessionPersistence',

    /*
     * The Field Token Generator
     * This is the class name (including namespace) for the class that
     * conforms to teh TokenFieldGeneratorInterface that creates the hash tokens
     * for the various used token fields such as Client, Session, User
     */
     'Token Field Generator' => 'eig\APIAuth\Tokens\TokenFieldGenerator',

    /*
     * All options for the JWT (JSON Web Token) settings
     */
    'JWT' => [

        /*
         * Who is issuing the token
         */
        'Issuer' => 'http://api.example.com',

        /*
         * Who is recieving the token
         */
        'Audience' => 'http://example.com',



    ]

];