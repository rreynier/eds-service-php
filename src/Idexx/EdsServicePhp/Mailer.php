<?php namespace Idexx\EdsServicePhp;

/**
 * This package leverages Resty.php for http requests
 *
 * @link https://github.com/fictivekin/resty.php
 */
use Resty\Resty;

/**
 * Class Mailer
 *
 * A simple transporter for IDEXX Email Delivery Service
 *
 * @package Idexx\EdsService
 * @link https://github.com/IDEXX/eds-service-php
 */
class Mailer
{
    /**
     * @var string EDS service endpoint
     */
    protected $endpoint;

    /**
     * @var string EDS service API version
     */
    protected $apiVersion = '1';

    /**
     * @var string Consumer application key
     */
    protected $applicationKey;

    /**
     * @see Mailer::setUpstreamParser()
     * @var null|\Closure
     */
    protected $upstreamParser = null;

    /**
     * @see Mailer::setDownstreamParser()
     * @var null|\Closure
     */
    protected $downstreamParser = null;

    /**
     * @see Mailer::setLogger()
     * @var null|\Closure
     */
    protected $logger = null;

    /**
     * @param string $endpoint
     * @param string $appKey
     * @param string $apiVersion
     */
    public function __construct( $endpoint, $appKey, $apiVersion = '1' )
    {
        $this->endpoint       = $endpoint;
        $this->applicationKey = $appKey;
        $this->apiVersion     = $apiVersion;
    }

    /**
     * @param MessageInterface $Message
     * @param array            $restyOptions
     *
     * @see Resty::__construct for options
     * @return mixed
     */
    public function send( MessageInterface $Message, $restyOptions = [ ] )
    {
        $request = $this->setUpResty( $restyOptions );

        if ( $Message->isValidForDelivery() ) {
            return $this->getUpstreamParser(
                $request->post( $this->getPostRoute(), $Message->toJson() )
            );
        }
    }

    /**
     * @param       $messageId
     * @param array $restyOptions
     *
     * @see Resty::__construct for options
     * @return array
     */
    public function getMessage( $messageId, $restyOptions = [ ] )
    {
        $request = $this->setUpResty( $restyOptions );

        return $this->getDownstreamParser(
            $request->get( $this->getRetrieveRoute( $messageId ) )
        );
    }

    /**
     * permits alternative method for reading submitted requests
     *
     * @param callable $parser
     */
    public function setUpstreamParser( \Closure $parser )
    {
        $this->upstreamParser = $parser;
    }

    /**
     * permits alternative method for reading requested messages
     *
     * @param callable $parser
     */
    public function setDownstreamParser( \Closure $parser )
    {
        $this->downstreamParser = $parser;
    }

    /**
     * sets an alternate logging method
     *
     * @see Resty::setLogger()
     *
     * @param \Closure $logger
     */
    public function setLogger( \Closure $logger )
    {
        $this->logger = $logger;
    }

    /**
     * retrieves sent message parser
     *
     * @param $response
     *
     * @return mixed
     * @throws MailServiceException
     */
    protected function getUpstreamParser( $response )
    {
        if ( is_callable( $this->upstreamParser ) ) {
            $parser = $this->upstreamParser;

            return $parser( $response );
        }

        return $this->parseUpstream( $response );
    }

    /**
     * retrieves requested message parser
     *
     * @param $response
     *
     * @return mixed
     */
    protected function getDownstreamParser( $response )
    {
        if ( is_callable( $this->downstreamParser ) ) {
            $parser = $this->downstreamParser;

            return $parser( $response );
        }

        return $this->parseDownstream( $response );
    }

    /**
     * reads the upstream response
     *
     * @param $response
     *
     * @return string message identifier
     * @throws MailServiceException
     */
    protected function parseUpstream( $response )
    {
        // Test resty first
        $this->checkRequestErrors( $response );

        // read EDS response
        if ( isset( $response['status'] ) && $response['status'] === 'REJECTED' ) {
            throw new MailServiceException( "Message was rejected: [%s]", $response['rejectionReason'] );
        }

        // return the messageId on success
        if ( isset( $response['status'] ) && $response['status'] === 'ACCEPTED' ) {
            return $response['messageId'];
        }

        throw new MailServiceException( 'Unexpected response returned' );
    }

    /**
     * reads the message request response
     *
     * @param $response
     *
     * @return mixed
     * @throws MailServiceException
     */
    protected function parseDownstream( $response )
    {
        // Test headers
        $this->checkRequestErrors( $response );

        // TODO: Parse downstream sentMessages as new Message for replay
        return $response;
    }

    /**
     * checks for common errors in the request response.
     * Resty provides error & error_msg in response when it encounters an http error
     * EDS returns a 404 with a false applicationKey, so we throw as well
     *
     * @param $response
     *
     * @throws MailServiceException
     */
    private function checkRequestErrors( $response )
    {
        // capture Resty errors
        if ( isset( $response['error'] ) && (bool)$response['error'] ) {
            throw new MailServiceException( sprintf( "Unable to transport mail [%s]", $response['error_msg'] ) );
        }

        // detect 404s
        if ( isset( $response['status'] ) && $response['status'] === 404 ) {
            throw new MailServiceException( "Request not found. Check application key" );
        }
    }

    /**
     * Sets up the http request engine Resty
     * Logger presence detection turns on debug mode
     *
     * @param array $options
     *
     * @return Resty
     */
    protected function setUpResty( $options = [ ] )
    {
        $resty = new Resty( $options );

        if ( is_callable( $this->logger ) ) {
            $resty->debug( true );
            $resty->setLogger( $this->logger );
        }

        return $resty;
    }

    /**
     * Generates the URI for message submission
     *
     * @return string
     */
    final private function getPostRoute()
    {
        return sprintf( "%s/api/v%s/%s/messages",
                        $this->endpoint,
                        $this->apiVersion,
                        $this->applicationKey
        );
    }

    /**
     * Generates the URI for message retrieval
     *
     * @param $messageId
     *
     * @return string
     */
    final private function getRetrieveRoute( $messageId )
    {
        return sprintf( "%s/api/v%s/%s/messages/%s",
                        $this->endpoint,
                        $this->apiVersion,
                        $this->applicationKey,
                        $messageId
        );
    }
}
