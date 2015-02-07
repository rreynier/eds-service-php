<?php

class MessageAttachmentTest extends TestFixture
{

    public function testAddingLocalAttachment()
    {
        $filePng  = __DIR__ . '/assets/file.png';
        $fileBmp  = __DIR__ . '/assets/file.bmp';
        $fileGif  = __DIR__ . '/assets/file.gif';
        $fileJpg  = __DIR__ . '/assets/file.jpg';
        $fileTif  = __DIR__ . '/assets/file.tif';
        $fileTxt  = __DIR__ . '/assets/file.txt';
        $fileDocx = __DIR__ . '/assets/file.docx';
        $fileXlsx = __DIR__ . '/assets/file.xlsx';
        $filePdf  = __DIR__ . '/assets/file.pdf';

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
            'attachments'      => [
                [
                    'attachmentName' => 'file.png',
                    'content'        => sprintf( "data:image/png;base64,%s",
                                                 base64_encode( file_get_contents( $filePng ) )
                    ),
                    'contentType'    => 'image/png'
                ],
                [
                    'attachmentName' => 'file.bmp',
                    'content'        => sprintf( "data:image/x-ms-bmp;base64,%s",
                                                 base64_encode( file_get_contents( $fileBmp ) )
                    ),
                    'contentType'    => 'image/x-ms-bmp'
                ],
                [
                    'attachmentName' => 'file.gif',
                    'content'        => sprintf( "data:image/gif;base64,%s",
                                                 base64_encode( file_get_contents( $fileGif ) )
                    ),
                    'contentType'    => 'image/gif'
                ],
                [
                    'attachmentName' => 'file.jpg',
                    'content'        => sprintf( "data:image/jpeg;base64,%s",
                                                 base64_encode( file_get_contents( $fileJpg ) )
                    ),
                    'contentType'    => 'image/jpeg'
                ],
                [
                    'attachmentName' => 'file.tif',
                    'content'        => sprintf( "data:image/tiff;base64,%s",
                                                 base64_encode( file_get_contents( $fileTif ) )
                    ),
                    'contentType'    => 'image/tiff'
                ],
                [
                    'attachmentName' => 'file.txt',
                    'content'        => sprintf( "data:text/plain;base64,%s",
                                                 base64_encode( file_get_contents( $fileTxt ) )
                    ),
                    'contentType'    => 'text/plain'
                ],
                [
                    'attachmentName' => 'file.docx',
                    'content'        => sprintf( "data:application/zip;base64,%s",
                                                 base64_encode( file_get_contents( $fileDocx ) )
                    ),
                    'contentType'    => 'application/zip' // ms docs default to app/zip mime
                ],
                [
                    'attachmentName' => 'file.xlsx',
                    'content' => sprintf( "data:application/zip;base64,%s",
                                                 base64_encode( file_get_contents( $fileXlsx ) )
                    ),
                    'contentType' => 'application/zip' // ms docs default to app/zip mime
                ],
                [
                    'attachmentName' => 'file.pdf',
                    'content' => sprintf( "data:application/pdf;base64,%s",
                                                 base64_encode( file_get_contents( $filePdf ) )
                    ),
                    'contentType'    => 'application/pdf'
                ],
            ],
            'exposeRecipients' => true
        ];

        $Mail = new \Idexx\EdsServicePhp\Message();
        $Mail->setSubject( 'Foo' );
        $Mail->setTo( 'someone@example.com', 'John' );
        $Mail->setFrom( 'internal@example.com', 'Mike' );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $filePng ) );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $fileBmp ) );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $fileGif ) );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $fileJpg ) );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $fileTif ) );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $fileTxt ) );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $fileDocx ) );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $fileXlsx ) );
        $Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $filePdf ) );
        $this->assertEquals( $canned, $Mail->toArray() );
    }
}
