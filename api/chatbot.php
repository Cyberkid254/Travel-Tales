<?php
header('Content-Type: application/json');

class ChatbotHandler {
    private $responses = [
        'greetings' => [
            'hi' => ["message" => "Hello! 👋 How can I help you plan your next adventure?", 
                    "suggestions" => ["Popular destinations", "Find budget trips", "Travel tips"]],
            'hello' => ["message" => "Hi there! Ready to explore some amazing destinations?",
                       "suggestions" => ["Show me destinations", "Book a trip", "Travel guides"]],
            'hey' => ["message" => "Hey! Looking for travel inspiration?",
                     "suggestions" => ["Trending destinations", "Travel stories", "Local experiences"]]
        ],
        'destinations' => [
            'popular' => ["message" => "Our most popular destinations include:\n- Bali, Indonesia 🏖️\n- Paris, France 🗼\n- Tokyo, Japan 🗾\n- New York, USA 🗽\n- Santorini, Greece 🏛️",
                         "suggestions" => ["Tell me about Bali", "Paris guide", "Book flights"]],
            'cheap' => ["message" => "Here are some budget-friendly destinations:\n- Bangkok, Thailand 🇹🇭\n- Porto, Portugal 🇵🇹\n- Mexico City, Mexico 🇲🇽\n- Budapest, Hungary 🇭🇺\n- Hanoi, Vietnam 🇻🇳",
                       "suggestions" => ["Budget travel tips", "Hostel options", "Local transport"]],
            'safe' => ["message" => "These destinations are known for safety:\n- Singapore 🇸🇬\n- Iceland 🇮🇸\n- New Zealand 🇳🇿\n- Japan 🇯🇵\n- Switzerland 🇨🇭",
                      "suggestions" => ["Travel insurance", "Safety tips", "Solo travel"]]
        ],
        'activities' => [
            'adventure' => ["message" => "Popular adventure activities:\n- Hiking in Nepal 🏔️\n- Scuba in Great Barrier Reef 🤿\n- Safari in Kenya 🦁\n- Skiing in Alps ⛷️\n- Surfing in Hawaii 🏄‍♂️",
                          "suggestions" => ["Book adventure", "Safety info", "Equipment rental"]],
            'cultural' => ["message" => "Enriching cultural experiences:\n- Tea ceremony in Kyoto 🍵\n- Cooking class in Italy 🍝\n- Art tours in Paris 🎨\n- Dance in Spain 💃\n- Meditation in India 🧘‍♀️",
                         "suggestions" => ["Book experience", "Local guides", "Cultural tips"]],
            'relaxation' => ["message" => "Perfect relaxation spots:\n- Maldives beaches 🏖️\n- Thai massage 💆‍♀️\n- Dead Sea floating 🌊\n- Hot springs in Iceland ♨️\n- Yoga retreats in Bali 🧘‍♀️",
                           "suggestions" => ["Spa packages", "Beach resorts", "Wellness tips"]]
        ],
        'booking' => [
            'how' => ["message" => "To book a trip:\n1. Browse our destinations\n2. Select your dates\n3. Choose number of travelers\n4. Complete payment\n\nNeed help with a specific destination?",
                     "suggestions" => ["Show destinations", "Payment options", "Group booking"]],
            'cancel' => ["message" => "You can cancel your booking up to 48 hours before departure for a full refund. Visit your account dashboard or contact our support team.",
                        "suggestions" => ["Refund policy", "Contact support", "Modify booking"]],
            'payment' => ["message" => "We accept various payment methods:\n- Credit/Debit cards 💳\n- PayPal\n- Bank transfer 🏦\n- Digital wallets 📱",
                         "suggestions" => ["Payment security", "Currency exchange", "Installments"]]
        ],
        'tips' => [
            'packing' => ["message" => "Essential packing tips:\n- Roll clothes to save space\n- Pack a first-aid kit\n- Bring universal adapter 🔌\n- Photocopy important docs\n- Pack power bank 🔋",
                         "suggestions" => ["Packing list", "Luggage tips", "Travel essentials"]],
            'budget' => ["message" => "Money-saving tips:\n- Book in advance\n- Travel off-season\n- Use local transport 🚌\n- Cook some meals\n- Get city passes 🎫",
                        "suggestions" => ["Budget planning", "Cheap flights", "Free activities"]],
            'photo' => ["message" => "Photography tips:\n- Golden hour shots 🌅\n- Local perspectives\n- Rule of thirds\n- Ask permission\n- Back up photos 📸",
                       "suggestions" => ["Best spots", "Camera gear", "Photo tours"]]
        ],
        'support' => [
            'contact' => ["message" => "You can reach us through:\n- Email: support@traveltales.com ✉️\n- Phone: 1-800-TRAVEL 📞\n- Live chat (24/7) 💬\n- Social media @TravelTales",
                        "suggestions" => ["Start chat", "Email support", "FAQ"]],
            'help' => ["message" => "I can help you with:\n- Finding destinations 🗺️\n- Booking trips ✈️\n- Travel tips 💡\n- Support queries ❓\n\nWhat would you like to know?",
                      "suggestions" => ["Book trip", "Get support", "Travel tips"]]
        ]
    ];

    private function findBestMatch($message) {
        $message = strtolower($message);
        
        // Check for greetings
        if (preg_match('/\b(hi|hello|hey|howdy)\b/', $message)) {
            $key = array_rand($this->responses['greetings']);
            return $this->responses['greetings'][$key];
        }
        
        // Check for destination queries
        if (strpos($message, 'popular') !== false || strpos($message, 'best') !== false || strpos($message, 'top') !== false) {
            return $this->responses['destinations']['popular'];
        }
        if (strpos($message, 'cheap') !== false || strpos($message, 'budget') !== false || strpos($message, 'affordable') !== false) {
            return $this->responses['destinations']['cheap'];
        }
        if (strpos($message, 'safe') !== false || strpos($message, 'security') !== false) {
            return $this->responses['destinations']['safe'];
        }
        
        // Check for activity queries
        if (strpos($message, 'adventure') !== false || strpos($message, 'exciting') !== false || strpos($message, 'thrill') !== false) {
            return $this->responses['activities']['adventure'];
        }
        if (strpos($message, 'culture') !== false || strpos($message, 'tradition') !== false || strpos($message, 'local') !== false) {
            return $this->responses['activities']['cultural'];
        }
        if (strpos($message, 'relax') !== false || strpos($message, 'spa') !== false || strpos($message, 'peaceful') !== false) {
            return $this->responses['activities']['relaxation'];
        }
        
        // Check for tips queries
        if (strpos($message, 'pack') !== false || strpos($message, 'luggage') !== false || strpos($message, 'bring') !== false) {
            return $this->responses['tips']['packing'];
        }
        if (strpos($message, 'save money') !== false || strpos($message, 'cheap') !== false) {
            return $this->responses['tips']['budget'];
        }
        if (strpos($message, 'photo') !== false || strpos($message, 'camera') !== false || strpos($message, 'picture') !== false) {
            return $this->responses['tips']['photo'];
        }
        
        // Check for booking queries
        if (strpos($message, 'book') !== false || strpos($message, 'reserve') !== false) {
            return $this->responses['booking']['how'];
        }
        if (strpos($message, 'cancel') !== false || strpos($message, 'refund') !== false) {
            return $this->responses['booking']['cancel'];
        }
        if (strpos($message, 'pay') !== false || strpos($message, 'payment') !== false) {
            return $this->responses['booking']['payment'];
        }
        
        // Check for support queries
        if (strpos($message, 'contact') !== false || strpos($message, 'support') !== false || strpos($message, 'help') !== false) {
            return $this->responses['support']['contact'];
        }
        
        // Default response with suggestions
        return [
            "message" => "I'm not sure about that. Here are some topics I can help you with:",
            "suggestions" => [
                "Popular destinations",
                "Travel tips",
                "Book a trip",
                "Contact support"
            ]
        ];
    }

    public function handleMessage($message) {
        try {
            error_log("Chatbot received message: " . $message);
            $response = $this->findBestMatch($message);
            error_log("Chatbot response: " . json_encode($response));
            
            return [
                'success' => true,
                'response' => $response['message'],
                'suggestions' => $response['suggestions'] ?? []
            ];
        } catch (Exception $e) {
            error_log("Chatbot error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while processing your message.'
            ];
        }
    }
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['message'])) {
        throw new Exception('No message provided');
    }
    
    $chatbot = new ChatbotHandler();
    $result = $chatbot->handleMessage($data['message']);
    
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 