<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * AddressBook
 *
 * @ORM\Table(name="address_book")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressBookRepository")\
 * @UniqueEntity("email")
 */
class AddressBook
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false, name="first_name")
     */
    protected $firstName;
    /**
     * @ORM\Column(type="string",nullable=false, name="last_name")
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="street_name")
     */
    protected $streetName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="street_number")
     */
    protected $streetNumber;

    /**
     * @ORM\Column(type="string", length=20, nullable=false, name="zipcode")
     */
    protected $zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="city")
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=50, nullable=false, name="country")
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=20, nullable=false, name="phone")
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="email", unique=true)
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(
     *      maxSize = "1024k",
     *      mimeTypes={"image/gif", "image/jpeg", "image/jpg", "image/png"},
     *      mimeTypesMessage = "Please upload a valid image"
     * )
     */
    protected $picture;

    /**
     * @ORM\Column(type="date", nullable=true, name="birthday")
     */
    protected $birthday;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param mixed $streetName
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
    }

    /**
     * @return mixed
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param mixed $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }


}
