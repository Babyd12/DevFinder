<?php

namespace App\Tests\Functional;

use Faker\Factory;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class EntrepriseTest extends ApiTestCase
{
    private string $jwtToken;
    private array $utilisateurConnecte;

    public function test_inscription_entreprise_par_defaut_si_aucun_compte(): void
    {
        $client = static::createClient();
        $faker = Factory::create();

        $email = 'entreprise@entreprise.com';

        $entityManager =  $client->getContainer()->get(EntityManagerInterface::class);
        $associaitonExistante = $entityManager->getRepository(Entreprise::class)->findOneBy(['email' => $email]);
        if ($associaitonExistante == null) {
            // L'utilisateur n'existe pas, procédez à l'inscription
            $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
            $numero_identificacion = $faker->regexify('/^\d{7} [0-9A-Z]{3}$/');
            $description = $faker->regexify('[A-Za-z0-9]{36}');

            $data = [
                "nom_complet" => 'Mon Entreprise',
                "email" => $email,
                "mot_de_passe" => 'Animaleman24@',
                "telephone" => $telephoneSenegal,
                "description" => $description,
                "numero_identification_naitonal" => $numero_identificacion,
            ];

            $client->request('POST', '/api/entreprise/inscription', [
                'headers' => ['Accept' => 'application/json'],
                'json' => $data
            ]);
            $this->assertResponseHeaderSame('accept-patch', 'application/merge-patch+json');
            $this->assertResponseStatusCodeSame(201);
            $this->assertEquals(201, $client->getResponse()->getStatusCode());
        } else {
            $client->request('GET', '/api/entreprise/' . $associaitonExistante->getId(), [
                'headers' => ['Accept' => 'application/json']

            ]);
            // var_dump($client->getResponse()->getContent()); die();
            $this->assertResponseStatusCodeSame(200);
            $this->assertResponseHeaderSame('accept-patch', 'application/merge-patch+json');

        }

    }

    public function test_connexion_entreprise(): void
    {
        $client = static::createClient();
        $data = [
            'email' => 'entreprise@entreprise.com',
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
     * @see connexion_entreprise
     * @var this->jwtToken hydrater depuis connexion_entreprise 
     * @
     */
    public function test_recuperer_utilisateur_connecter(): void
    {
        $this->test_connexion_entreprise();
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

    public function test_inscription_entreprise(): void
    {
        $client = static::createClient();
        $faker = Factory::create();

        $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
        $numero_identificacion = $faker->regexify('/^\d{7} [0-9A-Z]{3}$/');
        $description = $faker->regexify('[A-Za-z0-9]{36}');

        $data = [
            "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
            "email" => $faker->email(),
            "mot_de_passe" => password_hash('Animaleman24@', PASSWORD_BCRYPT),
            "telephone" => $telephoneSenegal,
            "description" => $description,
            "numero_identification_naitonal" => $numero_identificacion,
        ];

        // var_dump($data);

        $client->request('POST', '/api/entreprise/inscription', [
            'headers' => ['Accept' => 'application/json'],
            'json' => $data
        ]);

        $this->assertResponseHeaderSame('accept-patch', 'application/merge-patch+json');
        $this->assertResponseStatusCodeSame(201);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function test_afficher_liste_des_entreprises(): void
    {

        $client =  static::createClient();
        $client->request('GET', '/api/entreprise/liste', ['headers' => ['Accept' => 'application/json']]);

        $this->assertResponseHasHeader('cache-control');
        $this->assertResponseHasHeader('content-type', 'application/json; charset=utf-8');    

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

   
        // $this->assertResponseHeaderSame('content-type4', 'application/json; charset=utf-8');

        // $this->assertJsonContains(
        //     [
        //         '@context' => '/api/contexts/Module%20gestion%20de%20compte%20-Entreprise',
        //         '@id' => '/api/entreprise/liste',
        //         "@type" => "hydra:Collection",
        //         'hydra:member' => [],
        //     ]
        // );
        // $this->assertArrayHasKey('hydra:totalItems', json_decode($client->getResponse()->getContent(), true));
    }

    public function test_editer_entreprise_sans_token(): void
    {
        $client = static::createClient();
        $faker = Factory::create();

        $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
        $numero_identificacion = $faker->regexify('/^\d{7} [0-9A-Z]{3}$/');
        $description = $faker->regexify('[A-Za-z0-9]{36}');

        $data = [
            "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
            "email" => $faker->email(),
            "mot_de_passe" => password_hash('Animaleman24@', PASSWORD_BCRYPT),
            "telephone" => $telephoneSenegal,
            "description" => $description,
            "numero_identification_naitonal" => $numero_identificacion,
        ];
        $client->request('PUT', '/api/entreprise/1', [
            'headers' => ['Accept' => 'application/json'],
            'json' => $data
        ]);
        $this->assertResponseHasHeader('cache-control');
        $this->assertResponseHasHeader('content-type', 'application/problem+json; charset=utf-8');
        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertJsonContains(['code' => 401, 'message' => 'JWT Token not found']);
    }

    /**
     * @see connexion entreprise
     * @return string token
     * 
     * @see recupere_utilisateur_connecter
     * @return array instance d'une entreprise 
     */
    public function test_editer_entreprise_avec_token(): void
    {
        $client = static::createClient();
        $faker = Factory::create();
        // je récupère les informations de l'entreprise connecté de façons sécurisé
        $this->test_recuperer_utilisateur_connecter();

        $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
        $numero_identificacion = $faker->regexify('/^\d{7} [0-9A-Z]{3}$/');
        $description = $faker->regexify('[A-Za-z0-9]{36}');

        $data = [
            "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
            "telephone" => $telephoneSenegal,
            "description" => $description,
            "numero_identification_naitonal" => $numero_identificacion,
        ];

        $client->request('PUT', '/api/entreprise/' .  $this->utilisateurConnecte['id'], [
            'headers' => ['Accept' => 'application/json'],
            'auth_bearer' => $this->jwtToken,
            'json' => $data
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHasHeader('cache-control');
        $this->assertResponseHasHeader('content-type', 'application/merge-patch+json');
    }

    public function test_recuperer_une_entreprise_via_son_id(): void
    {
        $client = static::createClient();
        $email = 'entreprise@entreprise.com';

        $entityManager =  $client->getContainer()->get(EntityManagerInterface::class);
        $entrepriseExistante = $entityManager->getRepository(Entreprise::class)->findOneBy(['email' => $email]);

        $client->request('GET', '/api/entreprise/' . $entrepriseExistante->getId(), [
            'headers' => ['Accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertJsonContains([
            // 'id' => $entrepriseExistante->getId(),
            'nom_complet' => $entrepriseExistante->getNomComplet(),
            'email' => $entrepriseExistante->getEmail(),
            'telephone' => $entrepriseExistante->getTelephone(),
            'description' => $entrepriseExistante->getDescription(),
            'numero_identification_naitonal' => $entrepriseExistante->getNumeroIdentificationNaitonal(),
        ]);
    }

    public function test_supprimer_compte_entreprise_sans_token(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/entreprise/1', [
            'headers' => ['Accept' => 'application/json']
        ]);

        $this->assertResponseHasHeader('content-type', 'application/merge-patch+json');
        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    public function test_supprimer_compte_entreprise_avec_token(): void
    {
        $client = static::createClient();
        $faker = Factory::create();
        // $this->test_inscription_entreprise_par_defaut_si_aucun_compte();
        $this->test_recuperer_utilisateur_connecter();
        $client->request('DELETE', '/api/entreprise/' . $this->utilisateurConnecte['id'], [
            'headers' => ['Accept' => 'application/json'],
            'auth_bearer' => $this->jwtToken,
        ]);
  
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHasHeader('content-type', 'application/merge-patch+json');
       
    }
}
