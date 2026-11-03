<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Sistema de gestión',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Lan</b>MTK',
    'logo_img' => 'vendor/adminlte/dist/img/logo_mtk.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'MTK',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo_mtk.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo_mtk.png',
            'alt' => 'MTK',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

   'menu' => [
    [
        'text' => 'MikroTik / Hotspot',
        'icon' => 'fas fa-wifi',
        'submenu' => [
            ['text' => 'Resumen y routers', 'route' => 'mt.index', 'can' => 'mikrotik.ver', 'icon' => 'fas fa-network-wired'],
            ['text' => 'Fichas de visitantes', 'route' => 'mt.fichas', 'can' => 'mikrotik.fichas', 'icon' => 'fas fa-qrcode'],
            ['text' => 'Configuración de routers', 'route' => 'mt.configuracion', 'can' => 'mikrotik.configurar', 'icon' => 'fas fa-cogs'],
        ],
    ],
    [
        'text' => 'Gym',
        'icon' => 'fas fa-dumbbell',
        'submenu' => [
            ['text' => 'Resumen', 'route' => 'gym.index', 'can' => 'gym.ver', 'icon' => 'fas fa-chart-pie'],
            ['text' => 'Socios y membresías', 'route' => 'gym.socios', 'can' => 'gym.administrar', 'icon' => 'fas fa-users'],
            ['text' => 'Planes', 'route' => 'gym.planes', 'can' => 'gym.administrar', 'icon' => 'fas fa-tags'],
            ['text' => 'Control de acceso', 'route' => 'gym.accesos', 'can' => 'gym.accesos', 'icon' => 'fas fa-door-open'],
            ['text' => 'Reportes de asistencia', 'route' => 'gym.reportes', 'can' => 'gym.reportes', 'icon' => 'fas fa-list-alt'],
        ],
    ],
    // Top Nav Widgets
    [
        'type'         => 'fullscreen-widget',
        'topnav_right' => true,
    ],

    // --- ADMINISTRACIÓN DEL SISTEMA ---
    ['header' => 'ADMINISTRACIÓN Y AJUSTES'],

    [
        'text'    => 'Configuración',
        'icon'    => 'fas fa-cogs',
        'submenu' => [
            [
                'can'  => 'admin.configuracion.index',
                'text' => 'General',
                'url'  => 'admin/configuraciones',
                'icon' => 'fas fa-fw fa-sliders-h',
            ],
            [
                'can'  => 'admin.backups.index',
                'text' => 'Respaldo DB',
                'url'  => 'admin/backups',
                'icon' => 'fas fa-fw fa-database',
            ],
            [
                'can'  => 'admin.roles.index',
                'text' => 'Permisos y Roles',
                'url'  => 'admin/roles',
                'icon' => 'fas fa-fw fa-user-shield',
            ],
            [
                'text' => 'Departamentos',
                'url'  => 'netlan/admin/departamentos',
                'icon' => 'fas fa-fw fa-building',
            ],
            [
                'can'  => 'admin.empleados.index',
                'text' => 'Empleados',
                'url'  => 'netlan/admin/empleados',
                'icon' => 'fas fa-fw fa-users',
            ],
        ],
    ],

    // --- REDES E INFRAESTRUCTURA ---
    ['header' => 'INFRAESTRUCTURA Y RED'],

    [
        'text'    => 'Red de Datos',
        'icon'    => 'fas fa-network-wired',
        'submenu' => [
            [
                'can'  => 'admin.marcas.index',
                'text' => 'Marcas',
                'url'  => 'netlan/admin/marcas',
                'icon' => 'fas fa-fw fa-copyright',
            ],
            [
                'can'  => 'admin.modelos.index',
                'text' => 'Modelos',
                'url'  => 'netlan/admin/modelos',
                'icon' => 'fas fa-fw fa-cubes',
            ],
            [
                'can'  => 'admin.equipos.index',
                'text' => 'Equipos',
                'url'  => 'netlan/admin/equipos',
                'icon' => 'fas fa-fw fa-desktop',
            ],
            // [
            //     'can'  => 'admin.paneles.index',
            //     'text' => 'Paneles',
            //     'url'  => 'netlan/admin/paneles',
            //     'icon' => 'fas fa-fw fa-server',
            // ],
            // [
            //     'can'  => 'admin.empleados.index',
            //     'text' => 'Patch Panels',
            //     'url'  => 'netlan/admin/patchPanel',
            //     'icon' => 'fas fa-fw fa-ethernet',
            // ],
            // [
            //     'text' => 'Switches y Routers',
            //     'url'  => 'netlan/admin/switchs',
            //     'icon' => 'fas fa-fw fa-random',
            // ],
            // [
            //     'can'  => 'admin.ports.index',
            //     'text' => 'Puertos Patch Panel',
            //     'url'  => 'netlan/admin/ports',
            //     'icon' => 'fas fa-fw fa-plug',
            // ],
            // [
            //     'text' => 'Puertos Switch',
            //     'url'  => 'netlan/admin/switchs',
            //     'icon' => 'fas fa-fw fa-tty',
            // ],
            [
                'can'  => 'admin.connections.index',
                'text' => 'Conexiones',
                'url'  => 'netlan/admin/connections',
                'icon' => 'fas fa-fw fa-project-diagram',
            ],
        ],
    ],
    [
        'text'    => 'Mikrotik',
        'icon'    => 'fas fa-microchip',
        'can'     => 'admin.configuracion.index',
        'submenu' => [
            [
                'text' => 'SNMP',
                'url'  => 'admin/snmp/',
                'icon' => 'fas fa-fw fa-satellite-dish',
            ],
            [
                'text' => 'Clientes Internet',
                'url'  => 'admin/mikrotik/clientes',
                'icon' => 'fas fa-fw fa-wifi',
            ],
            [
                'text' => 'Clientes Conectados',
                'url'  => 'admin/mikrotik/conectados',
                'icon' => 'fas fa-fw fa-signal',
            ],
            [
                'text' => 'Diagrama de Red',
                'url'  => 'admin/diagramas/red',
                'icon' => 'fas fa-fw fa-sitemap',
            ],
        ],
    ],
    [
        'text'    => 'BTR',
        'icon'    => 'fas fa-bus',
        'submenu' => [
            [
                'can'  => 'admin.inventarioBTR.dashboard',
                'text' => 'Dashboard BTR',
                'url'  => 'admin/inventarioBTR/dashboard',
                'icon' => 'fas fa-fw fa-chart-line',
            ],
            [
                'can'  => 'admin.inventarioBTR.index',
                'text' => 'Inventario BTR',
                'url'  => 'admin/inventarioBTR',
                'icon' => 'fas fa-fw fa-boxes',
            ],
            [
                'can'  => 'admin.inventarioBTR.mapas_btr',
                'text' => 'Mapas BTR',
                'url'  => 'admin/inventarioBTR/mapas_btr',
                'icon' => 'fas fa-fw fa-map-marked-alt',
            ],
        ],
    ],

    // --- OPERACIONES Y PRODUCTIVIDAD ---
    ['header' => 'OPERACIONES'],

    [
        'text' => 'Agenda Semanal',
        'url'  => 'produccion/admin/agendas',
        'icon' => 'far fa-fw fa-calendar-alt',
    ],
    [
        'text'    => 'Liquidaciones',
        'icon'    => 'fas fa-fw fa-plane-departure',
        'submenu' => [
            [
                'text' => 'Historial Liquidaciones',
                'url'  => 'admin/liquidaciones',
                'icon' => 'fas fa-fw fa-history',
            ],
            [
                'text' => 'Liquidaciones',
                'url'  => 'admin/liquidaciones/liquidacion',
                'icon' => 'fas fa-fw fa-file-invoice-dollar',
            ],
        ],
    ],
    [
        'text' => 'Tickets',
        'url'  => 'admin/tickets/dashboard',
        'icon' => 'fas fa-fw fa-ticket-alt',
    ],
    [
        'can'   => 'admin.brands.index',
        'text'  => 'Drivers y Manuales',
        'icon'  => 'fas fa-fw fa-download',
        'route' => 'admin.brands.index',
    ],
    [
        'text'    => 'Productividad',
        'icon'    => 'fas fa-fw fa-tasks',
        'submenu' => [
            [
                'can'  => 'admin.marcaciones.index',
                'text' => 'Asistencia',
                'url'  => 'admin/marcaciones',
                'icon' => 'fas fa-fw fa-clock',
            ],
        ],
    ],

    ['header' => 'PRODUCCIÓN'],
    [
        'text' => 'Viáticos',
        'icon' => 'fas fa-fw fa-wallet',
        'can' => 'admin.viaticos.index',
        'submenu' => [
            ['text' => 'Historial de viáticos', 'route' => 'admin.viaticos.index', 'icon' => 'fas fa-fw fa-list'],
            ['text' => 'Calcular viático', 'route' => 'admin.viaticos.create', 'icon' => 'fas fa-fw fa-calculator'],
            ['text' => 'Ciudades y kilometraje', 'route' => 'admin.viaticos.ciudades', 'icon' => 'fas fa-fw fa-map-marked-alt', 'can' => 'administrar-ciudades'],
            ['text' => 'Solicitar viático', 'route' => 'admin.viaticos.solicitud', 'icon' => 'fas fa-fw fa-paper-plane'],
        ],
    ],

    // --- FINANZAS Y OTROS ---
    ['header' => 'GESTIÓN Y ANEXOS'],

    [
        'text'    => 'Finanzas',
        'icon'    => 'fas fa-fw fa-chart-pie',
        'submenu' => [
            [
                'text' => 'Dashboard',
                'url'  => 'admin/finanzas/dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
            ],
            [
                'text' => 'Ingresos',
                'url'  => 'admin/finanzas/ingresos',
                'icon' => 'fas fa-fw fa-hand-holding-usd',
            ],
            [
                'text' => 'Egresos',
                'url'  => 'admin/finanzas/egresos',
                'icon' => 'fas fa-fw fa-file-export',
            ],
        ],
    ],
    [
        'text'    => 'Anexos',
        'icon'    => 'fas fa-fw fa-folder-plus',
        'submenu' => [
            [
                'can'  => 'admin.alumnos.importar',
                'text' => 'Importar Excel',
                'url'  => 'admin/alumnos/importar',
                'icon' => 'fas fa-fw fa-file-excel',
            ],
            [
                'can' => 'admin.alumnos.crey',
                'text' => 'Panel de impresión',
                'route' => 'impresion.panel',
                'icon' => 'fas fa-fw fa-print',
            ],
            [
                'can'  => 'admin.alumnos.crey',
                'text' => 'Importar CREY',
                'url'  => 'admin/alumnos/crey',
                'icon' => 'fas fa-fw fa-file-import',
            ],
            [
                'can'  => 'admin.alumnos.foto',
                'text' => 'Preparar Foto',
                'url'  => 'admin/alumnos/foto',
                'icon' => 'fas fa-fw fa-camera',
            ],
            [
                'can'  => 'admin.apuntes.foto',
                'text' => 'Apuntes',
                'url'  => 'admin/apuntes',
                'icon' => 'fas fa-fw fa-sticky-note',
            ],
            [
                'can'  => 'admin.debts.dashboard-finanzas',
                'text' => 'Créditos',
                'url'  => 'admin/debts/dashboard-finanzas',
                'icon' => 'fas fa-fw fa-credit-card',
            ],
        ],
    ],

    // --- CONFIGURACIÓN DE CUENTA ---
    ['header' => 'CUENTA'],

    [
        'text' => 'Mi Perfil',
        'url'  => 'admin/settings',
        'icon' => 'fas fa-fw fa-user-cog',
    ],
],

    ['header' => 'labels'],






    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.4.0/js/dataTables.buttons.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.4.0/js/buttons.bootstrap4.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.4.0/js/buttons.html5.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.4.0/js/buttons.print.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.4.0/js/buttons.colVis.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.4.0/css/buttons.bootstrap4.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
