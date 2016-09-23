<?php
function say($text){
    static $translation = array(
        'ADMIN_ACCOUNT' => 'Admin account',
        'PASSWORD' => 'Password',
        'USERNAME' => 'Username',
        'STORAGE' => 'Storage',
        'DATABASE' => 'Database',
        'DATABASE_USER' => 'Database user',
        'DATABASE_PASSWORD' => 'Database password',
        'DATABASE_NAME' => 'Database name',
        'DATABASE_HOST' => 'Database host'
    );
    return $translation[$text];
}
?>
