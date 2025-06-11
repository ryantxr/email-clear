<?php
namespace App;
/*
 * This file is a proof of concept only.
 * Use as a reference only.
 * Do not modify
 */
use Webklex\PHPIMAP\Client as MailClient;
use Webklex\PHPIMAP\Support\Masks\Message; // Add this import for masks
use Webklex\PHPIMAP\ClientManager; // Correct class for instantiating clients

class MailDeleter
{
    protected $host, $port, $username, $password;
    protected $out;
    public function __construct($host, $port, $username, $password, ?\Closure $out=null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->out = $out;
    }

    public function delete()
    {
        try {
            $clientManager = new ClientManager();

            $this->out("Create ... {$this->host}");
            $client = $clientManager->make([
                'host'          => $this->host,
                'port'          => $this->port,
                'encryption'    => 'ssl',
                'validate_cert' => true,
                'username'      => $this->username,
                'password'      => $this->password,
                'protocol'      => 'imap'
            ]);
            $this->out("Created");
            
            // Connect to the IMAP server
            $client->connect();
            $this->out("Connected");
            
            // Get the inbox
            $inbox = $client->getFolder('INBOX');
            
            // Retrieve all messages (read and unread)
            $messages = $inbox->messages()->all()->get();
            foreach ($messages as $message) {
                $subject = $message->getSubject();
            
                // Regex to match your pattern
                if (preg_match('/[A-Z0-9]+ [A-Z0-9]+$/', $subject)) {
                    $this->out("Matched {$subject}");
                    // Mark email for deletion
                    $message->delete();
                } else {
                    $this->out("Skip {$subject}");
                }
            }
            
            // Expunge deleted messages
            $client->expunge();
            
        } catch ( \Webklex\PHPIMAP\Exceptions\MaskNotFoundException $e ) {
            $this->out(sprintf('%s %d', $e->getMessage(), $e->getCode()));
            $msg = sprintf('File %s Line %d', $e->getFile(), $e->getLine());
            $msg1 = null;
            $stack = $e->getTrace();
            $n = count($stack);
            foreach ( $stack as $i => $trace ) {
                if ( ! empty($msg1) ) $msg1 .= "\n";
                $function = sprintf('%s%s', ( ! empty($trace['class']) ? $trace['class'] . '::' : null), $trace['function']);
                $msg1 .= sprintf("%d %s %s:%d", --$n, $function, $trace['file']::null, $trace['line']::null);
            }
            if ( ! empty($msg1) )$msg .= "\n" . $msg1;
            $this->out($msg);
        } catch ( \Exception $e ) {
            $this->out(sprintf('%s %d', $e->getMessage(), $e->getCode()));
            $msg = sprintf('File: %s Line: %d', $e->getFile(), $e->getLine());
            $msg1 = null;
            $stack = $e->getTrace();
            $n = count($stack);
            foreach ( $stack as $i => $trace ) {
                if ( ! empty($msg1) ) $msg1 .= "\n";
                $function = sprintf('%s%s', ( ! empty($trace['class']) ? $trace['class'] . '::' : null), $trace['function']);
                $msg1 .= sprintf("%d %s %s:%d", --$n, $function, $trace['file']::null, $trace['line']::null);
            }
            if ( ! empty($msg1) )$msg .= "\n" . $msg1;
            $this->out($msg);
        }
        
    }

    protected function out($message)
    {
        if ( $this->out instanceof \Closure ) {
            ($this->out)($message);
        }
    }
}