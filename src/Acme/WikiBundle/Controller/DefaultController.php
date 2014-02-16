<?php

namespace Acme\WikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Acme\WikiBundle\Model\Page;
use Acme\WikiBundle\Model\PageQuery;
use Acme\WikiBundle\Form\Type\PageType;

class DefaultController extends Controller
{
    public function addAction($parentPage)
    {
        $parentPageObject = $this->_getPage($parentPage);
        if (is_null($parentPageObject)) 
        {
            return $this->_make404($parentPage); //selected parent page does not exist            
        }   
    	$pageObject = new Page;
        $request = $this->getRequest();
        $pName = $request->query->get('name');
        $pageObject->setPagename($pName);
        $pageObject->setParentId($parentPageObject->getId());         
    	$form = $this->createForm(new PageType, $pageObject);

    	if('POST' === $request->getMethod())
    	{

    		$form->handleRequest($request);
            if($form->get('pageName')->getData()=='')
            {
                // fill pageName using title field
                $pageObject->setPagename($this->_generatePageName($form->get('title')->getData()));
            }
    		if ($form->isValid()) 
            {
                $pageObject->setParentpath($parentPage);
                
                if(!is_null($this->_getPage($pageObject->getPath())))
                {
                    $form->get('pageName')->addError(new FormError('Page '.$pageObject->getPath().' is already exist'));
                    return $this->render('AcmeWikiBundle:Default:form.html.twig', array('form' => $form->createView(), 'action' => 'add'));
                }
                $pageObject->save();
                //show the added page
                return $this->redirect($this->generateUrl('acme_wiki_page', array('page' => $pageObject->getPath()))); 
        	}
    	}
        return $this->render('AcmeWikiBundle:Default:form.html.twig', array(
        	'form' => $form->createView(),
            'action' => 'add'
        	)
        );
    }

    public function indexAction($page)
    {
    	$pageObject = $this->_getPage($page);
        if(is_null($pageObject))
        {
            return $this->_make404($page);
        }
        $text = htmlspecialchars($pageObject->getText());
        //make **text** bold
        $text = preg_replace('/\*\*(.*?)\*\*/m', "<b>$1</b>", $text);
        //make //text// italic
        $text = preg_replace('/\/\/(.*?)\/\//m', "<i>$1</i>", $text);

        $text = preg_replace('/&quot;(.*?)&quot;/s', "&laquo;$1&raquo;", $text);
        //make __text__ underline
        $text = preg_replace('/__(.*?)__/m', "<u>$1</u>", $text);
        //link to the page   
        while(preg_match('/\[\[([a-z0-9_]+(\/[a-z0-9_]+)*)\s+(.*?)\]\]/', $text, $matches) === 1)
        {
            $str = $matches[0];
            $path = $matches[1];
            $linkText = $matches[3];
            if(is_null($this->_getPage($path)))
            {
                $pathArr = $this->_splitPagePath($path);
                $link = $this->renderView('AcmeWikiBundle:Default:linkAddPage.html.twig', 
                    array(
                        'parentPath'=>$pathArr['parentPath'],
                        'pageName'=>$pathArr['pageName'],
                        'linkText'=>$linkText,
                    )
                );
                $text = str_replace($str
                    , $link
                    , $text);                
            }
            else
            {
                $link = $this->renderView('AcmeWikiBundle:Default:linkPage.html.twig', 
                     array(
                        'path'=>"/".$path,
                        'linkText'=>$linkText,
                    )
                );
                $text = str_replace($str
                    , $link
                    , $text);
            }  
        }
        //external link        
        while(preg_match('/\[\[((http|https|ftp)\:\/\/(.*?))(?:\s+(.*?))?\]\]/', $text, $matches) === 1)
        {
            $str = $matches[0];
            $path = $matches[1];
            if(count($matches)==5)
            {
                $linkText = $matches[4]; //make link with text = $matches[4]
            }
            else
            {
                $linkText = $matches[1]; //make link with text = link addres;
            }
            $text = str_replace($str
                        , "<a href = \"".$path."\" target=\"_blank\">".$linkText."</a>"
                        , $text);
        }
        $pageObject->setText($text);
        $childrenPages = PageQuery::create()
            ->filterByParent($pageObject)
            ->find();
        $parentPage = PageQuery::create()
            ->filterByChildren_ref($pageObject)
            ->findOne();

        return $this->render('AcmeWikiBundle:Default:index.html.twig', 
            array(
                'page'=>$pageObject, 
                'childrenPages' => $childrenPages,
                'parentPage' => $parentPage
             ));
    }

    public function editAction($page)
    {
        $pageObject = $this->_getPage($page);

        if (is_null($pageObject)) 
        {
            return $this->_make404($page); //editing page does not exist            
        }

        $request = $this->getRequest();          
        $form = $this->createForm(new PageType, $pageObject);

        if('POST' === $request->getMethod())
        {
            $form->handleRequest($request);
            //if Welkome page
            if(is_null($pageObject->getPagename()))
            {
                $pageObject->setPagename('');
            }

            if ($form->isValid()) 
            {
                $pageObject->save();
                //show the added page
                return $this->redirect($this->generateUrl('acme_wiki_page', array('page' => $pageObject->getPath()))); 
            }
        }
        return $this->render('AcmeWikiBundle:Default:form.html.twig', array(
            'form' => $form->createView(),
            'action' => 'edit'
            )
        );
    }

    public function deleteAction($page)
    {
        $pageObject = $this->_getPage($page);

        if (is_null($pageObject)) 
        {
            return $this->_make404($page); //deleting page does not exist            
        }
        $request = $this->getRequest();
        if('POST' === $request->getMethod())
        {
            //$parentPath = $pageObject->getParentpath();
            $pageObject->delete();
            return $this->redirect($this->generateUrl('acme_wiki_page', array('page' => $pageObject->getParentpath())));     
        }

        return $this->render('AcmeWikiBundle:Default:delete.html.twig', array(
            'page' => $pageObject
            )
        );
    }
    /**
    * Find Page object by path
    */
    private function _getPage($pagePath) 
    {
        if(is_null($pagePath))  //should not happen
        {
            return null;
        } 
        $arr = $this->_splitPagePath($pagePath);
        $Page = PageQuery::create()
            ->filterByParentpath($arr['parentPath'])
            ->filterByPagename($arr['pageName'])
            ->findOne();
        return $Page;
    }

    private function _make404($page) 
    {       
        $response = new Response();
        $arr = $this->_splitPagePath($page);
        $response->setContent($this->renderView('AcmeWikiBundle:Default:notFound.html.twig', $arr));
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }

    /**
     * Split the page path to the parent page path and the page name.
     * @param   string $page path to the page. e.g. "/page1/page2"
     * @return  array ('parentPath' => value, 'pageName' => value)
     */
    private function _splitPagePath($page) 
    {
        if(!empty($page) && strpos($page, '/') !== 0)
        {
            $page = '/'.$page;
        }
        $arr = explode("/", $page);
        if(count($arr)>1)
        {
            $pageName = array_pop($arr);
            $parentPath = implode("/", $arr);
        }
        else 
        {
            $pageName = $page;
            $parentPath = null;
        }
        return array('parentPath' => $parentPath, 'pageName' => $pageName);
    }
    
    /**
    * Generate page name by page title
    * @param  String $title string by which page name should be generated
    * @return  String with only latin characters and numbers
    */
    private function _generatePageName($title)
    {
        $name = mb_strtolower($title, 'utf-8');
        // replace cyrillic chars
        $name = $this->_replaceCyrillic($title);
        // remove all invalid symbols
        $name = trim($name);
        $name = preg_replace('/\ +/', '_', $name);
        $name = preg_replace('/[^a-z0-9_]+/', '', $name);

        return $name;
    }
 
    private function _replaceCyrillic ($text)
    {
        $chars = array(
          'ґ'=>'g','є'=>'e','ї'=>'i','і'=>'i',
          'а'=>'a', 'б'=>'b', 'в'=>'v',
          'г'=>'g', 'д'=>'d', 'е'=>'e', 'ё'=>'yo',
          'ж'=>'zh', 'з'=>'z', 'и'=>'i', 'й'=>'i',
          'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n',
          'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s',
          'т'=>'t', 'у'=>'u', 'ф'=>'f', 'х'=>'h',
          'ц'=>'c', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'sch',
          'ы'=>'y', 'э'=>'e', 'ю'=>'yu', 'я'=>'ya',
          'ь'=>'', 'ъ' => '',
        );
        return strtr($text, $chars);
    }










}
