# Inventory System Chatbot Setup Guide

This guide will help you set up the ChatGPT-powered chatbot for your inventory management system.

## Features

- **Real-time Chat Interface**: Interactive chatbot integrated into the admin dashboard
- **Database Integration**: Queries your inventory data in real-time
- **Smart Responses**: Uses ChatGPT to provide intelligent, contextual responses
- **Data Visualization**: Displays query results in formatted tables
- **Fallback Support**: Works even when the AI service is unavailable

## Setup Instructions

### 1. Get OpenAI API Key

1. Visit [OpenAI Platform](https://platform.openai.com/)
2. Sign up or log in to your account
3. Navigate to API Keys section
4. Create a new API key
5. Copy the API key (starts with `sk-`)

### 2. Configure the API Key

1. Open `includes/chatbot_config.php`
2. Replace `YOUR_OPENAI_API_KEY_HERE` with your actual API key:
   ```php
   define('OPENAI_API_KEY', 'sk-your-actual-api-key-here');
   ```

### 3. Test the Installation

1. Log in to your admin panel
2. Navigate to the admin dashboard
3. Click the "Inventory Assistant Chatbot" panel to expand it
4. Try asking questions like:
   - "Show me low stock products"
   - "What are my top selling products?"
   - "How many products do I have?"
   - "Show me recent sales"

## Configuration Options

You can customize the chatbot behavior by modifying `includes/chatbot_config.php`:

### API Settings
- `OPENAI_MODEL`: AI model to use (default: gpt-3.5-turbo)
- `OPENAI_MAX_TOKENS`: Maximum response length (default: 500)
- `OPENAI_TEMPERATURE`: Response creativity (0.0-1.0, default: 0.7)

### Chatbot Settings
- `CHATBOT_ENABLED`: Enable/disable the chatbot (default: true)
- `CHATBOT_MAX_MESSAGE_LENGTH`: Maximum user message length (default: 500)
- `CHATBOT_FALLBACK_ENABLED`: Enable fallback responses (default: true)

### Database Settings
- `CHATBOT_MAX_RESULTS`: Maximum results per query (default: 20)
- `CHATBOT_LOW_STOCK_THRESHOLD`: Low stock threshold (default: 50)

## Supported Queries

The chatbot can handle various types of inventory-related questions:

### Product Queries
- "Show me all products"
- "What products are low in stock?"
- "Show me the most expensive products"
- "What are the cheapest products?"

### Sales Queries
- "Show me today's sales"
- "What are my recent sales?"
- "Show me top selling products"
- "What's my total revenue?"

### Category Queries
- "Show me all categories"
- "How many products are in each category?"

### General Queries
- "How many products do I have?"
- "How many users are registered?"
- "What's my inventory summary?"

## Troubleshooting

### Common Issues

1. **"Invalid or missing OpenAI API key"**
   - Check that you've set the API key in `includes/chatbot_config.php`
   - Ensure the API key starts with `sk-`

2. **"Chatbot is currently disabled"**
   - Set `CHATBOT_ENABLED` to `true` in the config file

3. **"AI service is currently unavailable"**
   - Check your internet connection
   - Verify your OpenAI API key is valid
   - Check if you have sufficient API credits

4. **No response from chatbot**
   - Check browser console for JavaScript errors
   - Ensure `chatbot_api.php` is accessible
   - Verify database connection

### Debug Mode

To enable debug mode, add this to the top of `chatbot_api.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Security Considerations

1. **API Key Security**: Never commit your API key to version control
2. **Access Control**: The chatbot respects user authentication
3. **Input Validation**: All user inputs are sanitized
4. **Rate Limiting**: Consider implementing rate limiting for production use

## Customization

### Adding New Query Types

To add support for new types of queries, modify the `queryInventoryData()` function in `chatbot_api.php`:

```php
// Add new query pattern
if (strpos($question, 'your_keyword') !== false) {
    $query = "YOUR_SQL_QUERY_HERE";
    $result = $db->query($query);
    while ($row = $db->fetch_assoc($result)) {
        $results[] = $row;
    }
}
```

### Styling the Chatbot

Modify the CSS in `admin.php` to customize the chatbot appearance:
- Change colors in the `.message-content` classes
- Adjust the chat container height
- Modify the input styling

## Support

If you encounter any issues:

1. Check the browser console for JavaScript errors
2. Verify all files are properly uploaded
3. Ensure your server supports cURL and JSON
4. Check that your database connection is working

## Cost Considerations

- OpenAI API charges per token used
- GPT-3.5-turbo is cost-effective for most use cases
- Monitor your usage in the OpenAI dashboard
- Consider implementing caching for frequently asked questions

## Future Enhancements

Potential improvements you could implement:

1. **Conversation Memory**: Remember previous context in the conversation
2. **Voice Input**: Add speech-to-text functionality
3. **Export Data**: Allow users to export query results
4. **Scheduled Reports**: Set up automated inventory reports
5. **Multi-language Support**: Support for different languages
6. **Advanced Analytics**: More sophisticated data analysis capabilities
