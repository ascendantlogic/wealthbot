<?php

namespace Wealthbot\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wealthbot\ClientBundle\Model\Beneficiary as BaseBeneficiary;
use Wealthbot\SignatureBundle\Entity\DocumentSignature;
use Wealthbot\SignatureBundle\Model\SignableInterface;

/**
 * Beneficiary
 */
class Beneficiary extends BaseBeneficiary implements SignableInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $account_id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    private $first_name;

    /**
     * @var string
     */
    private $last_name;

    /**
     * @var string
     */
    private $middle_name;

    /**
     * @var string
     */
    private $ssn;

    /**
     * @var \DateTime
     */
    private $birth_date;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $city;

    /**
     * @var integer
     */
    private $state_id;

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $relationship;

    /**
     * @var float
     */
    private $share;

    /**
     * @var \Wealthbot\AdminBundle\Entity\State
     */
    private $state;

    /**
     * @var \Wealthbot\ClientBundle\Entity\ClientAccount
     */
    private $account;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set account_id
     *
     * @param integer $accountId
     * @return Beneficiary
     */
    public function setAccountId($accountId)
    {
        $this->account_id = $accountId;

        return $this;
    }

    /**
     * Get account_id
     *
     * @return integer
     */
    public function getAccountId()
    {
        return $this->account_id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Beneficiary
     */
    public function setType($type)
    {
        return parent::setType($type);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return parent::getType();
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return Beneficiary
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return Beneficiary
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set middle_name
     *
     * @param string $middleName
     * @return Beneficiary
     */
    public function setMiddleName($middleName)
    {
        $this->middle_name = $middleName;

        return $this;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->getFirstName() . ' ' . $this->getMiddleName() . ' ' . $this->getLastName();
    }

    /**
     * Get middle_name
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middle_name;
    }

    /**
     * Set ssn
     *
     * @param string $ssn
     * @return Beneficiary
     */
    public function setSsn($ssn)
    {
        $this->ssn = $ssn;

        return $this;
    }

    /**
     * Get ssn
     *
     * @return string
     */
    public function getSsn()
    {
        return $this->ssn;
    }

    /**
     * Set birth_date
     *
     * @param \DateTime $birthDate
     * @return Beneficiary
     */
    public function setBirthDate($birthDate)
    {
        $this->birth_date = $birthDate;

        return $this;
    }

    /**
     * Get birth_date
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Beneficiary
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Beneficiary
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state_id
     *
     * @param integer $stateId
     * @return Beneficiary
     */
    public function setStateId($stateId)
    {
        $this->state_id = $stateId;

        return $this;
    }

    /**
     * Get state_id
     *
     * @return integer
     */
    public function getStateId()
    {
        return $this->state_id;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return Beneficiary
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set relationship
     *
     * @param string $relationship
     * @return Beneficiary
     */
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;

        return $this;
    }

    /**
     * Get relationship
     *
     * @return string
     */
    public function getRelationship()
    {
        return $this->relationship;
    }

    /**
     * Set share
     *
     * @param float $share
     * @return Beneficiary
     */
    public function setShare($share)
    {
        $this->share = $share;

        return $this;
    }

    /**
     * Get share
     *
     * @return float
     */
    public function getShare()
    {
        return $this->share;
    }

    /**
     * Set state
     *
     * @param \Wealthbot\AdminBundle\Entity\State $state
     * @return Beneficiary
     */
    public function setState(\Wealthbot\AdminBundle\Entity\State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return \Wealthbot\AdminBundle\Entity\State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set account
     *
     * @param \Wealthbot\ClientBundle\Entity\ClientAccount $account
     * @return Beneficiary
     */
    public function setAccount(\Wealthbot\ClientBundle\Entity\ClientAccount $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Wealthbot\ClientBundle\Entity\ClientAccount
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Get client account object
     *
     * @return \Wealthbot\ClientBundle\Model\ClientAccount;
     */
    public function getClientAccount()
    {
        return $this->account;
    }

    /**
     * Get id of source object
     *
     * @return mixed
     */
    public function getSourceObjectId()
    {
        return $this->id;
    }

    /**
     * Get type of document signature
     *
     * @return string
     */
    public function getDocumentSignatureType()
    {
        return DocumentSignature::TYPE_CHANGE_BENEFICIARY;
    }


}
