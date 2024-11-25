<?php
namespace AISpamShield\Spam;

use AIEngine\AIEngine;

/**
 * Class EmailValidator
 * Handles email validation to detect and log spam activities.
 */
class EmailValidator {
    /**
     * Constructor.
     * Hooks into the WordPress email sending process to validate emails.
     */
    public function __construct() {
        add_action('pre_wp_mail', [$this, 'validateAndLogEmail'], 10, 5);
    }

    /**
     * Validates and logs the email before sending.
     * 
     * @param string $to Recipient email address.
     * @param string $subject Email subject.
     * @param string $message Email body/message.
     * @param mixed $headers Email headers.
     * @param mixed $attachments Email attachments.
     * @return bool Returns false if the email is detected as spam, halting the send process.
     */
    public function validateAndLogEmail($to, $subject, $message, $headers, $attachments) {
        if ($this->isSpam($message)) {
            return false; // Stop the email from being sent
        }
    }

    /**
     * Checks if the email message is spam.
     * 
     * @param string $message The email message to check.
     * @return bool Returns true if the message is spam, false otherwise.
     */
    private function isSpam($message) {
        $api_key = get_option('api_key', '');
        if (empty($api_key)) {
            error_log('API key is missing for spam detection.');
            return true; // Assume spam if the API key is missing
        }

        // Initialize AI engine with API key and check for spam
        $ai_client = new AIEngine($api_key);
        $prompt = "Check if this message is spam: $message";
        $response_data = $ai_client->generateContent($prompt);
        
        // Assume the response_data is returned as a boolean
        return $response_data;
    }
}

// Overridden wp_mail function with spam check
if (!function_exists('wp_mail')) {
    /**
     * Custom wp_mail function.
     * 
     * Integrates spam checking before sending an email.
     *
     * @param string $to Recipient email address.
     * @param string $subject Email subject.
     * @param string $message Email body/message.
     * @param mixed $headers Email headers.
     * @param mixed $attachments Email attachments.
     * @return bool Returns false if the email is flagged as spam, otherwise sends the email.
     */
    function wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
        // Trigger spam check
        if (!apply_filters('pre_wp_mail', true, $to, $subject, $message, $headers, $attachments)) {
            return false; // Stop email if flagged as spam
        }
        // Send email if not spam
        return _wp_mail($to, $subject, $message, $headers, $attachments);
    }
}

// Placeholder for the real wp_mail function
if (!function_exists('_wp_mail')) {
    /**
     * Simulates sending an email.
     * 
     * This is a placeholder function meant to simulate the actual email sending function.
     *
     * @param string $to Recipient email address.
     * @param string $subject Email subject.
     * @param string $message Email body/message.
     * @param mixed $headers Email headers.
     * @param mixed $attachments Email attachments.
     * @return bool Simulates successful email send.
     */
    function _wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
        return true; // Simulating email sending
    }
}