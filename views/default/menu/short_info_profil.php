<?php 
  $cssAnsScriptFilesModule = array(
    '/css/default/short_info_profil.css',
  );
  HtmlHelper::registerCssAndScriptsFiles($cssAnsScriptFilesModule, $this->module->assetsUrl);
?>

<style type="text/css">
  .searchIcon{
    cursor: pointer;
  }
</style>
<div class="menu-info-profil <?php echo isset($type) ? $type : ''; ?> " data-tpl="short_info_profil">
    
    <?php
    //<div class="label label-inverse">new <span class="badge animated bounceIn bg-red">1</span></div>
    
    //if(isset(Yii::app()->session['userId'])) 
    $this->renderPartial('../default/menu/multi_tag_scope', array("me"=>$me)); ?>
    
    
    <div class="input-group pull-right">
      <span class="input-group-addon"><i class="fa fa-search searchIcon tooltips" data-toggle="tooltip" data-placement="bottom" title="Recherche Globale"></i></span>
      <input type="text" class="text-dark input-global-search hidden-xs" placeholder="<?php echo Yii::t("common","Search") ?> ..."/>
    </div>
    <div class="dropdown-result-global-search"></div>
    
    <div class="topMenuButtons pull-right">
    <?php 
    if( isset( Yii::app()->session['userId']) )
      echo $this->renderPartial('./menu/menuProfil',array( "me"=> $me)); 
    else { ?>
      <button class="btn-top btn btn-success  hidden-xs" onclick="showPanel('box-register');"><i class="fa fa-plus-circle"></i> <span class="hidden-sm hidden-md hidden-xs">S'inscrire</span></button>
      <button class="btn-top btn bg-red  hidden-xs" style="margin-right:10px;" onclick="showPanel('box-login');"><i class="fa fa-sign-in"></i> <span class="hidden-sm hidden-md hidden-xs">Se connecter</span></button> 
    <?php } ?>
    </div>
  </div>

<script>

  /* global search code is in assets/js/default/globalsearch.js */

  var timeoutGS = setTimeout(function(){ }, 100);
  var timeoutDropdownGS = setTimeout(function(){ }, 100);
  var searchPage = false;
  jQuery(document).ready(function() {

    $('.dropdown-toggle').dropdown();
    $(".menu-name-profil").click(function(){
      showNotif(false);
    });

    $('.input-global-search').keyup(function(e){
        clearTimeout(timeoutGS);
        if($('*[data-searchPage]').length>0){
          $('#searchBarText').val( $('.input-global-search').val() );
          timeoutGS = setTimeout(function(){startSearch(false); }, 800);
        }
        else
          timeoutGS = setTimeout(function(){ startGlobalSearch(0, indexStepGS); }, 800);
    });

    $('.searchIcon').click(function(e){
       if($('.searchIcon').hasClass('fa-search')){
          searchPage = true;
          $(".searchIcon").removeClass("fa-search").addClass("fa-file-text-o");
          $(".searchIcon").attr("title","Recherche ciblé (ne concerne que cette page)");
       }else{
          $(".searchIcon").removeClass("fa-file-text-o").addClass("fa-search");
          $(".searchIcon").attr("title","Recherche Globale");
       }

    });

    $('.input-global-search').click(function(e){
        if($(".dropdown-result-global-search").html() != ""){
          showDropDownGS(true);
        }
    });

    $('.dropdown-result-global-search').mouseenter(function(e){
        clearTimeout(timeoutDropdownGS);
    });
    $('.main-col-search, .mapCanvas, .main-menu-top').mouseenter(function(e){
        clearTimeout(timeoutDropdownGS);
        timeoutDropdownGS = setTimeout(function(){ 
            showDropDownGS(false);
        }, 300);
    });

    $('.moduleLabel').click(function(e){
        clearTimeout(timeoutDropdownGS);
        timeoutDropdownGS = setTimeout(function(){ 
            showDropDownGS(false);
        }, 300);
    });

    showDropDownGS(false);
  });




  </script>

