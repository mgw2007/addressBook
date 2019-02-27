<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\AddressBook;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Faker;

class AddressBookControllerTest extends WebTestCase
{

    protected $container;
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManager;
    protected $client;

    /**
     * @var Generator
     */
    protected $faker;

    public function setUp()
    {

        $this->faker = Faker\Factory::create();
        $this->client = static::createClient();
        $this->container = self::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();

        $connection = $this->entityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $query = $databasePlatform->getTruncateTableSQL(
            $this->entityManager->getClassMetadata(AddressBook::class)->getTableName()
        );
        $connection->executeUpdate($query);

    }

    /**
     * @return array
     */
    protected function getAddressBookData()
    {
        return [
            'firstName'    => $this->faker->firstName,
            'lastName'     => $this->faker->lastName,
            'email'        => $this->faker->email,
            'phone'        => $this->faker->phoneNumber,
            'streetNumber' => $this->faker->buildingNumber,
            'streetName'   => $this->faker->streetName,
            'zip'          => $this->faker->postcode,
            'city'         => $this->faker->city,
            'country'      => $this->faker->country,
        ];
    }

    protected function createAddressBook($data = null)
    {
        $data = $data ? $data : $this->getAddressBookData();

        $addressBook = new AddressBook();
        foreach ($data as $k => $v) {
            $addressBook->{"set" . ucfirst($k)}($v);
        }
        $this->entityManager->persist($addressBook);
        $this->entityManager->flush();
        return $addressBook;

    }


    public function testIndex()
    {
        $this->client->request('GET', '/');

        $response = $this->client->getResponse()->getContent();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains("Address book", $response);

    }


    public function testList()
    {
        $addressBook = $this->createAddressBook();
        $this->client->request('GET', '/list/');
        //create data and check its returned
        $response = $this->client->getResponse()->getContent();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains($addressBook->getFirstName(), $response);
        $this->assertContains($addressBook->getLastName(), $response);
        $this->assertContains($addressBook->getEmail(), $response);
        $this->assertContains($addressBook->getCity(), $response);

    }

    public function testCreateAddressBook()
    {
        $crawler = $this->client->request('GET', '/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $token = $crawler->filter('input#appbundle_addressbook__token')->attr('value');
        $this->assertTrue(($token && null !== $token));


        $form = $crawler->selectButton("Save")->form();

        $formName = 'appbundle_addressbook';
        $data = $this->getAddressBookData();
        foreach ($data as $k => $v) {
            $form["{$formName}[$k]"] = $v;

        }
        $form["{$formName}[_token]"] = $token;
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', '/list/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = $this->client->getResponse()->getContent();
        $this->assertContains($data['firstName'], $response);
    }


    public function testCreateAddressBookWithPicture()
    {
        $crawler = $this->client->request('GET', '/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $token = $crawler->filter('input#appbundle_addressbook__token')->attr('value');
        $this->assertTrue(($token && null !== $token));


        $form = $crawler->selectButton("Save")->form();

        $formName = 'appbundle_addressbook';
        $data = $this->getAddressBookData();
        foreach ($data as $k => $v) {
            $form["{$formName}[$k]"] = $v;

        }
        $form["{$formName}[_token]"] = $token;
        $form["{$formName}[picture]"]->upload(__DIR__ . '/default.png');
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', '/list/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = $this->client->getResponse()->getContent();
        $this->assertContains($data['firstName'], $response);
    }

    /**
     * @dataProvider provideInvalidImages
     */
    public function testCreateAddressBookWithInvalidPictureType($invalidImage)
    {
        $crawler = $this->client->request('GET', '/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $token = $crawler->filter('input#appbundle_addressbook__token')->attr('value');
        $this->assertTrue(($token && null !== $token));


        $form = $crawler->selectButton("Save")->form();

        $formName = 'appbundle_addressbook';
        $data = $this->getAddressBookData();
        foreach ($data as $k => $v) {
            $form["{$formName}[$k]"] = $v;

        }
        $form["{$formName}[_token]"] = $token;
        $form["{$formName}[picture]"]->upload(__DIR__ . "/" . $invalidImage[0]);
        $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = $this->client->getResponse()->getContent();
        $this->assertContains($invalidImage[1], $response);
    }

    public function provideInvalidImages()
    {
        return [
            [['4mImage.jpg', 'The file is too large']],
            [['notImage.txt', 'valid image']]
        ];
    }

    public function testCreateAddressBookEmailUnique()
    {
        $crawler = $this->client->request('GET', '/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $token = $crawler->filter('input#appbundle_addressbook__token')->attr('value');
        $this->assertTrue(($token && null !== $token));


        $form = $crawler->selectButton("Save")->form();

        $formName = 'appbundle_addressbook';

        $data = $this->getAddressBookData();
        $this->createAddressBook($data);

        foreach ($data as $k => $v) {
            $form["{$formName}[$k]"] = $v;

        }
        $form["{$formName}[_token]"] = $token;
        $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = $this->client->getResponse()->getContent();
        $this->assertContains('value is already used', $response);
    }
    public function testDeleteAddressBook()
    {
        $addressBook = $this->createAddressBook();
        $id = $addressBook->getId();
        $this->assertNotEmpty($addressBook->getId());
        $crawler = $this->client->request('DELETE', '/'.$addressBook->getId());
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $addressBookTest  = $this->entityManager->getRepository('AppBundle:AddressBook')->find($id);
        $this->assertNull($addressBookTest);
    }

    public function testUpdateAddressBook()
    {
        $addressBookObject = $this->createAddressBook();
        $id = $addressBookObject->getId();
        $crawler = $this->client->request('GET', "$id/edit");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $token = $crawler->filter('input#appbundle_addressbook__token')->attr('value');
        $this->assertTrue(($token && null !== $token));


        $form = $crawler->selectButton("Save")->form();

        $formName = 'appbundle_addressbook';
        $data = $this->getAddressBookData();
        foreach ($data as $k => $v) {
            $form["{$formName}[$k]"] = $v;

        }
        $form["{$formName}[_token]"] = $token;
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', '/list/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = $this->client->getResponse()->getContent();
        $this->assertContains($data['firstName'], $response);
    }


    public function testUpdateAddressBookWithPicture()
    {
        $addressBookObject = $this->createAddressBook();
        $id = $addressBookObject->getId();
        $crawler = $this->client->request('GET', "$id/edit");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $token = $crawler->filter('input#appbundle_addressbook__token')->attr('value');
        $this->assertTrue(($token && null !== $token));


        $form = $crawler->selectButton("Save")->form();

        $formName = 'appbundle_addressbook';
        $data = $this->getAddressBookData();
        foreach ($data as $k => $v) {
            $form["{$formName}[$k]"] = $v;

        }
        $form["{$formName}[_token]"] = $token;
        $form["{$formName}[picture]"]->upload(__DIR__ . '/default.png');
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', '/list/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = $this->client->getResponse()->getContent();
        $this->assertContains($data['firstName'], $response);
    }

    /**
     * @dataProvider provideInvalidImages
     */
    public function testUpdateAddressBookWithInvalidPictureType($invalidImage)
    {
        $addressBookObject = $this->createAddressBook();
        $id = $addressBookObject->getId();
        $crawler = $this->client->request('GET', "$id/edit");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $token = $crawler->filter('input#appbundle_addressbook__token')->attr('value');
        $this->assertTrue(($token && null !== $token));


        $form = $crawler->selectButton("Save")->form();

        $formName = 'appbundle_addressbook';
        $data = $this->getAddressBookData();
        foreach ($data as $k => $v) {
            $form["{$formName}[$k]"] = $v;

        }
        $form["{$formName}[_token]"] = $token;
        $form["{$formName}[picture]"]->upload(__DIR__ . "/" . $invalidImage[0]);
        $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = $this->client->getResponse()->getContent();
        $this->assertContains($invalidImage[1], $response);
    }

}
