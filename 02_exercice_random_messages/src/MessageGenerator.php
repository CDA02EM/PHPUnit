<?php

namespace App;

class MessageGenerator {
    private $messages = [
        'Bonjour !',
        'Bienvenue',
        'Ceci est un message aléatoire',
        'PHPUnit est amusant',
        'Testez avec précaution',
    ];

    public function generateMessage() {
        $randomIndex = array_rand($this->messages);
        return $this->messages[$randomIndex];
    }

    public function getMessages() {
        return $this->messages;
    }

    public function addMessage($message) {
        $this->messages[] = $message;
    }

    public function removeMessage($message) {
        $index = array_search($message, $this->messages);
        if ($index !== false) {
            unset($this->messages[$index]);
        }
    }
}