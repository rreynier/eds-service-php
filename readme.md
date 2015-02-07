# IDEXX EDS Service Adapter for PHP
A simple mail package for communicating with the Idexx Email Delivery Service

## Installation
Add the package to your composer.json
``` json
{
    "require": {
        "idexx/eds-service-php": "dev-master"
    }
}
```

## Example Simple Usage
``` php
$Message = new \Idexx\EdsServicePhp\Message(
        'Foo',
        [ 'someone@example.com' => 'John' ],
        [ 'internal@example.com' => 'Mike' ]
    );
```

## Example API usage
``` php
$Mail = new \Idexx\EdsServicePhp\Message();
$Mail->setSubject( 'Foo' );
$Mail->setTo( 'someone@example.com', 'John' );
$Mail->setFrom( 'internal@example.com', 'Mike' );
```

## Adding attachments
``` php
$Message = new \Idexx\EdsServicePhp\Message(
        'Foo',
        [ 'someone@example.com' => 'John' ],
        [ 'internal@example.com' => 'Mike' ]
    );
$Mail->addAttachment( new \Idexx\EdsServicePhp\MessageAttachment( $file ) );
```

# Transporting Messages to EDS
The Mailer transport class will communicate with the EDS service on your behalf leveraging [Resty.php](https://github.com/fictivekin/resty.php) as an http layer.

## Sending Message entities
``` php
<?php 
$Mailer = new \Idexx\EdsServicePhp\Mailer( 'endpointUri', 'applicationKey', 'apiVersion' );
$Mailer->send( $Message );
```

## Retrieving Message entities
``` php
<?php 
$Mailer = new \Idexx\EdsServicePhp\Mailer( 'endpointUri', 'applicationKey', 'apiVersion' );
$Mailer->getMessage( $messageId );
```

## Use of custom loggers and response parsers
Devlopers can tap into the Resty logging by supplying a callback function
``` php
$Mailer->setLogger(function($log) {} );
```
Setting custom response logger for upstream transports
``` php
$Mailer->setUpstreamParser(function($response){});
```
Likewise with the downstream request for a message
``` php 
$Mailer->setDownstreamParser(function($response){});
```

# Error Handling
This package response to invalid arguments with exceptions.

### MailServiceException
Thrown in a variety of circumstances when a message entity cannot be generated. 

### MailAttachmentException
Thrown when a passed file argument is empty, not found on disk, or cannot be encoded
