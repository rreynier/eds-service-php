<?php namespace Idexx\EdsServicePhp;

/**
 * Class Message
 *
 * @package Idexx\Message
 */
class Message implements MessageInterface
{
    /**
     * @see Message::setSubject()
     * @var string
     */
    public $subject;

    /**
     * @see Message::setReplyTo()
     * @var string
     */
    public $replyTo;

    /**
     * message recipient
     *
     * @see Message::setTo()
     * @var array
     */
    public $to = [ ];

    /**
     * message sender
     *
     * @see Message::setFrom()
     * @var array
     */
    public $from = [ ];

    /**
     * meta properties
     *
     * @see Message::addMeta()
     * @var array
     */
    public $meta = [ ];

    /**
     * @see MessageAttachment::getAttachment()
     * @var array
     */
    public $attachments = [ ];

    /**
     * array of recipients to carbon-copy
     *
     * @see Message::addCc()
     * @var array
     */
    public $ccs = [ ];

    /**
     * array of recipients to blind carbon-copy
     *
     * @see Message::addBcc()
     * @var array
     */
    public $bccs = [ ];

    /**
     * parse templates before setting to the body
     *
     * @see Message::setBody()
     * @var string
     */
    public $body;

    /**
     * set message entity as a commercial
     *
     * @see  Message::setCommercial()
     * @link http://www.business.ftc.gov/documents/bus61-can-spam-act-compliance-guide-business
     * @var bool
     */
    public $commercial = false;

    /**
     * whether to pack ccs & bccs into mail headers
     * set to false to generate separate mails for each recipients
     *
     * @see Message::showRecipients()
     * @var bool
     */
    public $exposeRecipients = true;

    /**
     * @var array
     * @see Message::compileMessageData()
     */
    private $message = [ ];

    /**
     * @param array $args
     */
    public function __construct( $subject = null, $recipient = null, $sender = null )
    {
        if ( !is_null( $subject ) && strlen( $subject ) ) {
            $this->setSubject( $subject );
        }

        if ( !is_null( $recipient ) ) {
            if ( is_string( $recipient ) ) {
                $this->setTo( $recipient );
            }
            if ( is_array( $recipient ) ) {
                $_r = each( $recipient );
                $this->setTo( $_r['key'], $_r['value'] );
            }
        }

        if ( !is_null( $sender ) ) {
            if ( is_string( $sender ) ) {
                $this->setFrom( $sender );
            }
            if ( is_array( $sender ) ) {
                $_s = each( $sender );
                $this->setFrom( $_s['key'], $_s['value'] );
            }
        }
    }

    /**
     * @param string $subject
     *
     * @throws MailServiceException
     */
    public function setSubject( $subject )
    {
        $this->subject = $subject;
    }

    /**
     * @param string $address
     * @param null   $name
     *
     * @throws MailServiceException
     */
    public function setTo( $address, $name = null )
    {
        if ( !filter_var( $address, FILTER_VALIDATE_EMAIL ) ) {
            throw new MailServiceException( sprintf( "Address [%s] is not valid for transport", $address ) );
        }

        $this->to = [
            'address' => $address,
            'name'    => $name ?: $address
        ];
    }

    /**
     * @param string $address
     * @param null   $name
     *
     * @throws MailServiceException
     */
    public function setFrom( $address, $name = null )
    {
        if ( !filter_var( $address, FILTER_VALIDATE_EMAIL ) ) {
            throw new MailServiceException( sprintf( "Address [%s] is not valid for transport", $address ) );
        }

        $this->from = [
            'address' => $address,
            'name'    => $name ?: $address
        ];
    }

    /**
     * @param string $replyTo
     *
     * @throws MailServiceException
     */
    public function setReplyTo( $replyTo )
    {
        if ( !filter_var( $replyTo, FILTER_VALIDATE_EMAIL ) ) {
            throw new MailServiceException( sprintf( "%s is not a valid email address", $replyTo ) );
        }

        $this->replyTo = $replyTo;
    }

    /**
     * @param array $cc
     */
    public function addCc( array $cc )
    {
        $this->ccs = array_merge( $this->ccs, $this->filterEmail( $cc ) );
    }

    /**
     * @param array $bcc
     */
    public function addBcc( array $bcc )
    {
        $this->bccs = array_merge( $this->bccs, $this->filterEmail( $bcc ) );
    }

    /**
     * @param boolean $showRecipients
     */
    public function showRecipients( $showRecipients )
    {
        $this->exposeRecipients = (bool)$showRecipients;
    }

    /**
     * @param string $body
     *
     * @throws MailServiceException
     */
    public function setBody( $body )
    {
        if ( !is_string( $body ) ) {
            throw new MailServiceException( "The body must be a string" );
        }

        $this->body = $body;
    }

    /**
     * @param boolean $commercial
     */
    public function setCommercial( $commercial )
    {
        $this->commercial = (bool)$commercial;
    }

    /**
     * @param array $meta
     */
    public function addMeta( array $meta )
    {
        if ( empty( $meta ) ) {
            return;
        }

        foreach ( $meta as $key => $value ) {
            $this->meta[] = [
                'name'  => $key,
                'value' => $value
            ];
        }
    }

    /**
     * @param MessageAttachmentInterface $attachment
     */
    public function addAttachment( MessageAttachmentInterface $attachment )
    {
        $this->attachments[] = $attachment->getAttachment();
    }

    /**
     * @param array $data
     *
     * @throws MailServiceException
     * @return array
     */
    private function filterEmail( $data )
    {
        if ( empty( $data ) ) {
            return $data;
        }

        if ( !is_array( $data ) ) {
            throw new MailServiceException( 'Address must be an array' );
        }

        $return = [ ];

        foreach ( $data as $address => $name ) {
            if ( !filter_var( $address, FILTER_VALIDATE_EMAIL ) ) {
                continue;
            }

            $return[] = [
                'name'    => $name,
                'address' => $address
            ];
        }

        return $return;
    }

    /**
     *
     */
    protected function compileMessageData()
    {
        $reflection = new \ReflectionClass( $this );

        foreach ( $reflection->getProperties( \ReflectionProperty::IS_PUBLIC ) as $property ) {

            if ( is_array( $this->{$property->getName()} ) && !empty( $this->{$property->getName()} ) ) {
                $this->message[$property->getName()] = $this->{$property->getName()};
            }
            elseif ( is_string( $this->{$property->getName()} ) && strlen( $this->{$property->getName()} ) ) {
                $this->message[$property->getName()] = $this->{$property->getName()};
            }
            elseif ( is_bool( $this->{$property->getName()} ) ) {
                $this->message[$property->getName()] = $this->{$property->getName()};
            }
        }

        return $this->message;
    }

    /**
     * @return bool
     * @throws MailServiceException
     */
    public function isValidForDelivery()
    {
        foreach ( [ 'to', 'from', 'subject' ] as $requiredField ) {
            if ( empty( $this->{$requiredField} )
            ) {
                throw new MailServiceException(
                    sprintf( "The required field [%s] is not present on the mail object",
                             $requiredField
                    )
                );
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $this->compileMessageData();

        return array_filter( $this->message );
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode( $this->toArray(), JSON_UNESCAPED_SLASHES );
    }
}
