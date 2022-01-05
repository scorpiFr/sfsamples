<?php
// src/Controller/BookController.php
namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\BookFormEntity;
use App\Form\CreateBookType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use App\Entity\Book;

class BookController extends AbstractController
{
    /**
     * create form
     *
     * @Route("/book/createform", name="bookcreateform")
     */
    public function myCreateForm(Request $request)
    {
        /*
        $this->addFlash('bookcreateform', [
            'data' => new BookFormEntity(),
            'errors' => [
                'authorId' => "Veuillez choisir un auteur."
            ]
        ]);
*/

        // parameters
        $entityClass = 'BookFormEntity';
        $formClass = 'CreateBookType';
        $flashbagName = 'bookcreateform';
        $templatePath = 'twig/book/createform.html.twig';

        // initialise
        $data = new BookFormEntity();
        $errors = [];

        // flashbag verification
        $flashBag = $request->getSession()->get('bookcreateform');
        if (!empty($flashBag)) {
            if (isset($flashBag['data']) && !empty($flashBag['data'])) {
                $data = $flashBag['data'];
            }
            if (isset($flashBag['errors']) && !empty($flashBag['errors'])) {
                $errors = $flashBag['errors'];
            }
        }

        // create form
        $form = $this->createForm(CreateBookType::class, $data);

        // template
        $twigData = ['form' => $form->createView(), 'errors'=>$errors];
        $html = $this->get('twig')->render('twig/book/createform.html.twig', $twigData);
        return new Response($html);
    }
    /**
     * create action
     *
     * @Route("/book/createaction", name="bookcreateaction", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        // initialize
        {
            $em = $this->getDoctrine()->getManager();

            $book = new BookFormEntity();
            $form = $this->createForm(CreateBookType::class, $book);
            $form->handleRequest($request);
        }

        // not submitted
        if (!$form->isSubmitted())
        {
            return $this->redirectToRoute('bookcreateform', [], Response::HTTP_FOUND); // 302
        }


        /*
        $validator = Validation::createValidator();
        $violations = $validator->validate($book, [
            new Length(['min' => 1]),
            new NotBlank(),
        ]);
        if (0 !== count($violations)) {
            dump($book);

            // Affiche les erreurs
            / ** @var ConstraintViolation $violation ** /
            foreach ($violations as $violation) {
                dump($violation);
                echo $violation->getCode() .' - '.$violation->getMessage().'<br>';
                die;
            }
            die;
        }
*/

        // not correct
        if (!$form->isValid()) {
            $request->getSession()->set('bookcreateform', [
                'data' => $book,
                'errors' => [
                    'authorId' => "Veuillez choisir un auteur."
                ]
            ]);
        }

        // processing
        {
            // create book entity and populate
            $dbBook = new Book();
            $fields = ['title', 'abstract', 'authorId'];
            foreach ($fields as $field) {
                $fct1 = 'set' . ucfirst($field);
                $fct2 = 'get' . ucfirst($field);
                $dbBook->$fct1($book->$fct2());
            }
            $em->persist($dbBook);
            $em->flush();
        }

        // redirect
        unset($em, $book, $form, $dbBook, $fields, $fct1, $fct2);
        return $this->redirectToRoute('booklist');
    }


    /**
     * list action
     *
     * @Route("/book/list", name="booklist")
     */
    public function listAction(BookRepository $bookRepository)
    {
        // init
        // session control

        // get books
        $books = $bookRepository->getAll();
        
        // template
        $twigData = ['books' => $books];
        $html = $this->get('twig')->render('twig/book/list.html.twig', $twigData);
        unset($books, $twigData);
        return new Response($html);
    }
}