<?php

namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\AddressBookType;
use AppBundle\Entity\AddressBook;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AddressBookTypeTest extends TypeTestCase
{
    private $objectManager;
    private $eventDispatcher;

    protected function setUp()
    {
        // mock any dependencies
        $this->objectManager = $this->createMock(AddressBook::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        parent::setUp();
    }

    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies
        $type = new AddressBookType($this->objectManager, $this->eventDispatcher);

        return [
            // register the type instances with the PreloadedExtension
            new PreloadedExtension([$type], []),
        ];
    }

    public function testSubmitValidData()
    {
        $formData = [
            'firstName'    => 'name1',
            'lastName'     => 'name12',
            'streetName'   => '22',
            'streetNumber' => '22',
            'zip'          => '1234',
            'city'         => 'cirt',
            'country'      => 'alex',
            'phone'        => '12345',
            'email'        => 'aa@aa.com',
            'birthday'     => '2018-02-02'
        ];

        $objectToCompare = new AddressBook();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(AddressBookType::class, $objectToCompare);

        $object = new AddressBook();
        foreach ($formData as $k => $v) {
            $object->{"set{$k}"}($v);
        }
        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}