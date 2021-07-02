<?php

namespace Tests\Unit\MongoDB\Model;

use Tests\User;

trait Update
{

    public function testUpdateDocument()
    {
        $user = User::whereEq('email', 'test@email.com')
                      ->update([
                          'name' => 'Jhon'
                      ]);

        $this->assertInstanceOf(\Tests\User::class, $user);
        $this->assertSame('Jhon', $user->name);
    }

    public function testUpdateManyDocuments()
    {
        $true = User::whereEq('email', 'test@email.com')
                      ->bulkUpdate([
                          'name' => 'Jhon'
                      ]);
        $this->assertTrue($true);
    }
}