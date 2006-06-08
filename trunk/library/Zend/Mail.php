<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Mail_Exception
 */
require_once 'Zend/Mail/Exception.php';

/**
 * Zend_Mime
 */
require_once 'Zend/Mime.php';

/**
 * Zend_Mime_Message
 */
require_once 'Zend/Mime/Message.php';

/**
 * Zend_Mime_Part
 */
require_once 'Zend/Mime/Part.php';


/**
 * Class for sending an email.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail extends Zend_Mime_Message
{

    /**
     * @var Zend_Mail_Transport_Interface
     */
    static protected $_defaultTransport = null;
    protected $_headers = array();
    protected $_recipients = array();
    protected $_charset = null;
    protected $_from = null;
    protected $_to = array();
    protected $_subject = null;
    protected $_textBody = false;
    protected $_htmlBody = false;
    protected $_hasAttachments = false;
    protected $_mimeBoundary = null;
    protected $_isMultipartAlternative = false;

    /**
     * sets the default Zend_Mail_Transport_Interface for all following
     * uses of Zend_Mail::send();
     *
     * @todo Allow passing a string to indicate the transport to load
     * @todo Allow passing in optional options for the transport to load
     * @param  Zend_Mail_Transport_Interface $transport
     */
    static public function setDefaultTransport(Zend_Mail_Transport_Interface $transport)
    {
        self::$_defaultTransport = $transport;
    }

    /**
     * Public constructor
     *
     * @param string $charset
     */
    public function __construct($charset='iso-8859-1')
    {
        $this->_charset = $charset;
    }

    /**
     * Set an arbitrary mime boundary for this mail object.
     * If not set, Zend_Mime will generate one.
     *
     * @param String $boundary
     */
    public function setMimeBoundary($boundary)
    {
      $this->_mimeBoundary = $boundary;
    }

    /**
     * returns the boundary string used for this
     * email.
     *
     * @return string
     */
    public function getMimeBoundary()
    {
        return $this->_mimeBoundary;
    }


    /**
     * Sets the Text body for this message.
     *
     * @param String $txt
     * @param String $charset
     * @return Zend_Mime_Part
    */
    public function setBodyText($txt, $charset=null)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new Zend_Mime_Part($txt);
        $mp->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $mp->type = Zend_Mime::TYPE_TEXT;
        $mp->disposition = Zend_Mime::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->_textBody = $mp;
        return $mp;
    }


    /**
     * Sets the HTML Body for this eMail
     *
     * @param String $html
     * @param String $charset
     * @return Zend_Mime_Part
     */
    public function setBodyHtml($html, $charset=null)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new Zend_Mime_Part($html);
        $mp->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $mp->type = Zend_Mime::TYPE_HTML;
        $mp->disposition = Zend_Mime::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->_htmlBody = $mp;
        return $mp;
    }


    /**
     * Adds an attachment to this eMail
     *
     * @param String $body
     * @param String $mimeType
     * @param String $disposition
     * @param String $encoding
     * @return Zend_Mime_Part Created Part Object for advanced settings
     */
    public function addAttachment($body,
                                  $mimeType    = Zend_Mime::TYPE_OCTETSTREAM,
                                  $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
                                  $encoding    = Zend_Mime::ENCODING_BASE64)
    {

        $mp = new Zend_Mime_Part($body);
        $mp->encoding = $encoding;
        $mp->type = $mimeType;
        $mp->disposition = $disposition;

        $this->addPart($mp);
        $this->_hasAttachments = true;

        return $mp;
    }


    /**
     * Encode header fields according to RFC1522 if it contains
     * non-printable characters
     *
     * @param String $value
     * @return String
     */
    protected function _encodeHeader($value)
    {
      if (Zend_Mime::isPrintable($value)) {
          return $value;
      } else {
          $quotedValue = Zend_Mime::encodeQuotedPrintable($value);
          $quotedValue = str_replace('?', '=3F', $quotedValue);
          return '=?' . $this->_charset . '?Q?' . $quotedValue . '?=';
      }
    }


    /**
     * Adds another custom header to this eMail
     * if append is true and the header does already
     * exist, append the given string to the existing
     * header.
     *
     * @param String $headerName
     * @param String $value
     * @param Boolean $append
     */
    protected function _storeHeader($headerName, $value, $append=false)
    {
        $value = strtr($value,"\r\n\t",'???');
        if ($append) {
            // append value if a header with this name already exists
            if (array_key_exists($headerName, $this->_headers) ) {
                $this->_headers[$headerName][1] .= ',' . Zend_Mime::LINEEND
                                                 . ' ' . $value;
            } else {
                $this->_headers[$headerName] = array($headerName, $value);
            }
        } else {
            $this->_headers[] = array($headerName, $value);
        }
    }


    /**
     * Add a recipient
     *
     * @param string $email
     */
    protected function _addRecipient($email, $to = false)
    {
        // prevent duplicates
        $this->_recipients[$email] = 1;

        if ($to) {
            $this->_to[] = $email;
        }
    }


    /**
     * Helper function for adding a Recipient and the
     * according header
     *
     * @param String $headerName
     * @param String $name
     * @param String $email
     */
    protected function _addRecipientAndHeader($headerName, $name, $email)
    {
        $email = strtr($email,"\r\n\t",'???');
        $this->_addRecipient($email, ('To' == $headerName) ? true : false);
        if ($name != '') {
            $name = $this->_encodeHeader('"' .$name. '" ');
        }

        $this->_storeHeader($headerName, $name .'<'. $email . '>', true);
    }


    /**
     * Adds to-header and recipient
     *
     * @param String $name
     * @param String $email
     */
    public function addTo($email, $name='')
    {
        $this->_addRecipientAndHeader('To', $name, $email);
    }


    /**
     * Adds Cc-header and recipient
     *
     * @param String $name
     * @param String $email
     */
    public function addCc($email, $name='')
    {
        $this->_addRecipientAndHeader('Cc', $name, $email);
    }


    /**
     * Adds Bcc recipient
     *
     * @param String $email
     */
    public function addBcc($email)
    {
        $email = strtr($email,"\r\n\t",'???');
        $this->_addRecipient($email);
    }

    /**
     * Return list of recipient email addresses
     *
     * @return Array (of strings)
     */
    public function getRecipients()
    {
        return array_keys($this->_recipients);
    }

    /**
     * Sets From Header and sender of the eMail
     *
     * @param String $email
     * @param String $name
     */
    public function setFrom($email, $name = '')
    {
        if ($this->_from === null) {
            $email = strtr($email,"\r\n\t",'???');
            $this->_from = $email;
            $this->_storeHeader('From', $this->_encodeHeader('"'.$name.'"').' <'.$email.'>', true);
        } else {
            throw new Zend_Mail_Exception('From Header set twice');
        }
    }


    /**
     * Sets the subject of the eMail
     *
     * @param String $subject
     */
    public function setSubject($subject)
    {
        if ($this->_subject === null) {
            $subject = strtr($subject,"\r\n\t",'???');
            $this->_subject = $subject;
            $this->_storeHeader('Subject', $this->_encodeHeader($subject));
        } else {
            throw new Zend_Mail_Exception('Subject set twice');
        }
    }

    /**
     * returns the subject of the mail
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Add a custom header to this eMail
     *
     * @param String $name
     * @param String $value
     * @param Boolean $append
     */
    public function addHeader($name, $value, $append=false)
    {
        if (in_array(strtolower($name), array('to', 'cc', 'bcc', 'from', 'subject'))) {
            throw new Zend_Mail_Exception('Cannot set standardheader here');
        }

        $value = strtr($value,"\r\n\t",'???');
        $value = $this->_encodeHeader($value);
        $this->_storeHeader($name, $value, $append);
    }


    /**
     * Return all Mail Headers as a string. If a boundary is
     * given, a multipart-header is generated with a mime-type
     * of multipart/alternative or multipart/mixed depending on
     * the MailParts in this ZMail object.
     *
     * @param String $boundary
     * @return String
     */
    protected function _getHeaders($boundary=null)
    {
        $out = $this->_headers;

        if ($boundary) {
            // Build Multipart Mail
            if ($this->_hasAttachments) {
                $type = Zend_Mime::MULTIPART_MIXED;
            } elseif ($this->_textBody && $this->_htmlBody) {
                $type = Zend_Mime::MULTIPART_ALTERNATIVE;
            } else {
                $type = Zend_Mime::MULTIPART_MIXED;
            }

            $out[] = array(
                'Content-Type', 
                $type . '; charset="' . $this->_charset . '";'
                  . Zend_Mime::LINEEND
                  . " " . 'boundary="' .$boundary. '"' . Zend_Mime::LINEEND
                  . 'MIME-Version: 1.0'
            );
        }

        // Sanity check on headers -- should not be > 998 characters
        $sane = true;
        foreach ($out as $header) {
            $first = true;
            foreach (explode(Zend_Mime::LINEEND, $header[1]) as $line) {
                if ($first) {
                    $line = $header[0] . ': ' . $line;
                }

                if (998 < strlen($line)) {
                    $sane = false;
                    break 2; 
                }
            }
        }
        if (!$sane) {
            throw new Zend_Mail_Exception('At least one mail header line is too long');
        }

        return $out;
    }


    /**
     * returns the sender of the mail
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->_from;
    }


    /**
     * Generate Mime Compliant Message from the current configuration
     *
     * Extends the parent class to ensure that multipart/alternative is
     * added to the mail parts if both text and html are present in a
     * message that contains an attachment.
     *
     * @return string
     */
    protected function _buildBody()
    {
        if ($this->_textBody && $this->_htmlBody) {
            // Generate unique boundary for multipart/alternative
            $mime = new Zend_Mime(null);
            $boundaryLine = $mime->boundaryLine();

            $body = $boundaryLine
                 . $this->_textBody->getHeaders()
                 . Zend_MIME::LINEEND
                 . $this->_textBody->getContent()
                 . Zend_MIME::LINEEND
                 . $boundaryLine
                 . $this->_htmlBody->getHeaders()
                 . Zend_MIME::LINEEND
                 . $this->_htmlBody->getContent()
                 . Zend_MIME::LINEEND
                 . $boundaryLine;

            $mp = new Zend_Mime_Part($body);
            $mp->type        = Zend_Mime::MULTIPART_ALTERNATIVE;
            $mp->boundary    = $mime->boundary();
            
            // Ensure first part contains text alternatives
            array_unshift($this->_parts, $mp);
            $this->_isMultipartAlternative = true;
        } elseif ($this->_htmlBody) {
            array_unshift($this->_parts, $this->_htmlBody);
        } elseif ($this->_textBody) {
            array_unshift($this->_parts, $this->_textBody);
        }
    }

    /**
     * Sends a Multipart eMail using the given Transport
     *
     * @param Zend_Mail_Transport_Interface $transport
     */
    protected function _sendMultiPart(Zend_Mail_Transport_Interface $transport)
    {
        $mime = new Zend_Mime($this->_mimeBoundary);
        $this->setMime($mime);
        $headers = $this->_getHeaders($mime->boundary());
        $body = $this->generateMessage();
        $this->_mimeBoundary = $mime->boundary();  // if no boundary was set before, set the used boundary now
        $transport->sendMail($this, $body, $headers, $this->_to);
    }

    /**
     * Sends a single part message using a given transport
     *
     * @param Zend_Mail_Transport_Interface $transport
     */
    protected function _sendSinglePart(Zend_Mail_Transport_Interface $transport)
    {
        $headers = array_merge($this->_getHeaders(), $this->getPartHeadersArray(0));
        $body = $this->generateMessage();
        $this->_mimeBoundary = null; // singlepart - no boundary used...
        $transport->sendMail($this, $body, $headers, $this->_to);
    }


    /**
     * Send this mail using the given transport
     *
     * @param Zend_Mail_Transport_Interface $transport
     */
    protected function _sendMail(Zend_Mail_Transport_Interface $transport)
    {
        $this->_buildBody();
        $count = count($this->_parts);

        if ($count > 1) {
            $this->_sendMultiPart($transport);
        } elseif ($count == 1) {
            $this->_sendSinglePart($transport);
        } else {
            echo "Found $count parts\n";
            echo ($this->_textBody || $this->_htmlBody) ? 'Text or HTML found' : 'No txt or HTML found';
            echo "\n";
            throw new Zend_Mail_Exception('Empty Mail cannot be sent');
        }
    }


    /**
     * Sends this email using the given transport or a previously
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param Zend_Mail_Transport_Interface $transport
     * @return void
     */
    public function send($transport=null)
    {
        if ($transport === null) {
            if (! self::$_defaultTransport instanceof Zend_Mail_Transport_Interface) {
                require_once 'Zend/Mail/Transport/Sendmail.php';
                $transport = new Zend_Mail_Transport_Sendmail();
            } else {
                $transport = self::$_defaultTransport;
            }
        }

        $this->_sendMail($transport);
    }

}
