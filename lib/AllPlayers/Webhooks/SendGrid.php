<?php
/**
 * @file
 * Provides the Email webhooks plugin definition.
 */

namespace AllPlayers\Webhooks;
require_once '../../unirest-php/lib/Unirest.php';
require_once '../../sendgrid-php/lib/SendGrid.php';

/**
 * Defines email app that will send emails using SendGrid.
 */
class SendGrid extends Webhook
{
    /**
     * The URL of the webhook.
     *
     * @var string
     */
    public $domain = '';

    /**
     * The authentication method used in the post request.
     *
     * @var string
     */
    public $authentication = 'basic_auth';

    /**
     * The sendgrid object.
     *
     * @var object
     */
    private $sendgrid = NULL;

    /**
     * Authenticate using basic auth.
     */
    public function __construct($args = array())
    {
        parent::__construct();
        SendGrid::register_autoloader();
        // @todo Supply sendgrid credentials.
        $this->sendgrid = new SendGrid('username', 'password');
    }

    /**
     * Overrides the post function to not do anything.
     */
    public function post($url)
    {
        // Don't need to do anything here.
    }

    /**
     * Sends a request.
     *
     * @param array $data
     *   Data to be sent to SendGrid.
     *
     * @return \Guzzle\Http\Message\Response
     *   Response from the service.
     */
    public function send(array $data)
    {
        $mail = new SendGrid\Mail();
        $mail->setFrom($data['from']);
        $mail->setSubject($data['subject']);
        if (is_array($data['body'])) {
            $data['body'] = implode($data['body']);
        }
        $mail->setText($data['body']);
        $to = explode(',', $data['to']);
        foreach ($to as $email) {
            $mail->addTo($email);
        }
        foreach ($data['headers'] as $key => $header) {
            $mail->addHeader($key, $header);
        }
        return $this->sendgrid->web->send($mail);
    }
}
