**Test Doubles**

Gérard Meszaros introduit le concept de *Test Doubles* dans son livre "xUnit Test Patterns" de la manière suivante :

> Parfois, il est simplement difficile de tester le système sous test (SUT) car il dépend d'autres composants qui ne peuvent pas être utilisés dans l'environnement de test. Cela peut être dû au fait qu'ils ne sont pas disponibles, qu'ils ne renverront pas les résultats nécessaires pour le test, ou parce que leur exécution aurait des effets secondaires indésirables. Dans d'autres cas, notre stratégie de test nécessite que nous ayons plus de contrôle ou de visibilité sur le comportement interne du SUT.
>
> Lorsque nous écrivons un test dans lequel nous ne pouvons pas (ou ne choisissons pas de) utiliser un composant dépendant réel (DOC), nous pouvons le remplacer par un *Test Double*. Le *Test Double* ne doit pas se comporter exactement comme le vrai DOC ; il doit simplement fournir la même API que le vrai, de sorte que le SUT pense que c'est le vrai !

Les méthodes `createStub(string $type)` et `createMock(string $type)` peuvent être utilisées dans un test pour générer automatiquement un objet qui peut agir comme un double de test pour le type original spécifié (interface ou classe extensible). Cet objet de double de test peut être utilisé dans tous les contextes où un objet du type original est attendu ou requis.

Limitation : méthodes finales, privées et statiques

Veuillez noter que les méthodes `final`, `private` et `static` ne peuvent pas être doublées. Elles sont ignorées par la fonctionnalité de double de test de PHPUnit et conservent leur comportement d'origine, sauf pour les méthodes `static` qui seront remplacées par une méthode lançant une exception.

Limitation : énumérations et classes en lecture seule

Les énumérations (`enum`) sont des classes `final` et ne peuvent donc pas être doublées. Les classes `readonly` ne peuvent pas être étendues par des classes qui ne sont pas `readonly` et ne peuvent donc pas être doublées.

Privilégiez le doublement d'interfaces plutôt que de classes

Non seulement en raison des limitations mentionnées ci-dessus, mais aussi pour améliorer la conception de votre logiciel, privilégiez le doublement d'interfaces plutôt que le doublement de classes.

**Test Stubs**

La pratique de remplacer un objet par un double de test qui retourne (éventuellement) des valeurs de retour configurées est appelée *stubbing*. Vous pouvez utiliser un *test stub* pour "remplacer un vrai composant sur lequel le SUT dépend afin que le test ait un point de contrôle pour les entrées indirectes du SUT. Cela permet au test de forcer le SUT à emprunter des chemins qu'il n'emprunterait peut-être pas autrement" (Gérard Meszaros).

### Création de *Test Stubs*

#### `createStub()`

La méthode `createStub(string $type)` renvoie un *test stub* pour l'interface ou la classe extensible spécifiée.

Toutes les méthodes du type d'origine sont remplacées par une implémentation qui renvoie une valeur générée automatiquement qui satisfait la déclaration de type de retour de la méthode sans appeler la méthode d'origine. Ces méthodes sont appelées "méthodes doublées".

Le comportement des méthodes doublées peut être configuré à l'aide de méthodes telles que `willReturn()` ou `willThrowException()`. Ces méthodes sont expliquées plus loin.

#### `createStubForIntersectionOfInterfaces()`

La méthode `createStubForIntersectionOfInterfaces(array $interfaces)` peut être utilisée pour créer un *test stub* pour une intersection d'interfaces basée sur une liste de noms d'interfaces.

Supposons que vous ayez les interfaces suivantes `X` et `Y` :

**Exemple 6.1 Une interface nommée X**
```php
<?php declare(strict_types=1);
interface X
{
    public function m(): bool;
}
```

**Exemple 6.2 Une interface nommée Y**
```php
<?php declare(strict_types=1);
interface Y
{
    public function n(): int;
}
```

Et que vous ayez une classe que vous voulez tester nommée `Z` :

**Exemple 6.3 Une classe nommée Z**
```php
<?php declare(strict_types=1);
interface Y
{
    public function n(): int;
}
```

Pour tester `Z`, nous avons besoin d'un objet qui satisfait le type d'intersection `X&Y`. Nous pouvons utiliser la méthode `createStubForIntersectionOfInterfaces(array $interfaces)` pour créer un *test stub* qui satisfait `X&Y` comme ceci :

**Exemple 6.4 Utilisation de createStubForIntersectionOfInterfaces() pour créer un *test stub* pour un type d'intersection**
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class StubForIntersectionExampleTest extends TestCase
{
    public function testCreateStubForIntersection(): void
    {
        $o = $this->createStubForIntersectionOfInterfaces([X::class, Y::class]);

        // $o est de type X ...
        $this->assertInstanceOf(X::class, $o);

        // ... et $o est de type Y
        $this->assertInstanceOf(Y::class, $o);
    }
}
```

#### `createConfiguredStub()`

La méthode `createConfiguredStub()` est une enveloppe pratique autour de `createStub()` qui permet de configurer les valeurs de retour à l'aide d'un tableau associatif (`['méthode' => <valeur de retour>]`) :

**Exemple 6.5 Utilisation de createConfiguredStub() pour créer un *test stub* et configurer les valeurs de retour**
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class CreateConfiguredStubExampleTest extends TestCase
{
    public function testCreateConfiguredStub(): void
    {
        $o = $this->createConfiguredStub(
            SomeInterface::class,
            [
                'doSomething'     => 'foo',
                'doSomethingElse' => 'bar',
            ]
        );

        // $o->doSomething() renvoie maintenant 'foo'
        $this->assertSame('foo', $o->doSomething());

        // $o->doSomethingElse() renvoie maintenant 'bar'
        $this->assertSame('bar', $o->doSomethingElse());
    }
}
```

### Configuration des *Test Stubs*

#### `willReturn()`

En utilisant la méthode `willReturn()`, par exemple, vous pouvez configurer une méthode doublée pour renvoyer une valeur spécifiée lorsqu'elle est

 appelée. Cette valeur configurée doit être compatible avec la déclaration de type de retour de la méthode.

Supposons que nous ayons une classe que nous voulons tester, `SomeClass`, qui dépend de `Dependency` :

**Exemple 6.6 La classe que nous voulons tester**
```php
<?php declare(strict_types=1);
final class SomeClass
{
    public function doSomething(Dependency $dependency): string
    {
        $result = '';

        // ...

        return $result . $dependency->doSomething();
    }
}
```

**Exemple 6.7 La dépendance que nous voulons stubber**
```php
<?php declare(strict_types=1);
interface Dependency
{
    public function doSomething(): string;
}
```

Voici un premier exemple de comment utiliser la méthode `createStub(string $type)` pour créer un *test stub* pour `Dependency` afin que nous puissions tester `SomeClass` sans utiliser une implémentation réelle de `Dependency` :

**Exemple 6.8 Stub d'un appel de méthode pour renvoyer une valeur fixe**
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class SomeClassTest extends TestCase
{
    public function testDoesSomething(): void
    {
        $sut = new SomeClass;

        // Créer un *test stub* pour l'interface Dependency
        $dependency = $this->createStub(Dependency::class);

        // Configurer le *test stub*
        $dependency->method('doSomething')
            ->willReturn('foo');

        $result = $sut->doSomething($dependency);

        $this->assertStringEndsWith('foo', $result);
    }
}
```

Limitation : Méthodes nommées "method"

L'exemple ci-dessus ne fonctionne que lorsque l'interface ou la classe d'origine ne déclare pas une méthode nommée "method".

Si l'interface ou la classe d'origine déclare une méthode nommée "method", alors `$stub->expects($this->any())->method('doSomething')->willReturn('foo');` doit être utilisé.

Dépréciation : Doublement d'interfaces (ou de classes) qui ont une méthode nommée "method"

À partir de PHPUnit 10.3, la prise en charge du doublement d'interfaces (ou de classes) qui ont une méthode nommée "method" est en cours de dépréciation douce, ce qui signifie que la dépréciation est uniquement dans la documentation.

À partir de PHPUnit 11, le doublement d'interfaces (ou de classes) qui ont une méthode nommée "method" déclenchera un avertissement de dépréciation. La prise en charge du doublement d'interfaces (ou de classes) qui ont une méthode nommée "method" sera supprimée dans PHPUnit 12.

Dans l'exemple ci-dessus, nous utilisons d'abord la méthode `createStub()` pour créer un *test stub*, un objet qui ressemble à une instance de `Dependency`.

Nous utilisons ensuite l'interface fluide que PHPUnit fournit pour spécifier le comportement du *test stub*.

"En coulisse", PHPUnit génère automatiquement une nouvelle classe PHP qui met en œuvre le comportement souhaité lorsque la méthode `createStub()` est utilisée.

Veuillez noter que `createStub()` doublera automatiquement et récursivement les valeurs de retour basées sur la déclaration de type de retour d'une méthode. Considérez l'exemple ci-dessous :

**Exemple 6.9 Une méthode avec une déclaration de type de retour**
```php
<?php declare(strict_types=1);
class C
{
    public function m(): D
    {
        // Faire quelque chose.
    }
}
```

Dans l'exemple ci-dessus, la méthode `C::m()` a une déclaration de type de retour indiquant que cette méthode renvoie un objet de type `D`. Lorsqu'un double de test pour `C` est créé et qu'aucune valeur de retour n'est configurée pour `m()` à l'aide de `willReturn()` (voir ci-dessus), par exemple, lorsque `m()` est invoquée, PHPUnit générera automatiquement un double de test pour `D` à renvoyer.

De même, si `m` avait une déclaration de type de retour pour un type scalaire, une valeur de retour telle que `0` (pour `int`), `0.0` (pour `float`), ou `[]` (pour `array`) serait générée.

Une liste de valeurs de retour souhaitées peut également être spécifiée. Voici un exemple :

**Exemple 6.10 Utilisation de willReturn() pour stubber un appel de méthode pour renvoyer une liste de valeurs dans l'ordre spécifié**
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class OnConsecutiveCallsExampleTest extends TestCase
{
    public function testOnConsecutiveCallsStub(): void
    {
        // Créer un *test stub* pour la classe SomeClass.
        $stub = $this->createStub(SomeClass::class);

        // Configurer le *test stub*.
        $stub->method('doSomething')
            ->willReturn(1, 2, 3);

        // $stub->doSomething() renvoie une valeur différente à chaque fois
        $this->assertSame(1, $stub->doSomething());
        $this->assertSame(2, $stub->doSomething());
        $this->assertSame(3, $stub->doSomething());
    }
}
```

### Configuration des *Test Stubs*

#### `willThrowException()`

Au lieu de renvoyer une valeur, une méthode doublée peut également lever une exception. Voici un exemple qui montre comment utiliser `willThrowException()` pour faire cela :

Exemple 6.11 Utilisation de willThrowException() pour simuler un appel de méthode et lever une exception
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ThrowExceptionExampleTest extends TestCase
{
    public function testThrowExceptionStub(): void
    {
        // Créer un *test stub* pour la classe SomeClass.
        $stub = $this->createStub(SomeClass::class);

        // Configurer le *test stub*.
        $stub->method('doSomething')
            ->willThrowException(new Exception);

        // $stub->doSomething() lève une exception Exception
        $stub->doSomething();
    }
}
```

#### `willReturnArgument()`

Parfois, vous voulez renvoyer l'un des arguments d'un appel de méthode (inchangé) en tant que résultat d'un appel de méthode doublé. Voici un exemple qui montre comment vous pouvez y parvenir en utilisant `willReturnArgument()` :

Exemple 6.12 Utilisation de willReturnArgument() pour simuler un appel de méthode et renvoyer l'un des arguments
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ReturnArgumentExampleTest extends TestCase
{
    public function testReturnArgumentStub(): void
    {
        // Créer un *test stub* pour la classe SomeClass.
        $stub = $this->createStub(SomeClass::class);

        // Configurer le *test stub*.
        $stub->method('doSomething')
            ->willReturnArgument(0);

        // $stub->doSomething('foo') renvoie 'foo'
        $this->assertSame('foo', $stub->doSomething('foo'));

        // $stub->doSomething('bar') renvoie 'bar'
        $this->assertSame('bar', $stub->doSomething('bar'));
    }
}
```

#### `willReturnCallback()`

Lorsque l'appel de méthode doublé doit renvoyer une valeur calculée plutôt qu'une valeur fixe (voir `willReturn()`) ou un argument (voir `willReturnArgument()`), vous pouvez utiliser `willReturnCallback()` pour que la méthode doublée renvoie le résultat d'une fonction de rappel ou d'une méthode. Voici un exemple :

Exemple 6.13 Utilisation de willReturnCallback() pour simuler un appel de méthode et renvoyer une valeur à partir d'un rappel
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ReturnCallbackExampleTest extends TestCase
{
    public function testReturnCallbackStub(): void
    {
        // Créer un *test stub* pour la classe SomeClass.
        $stub = $this->createStub(SomeClass::class);

        // Configurer le *test stub*.
        $stub->method('doSomething')
            ->willReturnCallback('str_rot13');

        // $stub->doSomething($argument) renvoie str_rot13($argument)
        $this->assertSame('fbzrguvat', $stub->doSomething('something'));
    }
}
```

#### `willReturnSelf()`

Lorsque vous testez une interface fluide, il est parfois utile de faire en sorte qu'une méthode doublée renvoie une référence à l'objet doublé. Voici un exemple qui montre comment vous pouvez utiliser `willReturnSelf()` pour y parvenir :

Exemple 6.14 Utilisation de willReturnSelf() pour simuler un appel de méthode et renvoyer une référence à l'objet doublé
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ReturnSelfExampleTest extends TestCase
{
    public function testReturnSelf(): void
    {
        // Créer un *test stub* pour la classe SomeClass.
        $stub = $this->createStub(SomeClass::class);

        // Configurer le *test stub*.
        $stub->method('doSomething')
            ->willReturnSelf();

        // $stub->doSomething() renvoie $stub
        $this->assertSame($stub, $stub->doSomething());
    }
}
```

#### `willReturnMap()`

Parfois, une méthode doublée doit renvoyer différentes valeurs en fonction d'une liste prédéfinie d'arguments. Voici un exemple qui montre comment utiliser `willReturnMap()` pour créer une carte qui associe des arguments avec des valeurs de retour correspondantes :

Exemple 6.15 Utilisation de willReturnMap() pour simuler un appel de méthode et renvoyer la valeur d'une carte
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ReturnMapExampleTest extends TestCase
{
    public function testReturnMapStub(): void
    {
        // Créer un *test stub* pour la classe SomeClass.
        $stub = $this->createStub(SomeClass::class);

        // Créer une carte d'arguments à des valeurs de retour.
        $map = [
            ['a', 'b', 'c', 'd'],
            ['e', 'f', 'g', 'h'],
        ];

        // Configurer le *test stub*.
        $stub->method('doSomething')
            ->willReturnMap($map);

        // $stub->doSomething() renvoie des valeurs différentes en fonction
        // des arguments fournis.
        $this->assertSame('d', $stub->doSomething('a', 'b', 'c'));
        $this->assertSame('h', $stub->doSomething('e', 'f', 'g'));
    }
}
```

### `getMockFromWsdl()`

Lorsque votre application interagit avec un service web, vous souhaitez le tester sans interagir réellement avec le service web. Pour créer des stubs et des mocks de services web, la méthode `getMockFromWsdl()` peut être utilisée.

Cette méthode renvoie un objet mock basé sur une description de service web en WSDL, alors que `createMock()` renvoie un objet mock basé sur une interface ou sur une classe.

Voici un exemple qui montre comment simuler le service web décrit dans `HelloService.wsdl` :

Exemple 6.25 Simuler un service web
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class WsdlStubExampleTest extends TestCase
{
    public function testWebserviceCanBeStubbed(): void
    {
        $service = $this->getMockFromWsdl(__DIR__ . '/HelloService.wsdl');

        $service->method('sayHello')
            ->willReturn('Hello');

        $this->assertSame('Hello', $service->sayHello('message'));
    }
}
```
> [!WARNING]
> Dépréciation : `getMockFromWsdl()` est déprécié
> 
> À partir de PHPUnit 10.1, la méthode `getMockFromWsdl()` est soft-dépréciée, ce qui signifie que sa déclaration est annotée avec `@deprecated` afin que les IDE et les outils d'analyse statique puissent avertir de son utilisation.
> 
> À partir de PHPUnit 11, l'utilisation de la méthode `getMockFromWsdl()` déclenchera un avertissement de dépréciation. La méthode sera supprimée dans PHPUnit 12.

### Configuration des objets Mock

Voici un exemple : supposons que nous voulons tester que la méthode correcte, `update()` dans notre exemple, est appelée sur un objet qui observe un autre objet.

Voici le code de la classe `Subject` et de l'interface `Observer` qui font partie du Système sous Test (SUT) :

Exemple 6.26 Classe `Subject` faisant partie du Système sous Test (SUT)
```php
<?php declare(strict_types=1);
final class Subject
{
    private array $observers = [];

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function doSomething(): void
    {
        // ...

        $this->notify('something');
    }

    private function notify(string $argument): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($argument);
        }
    }

    // ...
}
```

Exemple 6.27 Interface `Observer` faisant partie du Système sous Test (SUT)
```php
<?php declare(strict_types=1);
interface Observer
{
    public function update(string $argument): void;
}
```

Voici un exemple qui montre comment utiliser un objet mock pour tester l'interaction entre les objets `Subject` et `Observer` :

Exemple 6.28 Tester qu'une méthode est appelée une fois et avec un argument spécifié
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class SubjectTest extends TestCase
{
    public function testObserversAreUpdated(): void
    {
        $observer = $this->createMock(Observer::class);

        $observer->expects($this->once())
            ->method('update')
            ->with($this->identicalTo('something'));

        $subject = new Subject;

        $subject->attach($observer);

        $subject->doSomething();
    }
}
```

___

Nous utilisons d'abord la méthode `createMock()` pour créer un objet mock pour l'`Observer`.

Comme nous sommes intéressés à vérifier la communication entre deux objets (qu'une méthode est appelée et avec quels arguments elle est appelée), nous utilisons les méthodes `expects()` et `with()` pour spécifier à quoi doit ressembler cette communication.

La méthode `with()` peut prendre n'importe quel nombre d'arguments, correspondant au nombre d'arguments de la méthode qui est doublée. Vous pouvez spécifier des contraintes plus avancées sur les arguments de la méthode qu'une simple correspondance.

[Contraintes](https://docs.phpunit.de/en/11.0/test-doubles.htmlassertions.html#appendixes-assertions-assertthat-tables-constraints) montre les contraintes qui peuvent être appliquées aux arguments de méthode et voici une liste des matchers disponibles pour spécifier le nombre d'invocations :

-   `any()` retourne un matcher qui correspond lorsque la méthode pour laquelle il est évalué est exécutée zéro fois ou plus
    
-   `never()` retourne un matcher qui correspond lorsque la méthode pour laquelle il est évalué n'est jamais exécutée
    
-   `atLeastOnce()` retourne un matcher qui correspond lorsque la méthode pour laquelle il est évalué est exécutée au moins une fois
    
-   `once()` retourne un matcher qui correspond lorsque la méthode pour laquelle il est évalué est exécutée exactement une fois
    
-   `atMost(int $count)` retourne un matcher qui correspond lorsque la méthode pour laquelle il est évalué est exécutée au plus `$count` fois
    
-   `exactly(int $count)` retourne un matcher qui correspond lorsque la méthode pour laquelle il est évalué est exécutée exactement `$count` fois
    

## API MockBuilder

Comme mentionné précédemment, lorsque les valeurs par défaut utilisées par les méthodes `createStub()` et `createMock()` pour générer le test double ne correspondent pas à vos besoins, vous pouvez utiliser la méthode `getMockBuilder($type)` pour personnaliser la génération du test double en utilisant une interface fluide. Les méthodes fournies par le Mock Builder sont documentées ci-dessous.

### `setMockClassName()`

`setMockClassName($name)` peut être utilisé pour spécifier un nom de classe pour la classe de test double générée.

Dépréciation : `setMockClassName()` est déprécié

À partir de PHPUnit 10.3, la méthode `setMockClassName()` est soft-dépréciée, ce qui signifie que sa déclaration est annotée avec `@deprecated` afin que les IDE et les outils d'analyse statique puissent avertir de son utilisation.

À partir de PHPUnit 11, l'utilisation de la méthode `setMockClassName()` déclenchera un avertissement de dépréciation. La méthode sera supprimée dans PHPUnit 12.

### `setConstructorArgs()`

`setConstructorArgs(array $args)` peut être appelé pour fournir un tableau de paramètres qui est passé au constructeur de la classe d'origine (qui n'est pas remplacé par une implémentation bidon par défaut).

### `disableOriginal