<?php

namespace Tests\Unit\MongoDB\Model;

use Tests\User;

trait Delete
{

    public function testDeleteDocument()
    {
        $result = User::whereEq('email', 'test@email.com')
                      ->delete();

        $this->assertTrue($result);
    }

    public function testDeleteManyDocuments()
    {
        $true = User::whereEq('email', 'test@email.com')
                      ->deleteMany();
        $this->assertTrue($true);
        $result = $this->_user->first();
        print_r($result);exit;
    }
}