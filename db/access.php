<?php
    $capabilities = array(

        // Can edit objectives
    'block/acclaim:editbadge' => array(
        'captype' => 'write',
        'riskbitmask' => RISK_SPAM,
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW
            )
        ),
    
    'block/acclaim:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
                                                      
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),
                                                                   
    'block/acclaim:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
                                                                                
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
                                                                                                                                         
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
);
