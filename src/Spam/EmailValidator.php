<?php 
namespace AISpamShield\Spam;

use AIEngine\AIEngine;

class EmailValidator {
    public function __construct() {
        add_action('pre_wp_mail', [$this, 'validateAndLogEmail'], 10, 2);
    }

    public function validateAndLogEmail($to, $mail) {
        // Logging email details
        $message = $mail['message'];
        // Perform spam check
        if ($this->isSpam($message)) {
            error_log("Invalid email content detected. Email not sent.");
            return false; // Optionally, stop the email from sending by modifying the workflow
        } else {
            error_log("Email content is valid. Proceeding with sending the email.");
        }
    }

    private function isSpam($message) {
        $api_key = get_option('api_key', '');
        if (empty($api_key)) {
            error_log('API key is missing in the AI Comment Moderator plugin settings.');
            return false; // Consider email as spam if the API key is missing
        }

        // Initialize AI engine with API key
        $ai_client = new AIEngine($api_key);
        $response_mode = get_option('response_mode', 'professional');

        // Assuming the AIEngine class has a method `generateContent` that returns boolean
        $prompt = "This is post email content: ".$message." Check it is spam or not. If it is spam, return true; otherwise, return false.";

        $response_data = $ai_client->generateContent($prompt);

        return $response_data === "true"; // Adjust based on the actual return type from AIEngine
    }
}

// Ensure the overridden wp_mail function is declared globally and not inside the class
if (!function_exists('wp_mail')) {
    function wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
        // Trigger the pre_wp_mail action before the actual mail sending
        do_action('pre_wp_mail', $to, $subject, $message, $headers, $attachments);

        // Call the actual wp_mail function logic
        // Assuming _wp_mail is your actual mail function that sends the email
        return _wp_mail($to, $subject, $message, $headers, $attachments);
    }
}

// Dummy _wp_mail function to simulate WordPress' native wp_mail behavior
if (!function_exists('_wp_mail')) {
    function _wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
        // This function would normally handle sending the mail
        return true; // Simulating a successful mail send
    }
}