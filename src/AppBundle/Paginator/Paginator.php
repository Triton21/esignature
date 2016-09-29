<?php

namespace AppBundle\Paginator;

/**
 * Description of Paginator: Paginator class creates all the properties reqiured for twig display
 *
 * @author Peter
 */
class Paginator {
    
    private $page;
    
    private $limit;
    
    private $pageup;
    
    private $pagedown;
    
    private $pages;
    
    private $countAll;
    
    private $pagesarray;
    
    private $offset;
    
    
    
    function __construct($page, $countAll, $limit) {
        $this->setPage($page);
        $this->setPages($countAll, $limit);
        $this->setPagedown();
        $this->setPageup();
        $this->setCountAll($countAll);
        $this->setLimit($limit);
        $this->setPages($countAll, $limit);
        $this->setPagesarray();
        $this->setOffset($limit, $page);
    }
    
    public function getOffset()
    {
        return $this->offset;
    }
    
    public function setOffset($limit, $page)
    {
        $this->offset = ($limit * $page) - $limit;
    
        return $this;
    }
    
    public function getPagedown()
    {
        return $this->pagedown;
    }
    
    public function setPagedown()
    {
        $page = $this->page;
        if ($page == '1') {
            $pagedown = false;
        } else {
            $pagedown = $page - 1;
        }
        $this->pagedown = $pagedown;
    
        return $this;
    }
    
    public function getPageup()
    {
        return $this->pageup;
    }
    
    public function setPageup()
    {
        $page = $this->page;
        $pages = $this->pages;
        if ($pages == $page) {
            $pageup = false;
        } else {
            $pageup = $page + 1;
        }
        $this->pageup = $pageup;
    
        return $this;
    }
    
    public function getPage()
    {
        return $this->page;
    }
    
    public function setPage($page)
    {
        $this->page = $page;
    
        return $this;
    }
    
    public function getCountAll()
    {
        return $this->countAll;
    }
    
    public function setCountAll($countAll)
    {
        $this->countAll = $countAll;
    
        return $this;
    }
    
    public function getPages()
    {
        return $this->pages;
    }
    
    public function setPages($countAll, $limit)
    {
        $this->pages = ceil($countAll / $limit);
    
        return $this;
    }
    
    public function getLimit()
    {
        return $this->limit;
    }
    
    public function setLimit($limit)
    {
        $this->limit = $limit;
    
        return $this;
    }
    
    public function getPagesarray()
    {
        return $this->pagesarray;
    }
    
    public function setPagesarray()
    {   
        $pagesarray = [];
        $pages = $this->pages;
        $page = $this->page;
        for ($i = 1; $i <= $pages; $i++) {
            $pagesarray[] = $i;
        }
        if ($page <= '5') {
            $pagesarray = array_slice($pagesarray, 0, 10);
        }
        if ($page > '5') {
            $pagesarray = array_slice($pagesarray, $page - 5, 10);
        }
        
        $this->pagesarray = $pagesarray;
    
        return $this;
    }
    
    
    
}
