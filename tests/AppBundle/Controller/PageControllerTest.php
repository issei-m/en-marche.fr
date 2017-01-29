<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadPageData;
use AppBundle\DataFixtures\ORM\LoadProposalData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    use ControllerTestTrait;

    /**
     * @dataProvider providePages
     */
    public function testPages($path)
    {
        $this->client->request(Request::METHOD_GET, $path);
        $this->assertResponseStatusCode(Response::HTTP_OK, $response = $this->client->getResponse());

        // Assert Cloudflare will store this page in cache
        $this->assertContains('public, s-maxage=', $response->headers->get('cache-control'));
    }

    public function providePages()
    {
        return [
            ['/emmanuel-macron'],
            ['/emmanuel-macron/revolution'],
            ['/emmanuel-macron/mon-agenda'],
            ['/emmanuel-macron/le-programme'],
            ['/emmanuel-macron/le-programme/produire-en-france-et-sauver-la-planete'],
            ['/emmanuel-macron/le-programme/eduquer-tous-nos-enfants'],
            ['/le-mouvement'],
            ['/le-mouvement/notre-organisation'],
            ['/le-mouvement/les-comites'],
            ['/le-mouvement/les-evenements'],
            ['/le-mouvement/devenez-benevole'],
            ['/mentions-legales'],
        ];
    }

    public function testProposalDraft()
    {
        $this->client->request(Request::METHOD_GET, '/emmanuel-macron/le-programme/mieux-vivre-de-son-travail');
        $this->assertResponseStatusCode(Response::HTTP_NOT_FOUND, $this->client->getResponse());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->loadFixtures([
            LoadPageData::class,
            LoadProposalData::class,
        ]);

        $this->client = static::createClient();
    }

    protected function tearDown()
    {
        $this->loadFixtures([]);

        $this->client = null;

        parent::tearDown();
    }
}