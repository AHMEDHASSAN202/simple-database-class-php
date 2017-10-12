<?php

function pre()
{
    $vars = func_get_args();

    foreach ($vars as $var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}

require_once 'DB.php';

$db = DB::getInstance();

//pre($db->query('SELECT * FROM `users`')->results());

//$db->insert('users', ['name' => 'ahmed', 'email' => 'ahmed@gmail.com', 'password' => '1234567890']);

//pre($db->lastInsertId());

//$db->delete('users', ['id' , '=', '8']);

//$db->update('users', ['name' => 'anas', 'email' => 'anas@gmail.com'], ['name' , '=', 'hassan']);

//$db->getAll('users')->results();

//$db->get('users', ['id', '=', '2'])->result();
