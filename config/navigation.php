<?php
return [
    'params' => [
        'navigation' => [
            'createDriver' => [
                'label' => 'Create Driver',
                'iconClass' => 'fa fa-edit',
                'url' => ['driver/registry-driver/create'],
                'isDirect' => false,
            ],
            'statusDriver' => [
                'label' => 'Status Driver',
                'iconClass' => 'fa fa-user-tie',
                'navigation' => [
                    'pndgDriver' => [
                        'label' => 'Pending Driver',
                        'url' => ['driver/registry-driver/index-pndg'],
                        'isDirect' => false,
                    ],
                    'icorctDriver'=> [
                        'label' => 'Incorrect Driver',
                        'url' => ['driver/registry-driver/index-icorct'],
                        'isDirect' => false,
                    ],
                    'rjctDriver'=> [
                        'label' => 'Reject Driver',
                        'url' => ['driver/registry-driver/index-rjct'],
                        'isDirect' => false,
                    ],
                ]
            ],
            'apprvDriver' => [
                'label' => 'Approval Driver',
                'iconClass' => 'fa fa-check',
                'navigation' => [
                    'apprvPndgDriver' => [
                        'label' => 'Pending',
                        'url' => ['driver/status-driver/pndg-data-driver'],
                        'isDirect' => false,
                    ],
                    'apprvIcorctDriver' => [
                        'label' => 'Incorrect',
                        'url' => ['driver/status-driver/icorct-data-driver'],
                        'isDirect' => false,
                    ]
                ]
            ],
            'driver'=> [
                'label' => 'Driver',
                'iconClass' => 'fa fa-users',
                'url' => ['driver/person-as-driver/index'],
                'isDirect' => false,
            ],
        ]
    ]
];