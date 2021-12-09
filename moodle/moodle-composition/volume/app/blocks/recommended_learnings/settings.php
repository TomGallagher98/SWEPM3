<?php
$settings->add(new admin_setting_heading(
            'headerconfig',
            get_string('headerconfig', 'block_recommended_learnings'),
            get_string('descconfig', 'block_recommended_learnings')
        ));

$settings->add(new admin_setting_configcheckbox(
            'recommended_learnings/Allow_HTML',
            get_string('labelallowhtml', 'block_recommended_learnings'),
            get_string('descallowhtml', 'block_recommended_learnings'),
            '0'
        )); 