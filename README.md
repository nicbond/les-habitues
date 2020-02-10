# les-habitues

Bonjour Alain,

Voici mes explications concernant le test.
Il y a trois fichiers: ShopController dans le dossier Controller, Shop dans le dossier Entity, Curl dans le dossier Curl.

Comme vu avec toi par téléphone, j'ai procédé à un gros cut des données.
Mon entité Shop comprendra donc les données suivantes:
 - le nom du shop (l'enseigne)
 - son adresse
 - le code postal
 - la ville
 - l'image
 - une offre
 - ainsi que l'id_shop enregistré dans votre base de données.
Concernant les boutiques d'une enseigne, mon choix s'est porté sur la première.

1). A partir de l'url: https://www.leshabitues.fr/testapi/shops dans la méthode shopAction(), j'ai réussi à récupérer l'ensemble des données grâce aux fonctions stream_context_create 
et de file_get_contents. La fonction json_decode me permettant elle de convertir des chaînes de caractères en objet.
Cependant, pour me faciliter la vie, j'ai préféré parser mes données à l'aide de tableaux associatifs: j'ai donc ajouté TRUE en second paramètre dans le json_decode.
A partir d'un count des données, et dans une bouche Do while, j'ai enregistré dans la base les shops récupérés, en incrémentant une variable i.

Lors de notre échange téléphonique, tu m'as précisé que je devais être en mesure de récupérer les données afin de les enregistrer en BDD mais aussi de pouvoir les mettre à jour si déjà présentes.
J'ai donc décidé d'utiliser dans ma boucle do while de placer un try catch afin de récupérer les éventuelles exceptions lors des enregistrements.
En fonction de l'id_shop récupéré, je vais vérifier si celui-ci est présent en base:
 - si non, je procéde à son enregistrement.
 - si oui, je vais récupérer l'entité afin de procéder à sa mise à jour (car mon findOneBy est un array et non un objet).
 
 
2). Pour la création createShopAction, j'ai utilisé l'annotation rest de FOSRestBundle me permettant de spécifier l'url, la méthode utilisée.
J'ai mis en place le ParamConverter, qui après configuration dans le fichier de config, me permet de convertir les données contenues dans le body de Postman directement en un objet Shop.
Je n'ai ensuite plus qu'à procéder à son enregistrement dans ma base.

3).Pour la fonction delete, j'envoie en donnée mon id.
J'effectue en amont un contrôle sur sa présence en base et le cas échéant je retourne un code 404: HTTP_NOT_FOUND.
La suppression du shop s'effectuant par le remove.

4).Pour l'update des données, j'ai cru comprendre qu'il était plus approprié d'utiliser la méthode PUT que POST.
Et que si j'étais amené à effectuer la mise à jour que d'un seul champ, de faire attention à bien préciser que les champs vides ne doivent pas être updatés.
N'ayant pas vu de vrai exemple, j'ai procédé à une vérification de la data et en fonction de celle-ci de réaliser la mise à jour des champs concernés.


J'ai ensuite cherché comment pouvoir rendre mon code meilleur, et j'ai opté pour un service: CurlHTTP.
Ici, j'ai créé 3 méthodes: 
 - curlPost()
 - curlDelete()
 - curlUpdate()
avec en plus un constructeur me permettant de récupérer l'entity manager et le container si besoin.

Chaque méthode appellera ensuite la méthode getHttpCode() permettant de traiter les codes HTPP et de retourner ensuite la réponse :)

PS: il ne me manque plus que la phase refacto dans le controller afin d'appeler mon service créé (soit retirer toute trace de cURL dans le controller)
Mais je ne sais pas comment le configurer dans la version 3... et je n'ai pas le temps de chercher...





