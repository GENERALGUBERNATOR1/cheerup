<?php
namespace UserBundle\Entity;

use AppBundle\Entity\UserProfile;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="cheerup_user")
 */
class User extends BaseUser
{
    const NETWORK_VOLUNTEER = 'NETWORK_VOLUNTEER';
    const FORMER_MEMBER     = 'FORMER_MEMBER';

    private static $profileTypes = [
        self::NETWORK_VOLUNTEER => 'user.profile_type.network_volunteer',
        self::FORMER_MEMBER     => 'user.profile_type.former_member'
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Choice(callback = "getProfileTypes")
     */
    protected $profileType;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     * @var UserProfile
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\UserProfile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_profile_id", referencedColumnName="id")
     */
    private $userProfile;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function __construct()
    {
        parent::__construct();
        $this->userProfile = new UserProfile();
    }

    public function getFullName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * @return string
     */
    public function getProfileType()
    {
        return $this->profileType;
    }

    /**
     * @param string $profileType
     */
    public function setProfileType($profileType)
    {
        $this->profileType = $profileType;
    }

    /**
     * @return array
     */
    public static function getProfileTypes()
    {
        return array_keys(self::$profileTypes);
    }

    /**
     * @return array
     */
    public static function getProfileTypesChoices()
    {
        return self::$profileTypes;
    }
}
