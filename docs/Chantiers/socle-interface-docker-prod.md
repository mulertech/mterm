# Chantier — socle d'interface pour `docker-prod`

**État visé (C)** : `mterm` v2 offre une couche terminal qui lit les séquences d'échappement et l'UTF-8, rend toujours le terminal à son état normal, n'appelle plus de sous-processus pour ce qu'ANSI sait faire, et laisse capturer sa sortie. Par-dessus, une navigation par menus imbriqués et un rendu de voyant à quatre états, utilisables hors de `docker-prod`.

## Pourquoi ici et pas dans `docker-prod`

Les deux composants servent à toute application CLI présentant des états et des choix — ils ne portent rien de propre au déploiement. Les écrire dans `docker-prod` reviendrait à les en extraire plus tard, une fois que six panneaux les auront chacun contournés à leur façon.

Le rendu de voyant a une seconde raison d'exister ici : il **impose** une règle plutôt que de la rappeler. Quatre états — vert conforme, orange à surveiller, rouge non conforme, noir non exécutable — et un rouge qui porte le libellé de son correctif. Écrite dans un document, cette règle se perd ; écrite dans un composant qu'on ne peut pas contourner, elle tient.

## Ce qui est réécrit, et ce qui ne l'est pas

Les défauts sont **concentrés dans `Core/Terminal`** :

| Constat | Conséquence |
|---|---|
| `readChar()` lit un octet (`fgetc`) | une flèche en envoie trois (`\033[A`), un caractère accentué deux à quatre : la navigation aux flèches est impossible et la saisie accentuée cassée, alors que le paquet exige `ext-mbstring` |
| `specialMode()` / `normalMode()` appellent `stty` sans garantie de retour | une exception ou un `Ctrl+C` entre les deux laisse le terminal de l'utilisateur sans écho |
| `clear()` fait `system('clear')` | un processus forké à chaque écran, là où ANSI suffit — et notre interface repeint une page à chaque navigation |
| `supportsAnsi()` ignore `NO_COLOR` et le fait que la sortie ne soit pas un terminal | une redirection vers un fichier y écrit les codes d'échappement, ce qui gêne la capture des rapports |
| la sortie passe par `echo` en dur | rien ne peut la capturer ni la rediriger, et tout code qui affiche devient malaisé à tester |

**`Form`, `Command` et `Utils` ne sont pas réécrits.** Quatorze types de champs, un registre de commandes, un formateur de tableaux et une barre de progression qui fonctionnent et sont testés : les remplacer par du code neuf non éprouvé retarderait `docker-prod` pour un résultat fonctionnellement identique. Ils sont seulement adaptés à la nouvelle sortie.

## Étapes

- [x] 1. **Une sortie abstraite** plutôt qu'un `echo` en dur, pour que l'affichage soit capturable, redirigeable et testable. `Form`, `Utils` et `Command` s'y raccordent sans changer de comportement.
- [x] 2. **La couleur devient une énumération**, et le rendu honore `NO_COLOR` ainsi que l'absence de terminal sur la sortie. Une redirection vers un fichier ne doit plus y déposer de séquences d'échappement.
- [x] 3. **Le contrôle du terminal sans sous-processus** : effacement, positionnement du curseur et effacement de ligne par séquences ANSI. `system('clear')` disparaît.
- [x] 4. **Le mode brut rendu sûr** : la restauration passe par un gestionnaire d'arrêt, de sorte qu'une exception ou une interruption ne laisse jamais le terminal sans écho.
- [x] 5. **Une lecture qui comprend ce qu'elle lit** : séquences d'échappement — flèches, `Échap`, touches de fonction — et caractères UTF-8 multi-octets, au lieu d'un octet isolé.
- [x] 6. **Le rendu de voyant.** Quatre états, une forme et une couleur par état, un alignement commun, et un libellé de correctif obligatoire quand l'état est rouge. Le composant refuse un rouge sans correctif : c'est ce qui empêche un panneau écrit plus tard d'annoncer un problème sans dire quoi en faire.
- [x] 7. **L'abstraction de menu**, écrite mince. Imbrication, retour au niveau supérieur, sortie, et rappel du chemin courant. Elle s'appuie sur `SelectField`, qui gère déjà les listes de choix, mais un menu n'est pas un champ de formulaire : il porte une boucle et une pile de niveaux.
- [ ] 8. **Éprouver le menu contre un panneau réel** — le premier de `docker-prod`, quel qu'il soit — et le corriger **avant d'en écrire un second**. Deux propriétés ne se découvrent qu'à l'usage : comment une page qui met plusieurs secondes à se produire s'insère dans la boucle, et comment une action qui déverse une sortie longue rend la main au menu.
  - 15/09, essai de `mtprod` sur un VPS — dans le terminal intégré de PhpStorm, la dernière ligne de ce qui précédait restait visible au-dessus de chaque menu : ce terminal répond à `\033[2J` en poussant l'écran dans l'historique. Le terminal de macOS, lui, effaçait bien. Corrigé en v2.0.1 : `clear()` efface aussi l'historique (`\033[3J`) ; la séquence a été éprouvée à la main dans le terminal de PhpStorm, le menu corrigé reste à y revoir.
- [x] 9. **Tests et documentation** au niveau des composants existants : `tests/` couvre déjà `Core`, `Form`, `Utils` et `Command`, et le `README` documente chaque classe publique.
- [ ] 10. **Publier la version majeure.** La réécriture de `Core` rompt l'API, donc v2.0.0. `docker-prod` ne doit dépendre que d'une version publiée, jamais d'un `dev-main`.

## Décisions et raisons

- **Le voyant avant le menu.** Le premier est entièrement spécifié — quatre états, une règle d'affichage — donc sans risque de conception. Le second ne l'est qu'en apparence : sa forme dépend de contraintes qu'aucun panneau n'a encore révélées. **Promouvoir vers** : rien
- **Le menu est écrit mince et révisé tôt.** Le construire complet avant tout panneau serait concevoir pour des besoins imaginés. Le point de non-retour n'est pas le premier panneau mais le deuxième : extraire après deux panneaux coûte peu, après six c'est un remaniement. **Promouvoir vers** : rien
- **La refonte s'arrête à `Core`.** C'est là que sont tous les défauts constatés, et nulle part ailleurs. Réécrire ce qui fonctionne remplacerait du code éprouvé par du code neuf, pour un comportement identique — la définition même du gaspillage.
- **Les sous-processus disparaissent de l'affichage.** C'est le seul endroit où le mot « performance » a un sens dans une bibliothèque CLI : le coût est en entrées-sorties et en forks, jamais en calcul PHP. Deux forks par écran, sur une interface qui repeint à chaque navigation, se voient.
- **La sortie longue passe par une exécution directe.** Journaux d'un déploiement, `all-ai` : le processus écrit lui-même sur le terminal, et on garde le déroulé en temps réel — ce qui compte quand on regarde un déploiement se faire. La capturer pour la filtrer donnerait un résumé plus propre au prix du direct, mauvais échange pour une opération qu'on surveille. C'est le seul usage de `system()` qui subsiste, et il n'est pas dans le chemin d'affichage. **Promouvoir vers** : rien
- **Le menu porte le cycle d'action, pas son contenu.** Il enchaîne confirmation, exécution, rattrapage d'erreur et retour ; il ignore ce que l'action fait. Laisser ce cycle à chaque panneau ne coûterait pas du code dupliqué mais une interaction divergente — un panneau demandant « y/N », un autre « oui/non », un troisième ne confirmant pas. Déclarée plutôt qu'implémentée, la confirmation d'une action destructrice ne peut plus être oubliée par distraction. Le risque de concevoir pour des besoins imaginés ne s'applique pas : six panneaux sont spécifiés et leurs actions ont toutes la même forme. **Promouvoir vers** : rien
- **`mterm` est réveillé avant d'être une dépendance.** Sa dernière version date de mars 2025. Y toucher maintenant fait tourner sa chaîne d'intégration, confirme qu'il se construit sur le PHP courant et produit une version publiée — un dégrossissage qu'il vaut mieux faire avant que `docker-prod` n'en dépende, pas pendant. **Promouvoir vers** : rien

## À trancher avec l'utilisateur

- **Rien ne bloque.** Les questions de conception se sont refermées ; ce qui reste s'éprouvera contre le premier panneau, à l'étape 8.

## Ce qui reste

Les étapes 1 à 7 et 9 sont faites : `Core` est réécrit, `Form`, `Command` et `Utils` y sont raccordés, les deux composants d'interface existent et sont testés (351 tests, 98,5 % des lignes), `all-ai` est vert et le `README` documente chaque classe publique.

Deux étapes attendent ce qui est hors de ce dépôt :

- **L'étape 8** demande le premier panneau de `docker-prod`. Le menu est écrit mince exprès ; les deux propriétés à éprouver — une page lente dans la boucle, une action à sortie longue qui rend la main — ne se découvriront que là. `CommandRunner::runDirect()` est en place pour la seconde.
- **L'étape 10** demande une publication : commit, tag `v2.0.0`, release. Le dépôt reste à `dev-main` tant qu'elle n'est pas faite.

**Restauration du terminal et `ext-pcntl`** : une exception ou une sortie passent par le gestionnaire d'arrêt, mais un `Ctrl+C` ne restaure le terminal que si `ext-pcntl` est chargé — l'extension est déclarée en `suggest`, PHP tuant le processus sans exécuter les gestionnaires d'arrêt sur un signal non capté.

## Clôture

- [x] l'absence d'`ext-pcntl` est annoncée sur la sortie d'erreur, avec son remède — un mode brut irréversible ne dégrade plus en silence

- [x] aucun identifiant ni commentaire transitoire (`V2`, `New`, `Old`, `legacy`, `tmp`, `for now`, `TODO`)
- [x] aucun code mort, aucun code commenté
- [x] le rendu de voyant refuse un rouge sans libellé de correctif
- [ ] le menu a été éprouvé contre un panneau réel et corrigé avant le second
- [x] `./vendor/bin/mtdocker all-ai` vert
- [x] `README` à jour pour les deux composants
- [ ] v2.0.0 publiée et référencée par `docker-prod`
- [x] plus aucun `system()` dans le chemin d'affichage
- [x] une interruption pendant une saisie rend le terminal à son état normal
- [x] une sortie redirigée vers un fichier ne contient aucune séquence d'échappement
- [ ] décisions promues
- [ ] ce fichier supprimé, et `docs/Chantiers/` avec lui s'il ne contient rien d'autre
