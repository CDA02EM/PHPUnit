**1. Quel est le nom du framework de tests que nous avons utilisé dans le projet ?**
   - [ ] a) Composer
   - [x] b) PHPUnit
   - [ ] c) Symfony
   - [ ] d) Laravel

**2. Comment organisez-vous les tests dans PHPUnit pour tester différentes parties de votre application ?**
   - [ ] a) En utilisant des tests unitaires distincts.
   - [ ] b) En utilisant des fonctions de fournisseurs de données.
   - [x] c) En utilisant la configuration du fichier `phpunit.xml`.
   - [ ] d) En utilisant des méthodes statiques dans la classe de test.

**3. Comment déclarez-vous un fournisseur de données dans PHPUnit pour tester plusieurs cas avec une même méthode de test ?**
   - [ ] a) En utilisant l'attribut `DataProvider` directement dans la méthode de test.
   - [x] b) En déclarant une méthode statique et en utilisant l'attribut `DataProvider`.
   - [ ] c) En déclarant une méthode statique avec le nom `dataProvider` dans la classe de test.
   - [ ] d) En passant un tableau de données directement dans la méthode de test.

**4. Quelle est la principale différence entre `assertSame()` et `assertEquals()` dans PHPUnit ?**
   - [x] a) `assertSame()` compare les valeurs et les types, tandis que `assertEquals()` compare les valeurs sans tenir compte du type.
   - [ ] b) `assertSame()` et `assertEquals()` sont interchangeables et ont le même comportement.
   - [ ] c) Aucune différence significative entre les deux.

**5. Où est déclarée la configuration de l'autoloader dans le projet ?**
   - [ ] a) Dans le fichier `composer.json`.
   - [ ] b) Dans le fichier `CalculatorTest.php`.
   - [x] c) Dans le fichier `bootstrap.php`.
   - [x] d) Dans le fichier `phpunit.xml`.

**6. Comment spécifiez-vous le type d'exception que vous attendez dans un test avec PHPUnit ?**
   - [ ] a) En utilisant `expectExceptionCode()` avec le nom de la classe d'exception.
   - [x] b) En utilisant `expectException()` avec le nom de la classe d'exception.
   - [ ] c) En utilisant `assertExceptionType()` avec le nom de la classe d'exception.
   - [ ] d) Aucune méthode spécifique n'est nécessaire pour spécifier le type d'exception.

**7. Quel est l'effet de l'attribut `testdox="true"` dans le fichier `phpunit.xml` ?**
   - [x] a) Il active le mode "TestDox" qui génère une documentation basée sur les noms des méthodes de test.
   - [ ] b) Il désactive tous les tests dans le fichier `phpunit.xml`.
   - [ ] c) Il spécifie le dossier où les rapports de test seront générés.
   - [ ] d) Il configure la coloration de la sortie des tests.

**8. Comment exécutez-vous les tests avec PHPUnit dans le terminal ?**  
:point_up: On ai passé par la commande `vendor/bin/phpunit` mais il est possible d'exécuté PHPUnit avec la commande `phpunit` s'il est installer en global.
   - [ ] a) `phpunit run`
   - [ ] b) `phpunit start`
   - [ ] c) `phpunit test`
   - [x] d) `phpunit`

**9. Quand on test une exception, faut-il déclancher l'exception avant ou après l'instruction `$this->expectException()` ?**
   - [ ] a) Avant
   - [x] b) Après

**10. Qu'elle type d'argument doit contenir la méthode `$this->expectException()` ?**
   - [ ] a) `InvalidArgumentException::class`
   - [x] b) Le type de l'exception attendu
   - [ ] c) Une exception de type `DivisionByZeroError`


# Réponses :
1. b) PHPUnit
2. c) En utilisant la configuration du fichier `phpunit.xml`.
3. b) En déclarant une méthode statique et en utilisant l'attribut `DataProvider`.
4. a) `assertEquals()` compare les valeurs sans tenir compte du type, tandis que `assertSame()` compare les valeurs et les types.
5. c) Dans le fichier `bootstrap.php`.
6. b) En utilisant `expectException()` avec le nom de la classe d'exception.
7. a) Il active le mode "TestDox" qui génère une documentation basée sur les noms des méthodes de test.
8.  d) `phpunit`
9.  b) Après
10. b) Le type de l'exception attendu