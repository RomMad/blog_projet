<?php 
    // Adaptation de la pagination en fonction du nombre de pages et du positionnement
    $pageLink_1 = $page-1;
    $pageLink_2 = $page;
    $pageLink_3 = $page+1;
    $activepageLink_1 = "";
    $activepageLink_2 = "active";
    $activepageLink_3 = "";

    if ($page<$nbPages) {
        $nextPage = $page+1;
        $nextPageLink = "";
        $nextPageColorLink = "text-info";
    } else {
        $nextPage = $page;
        $nextPageLink = "disabled";
        $nextPageColorLink = "";
        $pageLink_1 = $page-2;
        $pageLink_2 = $page-1;
        $pageLink_3 = $page;
        $activepageLink_1 = "";
        $activepageLink_2 = "";
        $activepageLink_3 = "active disabled";
    };
    if ($page==1) {
     $prevPage = 1;
     $prevPageLink = "disabled";
     $prevPageColorLink = "";
     $pageLink_1 = $page;
     $pageLink_2 = $page+1;
     $pageLink_3 = $page+2;    
     $activepageLink_1 = "active disabled";
     $activepageLink_2 = "";
     $activepageLink_3 = ""; 
     };
    if ($page>1) {
        $pageLink_1 = $page-1;
        $pageLink_2 = $page;
        $pageLink_3 = $page+1;
        $prevPage = $page-1;
        $prevPageLink = "";
        $prevPageColorLink = "text-info";
    };
  
    if ($nbPages==2 && $page==2) {
         $nextPage = $page;
         $nextPageLink = "disabled";
         $nextPageColorLink = "";
         $pageLink_1 = $page-1;
         $pageLink_2 = $page;
         $activepageLink_1 = "";
         $activepageLink_2 = "active disabled";
    };
    if ($page==$nbPages && $page!=2) {
         $nextPage = $page;
         $nextPageLink = "disabled";
         $nextPageColorLink = "";
         $pageLink_1 = $page-2;
         $pageLink_2 = $page-1;
         $pageLink_3 = $page;
         $activepageLink_1 = "";
         $activepageLink_2 = "";
         $activepageLink_3 = "active disabled";
    }; 

?>