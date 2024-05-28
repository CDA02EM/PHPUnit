# Introduction & Présentation des tests unitaires

Nous allons aborder les tests unitaires de manière direct dans le code déjà écrit. Nous verrons plus tard une autre manière de procéder que l'on appelle TDD ,Tests Driven Developpment, qui consiste à créer des tests avant même d'implémenter la logique métier dans les classes de l'application.

Les tests unitaires vont vous aider à identifier et corriger les bugs, à refactorer le code et écrire de une documentation précise sur les fonctionnalités de vos méthodes. Les tests unitaires doivent couvrir toutes les possibilités algorithmiques d'un programme. Chaque test unitaire test une logique d'une méthode spécifique.

Nous verrons également qu'il existe des dépendances logiques entre les tests, des scénarios de tests, mais que les tests eux-mêmes sont isolés.

Retenez chaque test est une méthode qui est isolée des autres tests (méthodes) dans une classe de test.

Une application est bien testée si les tests unitaires couvrent 80% de la logique métier. Dans ce cas l'application a peu de chance de produire des bugs en production et également pourra facilement être étendue avec des nouvelles fonctionnalités, bien sûr on doit si l'application évolue refaire des tests.

**Martin Fowler:**

Concepteur et auteur conférencier informaticien britanique, pionnier et une référence dans la programmation agile.

"A chaque fois que vous avez la tentation de saisir quelque chose dans une instruction print ou dans une expression de débogage, écrivez le plutôt dans un test."

## Installation local avec composer

Vous devez avoir une version de PHP >= 8.2

On va installer **PHPUnit** qui est un framework de tests. Nous pouvons l'installer de manière globale, c'est-à-dire 
dans le dossier /usr/local/bin de notre machine ou alors, l'installer uniquement pour le projet en cours.

  ```bash
composer search phpunit
composer show phpunit/phpunit --all 

# Installation locale
composer require --dev phpunit/phpunit

# Installation globale
composer global require phpunit/phpunit
```

## Installation manuelle

Une autre manière installer phpunit est d'installer l'exécutable :

```bash
wget -O phpunit https://phar.phpunit.de/phpunit-11.phar
chmod +x phpunit
./phpunit --version

# Déplacez l'exécutable dans le dossier /usr/local/bin
mv ./phpunit.phar /usr/local/bin
```

Pour les utilisateurs Windows, vous pouvez vous rendre sur [ce lien](https://phar.phpunit.de/) pour télécharger le `.phar`. Mettre ce fichier dans un répertoir à la racine de votre système et exécuter (depuis le répertoir où se trouve le `.phar`) la commande adaptée, selon que vous soyez sur le **cmd.exe** ou **PowerShell** :
- cmd.exe : `echo @php "%~dp0phpunit.phar" %*>phpunit.bat`
- PowerShell : `Set-Content phpunit.bat '@php "%~dp0phpunit.phar" %*'`

Vous venez de créer un fichier `.bat` que vous pouvez renseigner dans vos variables d'environnement afin d'avoir les commandes en global sur votre système.

## Configuration de l'application

On teste la logique algorithmique d'une classe, chaque test est une méthode d'une classe de test. Les tests sont isolés les uns des autres, principes d'isolation des tests.

Pour commencer, on vas initialiser un nouveau projet avec `composer init`.  
Un fichier `composer.json` est générer. Mettez à jour ce fichier avec la config suivant :

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

Toute classe se trouvant dans le dossier `src/` doit faire partie du namespace `App` pour pouvoir être mappé et automatiquement chargé à l'appel de son namespace.

Installé la dépendance PHPUnit en local dans les dépendence de développement avec `composer require phpunit/phpunit --dev`.

Créé un répertoir `tests/` dans lequel se trouvera vos tests.

Vous pouvez ajouter les paramettres suivant dans votre `composer.json` :

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "App\\Tests\\": "tests/"
        }
    },
}
```

Créez un fichier `phpunit.xml` à la racine de l'application et ajoutez-y le script suivant :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="tests/bootstrap.php"
    colors="true"
>
    <testsuites>
        <testsuite name="User Tests">
            <directory>tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

`phpunit.xml` est notre fichier de configuration de phpunit.

Vous avez peut-être remarqué le `bootstrap="tests/bootstrap.php"`, il s'agit d'un fichier que nous devons créer dans le répertoir `tests` et dans lequel on se contentera du script suivant :

```php
require_once dirname(__DIR__).'/vendor/autoloader.php';
```

Vous venez tout just de configurer phpunit au sein de votre application :tada: .

## Déclencher son premier test

Avant toute chose nous allons créer une première classe dont nous testerons les méthodes dans le répertoir `src/` :

```php
namespace App;

class User {
    public function __construct(private string $name, private string $surname) {}

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function hello(string $name): string
    {
        return "Hello $name, I am ".$this->name;
    }
}
```

Dans le dossier `tests/` on vas pouvoir écrire notre premier test.

```php
<?php
use PHPUnit\Framework\TestCase;
use App\User;

class UserTest extends TestCase {

    public function testHello(): void
    {
        $user = new User('Jeremi', 'Duffay');

        $result = $user->hello('François');

        # On test si $reslut est bien le resultat attendu
        $this->assertSame('Hello François, I am Jeremi', $result);
    }
}
```

:point_up: À savoir quand vous crée un test, la syntaxe `testMethod` est une convention à respecter, sinon PHPUnit ne trouvera pas votre test.  
Alternativement vous pouvez utiliser l'attribut Test si vous voulez vraiment utiliser un nom particulier pour la méthode :

```php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\User;

class UserTest extends TestCase {

    #[Test]
    public function hello(): void
    {
        $user = new User('Jeremi', 'Duffay');

        $result = $user->hello('François');

        # On test si $reslut est bien le resultat attendu
        $this->assertSame('Hello François, I am Jeremi', $result);
    }
}
```

Il faudra aussi respecter le nom de la classe :

```php
class ClassTest extends TestCase {
    // ...code
}
```
> [!WARNING]
> Pour rappel on respect les convention de nommage des classes (`ClassTest`) et des méthodes (`testMéthode`).

Il existe plein d'autres méthodes d'assertion que vous pourrez découvrir dans la [doc de PHPUnit](https://docs.phpunit.de/en/11.0/assertions.html).

Vous avez par exemple `TestCase::asserEquals()` qui fait une comparaison sans se soucier du type.

En PHPUnit, `assertSame()` et `assertEquals()` sont deux méthodes utilisées pour effectuer des assertions dans les tests unitaires, mais ils diffèrent dans la manière dont ils comparent les valeurs.

1. `assertSame($expected, $actual)` :
   - La méthode `assertSame()` compare les valeurs et les types de deux variables.
   - Elle réussit si les valeurs des deux variables sont égales et si elles sont du même type (identiques).
   - Cela signifie que si les valeurs sont égales, mais les types sont différents, l'assertion échouera.

   Exemple :
   ```php
   $this->assertSame(42, 42);       // Assertion réussie (valeurs et types identiques)
   $this->assertSame(42, '42');     // Assertion échoue (types différents)
   $this->assertSame(42, 43);       // Assertion échoue (valeurs différentes)
   ```

2. `assertEquals($expected, $actual)` :
   - La méthode `assertEquals()` compare les valeurs de deux variables sans tenir compte de leur type.
   - Elle réussit si les valeurs des deux variables sont égales, même si les types sont différents.
   - Cela signifie que si les valeurs sont égales, l'assertion réussira, même si les types sont différents.

   Exemple :
   ```php
   $this->assertEquals(42, 42);    // Assertion réussie (valeurs identiques, types différents)
   $this->assertEquals(42, '42');  // Assertion réussie (valeurs identiques, types différents)
   $this->assertEquals(42, 43);    // Assertion échoue (valeurs différentes)
   ```

En résumé, la principale différence réside dans la prise en compte ou non du type lors de la comparaison. Utilisez `assertSame()` lorsque vous voulez vous assurer que les valeurs sont identiques en termes de valeur et de type, et utilisez `assertEquals()` lorsque vous souhaitez comparer les valeurs indépendamment de leur type.

## Les méthodes `setUp()` et `tearDown()`

La méthode **setUp** dans la classe MessageTest est appelée en premier avant tous les autres tests, et est appelée avant chaque test.

Nous n'implémentons pas la méthode **tearDown**, elle est utile si la méthode **setUp** alloue des ressources externes comme l'ouverture de fichiers ou base de données. Elle permettra dans ce cas de fermer ces ressources.

Rappel : une classe tests est basée sur l'isolation, en terme de propriétés définies dans la classe de test, de chaque test. Chaque test repart avec ces propriété ré-initialisée.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php"
         colors="true">
    <testsuites>
        <testsuite name="Message">
            <directory>./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

Créez le test suivant :

```php

use PHPUnit\Framework\TestCase;
use App\Message;

class MessageTest extends TestCase{

    protected Message $message;


    public function setUp():void{
        $this->message = new Message('en');
    }

    public function testOne(){

        $this->assertSame("Hello World!",$this->message->get());
    }
}
```

On vous donne la classe métier à tester :

```php
namespace App;

class Message
{

    public function __construct(
            private string $lang = 'en',  
            private array $translates = ['fr' => 'Bonjour les gesn!', 'en' => 'Hello World!']
        ) {}

    public function get(): string
    {

         return $this->translates[$this->lang];
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }
}
```

Lancez maintenant le test suivant en console à la racine du dossier Message :

```bash
vendor/bin/phpunit
```

Indication : si tous les tests sont bons alors, ils seront verts.

**Question changez la langue dans un test supplémentaire et vérifiez que la classe pour la version fr renvoie bien "Bonjour tout le monde!." Si ce n'est pas le cas corriger le code métier pour faire passer le test.**

## Sauter un test sous condition

Si votre test demande une connexion à une base de données, il serait inutile d'exécuter un test qui requière de communiquer avec cette base de données s'il n'est pas possible de s'y connecter.

```php
use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        # On vérifie si l'extention pgsql est bien activé.
        if (!extension_loaded('pgsql')) {
            # Si elle ne l'ai pas, alors le test ne sera pas exécuté
            $this->markTestSkipped(
                'The PostgreSQL extension is not available',
            );
        }
    }

    # testConnection sera concerné comme tout les tests de cette class
    public function testConnection(): void
    {
        // ...
    }
}
```

## Configuration des tests fournisseurs de données et exceptions

Nous pouvons allez plus loin dans la configuration des tests, pour bien comprendre le framework de tests nous allons présenter d'autres techniques. Nous pouvons en effet fournir aux tests des valeurs arbitraires.

Un fournisseur de tests devra être une méthode statique publique et retourner soit un tableau, soit un objet qui implémente une interface Iterator. 

Autrement dit la méthode static doit retourner un itérateur.

On utilise l'attribut DataProvider() dans lequel nous renseignons le fournisseur de tests :

```php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider

class DataTest extends TestCase
{
    #[DataProvider('additionProvider')]
    public function testAdd(int $a, int $b, int $expected): void
    {
        $this->assertSame($expected, $a + $b);
    }

    public static function additionProvider()
    {
        return [
            [0, 0, 0], // $a, $b, $expected
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 2]
        ];
    }
}
```

Vous pouvez également tester les exceptions, et même le type d'exception que votre code métier retourne :

```php
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testException()
    {
        # On déclare le type d'exception attendu
        $this->expectException(InvalidArgumentException::class);
        # On déclenche volontairement une erreur pour obtenir l'exception attendue
        $this->model->save($user);
    }
}
```

## 01 Exercice Calculator

Le but de cet exercice et de vous faire appréhender l'organisation des tests.

Dans la suite des tests on prendra comme précision 2 chiffres après la virgule.

Dans les questions 1 et 2 vous testerez uniquement la classe Calculator. Les classes dans le dossier Model seront testées à partir de la question 4.

1. Utilisez le code dans le dossier 01_exercice_calculator. Installez le projet avec ces dépendance. 

*Attention, `assertEquals()` et `assertSame()` sont différents. La deuxième méthode vérifie le type strictement.*

2. Testez la classe Calculator en utilisant les concepts Provider (passer un tableau de valeurs ) et d'exception décrit précédemment.

*Indications : vous devez tester le type des arguments et également les exceptions levées directement dans le code métier. Pensez à faire un trait pour factoriser les providers.*

3. Testez maintenant le message renvoyé par l'exception.

```php
 $this->expectExceptionMessage('Impossible de diviser par zéro.');
```

*Dans la suite tous les résultats sont des entiers qui n'ont pas de partie décimale, voyez le type NumberString qui est retourné dans l'addition et la division.*

1. Testez maintenant la partie Model dans le dossier src. Réorganisez le fichier XML de configuration des tests comme suit, n'utilisez pas de Provider pour cette partie vous testerez uniquement add et divisor comme dans les exemples ci-après.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php"
         colors="true">
    <testsuites>
        <testsuite name="Calculator">
            <file>./tests/CalculatorTest.php</file>
        </testsuite>
         <testsuite name="Model">
           <directory>./tests/Model/ModelTest.php</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

Structure du dossier tests :

```txt
tests
    ├── CalculatorTest.php
    ├── Model
    │   └── ModelTest.php
    └── bootstrap.php
```

Faites les trois tests suivants :

- testez l'addition de deux nombres de type Number.

```php
public function testAdd()
{

}
```

- Testez la division de deux nombre de type Number.

```php
public function testDivisor()
{

}
```

- Testez l'exception division par zéro.

```php
public function testExceptionDivisor()
{

}
```

Bien sûr, voici une version étendue de l'exercice avec quelques étapes supplémentaires :

---

## 02. Exercice : Génération de Messages Aléatoires

### Objectif :
Créer une classe `MessageGenerator` qui génère des messages aléatoires et ajouter des fonctionnalités supplémentaires.

> [!TIP]
> Vous allez pouvoir vous aider de la documentation de phpunint. Sur le lien suivant vous trouverez les différentes méthode d'assertion disponible :
> 
> https://docs.phpunit.de/en/11.0/assertions.html

### Étapes :

1. **Classe `MessageGenerator` :**
   - Créez une classe `MessageGenerator` avec une méthode `generateMessage()` qui génère un message aléatoire à chaque appel.
   - Les messages peuvent être choisis parmi un ensemble prédéfini de messages.

2. **Ajout de Nouvelles Fonctionnalités :**
   - Ajoutez une méthode `getMessages()` qui retourne l'ensemble prédéfini de messages.
   - Ajoutez une méthode `addMessage($message)` qui permet d'ajouter un nouveau message à l'ensemble.
   - Ajoutez une méthode `removeMessage($message)` qui permet de supprimer un message de l'ensemble.

3. **Tests avec PHPUnit :**
   - Écrivez des tests PHPUnit pour la classe `MessageGenerator`.
   - Testez la méthode `generateMessage()` pour vous assurer qu'elle génère un message chaque fois qu'elle est appelée.
   - Testez la méthode `getMessages()` pour vous assurer qu'elle renvoie l'ensemble prédéfini de messages.
   - Testez les méthodes `addMessage()` et `removeMessage()` pour vérifier l'ajout et la suppression corrects des messages.

### Classe MessageGenerator :

```php
class MessageGenerator {
    private $messages = [
        'Bonjour !',
        'Bienvenue',
        'Ceci est un message aléatoire',
        'PHPUnit est amusant',
        'Testez avec précaution',
    ];

    public function generateMessage() {
        // ...code
    }

    public function getMessages() {
        // ...code
    }

    public function addMessage($message) {
        // ...code
    }

    public function removeMessage($message) {
        // ...code
    }
}
```

### Conseils :
- Utilisez la fonction `array_rand()` pour choisir un indice aléatoire dans le tableau des messages.
- Assurez-vous que les méthodes `addMessage()` et `removeMessage()` fonctionnent correctement en utilisant les assertions appropriées.
- Utilisez `assertIsArray()` pour vérifier que `getMessages()` renvoie bien un tableau.

### 02.2 Exercice: Ajouter un compteur de message à la classe `MessageGenerator()` et ajouté la méthode de test de la nouvelle fonctionnalité.

### 02.3 Exercice: Filtrage de Messages par Mot-clé

#### Objectif :
Ajouter une nouvelle fonctionnalité à la classe `MessageGenerator` permettant de filtrer les messages par un mot-clé donné.

#### Étapes :

1. **Nouvelle Fonctionnalité :**
   - Ajoutez une méthode `filterMessagesByKeyword($keyword)` qui prend un mot-clé en paramètre et retourne un tableau contenant uniquement les messages qui contiennent ce mot-clé.

2. **Tests avec PHPUnit :**
   - Écrivez des tests PHPUnit pour la nouvelle méthode `filterMessagesByKeyword()`.
   - Testez le filtrage correct des messages en utilisant un mot-clé existant dans certains messages.
   - Testez le cas où aucun message ne contient le mot-clé spécifié.
   - Testez le cas où le mot-clé est une chaîne vide.

### Classe MessageGenerator (Mise à jour) :

```php
class MessageGenerator {
    // ...code

    public function filterMessagesByKeyword($keyword) {
        // ...code
    }
}
```


## 03. Exercice: Créer un panier

### Partie 1: Première Classe

On vas premièrement créer une classe `Cart` qui comportera toutes les responsabilitées.

Cette classe aura les méthodes :
- addProduct()
- removeProduct()
- reset()
- total()

Un produit sera représenté par un objet comportant les propriétées "price", "quantity" et "name".

```php
class Cart {

}

$cart = new Cart();

$product1 = [
    "productName" => 'Laptop',
    "price" => '1200.0',
    "quantity" => '2',
];
$product2 = [
    "productName" => 'Phone',
    "price" => '800.0',
    "quantity" => '1',
];

$cart->addProduct($product1);
$cart->addProduct($product2);

echo "Total du panier : $" . $cart->total(); // Affiche : Total du panier : $3200.0
```

### Partie 1.2: Création des tests unitaire pour la classe `Cart`

Créer la classe CartTest avec PHPUnit

### Partie 2: Décomposition de la classe

Dans cette partie nous allons séparer les responsabilitées de notre classe `Cart` en la décomposant en trois classes : `Cart`, `Product` et `Storage`.

Cette décomposition permettra de séparer les responsabilités et rendra le code plus modulaire.

Classe `Cart`

```php
<?php

class Cart
{
    private Storage $storage;

    public function __construct(Storage $storage)
    {
        // ...code
    }

    public function addProduct(Product $product): void
    {
        // ...code
    }

    public function removeProduct(string $productName): void
    {
        // ...code
    }

    public function reset(): void
    {
        // ...code
    }

    public function total(): float
    {
        return $this->storage->calculateTotal();
    }
}
```

Classe `Product`

```php
<?php

class Product
{
    public function __construct(private string $name, float $price, int $quantity) {}

    public function getName(): string
    {
        // ...code
    }

    public function getTotalPrice(): float
    {
        // ...code
    }
}
```

Class `Storage`

```php
<?php

class Storage
{
    private array $products = [];

    public function add(Product $product): void
    {
        // ...code
    }

    public function remove(string $productName): void
    {
        // ...code
    }

    public function reset(): void
    {
        // ...code
    }

    public function calculateTotal(): float
    {
        // ...code
    }
}
```

### Partie 2.2: Complété les tests unitaires

Mettez à jour votre classe `Cart` et créé les autres classes de tests

## 03 TP Fixtures parti 1/2

Lors de tests vous aurez besoin de fixtures (données d'exemple) pour tester des comportements. Nous allons découvrir cette notion. Récupérez dans le dossier Exercices l'exercice 03_Exercice_Model.

Remarques sur la méthode setUp dans ce TP, lisez les commentaires ci-dessous :


```php
public function setUp(): void
{
    // On utilise sqlite en mode vive
    $this->pdo = new \PDO('sqlite::memory:');
    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // On crée une table user dans la base de données en mémoire vive
    $this->pdo->exec(
        "CREATE TABLE IF NOT EXISTS user
        (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR( 225 )
        )
        "
    );

}
```

1. Hydratez les tests (fixtures) avec les données suivantes :

```php
$users = [
    ['username' => 'Alan'],
    ['username' => 'Sophie'],
    ['username' => 'Bernard'],
];
```

Remarque sur PDO vous pouvez demander à PDO de vous retourner les données sous forme d'un objet de type User voyez le code dans ce cas :


- Dans la classe modèle

```php

namespace App;

class User {

    private string $username;
    private int $id;

    public function __set($name, string $value):void
    {
        $this->$name = $value;
    }

    public function __get(string $name):string
    {
        return $this->$name;
    }

}
```

- Dans le modèle et la méthode fetchAll

```php
 public function all()
{
    $stmt = $this->pdo->query("SELECT * FROM user");

    return $stmt->fetchAll(\PDO::FETCH_CLASS, 'App\\User');
}
```

2. Faites les tests décrit dans le fichier UserTest.

3. Créez le ModelPrepare qui s'occupera de sécuriser les requêtes en les préparant.


## 03 TP Fixtures parti 2/2

Nous allons maintenant utiliser un composant pour automatiser l'import des données afin de faire des tests avec une données mieux organiser.

Modifiez le modèle en conséquence des données d'exemple dans cette partie de l'exercice.

Installez la dépendance suivante :

```bash
composer require symfony/yaml --dev
```

Récupérez le dossier _data dans le dossier Examples et à l'aide du code ci-dessous importez ces données d'exemple dans une autre classe de test UserYamlTest. Vous testerez les mêmes méthodes (ne changez pas le code des méthodes de tests).

```php
use Symfony\Component\Yaml\Parser;

$yaml = new Parser();
$users = $yaml->parse(file_get_contents(__DIR__.'/_data/seed.yml'));
```

**Remarque : les seeds YAML sont légers et pratiques, couplés avec des bases de données en mémoire pour tester qu'une partie de la logique des modèles est performant.**

## 04 Exercice Iterator

1. Une classe PHP qui implémente l'interface Iterator est une classe itérable, à partir de l'exemple suivant créez une classe Even, elle n'affiche que les nombres paires, fixez un maximun de valeur(s) à retourner. Une fois la classe créée testez son bon fonctionnement, vous pouvez imaginer les tests avant même d'implémenter le code utile dans les classes métiers (TDD).

```php

$even = new Even(max:100);

foreach($even as $n){
    echo $n;
}

// 024681012141618202224262830...98

```

- La classe ci-dessous est itérable, elle implémente l'interface PHP Iterator, chaque méthode de cette classe rend la classe itérable.

```php

class Example implements Iterator {
    
    public function __construct(private int $position = 0, array $array =[
        "premierelement",
        "secondelement",
        "dernierelement"] ) {
    }

    public function rewind() {
        var_dump(__METHOD__); // __METHOD__ donne le nom de la méthode appelée dans le script courant
        $this->position = 0;
    }

    public function current() {
        var_dump(__METHOD__);
        return $this->array[$this->position];
    }

    public function key() {
        var_dump(__METHOD__);
        return $this->position;
    }

    public function next() {
        var_dump(__METHOD__);
        ++$this->position;
    }

    public function valid() {
        var_dump(__METHOD__);
        return isset($this->array[$this->position]);
    }
}
```

## 05 Exercice Suite de Fibonacci TDD

### Partie 1

Créez une classe de tests vérifiant l'algorithmique de la suite de Fibonacci dans une classe, vous devez écrire les tests avant d'implémenter la logique métier de la classe. Une fois un test ou les tests réalisés vous devez vérifier qu'il(s) soi(ent) valide en implémentant le code métier du/des test(s).

Organisez le projet de manière conforme aux recommandations des standards PHP.

### Partie 2

Une manière d'optimiser l'empreinte mémoire d'un script est l'utilisation des générateurs. Un générateur est itérable et une fois itéré ne peut plus l'être pour une instance de générateur donné.

Refactorez dans une autre classe en utilisant l'approche des générateurs et testez à l'aide du principe TDD.

Un générateur est une fonction ou méthode ayant le mot clé yield.

```php
function gen(){
    yield 1;
    yield 2;
    yield 3;
}

// création d'une instance 
$gen = gen(); 

foreach($gen as $num) echo $num; // 1, 2, 3

// Si on ré-itère sur le générateur il est cette fois vide
foreach($gen as $num) echo $num; // aucune valeur
```

Une autre idée de générateur 

```php

function genWhile($max = 10){
    while($max > 0){
        yield $max;
        $max--;
    }
}

```

## Nombre narcissique

Un nombre narcissique est un nombre qui s'écrit comme suit :

```php
153 = 1**3 + 5**3 + 3**3;
```

où la puissance 3 désigne la puissance de 10 du nombre : 3 chiffres == 10**3.

Ecrire un test qui teste si on a un nombre narcissique, utilisez le principe TDD.

## Mock

Nous avons vu comment Mocker une classe en implémentant la logique métier dans une classe. Phpunit propose la création de Mock de manière automatique.

```php
class Model
{
    public function all():array
    {
        // Do something. with DB
    }

    // ...
}

```

Dans votre classe de tests vous allez écrire :

```php
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testStub()
    {
        // Créer un bouchon pour la classe SomeClass.
        $stub = $this->createMock(Model::class);

        // Configurer le bouchon.
        $stub->method('all')
             ->willReturn(['phpunit','behat']);
.
        $this->assertContains('phpunit', $stub->all());
    }
}
```

Le retour des méthodes peut être plus complexe :

- returnValue()

- returnArgument()

```php
->will($this->returnArgument(0)); // $stub->all('foo'); $stub->all('bar')
```

- returnCallback()

```php
->will($this->returnCallback('str_rot13'));
```

- will
```php
->will($this->onConsecutiveCalls(2, 3, 5, 7));
```

La méthode will avec une exception peut également être utilisée :

- will
```php
$this->throwException(new Exception));
```

Des extensions comme ci-dessous vous permet de Mocker un flux qui ne consommera aucune ressource (voir dans la documentation).

```text
mikey179/vfsStream
```

Vous permettrons de tester les flux comme les fichiers et donc vous n'avez techniquement pas besoin des fichiers eux-mêmes, aucun flux n'est ouvert et du reste il est donc inutile à chercher à le fermer ... 


### Exemple

```php
use PHPUnit\Framework\TestCase;

class Example
{
    protected $id;
    protected $directory;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory . DIRECTORY_SEPARATOR . $this->id;

        if (!file_exists($this->directory)) {
            mkdir($this->directory, 0700, true);
        }
    }
}
```

- En utilisant le composant 

```php
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('exampleDir'));
    }

    public function testDirectoryIsCreated()
    {
        $example = new Example('id');
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('id'));

        $example->setDirectory(vfsStream::url('exampleDir'));
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('id'));
    }
}
```