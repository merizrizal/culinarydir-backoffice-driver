<?php
return [
    'params' => [
        'navigation' => [
            'CreateDriver' => [
                'label' => 'Create Data',
                'iconClass' => 'fa fa-edit',
                'url' => ['driver/person-as-driver/index'],
                'isDirect' => false,
            ],
            'PndgDriver' => [
                'label' => 'Pending Data',
                'iconClass' => 'fa fa-hourglass-half',
                'url' => [''],
                'isDirect' => false,
            ],
            'icorctDriver'=> [
                'label' => 'Incorrect Data',
                'iconClass' => 'fa fa-exclamation-circle',
                'url' => [''],
                'isDirect' => false,
            ],
            'rjctDriver'=> [
                'label' => 'Reject Data',
                'iconClass' => 'fa fa-window-close',
                'url' => [''],
                'isDirect' => false,
            ],
            'driver'=> [
                'label' => 'Driver',
                'iconClass' => 'fa fa-users',
                'url' => [''],
                'isDirect' => false,
            ],
        ]
    ]
];