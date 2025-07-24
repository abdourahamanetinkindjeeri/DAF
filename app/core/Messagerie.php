<?php

namespace App\Core;

use Twilio\Rest\Client;
use \Exception;

// dump_die(TWILIO_SID);
class Messagerie
{
  private Client $twilio;
  private string $messagingServiceSid;

 

  public function __construct()
  {

    foreach (['TWILIO_SID', 'TOKEN', 'MESSAGING_SID'] as $const) {
      if (!defined($const)) {
        throw new \RuntimeException("Constante $const manquante pour la messagerie.");
      }
    }


    $this->twilio = new Client(TWILIO_SID, TOKEN);
    $this->messagingServiceSid = MESSAGING_SID;
  }



  public function sendMessage(string $to, string $body): string
  {


    try {
      $message = $this->twilio->messages->create(
        $to,
        [
          'messagingServiceSid' => $this->messagingServiceSid,
          'body' => $body,
        ]
      );
      // dump_die(SID . '--------' . TOKEN . '--------------' . MESSAGING_SID);
      return $message->sid;
    } catch (Exception $e) {
      throw new \RuntimeException("Échec de l'envoi du SMS : " . $e->getMessage());
    }
  }
}
