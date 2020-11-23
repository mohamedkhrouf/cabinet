<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="I think you're already registered!"
 * )
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToMany(targetEntity="User",  inversedBy="doctors")
     * @ORM\JoinTable(name="user_user",
     *     joinColumns={@ORM\JoinColumn(name="doctor_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="patient_id", referencedColumnName="id")}
     * )
     */
    private $patients;

    /**
     * @ORM\ManyToMany(targetEntity="User",  mappedBy="patients")
     */
    private $doctors;
    /**
     * @ORM\Column(type="string")
     */
    private $cin;
    /**
     * @ORM\Column(type="string")
     */
    private $num;
    /**
     * @ORM\Column(type="string")
     */
    private $genre;

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }
    /**
     * @ORM\Column(type="string")
     */
    private $age;
    /**
     * @ORM\Column(type="string")
     */
    private $description;
    /**
     * @ORM\Column(type="string")
     */
    private $address;
    /**
     * @return mixed
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * @param mixed $num
     */
    public function setNum($num)
    {
        $this->num = $num;
    }
    /**
     * @return mixed
     */
    public function getCin()
    {
        return $this->cin;
    }

    /**
     * @param mixed $cin
     */
    public function setCin($cin)
    {
        $this->cin = $cin;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->patients = new ArrayCollection();
        $this->doctors = new ArrayCollection();
        $this->roles = array('ROLE_ADMIN');
    }
    public function getPatients()
    {
        return $this->patients;
    }
    public function makeUser(){
$this->roles = array('ROLE_USER');}
    public function addPatient(User $patient)
    {   if (!$this->patients->contains($patient)) {
        $this->patients[] = $patient;
    }

    }
    public function addDoctor(User $doctor)
    {      if (!$this->doctors->contains($doctor)){
        $this->doctors[] = $doctor;}
        if (!$this->patients->contains($this)) {
        $doctor->addPatient($this);
        // Add the relation in the proper way
    }
    }





    public function getDoctors()
    {
        return $this->doctors;
    }


    public function removePatient(User $patient)
    {   if ($this->patients->contains($patient)) {
        $this->patients->removeElement($patient);
        $patient->removeDoctor($this);
    }


    }
    public function removeDoctor(User $doctor)
    {
        if ($this->doctors->contains($doctor)) {
            $this->doctors->removeElement($doctor);


        }
    }



}