<?php
/**
 * Chatbot Configuration File
 * 
 * This file contains configuration settings for the inventory chatbot.
 * Make sure to keep your API keys secure and never commit them to version control.
 */

// OpenAI API Configuration
define('OPENAI_API_KEY', 'YOUR_OPENAI_API_KEY_HERE'); // Replace with your actual OpenAI API key
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');
define('OPENAI_MODEL', 'gpt-3.5-turbo');
define('OPENAI_MAX_TOKENS', 500);
define('OPENAI_TEMPERATURE', 0.7);

// Chatbot Settings
define('CHATBOT_MAX_MESSAGE_LENGTH', 500);
define('CHATBOT_ENABLED', true); // Set to false to disable the chatbot

// Database Query Limits
define('CHATBOT_MAX_RESULTS', 20);
define('CHATBOT_LOW_STOCK_THRESHOLD', 50);

// Response Settings
define('CHATBOT_FALLBACK_ENABLED', true); // Enable fallback responses when API fails
define('CHATBOT_DATA_TABLES_ENABLED', true); // Enable data table display in responses

/**
 * Get OpenAI API Key
 * 
 * @return string The OpenAI API key
 */
function getOpenAIKey() {
    return OPENAI_API_KEY;
}

/**
 * Check if chatbot is enabled
 * 
 * @return bool True if chatbot is enabled, false otherwise
 */
function isChatbotEnabled() {
    return CHATBOT_ENABLED;
}

/**
 * Validate API key format
 * 
 * @param string $apiKey The API key to validate
 * @return bool True if valid format, false otherwise
 */
function isValidAPIKey($apiKey) {
    return !empty($apiKey) && 
           $apiKey !== 'YOUR_OPENAI_API_KEY_HERE' && 
           strlen($apiKey) > 20 && 
           strpos($apiKey, 'sk-') === 0;
}

/**
 * Get chatbot configuration
 * 
 * @return array Configuration array
 */
function getChatbotConfig() {
    return [
        'api_key' => getOpenAIKey(),
        'api_url' => OPENAI_API_URL,
        'model' => OPENAI_MODEL,
        'max_tokens' => OPENAI_MAX_TOKENS,
        'temperature' => OPENAI_TEMPERATURE,
        'max_message_length' => CHATBOT_MAX_MESSAGE_LENGTH,
        'enabled' => isChatbotEnabled(),
        'max_results' => CHATBOT_MAX_RESULTS,
        'low_stock_threshold' => CHATBOT_LOW_STOCK_THRESHOLD,
        'fallback_enabled' => CHATBOT_FALLBACK_ENABLED,
        'data_tables_enabled' => CHATBOT_DATA_TABLES_ENABLED
    ];
}
?>
