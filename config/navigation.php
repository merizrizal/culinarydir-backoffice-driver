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
                        'label' => 'Pending',
                        'url' => ['driver/registry-driver/index-pndg'],
                        'isDirect' => false,
                    ],
                    'icorctDriver'=> [
                        'label' => 'Incorrect',
                        'url' => ['driver/registry-driver/index-icorct'],
                        'isDirect' => false,
                    ],
                    'rjctDriver'=> [
                        'label' => 'Reject',
                        'url' => ['driver/registry-driver/index-rjct'],
                        'isDirect' => false,
                    ],
                ]
            ],
            'apprvDriver' => [
                'label' => 'Approval Driver',
                'iconClass' => 'fa fa-check',
                'navigation' => [
                    'approvalPndgDriver' => [
                        'label' => 'Pending',
                        'url' => ['driver/status-driver/pndg-driver'],
                        'isDirect' => false,
                    ],
                    'approvalIcorctDriver' => [
                        'label' => 'Incorrect',
                        'url' => ['driver/status-driver/icorct-driver'],
                        'isDirect' => false,
                    ],
                    'approvalApprvDriver' => [
                        'label' => 'Approve',
                        'url' => ['driver/status-driver/apprv-driver'],
                        'isDirect' => false,
                    ]
                ]
            ],
            'driver' => [
                'label' => 'Driver',
                'iconClass' => 'fa fa-users',
                'url' => ['driver/person-as-driver/index'],
                'isDirect' => false,
            ],
            'userAsDriver' => [
                'label' => 'User As Driver',
                'iconClass' => 'fa fa-user',
                'url' => ['driver/user-as-driver/index'],
                'isDirect' => false,
            ]
        ]
    ]
];