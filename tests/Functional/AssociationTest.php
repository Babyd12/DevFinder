<?php

namespace App\Tests\Functional;

use Faker\Factory;
use App\Entity\Association;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AssociationTest extends ApiTestCase
{
    private string $jwtToken;
    private array $utilisateurConnecte;

    public function test_inscription_association_par_defaut_si_aucun_compte(): void
    {
        $client = static::createClient();
        $faker = Factory::create();

        $email = 'association@association.com';

        $entityManager =  $client->getContainer()->get(EntityManagerInterface::class);
        $associaitonExistante = $entityManager->getRepository(Association::class)->findOneBy(['email' => $email]);
        if (!$associaitonExistante) {
            // L'utilisateur n'existe pas, procédez à l'inscription
            $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
            $numero_identificacion = $faker->regexify('/^\d{7} [0-9A-Z]{3}$/');
            $description = $faker->regexify('[A-Za-z0-9]{36}');

            $data = [
                "nom_complet" => 'Mon Association',
                "email" => $email,
                "mot_de_passe" => password_hash('Animaleman24@', PASSWORD_BCRYPT),
                "telephone" => $telephoneSenegal,
                "description" => $description,
                "numero_identification_naitonal" => $numero_identificacion,
            ];

            $client->request('POST', '/api/association/inscription', [
                'headers' => ['Accept' => 'application/json'],
                'json' => $data
            ]);
            $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
            $this->assertResponseStatusCodeSame(201);
            $this->assertEquals(201, $client->getResponse()->getStatusCode());
        }

        $client->request('GET', '/api/association/' . $associaitonExistante->getId(), [
            'headers' => ['Accept' => 'application/json']
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        // $this->assertJsonContains(
        //     [
        //         '@context' => '/api/contexts/Module%20gestion%20de%20compte%20-Association',
        //         '@id' => '/api/association/liste',
        //         "@type" => "hydra:Collection",
        //         'hydra:member' => [],
        //     ]
        // ); 
        // $this->assertArrayHasKey('hydra:totalItems', json_decode($client->getResponse()->getContent(), true));
        // $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
    
    public function connexion_association(): void
    {
        $client = static::createClient();
        $data = [
            'email' => 'association@association.com',
            'mot_de_passe' => 'password',
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
     * @see connexion_association
     * @var this->jwtToken hydrater depuis connexion_association 
     * @
     */
    public function recuperer_utilisateur_connecter(): void
    {
        $this->connexion_association();
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

    public function test_inscription_association(): void
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

        $client->request('POST', '/api/association/inscription', [
            'headers' => ['Accept' => 'application/json'],
            'json' => $data
        ]);

        $this->assertResponseHeaderSame('accept-patch', 'application/merge-patch+json');
        $this->assertResponseStatusCodeSame(201);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }


    public function test_afficher_liste_des_associations(): void
    {

        $client =  static::createClient();
        $client->request('GET', '/api/association/liste', ['headers' => ['Accept' => 'application/json']]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        // $this->assertJsonContains(
        //     [
        //         '@context' => '/api/contexts/Module%20gestion%20de%20compte%20-Association',
        //         '@id' => '/api/association/liste',
        //         "@type" => "hydra:Collection",
        //         'hydra:member' => [],
        //     ]
        // );
        // $this->assertArrayHasKey('hydra:totalItems', json_decode($client->getResponse()->getContent(), true));
    }

    public function test_editer_association_sans_token(): void
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
        $client->request('PUT', '/api/association/1', [
            'headers' => ['Accept' => 'application/json'],
            'json' => $data
        ]);

        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertJsonContains(['code' => 401, 'message' => 'JWT Token not found']);
    }

    /**
     * @see connexion association
     * @return string token
     * 
     * @see recupere_utilisateur_connecter
     * @return array instance d'une association 
     */
    public function test_editer_association_avec_token(): void
    {
        $client = static::createClient();
        $faker = Factory::create();
        // je connecte une association
        $this->connexion_association();
        // je récupère les informations de l'association connecté de façons sécurisé
        $this->recuperer_utilisateur_connecter();

        $telephoneSenegal = $faker->regexify('/^77\d{3}\d{2}\d{2}$/');
        $numero_identificacion = $faker->regexify('/^\d{7} [0-9A-Z]{3}$/');
        $description = $faker->regexify('[A-Za-z0-9]{36}');

        $data = [
            "nom_complet" => $faker->firstName() . ' ' . $faker->lastName(),
            "telephone" => $telephoneSenegal,
            "description" => $description,
            "numero_identification_naitonal" => $numero_identificacion,
        ];

        $client->request('PUT', '/api/association/' .  $this->utilisateurConnecte['id'], [
            'headers' => ['Accept' => 'application/json'],
            'auth_bearer' => $this->jwtToken,
            'json' => $data
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }



    public function test_recuperer_une_association_via_son_id(): void
    {
        $client = static::createClient();
        $email = 'association@association.com';
    
        $entityManager =  $client->getContainer()->get(EntityManagerInterface::class);
        $associationExistante = $entityManager->getRepository(Association::class)->findOneBy(['email' => $email]);
    
      $client->request('GET', '/api/association/' . $associationExistante->getId(), [
            'headers' => ['Accept' => 'application/json']
        ]);
    
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        $this->assertJsonContains([
            // 'id' => $associationExistante->getId(),
            'nom_complet' => $associationExistante->getNomComplet(),
            'email' => $associationExistante->getEmail(),
            'telephone' => $associationExistante->getTelephone(),
            'description' => $associationExistante->getDescription(),
            'numero_identification_naitonal' => $associationExistante->getNumeroIdentificationNaitonal(),
        ]);
    }

    public function test_supprimer_compte_association_avec_token(): void
    {
        $client = static::createClient();
        $faker = Factory::create();
        // je connecte une association
        $this->connexion_association();
        // je récupère les informations de l'association connecté de façons sécurisé
        $this->recuperer_utilisateur_connecter();

        $client->request('DELETE', '/api/association/'.$this->utilisateurConnecte['id'], [
            'headers' => ['Accept' => 'application/json'],
            'auth_bearer' => $this->jwtToken,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}

