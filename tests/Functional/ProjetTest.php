<?php

namespace App\Tests\Functional;

use DateTime;
use DateTimeZone;
use Faker\Factory;
use App\Entity\Apprenant;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Tests\Functional\AssociationTest as FunctionalAssociationTest;

class ProjetTest extends ApiTestCase
{
    private string $jwtToken;
    private array $utilisateurConnecte;


    public function test_connexion_avant_ajout_projet(): void
    {
        $association = new FunctionalAssociationTest();
        $association->test_inscription_association_par_defaut_si_aucun_compte();
        $association->assertResponseIsSuccessful();

        $client = static::createClient();
        $data = [
            'email' => 'association@association.com',
            'mot_de_passe' => 'Animaleman24@'
        ];

        $response =  $client->request('POST', '/api/connexion', [
            'headers' => ['Accept' => 'application/json'],
            'json' => $data,
        ]);

        $this->assertResponseIsSuccessful();
        //le token est-il present comme clé de tableau dans la response ?
        $this->assertArrayHasKey('token', $response->toArray());
        $this->jwtToken =  $response->toArray()['token'];
    }



    public function test_association_ajouter_projet_sans_langage_de_programmation(): void
    {
        $client = static::createClient();
        $faker = Factory::create();
        $titre = $faker->regexify('[A-Za-z]{16}');
        $nomFichierFictif = 'mon_fichier.pdf';
        $cheminFichierReel = __DIR__ . '/fichiers/test-65e75365213b8051506017.docx'; // Remplacez par le chemin réel du fichier
        $fichier = new UploadedFile($cheminFichierReel, $nomFichierFictif);
        
        $demain = new DateTime('tomorrow', new DateTimeZone('UTC'));
        $format = $demain->format('Y-m-d\TH:i:s.u\Z');
        
        $data = [
            // 'CahierDecharge' =>$fichier,
            'titre' => $titre,
            'nombre_de_participant' => '2',
            'date_limite' => $demain,
            'langage_de_programmation' => [
                "/api/langage/1"
            ],
            'statu' => 'En cours'
        ];
        
        $this->test_connexion_avant_ajout_projet();
        
        $response = $client->request('POST', 'http://localhost/api/projet/ajouter', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'multipart/form-data',
                'auth_bearer' => 'Bearer ' . $this->jwtToken,
            ],
            'json'=>$data
        ]);
        
        // dd($response);
        $this->assertResponseHasHeader('content-type', 'application/merge-patch+json');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // public function test_afficher_liste_des_apprenants(): void
    // {

    //     $client =  static::createClient();
    //     $client->request('GET', '/api/apprenant/liste', ['headers' => ['Accept' => 'application/json']]);

    //     $this->assertResponseIsSuccessful();
    //     $this->assertResponseStatusCodeSame(200);

    //     $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

    //     // $this->assertJsonContains(
    //     //     [
    //     //         '@context' => '/api/contexts/Module%20gestion%20de%20compte%20-Apprenant',
    //     //         '@id' => '/api/apprenant/liste',
    //     //         "@type" => "hydra:Collection",
    //     //         'hydra:member' => [],
    //     //     ]
    //     // );
    //     // $this->assertArrayHasKey('hydra:totalItems', json_decode($client->getResponse()->getContent(), true));
    // }

    // public function test_editer_apprenant_sans_token(): void
    // {
    //     $client = static::createClient();
    //     $faker = Factory::create();

    //     $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');

    //     $description = $faker->regexify('[A-Za-z0-9]{36}');

    //     $data = [
    //         "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
    //         "email" => $faker->email(),
    //         "mot_de_passe" => password_hash('Animaleman24@', PASSWORD_BCRYPT),
    //         "telephone" => $telephoneSenegal,
    //         "description" => $description,

    //     ];
    //     $client->request('PUT', '/api/apprenant/1', [
    //         'headers' => ['Accept' => 'application/json'],
    //         'json' => $data
    //     ]);

    //     $this->assertResponseHeaderSame('content-type', 'application/json');
    //     $this->assertResponseStatusCodeSame(401);
    //     $this->assertEquals(401, $client->getResponse()->getStatusCode());
    //     $this->assertJsonContains(['code' => 401, 'message' => 'JWT Token not found']);
    // }

    // /**
    //  * @see connexion apprenant
    //  * @return string token
    //  * 
    //  * @see recupere_utilisateur_connecter
    //  * @return array instance d'une apprenant 
    //  */
    // public function test_editer_apprenant_avec_token(): void
    // {
    //     $client = static::createClient();
    //     $faker = Factory::create();
    //     // je récupère les informations de l'apprenant connecté de façons sécurisé
    //     $this->test_recuperer_utilisateur_connecter();

    //     $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');

    //     $description = $faker->regexify('[A-Za-z0-9]{36}');

    //     $data = [
    //         "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
    //         "telephone" => $telephoneSenegal,
    //         "description" => $description,

    //     ];

    //     $client->request('PUT', '/api/apprenant/' .  $this->utilisateurConnecte['id'], [
    //         'headers' => ['Accept' => 'application/json'],
    //         'auth_bearer' => $this->jwtToken,
    //         'json' => $data
    //     ]);
    //     $this->assertResponseIsSuccessful();
    //     $this->assertResponseHeaderSame('content-type', 'application/json');
    // }

    // public function test_recuperer_une_apprenant_via_son_id(): void
    // {
    //     $client = static::createClient();
    //     $email = 'apprenant@apprenant.com';

    //     $entityManager =  $client->getContainer()->get(EntityManagerInterface::class);
    //     $apprenantExistante = $entityManager->getRepository(Apprenant::class)->findOneBy(['email' => $email]);

    //     $client->request('GET', '/api/apprenant/' . $apprenantExistante->getId(), [
    //         'headers' => ['Accept' => 'application/json']
    //     ]);

    //     $this->assertResponseStatusCodeSame(200);
    //     $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    //     $this->assertJsonContains([
    //         // 'id' => $apprenantExistante->getId(),
    //         'nom_complet' => $apprenantExistante->getNomComplet(),
    //         'email' => $apprenantExistante->getEmail(),
    //         // 'telephone' => $apprenantExistante->getTelephone(),
    //         // 'description' => $apprenantExistante->getDescription(),
    //     ]);
    // }

    // public function test_supprimer_compte_apprenant_sans_token(): void
    // {
    //     $client = static::createClient();
    //     $client->request('DELETE', '/api/apprenant/1', [
    //         'headers' => ['Accept' => 'application/json']
    //     ]);

    //     $this->assertResponseHeaderSame('content-type', 'application/json');
    //     $this->assertResponseStatusCodeSame(401);
    //     $this->assertEquals(401, $client->getResponse()->getStatusCode());
    //     $this->assertJsonContains(['message' => 'JWT Token not found']);
    // }

    // public function test_supprimer_compte_apprenant_avec_token(): void
    // {
    //     $client = static::createClient();
    //     $faker = Factory::create();
    //     $this->test_inscription_apprenant_par_defaut_si_aucun_compte();
    //     $this->test_recuperer_utilisateur_connecter();
    //     $client->request('DELETE', '/api/apprenant/' . $this->utilisateurConnecte['id'], [
    //         'headers' => ['Accept' => 'application/json'],
    //         'auth_bearer' => $this->jwtToken,
    //     ]);
    //     // dump($client->getResponse()->getContent());
    //     $this->assertResponseStatusCodeSame(200);
    //     $this->assertResponseHeaderSame('content-type', 'application/json');
    // }
}
