<?php
$settings->add(new admin_setting_heading(
            'headerconfig',
            get_string('headerconfig', 'block_testblock'),
            get_string('descconfig', 'block_testblock')
        ));

$settings->add(new admin_setting_configcheckbox(
            'testblock/Allow_HTML',
            get_string('labelallowhtml', 'block_testblock'),
            get_string('descallowhtml', 'block_testblock'),
            '0'
        ));

if ($ADMIN->fulltree) {
    // Display Course Categories on the recently accessed courses block items.
    $settings->add(new admin_setting_configcheckbox(
        'block_recentlyaccessedcourses/displaycategories',
        get_string('displaycategories', 'block_recentlyaccessedcourses'),
        get_string('displaycategories_help', 'block_recentlyaccessedcourses'),
        1));
}