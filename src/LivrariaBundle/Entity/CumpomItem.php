<?php

namespace LivrariaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Description of CumpomItem
 *
 * @ORM\Entity
 * ORM\Table(name="cupom_item")
 */
class CumpomItem 
{ 
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    
    private $id;
     /**
     * @ORM\ManyToOne(targetEntity="Cumpom")
     * @ORM\JoinColumn(name="cupom_id", referencedColumnName="id")
     */
    
    private $cupomId;
    
    /**
     * ORM\Column(type="integer")
     */
    private $ordemItem;
    
    /**
     *
     * ORM\Column(type="integer")
     */
    private $itemCod;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $descricaoItem;
    
    /**
     *
     * @return integer
     */
    
    private $quantidade;
    
    /**
     *
     * @ORM\Column(type="decimal", scale=2)
     */
    private $valorUnitario implements \JsonSerielizable;
    
    

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
     * Set descricaoItem
     *
     * @param string $descricaoItem
     *
     * @return CumpomItem
     */
    public function setDescricaoItem($descricaoItem)
    {
        $this->descricaoItem = $descricaoItem;

        return $this;
    }

    /**
     * Get descricaoItem
     *
     * @return string
     */
    public function getDescricaoItem()
    {
        return $this->descricaoItem;
    }

    /**
     * Set valorUnitario
     *
     * @param string $valorUnitario
     *
     * @return CumpomItem
     */
    public function setValorUnitario($valorUnitario)
    {
        $this->valorUnitario = $valorUnitario;

        return $this;
    }

    /**
     * Get valorUnitario
     *
     * @return string
     */
    public function getValorUnitario()
    {
        return $this->valorUnitario;
    }

    /**
     * Set cupomId
     *
     * @param \LivrariaBundle\Entity\Cumpom $cupomId
     *
     * @return CumpomItem
     */
    public function setCupomId(\LivrariaBundle\Entity\Cumpom $cupomId = null)
    {
        $this->cupomId = $cupomId;

        return $this;
    }

    /**
     * Get cupomId
     *
     * @return \LivrariaBundle\Entity\Cumpom
     */
    public function getCupomId()
    {
        return $this->cupomId;
    }
    
    public function jsonSerialize()
    {
        return array(
            "descricao"=>  $this->getDescricaoItem();
            "valor"=>  $this->getValorUnitario();
            //"numOrdem"=>  $this->getOrdemItem();
            //"codigo"=> $this->getCodigo();
        )
    }
}
