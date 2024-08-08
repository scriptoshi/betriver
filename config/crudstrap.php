<?php

return [

    'custom_template' => true,

    /*
    |--------------------------------------------------------------------------
    | Crud Generator Template Stubs Storage Path
    |--------------------------------------------------------------------------
    |
    | Here you can specify your custom template path for the generator.
    |
     */

    'path' => base_path('resources/crud-strap/'),
    /*
  
    /**
     * Delimiter for template vars
     */
    'delimiter' => ['%%', '%%'],

    /*
    |--------------------------------------------------------------------------
    | Dynamic templating
    |--------------------------------------------------------------------------
    |
    | Here you can specify your customs templates for the generator.
    | You can set new templates or delete some templates if you do not want them.
    | You can also choose which values are passed to the views and you can specify a custom delimiter for all templates
    |
    | Those values are available :
    'formFieldsHtml',
    'crudName',
    'crudNameSingular',
    'primaryKey',
    'modelName',
    'formHeadingHtml',
    'formBodyHtml',
    'filledVueForm',
    'emptyVueForm',
    'propsHtml',
    'formBodyVueHtml'
    |
    |
     */
    'dynamic_view_template' => [
        'index',
        'create',
        'edit',
        /*
         * Add new stubs templates here if you need to, like action, datatable...
         * custom_template needs to be activated for this to work
         */
    ],

    'themes' => [
        // thie represen
        'default' => [
            # default "crud/
            'folder' => "crud/",
            # default 'Models'
            'modelNamespace' => '',
            # Use softdeletes on the models. default true.
            'softdeletes' => true,
            /*
                use this to create contollers in another namespace
                leave this empty to create contollers in \App\Http\Controllesr
                setting this to 'Admin' will created  a controller in \App\Http\Controllers\Admin\YourController.php
             */
            'controllerNamespace' => '',
            'primaryKey' => 'id',  //The name of the primary key on your models.
            'pagination' => 25,
            // use this to add prefix to your routes
            // example: setting this to admin will creates routes under Admin group
            'routeGroup' => '', //Prefix of the route group
            'force' => false, //Replace Items if they exists
            /*items to create comma separated
                *possible values : 
                policy,transformer,controller,model,migration,view,routes,factory,resource,lang, enums
                use 'all' or null to make everything
                */
            'only' => ['all'],

        ],

        'bets' => [
            'folder' => base_path("crud/bets/"),
            'viewPath' => 'Admin',
            'force' => true,
            'only' => ['policy', 'controller', 'migration', 'routes', 'model', 'resource', 'enums', 'view'],
        ],
        'admin' => [
            'folder' => base_path("crud/bets/"),
            'viewPath' => 'Admin',
            'controllerNamespace' => 'Admin',
            'force' => true,
            'only' => ['controller', 'view'],
        ],


    ],

];
