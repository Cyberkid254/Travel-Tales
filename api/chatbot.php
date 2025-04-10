<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$message = strtolower(trim($data['message']));
$sessionId = $data['sessionId'] ?? null;
$timestamp = $data['timestamp'] ?? date('c');

// Predefined responses
$responses = [
    'greetings' => [
        'patterns' => ['hello', 'hi', 'hey', 'greetings', 'good morning', 'good afternoon', 'good evening'],
        'response' => "Hello! 👋 How can I help you plan your perfect trip today?",
        'suggestions' => ['Show me popular destinations', 'How to book a trip?', 'Travel tips']
    ],
    'destinations' => [
        'patterns' => ['popular destinations', 'where to go', 'best places', 'recommend', 'destination'],
        'response' => "Here are some of our most popular destinations:\n\n🗼 Paris, France\n🗽 New York City, USA\n🏺 Santorini, Greece\n🗻 Tokyo, Japan\n\nWhich one would you like to learn more about?",
        'suggestions' => ['Tell me about Paris', 'Show me Tokyo guides', 'Santorini travel tips']
    ],
    'booking' => [
        'patterns' => ['book', 'reservation', 'how to book', 'booking process'],
        'response' => "Booking with Travel Tales is easy! Here's how:\n\n1. Choose your destination\n2. Select your travel dates\n3. Pick your accommodation\n4. Add any activities\n5. Complete payment\n\nWould you like me to help you start the booking process?",
        'suggestions' => ['Start booking', 'View available dates', 'Payment options']
    ],
    'tips' => [
        'patterns' => ['tips', 'advice', 'suggestions', 'help', 'guide'],
        'response' => "Here are some essential travel tips:\n\n✈️ Book flights 3-4 months in advance\n🏨 Read recent accommodation reviews\n💰 Set a daily budget\n🎒 Pack light and smart\n\nWould you like more specific tips for any category?",
        'suggestions' => ['Flight booking tips', 'Accommodation tips', 'Budgeting advice']
    ],
    'contact' => [
        'patterns' => ['contact', 'support', 'help desk', 'phone', 'email'],
        'response' => "You can reach our support team through:\n\n📧 Email: support@traveltales.com\n📞 Phone: +1-888-TRAVEL-TALES\n💬 Live Chat: Available 24/7\n\nHow would you like to contact us?",
        'suggestions' => ['Send email', 'Start live chat', 'View contact hours']
    ]
];

// Find the best matching response
function findResponse($message, $responses) {
    foreach ($responses as $category => $data) {
        foreach ($data['patterns'] as $pattern) {
            if (strpos($message, $pattern) !== false) {
                return [
                    'response' => $data['response'],
                    'suggestions' => $data['suggestions']
                ];
            }
        }
    }
    
    // Default response if no pattern matches
    return [
        'response' => "I'm here to help you plan your perfect trip! You can ask me about:\n\n• Popular destinations\n• Booking process\n• Travel tips\n• Support and contact\n\nWhat would you like to know?",
        'suggestions' => ['Show me destinations', 'How to book?', 'Travel tips', 'Contact support']
    ];
}

// Log the interaction (you might want to store this in a database)
$logEntry = [
    'timestamp' => $timestamp,
    'session_id' => $sessionId,
    'message' => $message,
];

// Get the appropriate response
$responseData = findResponse($message, $responses);

// Send the response
echo json_encode([
    'response' => $responseData['response'],
    'suggestions' => $responseData['suggestions'],
    'timestamp' => date('c')
]);
?> 