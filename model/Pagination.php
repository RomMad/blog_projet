<?php
namespace model;

class Pagination {

    protected   $_typeItem,
                $_nbItems,
                $_linkNbDisplayed,
                $_link,
                $_anchor,
                $_nbPages,
                $_currentPage,
                $_firstPage,
                $_lastpage,
                $idSelectNumber;

    public  $_nbLimit,
            $_nbDisplayed;
            
    public function __construct($typeItem, $nbItems, $linkNbDisplayed, $link, $anchor) {
        $this->_typeItem = $typeItem;
        $this->_nbItems = $nbItems;
        $this->_linkNbDisplayed = $linkNbDisplayed;
        $this->_link = $link;
        $this->_anchor = $anchor;
        $this->_idSelectNumber = 0;
        $this->init();
    }

    // Adaptation de la pagination en fonction du nombre de pages et du positionnement
    public function init() {
        // Vérification si informations dans variable POST
        if (!empty($_POST["nbDisplayed"])) {
            $this->_nbDisplayed = $_POST["nbDisplayed"];
            setcookie("pagination[nbDisplayed_" . $this->_typeItem . "]", $this->_nbDisplayed, time() + 365*24*3600, NULL, NULL, FALSE, FALSE);
        } elseif (!empty($_COOKIE["pagination"]["nbDisplayed_" . $this->_typeItem])) {
            $this->_nbDisplayed = $_COOKIE["pagination"]["nbDisplayed_" . $this->_typeItem];
        } else {
            $this->_nbDisplayed = 20;
        }

        // Vérification si informations dans variable GET
        if (!empty($_GET["page"]) && $_GET["page"] >=1) {
            $this->_currentPage = $_GET["page"];
        } else {
            $this->_currentPage = 1;
        }
        // Calcul le nombre de pages par rapport aux nombre d'articles
        $this->_nbLimit = ($this->_currentPage - 1) * $this->_nbDisplayed;
        $this->_nbPages = ceil($this->_nbItems / $this->_nbDisplayed);

        // Détermine la première page à afficher en fonction de la position de la page actuelle
        if ($this->_currentPage == $this->_nbPages && $this->_currentPage > 2) {
            $this->_firstPage = $this->_currentPage - 2;
        } elseif ($this->_currentPage > 1) {
            $this->_firstPage = $this->_currentPage - 1;
        } else {
            $this->_firstPage = 1;
        }
        // Détermine la dernière page à afficher en fonction de la position de la page actuelle
        if ($this->_currentPage == 1) {
            $this->_lastPage = $this->_currentPage + 3;
        } else {
            $this->_lastPage = $this->_currentPage + 2;
        }
    }
    
    public function view() {
        $this->_idSelectNumber ++ ;
        require "view/frontend/paginationView.php";
    }
}