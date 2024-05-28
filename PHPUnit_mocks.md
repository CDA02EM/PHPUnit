# Les Mocks dans PHPUnit

## Introduction

Les mocks sont des objets de test utilisés pour simuler le comportement d'objets réels dans le cadre de tests unitaires. PHPUnit offre une puissante fonctionnalité de mock qui permet de créer des objets simulés, appelés doubles de test, pour isoler et tester des parties spécifiques de votre code.

## Pourquoi utiliser des Mocks ?

Les mocks sont utiles pour plusieurs raisons :

1. **Isolation des tests :** Les mocks permettent d'isoler la logique que vous testez, en remplaçant les dépendances externes par des objets simulés. Cela garantit que vos tests se concentrent sur une seule unité de code à la fois.

2. **Contrôle du comportement :** Vous pouvez spécifier le comportement attendu des mocks, comme les méthodes qui doivent être appelées, avec quels arguments, et quelles valeurs elles doivent renvoyer. Cela facilite la vérification du bon fonctionnement de votre code.

3. **Éviter les effets secondaires :** En utilisant des mocks, vous pouvez éviter les effets secondaires indésirables, tels que l'écriture dans une base de données réelle ou l'envoi de courriers électroniques lors de l'exécution de tests.

## Création de Mocks

### `createMock()`

La méthode `createMock(string $type)` crée un objet simulé pour une interface ou une classe extensible spécifiée. Toutes les méthodes de l'objet d'origine sont remplacées par une implémentation qui renvoie une valeur générée automatiquement.

Exemple :

```php
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function testMockExample()
    {
        $mock = $this->createMock(MyClass::class);

        $mock->expects($this->once())
            ->method('myMethod')
            ->willReturn('mocked result');

        $this->assertEquals('mocked result', $mock->myMethod());
    }
}
```

### `createConfiguredMock()`

La méthode `createConfiguredMock()` est un wrapper pratique autour de `createMock()` qui permet de configurer les valeurs de retour à l'aide d'un tableau associatif.

Exemple :

```php
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function testConfiguredMockExample()
    {
        $mock = $this->createConfiguredMock(
            MyClass::class,
            ['myMethod' => 'configured result']
        );

        $this->assertEquals('configured result', $mock->myMethod());
    }
}
```

### `getMockBuilder()`

Lorsque les défauts des méthodes `createStub()` et `createMock()` ne répondent pas à vos besoins, la méthode `getMockBuilder($type)` peut être utilisée avec une interface fluide pour personnaliser la génération du double de test.

Exemple :

```php
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function testBuilderExample()
    {
        $stub = $this->getMockBuilder(MyClass::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Configurer le stub...

        $this->assertTrue($stub->myMethod());
    }
}
```

## Configuration des Mocks

La configuration des mocks peut inclure des éléments tels que la désactivation des constructeurs d'origine, la désactivation du clonage d'origine, la désactivation du clonage des arguments, etc. Chaque méthode de configuration offre un contrôle fin sur le comportement du mock.

**Note importante :** Certains de ces paramètres sont dépréciés et seront supprimés dans les futures versions de PHPUnit, il est donc conseillé de rester à jour sur les versions et les changements.

## Conclusion

Les mocks dans PHPUnit sont des outils puissants pour simplifier et améliorer vos tests unitaires. En les utilisant correctement, vous pouvez isoler des parties spécifiques de votre code, contrôler le comportement des dépendances et améliorer la qualité de vos tests.

N'oubliez pas de consulter la documentation officielle de PHPUnit pour rester informé des dernières fonctionnalités et changements. Happy testing!

Bien sûr, voici quelques exercices pour pratiquer l'utilisation de mocks avec PHPUnit. Ces exercices couvrent différents aspects des mocks, de la configuration à la vérification des appels de méthode. 

# Exercices

**Exercice 1: Création de Mocks**

1. Créez une classe `UserService` avec une méthode `getUserById($userId)`. Cette méthode doit retourner un tableau associatif représentant un utilisateur.

2. Créez une classe de test PHPUnit pour tester `UserService`. Utilisez un mock pour simuler le comportement de `getUserById` et assurez-vous qu'il retourne un utilisateur fictif.

**Exercice 2: Configuration de Mocks**

1. Ajoutez une nouvelle méthode `updateUser($userId, $data)` à `UserService`. Cette méthode devrait mettre à jour les détails de l'utilisateur avec les données fournies.

2. Dans votre classe de test, créez un mock pour `UserService` en utilisant `createMock()`. Configurez le mock pour que la méthode `updateUser` retourne `true` et vérifiez si l'appel à `updateUser` avec des paramètres spécifiques renvoie la valeur attendue.

**Exercice 3: Vérification des Appels**

1. Créez une classe `EmailService` avec une méthode `sendWelcomeEmail($userId)`. Cette méthode devrait envoyer un email de bienvenue à l'utilisateur.

2. Modifiez votre classe de test pour créer un mock pour `EmailService`. Attachez ce mock à votre instance de `UserService` pendant le test.

3. Appelez la méthode `getUserById` sur `UserService` et assurez-vous que la méthode `sendWelcomeEmail` de votre mock `EmailService` est appelée avec le bon utilisateur.

**Exercice 4: Configuration Avancée**

1. Ajoutez une méthode `deleteUser($userId)` à `UserService`. Cette méthode devrait supprimer l'utilisateur avec l'ID fourni.

2. Créez un mock pour `UserService` en utilisant `getMockBuilder()`. Configurez ce mock pour que la méthode `deleteUser` retourne `true` lorsque l'appelé avec un certain utilisateur et `false` avec un autre utilisateur. 

3. Testez le comportement de votre mock en appelant `deleteUser` avec différents utilisateurs et vérifiez si la méthode retourne les valeurs attendues.

**Exercice 5: ArgumentMatchers**

1. Modifiez votre exercice précédent en utilisant des argument matchers pour vérifier que la méthode `deleteUser` est appelée avec le bon utilisateur.

2. Utilisez `withConsecutive` pour spécifier différents comportements pour plusieurs appels de la même méthode.

**Note :** Assurez-vous de consulter la [documentation officielle de PHPUnit sur les mocks](https://docs.phpunit.de/en/11.0/test-doubles.html#mock-objects) pour référence et détails sur les fonctionnalités. Happy mocking!