<?php

class MailerTransportTest extends TestFixture
{
    /**
     * @expectedException \Idexx\EdsServicePhp\MailServiceException
     */
    public function testMessageInvalidWithoutTo()
    {
        $Message = new \Idexx\EdsServicePhp\Message();
        $Message->setSubject( 'Foo' );
        $Message->setFrom( 'foo@example.com' );

        $Mail = new \Idexx\EdsServicePhp\Mailer( 'foo.com', 'fooKey' );
        $Mail->send( $Message );
    }

    /**
     * @expectedException \Idexx\EdsServicePhp\MailServiceException
     */
    public function testMessageInvalidWithoutFrom()
    {
        $Message = new \Idexx\EdsServicePhp\Message();
        $Message->setSubject( 'Foo' );
        $Message->setTo( 'foo@example.com' );

        $Mail = new \Idexx\EdsServicePhp\Mailer( 'foo.com', 'fooKey' );
        $Mail->send( $Message );
    }

    /**
     * @expectedException \Idexx\EdsServicePhp\MailServiceException
     */
    public function testMessageInvalidWithoutSubject()
    {
        $Message = new \Idexx\EdsServicePhp\Message();
        $Message->setTo( 'foo@example.com' );
        $Message->setFrom( 'foo@example.com' );

        $Mail = new \Idexx\EdsServicePhp\Mailer( 'foo.com', 'fooKey' );
        $Mail->send( $Message );
    }

    /**
     * Test capturing data in Mailer response
     */
    public function testUpstreamResponseClosure()
    {
        $Message = new \Idexx\EdsServicePhp\Message();
        $Message->setSubject( 'Foo' );
        $Message->setFrom( 'foo@example.com' );
        $Message->setTo( 'foo@example.com' );

        $Mail = new \Idexx\EdsServicePhp\Mailer( 'foo.com', 'fooKey' );

        $Mail->setUpstreamParser( function ( $response ) {
            $this->assertInternalType( 'array', $response );
            $this->assertNotEmpty( $response );
        }
        );

        $Mail->send( $Message );
    }

    /**
     * Test capturing data in Mailer response
     */
    public function testDownstreamResponseClosure()
    {
        $Mail = new \Idexx\EdsServicePhp\Mailer( 'foo.com', 'fooKey' );

        $Mail->setDownstreamParser( function ( $response ) {
            $this->assertInternalType( 'array', $response );
            $this->assertNotEmpty( $response );
        }
        );

        $Mail->getMessage( 1 );
    }
}
