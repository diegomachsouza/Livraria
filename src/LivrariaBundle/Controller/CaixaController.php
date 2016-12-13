<?php

namespace LivrariaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use LivrariaBundle\Entity\Cumpom;
use LivrariaBundle\Entity\CumpomItem;

/**
 * Description of CaixaController
 *
 * @author aluno
 */
class CaixaController extends Controller
{
    /**
     * @Route("/caixa")
     */
    public function pdvAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $cupom = new Cumpom();
        $cupom->setData(new \DateTime());
        $cupom->setValorTotal(0);
        $cupom->setVendedor(1);
        
        $em->persist($cupom);  //Pega o obj e joga na camada de persistência da memória
        $em->flush();           //O flush joga no BD e descarta

        
        $request->getSession()->set('cupom-id', $cupom->getId());
        
        
        
        return $this->render("LivrariaBundle:Caixa:pdv.html.twig");
    }
    
    /**
     * @Route("/caixa/carregar", name="pesquisar")
     * @Method("POST")
     */
    public function carregarProdutoAction(Request $request) 
    {
         $em = $this->getDoctrine()->getManager();
       
         $codProd = $request->request->get('codigo');
         
         $produto = $em->getRepository('LivrariaBundle:Produtos')
                 ->find($codProd);
         
         $cupomId = $request->getSession()->get('cupom-id');
         $cupom = $em->getRepository('LivrariaBundle:Cumpom')->find($cupomId);
         
         $quantItem = $em->getRepository('LivrariaBundle:CumpomItem')->findBy(array("cupomId" => $cupomId));
         

                 
         /* if($produto == null)
         {
             return $this->json('erro');
         } */
         
         if ($produto instanceof Produtos)
         {
             $novoItem = new CumpomItem();
             $novoItem->setCupomId($cupom);
             $novoItem->setDescricaoItem($produto->getNome());
             $novoItem->setItemCod($codProd);
             $novoItem->setQuantidade(1);
             $novoItem->setValorUnitario($produto->getPreco());
             $novoItem->setOrderItem(count($quantItem)+1);
             
             $em->persists($novoItem); //Pega o obj e joga na camada de persistência da memória
             $em->flush();              //O flush joga no BD e descarta
             
             $retorno['status'] = "ok";
             $retorno["produto"] = $produto;
             
         } else{
             $retorno['status'] = "erro";
             $retorno["mensagem"] = "Produto não encontrado";
         }
         
         return $this->json('ok');
    }
    
    /**
     *@Route("/caixa/Estorno/{item}", name="item-estorno")
     */
    public function estornarItemAction(Request $request, $item)
    {
        $cupomId = $request->getSession()->get('cupom-id');
        
        $em = $this->getDoctrine()->getManager();
        
        $itemOld = $em->getRepository('LivrariaBundle:CumpomItem')->findBy(array(
            'cupomId'=>$cupomId,
            'ordemItem'=>$item
        ));
        
        $cupom = $em->getRepository('LivrariaBundle:Produtos')
                ->find($cupomId);
        
        
        $itemEstorno = new CumpomItem();
        $itemEstorno->setCupomId($cupom);
        $itemEstorno->setDescricaoItem("Estorno do Item: $item");
        $itemEstorno->setItemCod(9999);  //No fechamento, isso bate!
        $itemEstorno->setQuantidade(1);
        $itemEstorno->setValorUnitario($item->getValorUnitario() * -1);
        
        $em->persist($cupom);  //Pega o obj e joga na camada de persistência da memória
        $em->flush();           //O flush joga no BD e descarta

        return $this->redirectToRoute('caixa-index');
        
    }
    
    /**
     *@Route("/caixa/Cancelar", name="cancelar")
     */
    public function cancelarVendaAction(Request $request)
    {
        $cupomId = $request->getSession()->get('cupom_id');
        
        $em = $this->getDoctrine()->getManager();
        $cupom = $em->getRepository('LivrariaBundle:Cumpom')->find($cupomId);
        
        $cupom->setStatus('CANCELADO');
        $em->persist($cupom);  //Pega o obj e joga na camada de persistência da memória
        $em->flush();           //O flush joga no BD e descarta
        
        return $this->json('ok');
        
        
    }
    
    /**
     *@Route("/caixa/Cancelar", name="finalizarVenda")
     */
    public function finalizarVendaAction(Request $request)
    {
        $cupomId = $request->getSession()->get('cupom_id');
        
        $em = $this->getDoctrine()->getManager();
        $cupom = $em->getRepository('LivrariaBundle:Cumpom')->find($cupomId);
        
        $valotTotal = 0;
        
        
        $items = $em->getRepository("LivrariaBundle:CumpomItem")
                ->findBy(array(
                    "cupomId" => $request->getSession()->get('cupom-id')
                )); 
        forEach($items as $item)
        {
            $valorTotal = +$item->getValorUnitario();
            $produto = $em->getRepository('LivrariaBundle:Produtos')->find($item->getItemCod());
            
            $produto->setQuantidade($produto->getQuantidade() -1);
            $em->persist($produto);  
        }
        
        
        
        
        $cupom->setValorTotal($valorTotal);
        
        $cupom->setStatus('FINALIZADO');
        $em->persist($cupom);  //Pega o obj e joga na camada de persistência da memória
        $em->flush();           //O flush joga no BD e descarta
        
        $request->getSession()->set('cupom-id', null);
        
        //Baixar os items no estoque
        //Fechar o total da compra
        
        //return $this->json('ok');
    }
    
    /**
     * @Route("/caixa/listar", name="listagem_items")
     */
    public function listarItensAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $items = $em->getRepository("LivrariaBundle:CumpomItem")
                ->findBy(array(
                    "cupomId" => $request->getSession()->get('cupom-id')
                ));        
        
        return $this->json($items);
        
        
    }
}


