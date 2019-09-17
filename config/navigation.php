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
            'pndgDriver' => [
                'label' => 'Pending Driver',
                'iconClass' => 'fa fa-hourglass-half',
                'url' => ['driver/registry-driver/index-pndg'],
                'isDirect' => false,
            ],
            'icorctDriver'=> [
                'label' => 'Incorrect Driver',
                'iconClass' => 'fa fa-exclamation-circle',
                'url' => ['driver/registry-driver/index-icorct'],
                'isDirect' => false,
            ],
            'rjctDriver'=> [
                'label' => 'Reject Driver',
                'iconClass' => 'fa fa-window-close',
                'url' => ['driver/registry-driver/index-rjct'],
                'isDirect' => false,
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