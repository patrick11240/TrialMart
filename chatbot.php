<?php
require_once 'db_config.php';

function getBotResponse($message) {
    $conn = connectPDO(); // Use connectPDO instead of connectDB
    
    // Convert message to lowercase for better matching
    $message = strtolower(trim($message));
    
    try {
        // Search for matching keywords in bot_responses table
        $stmt = $conn->prepare("SELECT response FROM bot_responses WHERE LOWER(:message) LIKE CONCAT('%', keyword, '%') LIMIT 1");
        $stmt->bindParam(':message', $message);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['response'];
        } else {
            return "I'm sorry, I don't understand that. Could you please rephrase or ask something else?";
        }
    } catch(PDOException $e) {
        return "Sorry, I'm having trouble processing your request.";
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['message'] ?? '';
    if (!empty($userMessage)) {
        $response = getBotResponse($userMessage);
        
        // Store chat history if needed
        $conn = connectPDO(); // Use connectPDO instead of connectDB
        try {
            // Store user message
            $stmt = $conn->prepare("INSERT INTO chat_history (user_id, message, is_bot) VALUES (1, :message, 0)");
            $stmt->bindParam(':message', $userMessage);
            $stmt->execute();
            
            // Store bot response
            $stmt = $conn->prepare("INSERT INTO chat_history (user_id, message, is_bot) VALUES (1, :message, 1)");
            $stmt->bindParam(':message', $response);
            $stmt->execute();
        } catch(PDOException $e) {
            // Handle error if needed
        }
        
        echo json_encode(['response' => $response]);
    }
}
?>
