<?php

use PHPUnit\Framework\TestCase;
use App\MessageGenerator;

class MessageGeneratorTest extends TestCase {
    protected MessageGenerator $generator;

    public function setUp(): void 
    {
        // Créer l'instance pour chaque teste
    }

    public function testGenerateMessage() {
        $message = $this->generator->generateMessage();
        // on vérifie que les message son contenu dans $message avec assertContains()

    }

    public function testGetMessages() {
        // on vérifie que c'est un array avec assertIsArray()
    }

    public function testAddAndRemoveMessage() {
        
        $newMessage = 'Nouveau message';

        // Teste l'ajout d'un nouveau message
        $this->generator->addMessage($newMessage);
        $this->assertContains();

        // Teste la suppression du message ajouté
        $this->generator->removeMessage($newMessage);
        $this->assertNotContains();
    }
}