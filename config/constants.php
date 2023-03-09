<?php

return [
    'pagination' => [
        'items_per_page' => 15,
    ],
    'uipath' => [
        'orchestrator' => [
            'cloud' => [
                'url_prefix' => 'https://cloud.uipath.com',
            ],
            'identity_server_token_endpoint' => [
                'cloud' => '%s/identity_/connect/token',
                'on_premise' => '%s/identity/connect/token',
                'default_scopes' => 'OR.Administration.Read OR.Assets.Read OR.Execution.Read OR.Folders.Read OR.Jobs.Read OR.License.Read OR.Machines.Read OR.Monitoring OR.Queues.Read OR.Robots.Read OR.Settings.Read OR.Users.Read OR.Webhooks',
                'timeout' => 3,
            ],
            'api_endpoint' => [
                'suffixes' => [
                    'cloud' => [
                        'folders' => [
                            'all' => '/%s/orchestrator_/odata/Folders',
                        ],
                        'releases' => [
                            'all' => '/%s/orchestrator_/odata/Releases',
                        ],
                        'machines' => [
                            'all' => '/%s/orchestrator_/odata/Machines/UiPath.Server.Configuration.OData.GetAssignedMachines(folderId=%s)',
                        ],
                        'queue_definitions' => [
                            'all' => '/%s/orchestrator_/odata/QueueDefinitions',
                        ],
                        'sessions' => [
                            'all' => '/%s/orchestrator_/odata/Sessions',
                        ],
                        'alerts' => [
                            'all' => '/%s/orchestrator_/odata/Alerts?$filter=State eq Unread',
                        ],
                        'webhooks' => [
                            'all' => '/%s/orchestrator_/odata/Webhooks',
                            'remove' => '/%s/orchestrator_/odata/Webhooks(%s)',
                        ],
                    ],
                    'on_premise' => [
                        'folders' => [
                            'all' => '/odata/Folders',
                        ],
                        'releases' => [
                            'all' => '/odata/Releases',
                        ],
                        'machines' => [
                            'all' => '/odata/Machines/UiPath.Server.Configuration.OData.GetAssignedMachines(folderId=%s)',
                        ],
                        'queue_definitions' => [
                            'all' => '/odata/QueueDefinitions',
                        ],
                        'sessions' => [
                            'all' => '/odata/Sessions',
                        ],
                        'alerts' => [
                            'all' => '/odata/Alerts?$filter=State eq Unread',
                        ],
                        'webhooks' => [
                            'all' => '/odata/Webhooks',
                            'remove' => '/odata/Webhooks(%s)',
                        ],
                    ]
                ]
            ]
        ],
        'elasticsearch' => [
            'default_index_configuration' => 'index="${event-properties:item=indexName}-${date:format=yyyy.MM}',
        ]
    ]
];
