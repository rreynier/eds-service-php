<?php namespace Idexx\EdsServicePhp;

/**
 * Class MessageAttachment
 *
 * @package Idexx\Message
 */
class MessageAttachment implements MessageAttachmentInterface
{
    /**
     * @var string stashed file path
     */
    private $file;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var string|string base64 representation of file contents
     */
    protected $content;

    /**
     * @param      $file
     * @param null $name
     *
     * @throws MailAttachmentException
     */
    public function __construct( $file, $name = null )
    {
        if ( empty( $file ) ) {
            throw new MailAttachmentException( 'The attachment path cannot be empty' );
        }

        $this->name = $name;

        if ( file_exists( $file ) ) {
            $this->file = $file;
        }
        else {
            throw new MailAttachmentException( "Cannot attach file [%s]", $file );
        }

        // detect attachment is base64
        if ( !base64_decode( $file, true ) ) {
            if ( !$this->content = base64_encode( file_get_contents( $this->file ) ) ) {
                throw new MailAttachmentException( sprintf( "Error processing attachment [%s]", $file ) );
            }
        }
    }

    /**
     * @return null|string
     */
    protected function getName()
    {
        if ( !$this->name ) {
            $this->name = basename( $this->file );
        }

        return $this->name;
    }

    /**
     * @return string
     */
    protected function getContent()
    {
        return sprintf( "data:%s;base64,%s", $this->getMime(), $this->content );
    }

    /**
     * @return string
     */
    protected function getMime()
    {
        return $this->getMimeType( $this->file );
    }

    /**
     * Copyright (c) 2008, Donovan Schönknecht.  All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions are met:
     *
     * - Redistributions of source code must retain the above copyright notice,
     *   this list of conditions and the following disclaimer.
     * - Redistributions in binary form must reproduce the above copyright
     *   notice, this list of conditions and the following disclaimer in the
     *   documentation and/or other materials provided with the distribution.
     *
     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
     * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
     * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
     * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
     * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
     * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
     * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
     * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
     * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
     * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
     * POSSIBILITY OF SUCH DAMAGE.
     *
     * Amazon S3 is a trademark of Amazon.com, Inc. or its affiliates.
     *
     */
    protected function getMimeType( $filepath )
    {

        if ( extension_loaded( 'fileinfo' ) && isset( $_ENV['MAGIC'] ) &&
             ( $finfo = finfo_open( FILEINFO_MIME, $_ENV['MAGIC'] ) ) !== false
        ) {

            if ( ( $type = finfo_file( $finfo, $filepath ) ) !== false ) {
                // Remove the charset and grab the last content-type
                $type = explode( ' ', str_replace( '; charset=', ';charset=', $type ) );
                $type = array_pop( $type );
                $type = explode( ';', $type );
                $type = trim( array_shift( $type ) );
            }
            finfo_close( $finfo );
            // If anyone is still using mime_content_type()
        }
        elseif ( function_exists( 'mime_content_type' ) ) {
            $type = trim( mime_content_type( $filepath ) );
        }

        if ( $type !== false && strlen( $type ) > 0 ) {
            return $type;
        }

        // Otherwise do it the old fashioned way
        static $exts = array(
            'jpg'  => 'image/jpeg',
            'gif'  => 'image/gif',
            'png'  => 'image/png',
            'tif'  => 'image/tiff',
            'tiff' => 'image/tiff',
            'ico'  => 'image/x-icon',
            'swf'  => 'application/x-shockwave-flash',
            'pdf'  => 'application/pdf',
            'zip'  => 'application/zip',
            'gz'   => 'application/x-gzip',
            'tar'  => 'application/x-tar',
            'bz'   => 'application/x-bzip',
            'bz2'  => 'application/x-bzip2',
            'txt'  => 'text/plain',
            'asc'  => 'text/plain',
            'htm'  => 'text/html',
            'html' => 'text/html',
            'css'  => 'text/css',
            'js'   => 'text/javascript',
            'xml'  => 'text/xml',
            'xsl'  => 'application/xsl+xml',
            'ogg'  => 'application/ogg',
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/x-wav',
            'avi'  => 'video/x-msvideo',
            'mpg'  => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'mov'  => 'video/quicktime',
            'flv'  => 'video/x-flv',
            'php'  => 'text/x-php'
        );
        $ext = strtolower( pathInfo( $filepath, PATHINFO_EXTENSION ) );

        return isset( $exts[$ext] ) ? $exts[$ext] : 'application/octet-stream';
    }

    /**
     * generates array of content data
     * @see Message::addAttachment()
     * @return array
     */
    public function getAttachment()
    {
        return [
            'attachmentName' => $this->getName(),
            'content'        => $this->getContent(),
            'contentType'    => $this->getMime()
        ];
    }
}
