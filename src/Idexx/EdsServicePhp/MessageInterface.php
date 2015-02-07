<?php namespace Idexx\EdsServicePhp;

/**
 * Class Message
 *
 * @package Idexx\Message
 */
interface MessageInterface
{
    /**
     * @param string $subject mail subject line
     *
     * @throws MailServiceException
     */
    public function setSubject( $subject );

    /**
     * @param string $address recipient address
     * @param null   $name    recipient name
     *
     * @throws MailServiceException
     */
    public function setTo( $address, $name = null );

    /**
     * @param string $address recipient address
     * @param null   $name    recipient name
     *
     * @throws MailServiceException
     */
    public function setFrom( $address, $name = null );

    /**
     * @param string $replyTo reply-to address
     *
     * @throws MailServiceException
     */
    public function setReplyTo( $replyTo );

    /**
     * @param array $cc [ address => name, [] ]
     */
    public function addCc( array $cc );

    /**
     * @param array $bcc [ address, name, [] ]
     */
    public function addBcc( array $bcc );

    /**
     * @param boolean $showRecipients flag recipient exposure
     */
    public function showRecipients( $showRecipients );

    /**
     * @param string $body message body as string
     *
     * @throws MailServiceException
     */
    public function setBody( $body );

    /**
     * @param boolean $commercial flag entity as commercial nature
     */
    public function setCommercial( $commercial );

    /**
     * @param array $meta add additional data to message
     */
    public function addMeta( array $meta );

    /**
     * @param MessageAttachmentInterface $attachment
     */
    public function addAttachment( MessageAttachmentInterface $attachment );

    /**
     * ensure entity has required parameters for mail
     *
     * @return bool
     * @throws MailServiceException
     */
    public function isValidForDelivery();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function toJson();
}
