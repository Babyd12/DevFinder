<?php

namespace App\Tests\Functional;

use Faker\Factory;
use App\Entity\Apprenant;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApprenantTest extends ApiTestCase
{
    private string $jwtToken;
    private array $utilisateurConnecte;

    public function test_inscription_apprenant_par_defaut_si_aucun_compte(): void
    {
        $client = static::createClient();
        $faker = Factory::create();

        $email = 'apprenant@apprenant.com';

        $entityManager =  $client->getContainer()->get(EntityManagerInterface::class);
        $associaitonExistante = $entityManager->getRepository(Apprenant::class)->findOneBy(['email' => $email]);
        if ($associaitonExistante == null) {
            // L'utilisateur n'existe pas, procédez à l'inscription
            $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
           
            $description = $faker->regexify('[A-Za-z0-9]{36}');

            $data = [
                "nom_complet" => 'Mon Apprenant',
                "email" => $email,
                "mot_de_passe" => 'Animaleman24@',
                "telephone" => $telephoneSenegal,
                "description" => $description,
        
            ];

            $client->request('POST', '/api/apprenant/inscription', [
                'headers' => ['Accept' => 'application/json'],
                'json' => $data
            ]);
            $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
            $this->assertResponseStatusCodeSame(201);
            $this->assertEquals(201, $client->getResponse()->getStatusCode());
        } else {
            $client->request('GET', '/api/apprenant/' . $associaitonExistante->getId(), [
                'headers' => ['Accept' => 'application/json']

            ]);
            // var_dump($client->getResponse()->getContent()); die();
            $this->assertResponseStatusCodeSame(200);
            $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        }

        // $this->assertJsonContains(
        //     [
        //         '@context' => '/api/contexts/Module%20gestion%20de%20compte%20-Apprenant',
        //         '@id' => '/api/apprenant/liste',
        //         "@type" => "hydra:Collection",
        //         'hydra:member' => [],
        //     ]
        // ); 
        // $this->assertArrayHasKey('hydra:totalItems', json_decode($client->getResponse()->getContent(), true));
        // $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function test_connexion_apprenant(): void
    {
        $client = static::createClient();
        $data = [
            'email' => 'apprenant@apprenant.com',
            'mot_de_passe' => 'Animaleman24@'
        ];

        $response =  $client->request('POST', '/connexion', [
            'headers' => ['Accept' => 'application/json'],
            'json' => $data,
        ]);

        $this->assertResponseIsSuccessful();
        //le token est-il present comme clé de tableau dans la response ?
        $this->assertArrayHasKey('token', $response->toArray());
        $this->jwtToken =  $response->toArray()['token'];
    }

    /**
     * @see connexion_apprenant
     * @var this->jwtToken hydrater depuis connexion_apprenant 
     * @
     */
    public function test_recuperer_utilisateur_connecter(): void
    {
        $this->test_connexion_apprenant();
        $client = static::createClient();
        $response =  $client->request('POST', '/api/utilisateur/connecte', [
            'headers' => ['Accept' => 'application/json'],
            'auth_bearer' => $this->jwtToken
        ]);

        $this->assertResponseIsSuccessful();
        $this->utilisateurConnecte = $response->toArray();

        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains([
            'id' => $this->utilisateurConnecte['id'],
            'nom_complet' => $this->utilisateurConnecte['nom_complet'],
            'email' => $this->utilisateurConnecte['email'],
            'role' =>  $this->utilisateurConnecte['role'],

        ]);
        // var_dump($this->utilisateurConnecte);
    }

    public function test_inscription_apprenant(): void
    {
        $client = static::createClient();
        $faker = Factory::create();

        $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
       
        $description = $faker->regexify('[A-Za-z0-9]{36}');

        $data = [
            "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
            "email" => $faker->email(),
            "mot_de_passe" => password_hash('Animaleman24@', PASSWORD_BCRYPT),
            "telephone" => $telephoneSenegal,
            "description" => $description,
        ];

        // var_dump($data);

        $client->request('POST', '/api/apprenant/inscription', [
            'headers' => ['Accept' => 'application/json'],
            'json' => $data
        ]);

        $this->assertResponseHeaderSame('accept-patch', 'application/json');
        $this->assertResponseStatusCodeSame(201);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function test_afficher_liste_des_apprenants(): void
    {

        $client =  static::createClient();
        $client->request('GET', '/api/apprenant/liste', ['headers' => ['Accept' => 'application/json']]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        // $this->assertJsonContains(
        //     [
        //         '@context' => '/api/contexts/Module%20gestion%20de%20compte%20-Apprenant',
        //         '@id' => '/api/apprenant/liste',
        //         "@type" => "hydra:Collection",
        //         'hydra:member' => [],
        //     ]
        // );
        // $this->assertArrayHasKey('hydra:totalItems', json_decode($client->getResponse()->getContent(), true));
    }

    public function test_editer_apprenant_sans_token(): void
    {
        $client = static::createClient();
        $faker = Factory::create();

        $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
       
        $description = $faker->regexify('[A-Za-z0-9]{36}');

        $data = [
            "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
            "email" => $faker->email(),
            "mot_de_passe" => password_hash('Animaleman24@', PASSWORD_BCRYPT),
            "telephone" => $telephoneSenegal,
            "description" => $description,
    
        ];
        $client->request('PUT', '/api/apprenant/1', [
            'headers' => ['Accept' => 'application/json'],
            'json' => $data
        ]);

        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertJsonContains(['code' => 401, 'message' => 'JWT Token not found']);
    }

    /**
     * @see connexion apprenant
     * @return string token
     * 
     * @see recupere_utilisateur_connecter
     * @return array instance d'une apprenant 
     */
    public function test_editer_apprenant_avec_token(): void
    {
        $client = static::createClient();
        $faker = Factory::create();
        // je récupère les informations de l'apprenant connecté de façons sécurisé
        $this->test_recuperer_utilisateur_connecter();

        $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
       
        $description = $faker->regexify('[A-Za-z0-9]{36}');

        $data = [
            "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
            "telephone" => $telephoneSenegal,
            "description" => $description,
    
        ];

        $client->request('PUT', '/api/apprenant/' .  $this->utilisateurConnecte['id'], [
            'headers' => ['Accept' => 'application/json'],
            'auth_bearer' => $this->jwtToken,
            'json' => $data
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function test_recuperer_une_apprenant_via_son_id(): void
    {
        $client = static::createClient();
        $email = 'apprenant@apprenant.com';

        $entityManager =  $client->getContainer()->get(EntityManagerInterface::class);
        $apprenantExistante = $entityManager->getRepository(Apprenant::class)->findOneBy(['email' => $email]);

        $client->request('GET', '/api/apprenant/' . $apprenantExistante->getId(), [
            'headers' => ['Accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertJsonContains([
            // 'id' => $apprenantExistante->getId(),
            'nom_complet' => $apprenantExistante->getNomComplet(),
            'email' => $apprenantExistante->getEmail(),
            // 'telephone' => $apprenantExistante->getTelephone(),
            // 'description' => $apprenantExistante->getDescription(),
        ]);
    }

    public function test_supprimer_compte_apprenant_sans_token(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/apprenant/1', [
            'headers' => ['Accept' => 'application/json']
        ]);

        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    public function test_supprimer_compte_apprenant_avec_token(): void
    {
        $client = static::createClient();
        $faker = Factory::create();
        $this->test_inscription_apprenant_par_defaut_si_aucun_compte();
        $this->test_recuperer_utilisateur_connecter();
        $client->request('DELETE', '/api/apprenant/' . $this->utilisateurConnecte['id'], [
            'headers' => ['Accept' => 'application/json'],
            'auth_bearer' => $this->jwtToken,
        ]);
        // dump($client->getResponse()->getContent());
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json');
       
    }
}
