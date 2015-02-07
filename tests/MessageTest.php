<?php

class MessageTest extends TestFixture
{
    /**
     * Test simple message with params via constructor
     */
    public function testSimpleMessage()
    {
        $canned = [
            'subject'          => 'Foo',
            'to'               => [
                'name'    => 'John',
                'address' => 'someone@example.com'
            ],
            'from'             => [
                'name'    => 'Mike',
                'address' => 'internal@example.com'
            ],
            'exposeRecipients' => true
        ];

        $Mail = new \Idexx\EdsServicePhp\Message(
            'Foo',
            [ 'someone@example.com' => 'John' ],
            [ 'internal@example.com' => 'Mike' ]
        );

        $this->assertEquals( $canned, $Mail->toArray() );
    }

    /**
     * Test simple message setting via API
     *
     * @throws \Idexx\EdsServicePhp\MailServiceException
     */
    public function testSimpleThroughApi()
    {
        $canned = [
            'subject'          => 'Foo',
            'to'               => [
                'name'    => 'John',
                'address' => 'someone@example.com'
            ],
            'from'             => [
                'name'    => 'Mike',
                'address' => 'internal@example.com'
            ],
            'exposeRecipients' => true
        ];

        $Mail = new \Idexx\EdsServicePhp\Message();
        $Mail->setSubject( 'Foo' );
        $Mail->setTo( 'someone@example.com', 'John' );
        $Mail->setFrom( 'internal@example.com', 'Mike' );

        $this->assertEquals( $canned, $Mail->toArray() );
    }

    /**
     * Test adding multiple carbon copy recipients
     *
     * @throws \Idexx\EdsServicePhp\MailServiceException
     */
    public function testAddingCCs()
    {
        $canned = [
            'subject'          => 'Foo',
            'to'               => [
                'name'    => 'John',
                'address' => 'someone@example.com'
            ],
            'from'             => [
                'name'    => 'Mike',
                'address' => 'internal@example.com'
            ],
            'ccs'               => [
                [
                    'name'    => 'Joe',
                    'address' => 'joe@example.com'
                ],
            ],
            'exposeRecipients' => true
        ];

        $Mail = new \Idexx\EdsServicePhp\Message();
        $Mail->setSubject( 'Foo' );
        $Mail->setTo( 'someone@example.com', 'John' );
        $Mail->setFrom( 'internal@example.com', 'Mike' );
        $Mail->addCc( [ 'joe@example.com' => 'Joe' ] );

        $this->assertEquals( $canned, $Mail->toArray() );

        // add another email
        $canned['ccs'][] = [
            'name'    => 'Mary',
            'address' => 'mary@example.com'
        ];
        $Mail->addCc( [ 'mary@example.com' => 'Mary' ] );

        // add two more emails
        $canned['ccs'][] = [
            'name'    => 'Ryan',
            'address' => 'ryan@example.com'
        ];
        $canned['ccs'][] = [
            'name'    => 'Collin',
            'address' => 'collin@example.com'
        ];

        $Mail->addCc( [ 'ryan@example.com' => 'Ryan', 'collin@example.com' => 'Collin' ] );
        $this->assertEquals( $canned, $Mail->toArray() );
    }

    /**
     * Test adding multiple blind carbon copy recipients
     *
     * @throws \Idexx\EdsServicePhp\MailServiceException
     */
    public function testAddingBCCs()
    {
        $canned = [
            'subject'          => 'Foo',
            'to'               => [
                'name'    => 'John',
                'address' => 'someone@example.com'
            ],
            'from'             => [
                'name'    => 'Mike',
                'address' => 'internal@example.com'
            ],
            'bccs'              => [
                [
                    'name'    => 'Joe',
                    'address' => 'joe@example.com'
                ],
            ],
            'exposeRecipients' => true
        ];

        $Mail = new \Idexx\EdsServicePhp\Message();
        $Mail->setSubject( 'Foo' );
        $Mail->setTo( 'someone@example.com', 'John' );
        $Mail->setFrom( 'internal@example.com', 'Mike' );
        $Mail->addBcc( [ 'joe@example.com' => 'Joe' ] );

        $this->assertEquals( $canned, $Mail->toArray() );

        // add another email
        $canned['bccs'][] = [
            'name'    => 'Mary',
            'address' => 'mary@example.com'
        ];
        $Mail->addBcc( [ 'mary@example.com' => 'Mary' ] );

        // add two more emails
        $canned['bccs'][] = [
            'name'    => 'Ryan',
            'address' => 'ryan@example.com'
        ];
        $canned['bccs'][] = [
            'name'    => 'Collin',
            'address' => 'collin@example.com'
        ];

        $Mail->addBcc( [ 'ryan@example.com' => 'Ryan', 'collin@example.com' => 'Collin' ] );
        $this->assertEquals( $canned, $Mail->toArray() );
    }

    /**
     * Test adding meta data
     *
     * @throws \Idexx\EdsServicePhp\MailServiceException
     */
    public function testAddingMeta()
    {
        $canned = [
            'subject'          => 'Foo',
            'to'               => [
                'name'    => 'John',
                'address' => 'someone@example.com'
            ],
            'from'             => [
                'name'    => 'Mike',
                'address' => 'internal@example.com'
            ],
            'meta'             => [
                [
                    'name'  => 'foo',
                    'value' => 'foo'
                ],
            ],
            'exposeRecipients' => true
        ];

        $Mail = new \Idexx\EdsServicePhp\Message();
        $Mail->setSubject( 'Foo' );
        $Mail->setTo( 'someone@example.com', 'John' );
        $Mail->setFrom( 'internal@example.com', 'Mike' );
        $Mail->addMeta( [ 'foo' => 'foo' ] );

        $this->assertEquals( $canned, $Mail->toArray() );

        // add another meta
        $canned['meta'][] = [
            'name'  => 'bar',
            'value' => 'bar'
        ];
        $Mail->addMeta( [ 'bar' => 'bar' ] );

        // add two more meta elements
        $canned['meta'][] = [
            'name'  => 'baz',
            'value' => 'baz'
        ];
        $canned['meta'][] = [
            'name'  => 'qux',
            'value' => 'qux'
        ];

        $Mail->addMeta( [ 'baz' => 'baz', 'qux' => 'qux' ] );
        $this->assertEquals( $canned, $Mail->toArray() );
    }

    /**
     * Test invalid email throws exception
     *
     * @expectedException \Idexx\EdsServicePhp\MailServiceException
     */
    public function testSimpleEmailAddressFailure()
    {
        new \Idexx\EdsServicePhp\Message(
            'Foo',
            [ 'invalidEmail' => 'John' ],
            [ 'internal@example.com' => 'Mike' ]
        );
    }

    /**
     * Test invalid email through api throws exception
     *
     * @expectedException \Idexx\EdsServicePhp\MailServiceException
     */
    public function testEmailAddressFailure()
    {
        $Mail = new \Idexx\EdsServicePhp\Message();

        $Mail->setTo( 'invalidEmail' );
    }

    /**
     * Test toggling commercial property
     *
     * @throws \Idexx\EdsServicePhp\MailServiceException
     */
    public function testSettingCommercial()
    {
        $canned = [
            'subject'          => 'Foo',
            'to'               => [
                'name'    => 'John',
                'address' => 'someone@example.com'
            ],
            'from'             => [
                'name'    => 'Mike',
                'address' => 'internal@example.com'
            ],
            'exposeRecipients' => true,
        ];

        $Mail = new \Idexx\EdsServicePhp\Message();
        $Mail->setSubject( 'Foo' );
        $Mail->setTo( 'someone@example.com', 'John' );
        $Mail->setFrom( 'internal@example.com', 'Mike' );

        $this->assertEquals( $canned, $Mail->toArray() );

        // add commercial prop
        $canned['commercial'] = true;
        $Mail->setCommercial( true );

        $this->assertEquals( $canned, $Mail->toArray() );
    }
}
