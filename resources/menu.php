<?php

declare(strict_types=1);

/*
 * GENERATED FILE — DO NOT EDIT.
 *
 * Source: src/v4/shell-render.js (NAV) in ColorlibHQ/gentelella.
 * Regenerate: npm run export:php
 *
 * The default demo sidebar. Consumer apps set their own menu in
 * config/gentelella.php; this ships as the fallback so a fresh install has a
 * populated sidebar out of the box.
 */

return [
    [
        'label' => 'General',
        'items' => [
            [
                'text' => 'Dashboards',
                'icon' => 'dashboard',
                'children' => [
                    [
                        'key' => 'dashboard',
                        'text' => 'Operations',
                        'page' => 'index',
                    ],
                    [
                        'key' => 'dashboard-2',
                        'text' => 'Analytics',
                        'page' => 'index2',
                    ],
                    [
                        'key' => 'dashboard-3',
                        'text' => 'Sales',
                        'page' => 'index3',
                    ],
                    [
                        'key' => 'dashboard-4',
                        'text' => 'System health',
                        'page' => 'index4',
                    ],
                ],
            ],
            [
                'text' => 'Forms',
                'icon' => 'forms',
                'badge' => ['text' => 'Hot', 'class' => 'badge-red'],
                'children' => [
                    [
                        'key' => 'forms',
                        'text' => 'General',
                        'page' => 'form',
                    ],
                    [
                        'key' => 'form-advanced',
                        'text' => 'Advanced controls',
                        'page' => 'form_advanced',
                    ],
                    [
                        'key' => 'form-buttons',
                        'text' => 'Buttons',
                        'page' => 'form_buttons',
                    ],
                    [
                        'key' => 'form-upload',
                        'text' => 'Upload',
                        'page' => 'form_upload',
                    ],
                    [
                        'key' => 'form-validation',
                        'text' => 'Validation',
                        'page' => 'form_validation',
                    ],
                    [
                        'key' => 'form-wizards',
                        'text' => 'Wizard',
                        'page' => 'form_wizards',
                    ],
                ],
            ],
            [
                'text' => 'Tables',
                'icon' => 'tables',
                'children' => [
                    [
                        'key' => 'tables',
                        'text' => 'Static',
                        'page' => 'tables',
                    ],
                    [
                        'key' => 'tables-dynamic',
                        'text' => 'Dynamic',
                        'page' => 'tables_dynamic',
                    ],
                ],
            ],
            [
                'text' => 'Charts',
                'icon' => 'charts',
                'badge' => ['text' => 'New', 'class' => 'badge-teal'],
                'children' => [
                    [
                        'key' => 'charts',
                        'text' => 'Chart cards',
                        'page' => 'chartjs',
                    ],
                    [
                        'key' => 'echarts',
                        'text' => 'ECharts gallery',
                        'page' => 'echarts',
                    ],
                    [
                        'key' => 'other-charts',
                        'text' => 'SVG charts',
                        'page' => 'other_charts',
                    ],
                ],
            ],
            [
                'key' => 'calendar',
                'text' => 'Calendar',
                'icon' => 'calendar',
                'page' => 'calendar',
            ],
            [
                'key' => 'map',
                'text' => 'Map',
                'icon' => 'map',
                'page' => 'map',
            ],
        ],
    ],
    [
        'label' => 'Apps',
        'items' => [
            [
                'key' => 'chat',
                'text' => 'Chat',
                'icon' => 'chat',
                'page' => 'chat',
                'badge' => ['text' => '3', 'class' => 'badge-teal'],
            ],
            [
                'key' => 'inbox',
                'text' => 'Inbox',
                'icon' => 'mail',
                'page' => 'inbox',
            ],
            [
                'key' => 'kanban',
                'text' => 'Kanban',
                'icon' => 'kanban',
                'page' => 'kanban',
            ],
            [
                'key' => 'files',
                'text' => 'Files',
                'icon' => 'files',
                'page' => 'file_manager',
            ],
            [
                'key' => 'notifications',
                'text' => 'Notifications',
                'icon' => 'bell',
                'page' => 'notifications',
            ],
        ],
    ],
    [
        'label' => 'E-commerce',
        'items' => [
            [
                'key' => 'storefront',
                'text' => 'Storefront',
                'icon' => 'shop',
                'page' => 'e_commerce',
            ],
            [
                'key' => 'product',
                'text' => 'Product',
                'icon' => 'tag',
                'page' => 'product_detail',
            ],
            [
                'text' => 'Orders',
                'icon' => 'cart',
                'children' => [
                    [
                        'key' => 'orders',
                        'text' => 'All orders',
                        'page' => 'orders',
                    ],
                    [
                        'key' => 'order-detail',
                        'text' => 'Order detail',
                        'page' => 'order_detail',
                    ],
                ],
            ],
            [
                'key' => 'invoice',
                'text' => 'Invoice',
                'icon' => 'receipt',
                'page' => 'invoice',
            ],
            [
                'key' => 'pricing',
                'text' => 'Pricing',
                'icon' => 'price',
                'page' => 'pricing_tables',
            ],
        ],
    ],
    [
        'label' => 'Projects',
        'items' => [
            [
                'key' => 'projects',
                'text' => 'All projects',
                'icon' => 'projects',
                'page' => 'projects',
            ],
            [
                'key' => 'project-detail',
                'text' => 'Project detail',
                'icon' => 'pages',
                'page' => 'project_detail',
            ],
        ],
    ],
    [
        'label' => 'UI library',
        'items' => [
            [
                'key' => 'ui',
                'text' => 'Elements',
                'icon' => 'ui',
                'page' => 'general_elements',
            ],
            [
                'key' => 'widgets',
                'text' => 'Widgets',
                'icon' => 'pages',
                'page' => 'widgets',
                'badge' => ['text' => '5', 'class' => 'badge-blue'],
            ],
            [
                'key' => 'playground',
                'text' => 'Playground',
                'icon' => 'code',
                'page' => 'playground',
                'badge' => ['text' => 'New', 'class' => 'badge-teal'],
            ],
            [
                'key' => 'theme',
                'text' => 'Theme',
                'icon' => 'paint',
                'page' => 'theme',
                'badge' => ['text' => 'New', 'class' => 'badge-teal'],
            ],
            [
                'key' => 'typography',
                'text' => 'Typography',
                'icon' => 'type',
                'page' => 'typography',
            ],
            [
                'key' => 'icons',
                'text' => 'Icons',
                'icon' => 'icons',
                'page' => 'icons',
            ],
            [
                'key' => 'media',
                'text' => 'Media',
                'icon' => 'media',
                'page' => 'media_gallery',
            ],
        ],
    ],
    [
        'label' => 'Admin',
        'items' => [
            [
                'key' => 'users',
                'text' => 'Contacts',
                'icon' => 'users',
                'page' => 'contacts',
            ],
            [
                'key' => 'user_management',
                'text' => 'User management',
                'icon' => 'profile',
                'page' => 'user_management',
            ],
            [
                'key' => 'profile',
                'text' => 'Your profile',
                'icon' => 'profile',
                'page' => 'profile',
            ],
            [
                'key' => 'settings',
                'text' => 'Settings',
                'icon' => 'settings',
                'page' => 'settings',
            ],
            [
                'key' => 'faq',
                'text' => 'Help center',
                'icon' => 'help',
                'page' => 'faq',
            ],
        ],
    ],
    [
        'label' => 'Layouts',
        'items' => [
            [
                'key' => 'fixed-sidebar',
                'text' => 'Fixed sidebar',
                'icon' => 'layout',
                'page' => 'fixed_sidebar',
            ],
            [
                'key' => 'fixed-footer',
                'text' => 'Fixed footer',
                'icon' => 'layout',
                'page' => 'fixed_footer',
            ],
            [
                'key' => 'level2',
                'text' => 'Nested page',
                'icon' => 'pages',
                'page' => 'level2',
            ],
            [
                'key' => 'plain',
                'text' => 'Blank',
                'icon' => 'pages',
                'page' => 'plain_page',
            ],
        ],
    ],
];
