<?php

namespace Recruitment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Database\Service\EntityManagerService;

/**
 * Description of AddressController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AddressController extends AbstractActionController
{

    use EntityManagerService;

    public function searchByZipcodeAction()
    {

        $request = $this->getRequest();

        try {

            if ($request->isPost()) {

                $data = $request->getPost();

                $em = $this->getEntityManager();

                $address = $em->getRepository('Recruitment\Entity\Address')->findOneBy(array(
                    'addressPostalCode' => $data['zipcode'],
                ));

                $hydrator = new DoctrineHydrator($em);

                $whitelist = array('addressState', 'addressCity', 'addressNeighborhood', 'addressStreet');
                $result = $address !== null ? array_intersect_key(
                        $hydrator->extract($address), array_flip($whitelist)
                    ) : null;


                return new JsonModel(array(
                    'response' => true,
                    'data' => $result,
                ));
            }

            return new JsonModel(array(
                'response' => false,
                'msg' => 'Requisição sem dados',
            ));
        } catch (\Exception $ex) {

            return new JsonModel([
                'response' => false,
                'msg' => $ex->getMessage(),
            ]);
        }
    }

}
