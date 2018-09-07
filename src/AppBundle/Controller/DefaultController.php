<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/inventories", name="inventories")
     */
    public function inventoriesAction(Request $request)
    {
        return $this->render('inventories.html.twig', [
            'items' => $this->getItems()
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $items = $this->getItems();
            $items[] = $request->request->all();

            $this->saveItems($items);

            return $this->redirectToRoute("inventories");
        }
        else {
            $dataDir = $this->getParameter('data');
            $jsonFile = sprintf("%s/formvals.json", $dataDir);
            $data     = json_decode(file_get_contents($jsonFile));

            return $this->render('create.html.twig',[
                'locations' => $data->location,
                'statuses'   => $data->status
            ]);
        }
    }

    /**
     * @Route("/delete/item/{index}", name="delete")
     */
    public function deleteAction($index, Request $request)
    {
        $items = $this->getItems();

        unset($items[$index]);

        $this->saveItems(array_values($items));
        
        return $this->redirectToRoute("inventories");
    }

    /**
     * @Route("/edit/item/{index}", name="edit")
     */
    public function editAction($index, Request $request)
    {
        if ($request->isMethod('POST')) {
            $items = $this->getItems();

            $items[$index] = $request->request->all();

            $this->saveItems($items);
        
            return $this->redirectToRoute("inventories");
        }
        else {
            $items = $this->getItems();
            $dataDir = $this->getParameter('data');
            $jsonFile = sprintf("%s/formvals.json", $dataDir);
            $data     = json_decode(file_get_contents($jsonFile));

            return $this->render('edit.html.twig',[
                    'locations' => $data->location,
                    'statuses'  => $data->status,
                    'item'      => $items[$index]
            ]);
        }
    }

    public function getItems()
    {
        $dataDir = $this->getParameter('data');
        $jsonFile = sprintf("%s/items.json", $dataDir);

        $data = [];
        if (file_exists($jsonFile)) {
            $data = json_decode(file_get_contents($jsonFile));
        }

        return $data;
    }

    public function saveItems($data)
    {
        $dataDir = $this->getParameter('data');
        $jsonFile = sprintf("%s/items.json", $dataDir);
        
        file_put_contents($jsonFile, json_encode($data));
    }
}
