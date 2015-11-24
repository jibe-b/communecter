<?php 
$this->renderPartial('newsSV');

$cssAnsScriptFilesModule = array(
	'/plugins/ScrollToFixed/jquery-scrolltofixed-min.js',
	'/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
	'/plugins/jquery.appear/jquery.appear.js'
);

HtmlHelper::registerCssAndScriptsFiles( $cssAnsScriptFilesModule ,Yii::app()->theme->baseUrl."/assets");
?>	
	<!-- start: PAGE CONTENT -->
<?php 
if( isset($_GET["isNotSV"])) {
	/*
	$this->renderPartial('../default/panels/toolbar',array("toolbarStyle"=>"width:50px")); */
	$contextName = "";
	$contextIcon = "bookmark fa-rotate-270";
	$contextTitle = "";
	if(@$_GET["type"]) $type=$_GET["type"];
	if( isset($type) && $type == Organization::COLLECTION && isset($organization) ){
		Menu::organization( $organization );
		$thisOrga = Organization::getById($organization["_id"]);
		$contextName = Yii::t("common","Organization")." : ".$thisOrga["name"];
		$contextIcon = "users";
		$contextTitle = Yii::t("common","Participants");
	}
	else if( isset($type) && $type == City::COLLECTION && isset($city) ){
		Menu::city( $city );
		$contextName = Yii::t("common","City")." : ".$city["name"];
		$contextIcon = "university";
		$contextTitle = Yii::t("common", "DIRECTORY Local network of")." ".$city["name"];
	}
	else if( isset($type) && $type == Person::COLLECTION && isset($person) ){
		Menu::person( $person );
		$contextName = Yii::t("common","Person")." : ".$person["name"];
		$contextIcon = "user";
		$contextTitle =  Yii::t("common", "DIRECTORY of")." ".$person["name"];
	}
	else if( isset($type) && $type == PROJECT::COLLECTION && isset($project) ){
		Menu::project( $project );
		$contextName = Yii::t("common","Project")." : ".$project["name"];
		$contextIcon = "lightbulb-o";
		$contextTitle = Yii::t("common", "Contributors of project");//." ".$project["name"];
	}
	/*else
		$this->toolbarMBZ = array(
		    array( 'tooltip' => "Add a Person, Organization, Event or Project", "iconClass"=>"fa fa-plus" , "iconSize"=>"" ,"href"=>"<a class='tooltips btn btn-default' href='#' onclick='showPanel(\"box-add\",null,\"ADD SOMETHING TO MY NETWORK\")' ")
		);*/
	Menu::news();
	$this->renderPartial('../default/panels/toolbar'); 
}
?>
<style>
#btnCitoyens:hover{
	color:#F3D116;
	border-left: 5px solid #F3D116;
}
#btnOrganization:hover{
	color:#93C020;
	border-left: 5px solid #93C020;
}
#btnEvent:hover{
	color:#F9B21A;
	border-left: 5px solid #F9B21A;
}
#btnProject:hover{
	color:#8C5AA1;
	border-left: 5px solid #8C5AA1;
}
.timeline-scrubber{
	min-width: 115px;
	top:125px;
	.optionFilter{
		margin-bottom:20px;
	}
}
.timeline{
	float: left;
	min-width: 100%;
}
.date_separator span{
	font-family: "homestead";
	font-size: 21px;
	background-color: white !important;
	color: #315C6E !important;
	border: none !important;
	border-radius: 0px !important;
	border-bottom: 1px dashed #315C6E !important;

}

#newsHistory{
	overflow: scroll;
	overflow-x: hidden;
	position:fixed;
	top:100px;
	/*padding-top:100px !important;*/
	bottom:0px;
	right:0px;
	left:70px;
}

#tagFilters a.filter, #scopeFilters a.filter{
	background-color: rgba(245, 245, 245, 0.7);
	font-size: 14px;
	padding: 5px;
	float: left;
	margin-right: 5px;
	margin-bottom: 5px;
	border-radius: 0px;
	-moz-box-shadow: -1px 1px 3px -2px #656565;
	-webkit-box-shadow: -1px 1px 3px -2px #656565;
	-o-box-shadow: -1px 1px 3px -2px #656565;
	box-shadow: -1px 1px 3px -2px #656565;
	filter:progid:DXImageTransform.Microsoft.Shadow(color=#656565, Direction=224, Strength=3);
}
.filterNewsActivity{
	margin-bottom: 10px;
}
.filterNewsActivity .btn-green{
	background-color: #3A758D;
	border-color: #315C6E;
}
.filterNewsActivity .btn-green:hover{
	background-color: #315C6E;
}

.filterNewsActivity .btn-dark-green{
	background-color: #315C6E;
	border-color: #315C6E;
}
.timeline-scrubber{
	right: 65px;
    position: fixed;
    top: 215px;
}

.btn-add-something{
	border-radius: 0px;
	padding: 7px 0px;
	font-size: 12px;
}

.optionFilter{
	margin-bottom:20px;
}

#formActivity .bg-yellow, #formActivity .bg-green, 
#formActivity .bg-orange, #formActivity .bg-purple{
	border-bottom: 3px solid transparent;
}

#formActivity .bg-yellow:hover, #formActivity .bg-green:hover, 
#formActivity .bg-orange:hover, #formActivity .bg-purple:hover{
border-bottom: 3px solid rgba(255, 255, 255, 0.8);
}
div.timeline .spine{
	border-radius:0px;
	z-index: 0;

}
div.timeline .date_separator span{
	z-index: 3;
}
</style>

<div id="formCreateNewsTemp" style="float: none;" class="center-block">
	<div class='no-padding form-create-news-container'>
		<h5 class='padding-10 partition-light no-margin text-left header-form-create-news'><i class='fa fa-pencil'></i> <?php echo Yii::t("news","Share a thought, an idea",null,Yii::app()->controller->module->id) ?> </h5>
		<form id='ajaxForm'></form>
	 </div>
</div>
<div id="newsHistory" class="padding-20">
	<div class="col-md-12">

		<!-- start: TIMELINE PANEL -->
		<div class="panel panel-white" style="padding-top:10px;">
			<div class="panel-heading border-light  <?php if( isset($_GET["isNotSV"])) echo "hidden"; ?>">
				<h4 class="panel-title">News</h4>
				<ul class="panel-heading-tabs border-light">
		        	<?php if( !isset($_GET["isNotSV"])) { ?>
		        	<li>
		        		<a class="new-news btn btn-info" href="#new-News" data-notsubview="1"><?php echo Yii::t("common","Add");?> <i class="fa fa-plus"></i></a>
		        	</li>
		        	<?php } 
		        	/* ?>
		        	<!-- <li>
		        		<a class="new-news btn btn-info" href="#new-News" data-notsubview="1">Add <i class="fa fa-plus"></i></a>
		        	</li> -->
		        	<?php /* ?>
			        <li class="panel-tools">
			          <div class="dropdown">
			            <a data-toggle="dropdown" class="btn btn-xs dropdown-toggle btn-transparent-grey">
			              <i class="fa fa-cog"></i>
			            </a>
			            <ul class="dropdown-menu dropdown-light pull-right" role="menu">
			              <li>
			                <a class="panel-collapse collapses" href="#"><i class="fa fa-angle-up"></i> <span>Collapse</span> </a>
			              </li>
			              <li>
			                <a class="panel-expand" href="#">
			                  <i class="fa fa-expand"></i> <span>Fullscreen</span>
			                </a>
			              </li>
			              </ul>
		          	  </div>
			        </li>
			        <?php */?>
		        </ul>
			</div>
			<div id="top" class="panel-body panel-white">
				<div id="tagFilters" class="optionFilter pull-left center col-md-10" style="display:none;" ></div>
				<div id="scopeFilters" class="optionFilter pull-left center col-md-10" style="display:none;" ></div>
	
				<div id="timeline" class="col-md-10">
					<div class="timeline">
						<div class="center filterNewsActivity">
							<div class="btn-group">
								<a id="btnNews" href="javascript:;"  class="filter btn btn-dark-green" data-filter=".news" style="width:140px;">
									<i class="fa fa-rss"></i> <?php echo Yii::t("news","News") ?>
								</a>
								<a id="btnActivity" href="javascript:;" class="filter btn btn-green" data-filter=".activityStream" style="width:140px;">
									<i class="fa fa-exchange"></i> <?php echo Yii::t("news","Activity") ?>
								</a>
							</div>
						</div>
						<div class="newsTLnews">
						</div>
						<div class="newsTLactivity">
						</div>
					</div>
				</div>
				<ul class="timeline-scrubber inner-element newsTLmonthsListnews col-md-2"></ul>
				<ul class="timeline-scrubber inner-element newsTLmonthsListactivity col-md-2"></ul>
				

			</div>
			<div class="title-processing homestead stream-processing center fa-2x" style="margin-right:100px;"><i class="fa fa-spinner fa-spin"></i> Processing... </div>
		</div>
		<!-- end: TIMELINE PANEL -->
	</div>
</div>
<style type="text/css">
	div.timeline .columns > li:nth-child(2n+2) {margin-top: 10px;}
	.timeline_element {padding: 10px;}
</style>

<?php 
foreach($news as $key => $oneNews){
	$news[$key]["typeSig"] = "news";	
}
?>

<!-- end: PAGE CONTENT-->
<script type="text/javascript">
/*
	Global Variables to initiate timeline
	- offset => represents measure of last newsFeed (element for each stream) to know when launch loadStrean
	- lastOffset => avoid repetition of scrolling event (unstable behavior)
	- dateLimit => date to know until when get new news
*/
var news = <?php echo json_encode($news)?>;
var newsReferror={
		"news":{
			"offset":"",
			"dateLimit":0,
			"lastOffset":""
			},
		"activity":{
			"offset":"",
			"dateLimit":0,
			"lastOffset":""
			},
	};

var contextParentType = <?php echo json_encode(@$contextParentType) ?>;
var contextParentId = <?php echo json_encode(@$contextParentId) ?>;
var countEntries = 0;
var offset="";
var	dateLimit = 0;	
var lastOffset="";
var streamType="news";
var months = ["<?php echo Yii::t('common','january') ?>", "<?php echo Yii::t('common','febuary') ?>", "<?php echo Yii::t('common','march') ?>", "<?php echo Yii::t('common','april') ?>", "<?php echo Yii::t('common','may') ?>", "<?php echo Yii::t('common','june') ?>", "<?php echo Yii::t('common','july') ?>", "<?php echo Yii::t('common','august') ?>", "<?php echo Yii::t('common','september') ?>", "<?php echo Yii::t('common','october') ?>", "<?php echo Yii::t('common','november') ?>", "<?php echo Yii::t('common','december') ?>"];
var contextMap = {
	"tags" : [],
	"scopes" : {
		codeInsee : [],
		codePostal : [], 
		region :[],
		addressLocality : []
	},
};
var formCreateNews;
<?php if( !isset($_GET["isNotSV"]) ) { ?>
	var Sig = null;
<?php } ?>

jQuery(document).ready(function() 
{
	$(".moduleLabel").html("<i class='fa fa-<?php echo $contextIcon ?>'></i> <?php echo $contextName; ?>  <a href='javascript:showMap()' id='btn-center-city'><i class='fa fa-map-marker'></i></a>");
	<?php if( !isset($_GET["isNotSV"]) ) { ?>
		Sig = SigLoader.getSig();
		Sig.loadIcoParams();
	<?php } ?>	
	//Construct the first NewsForm
	buildDynForm();
	
	<?php if( isset($_GET["isNotSV"]) ) { ?>
		Sig.restartMap();
		Sig.showMapElements(Sig.map, news);
	<?php } ?>
	
	// If à enlever quand généralisé à toutes les parentType (Person/Project/Organization/Event)
	//alert(contextParentType);
	if(contextParentType=="citoyens" || contextParentType=="projects" || contextParentType=="organizations"){
		// SetTimeout => Problem of sequence in js script reader
		setTimeout(function(){loadStream()},0);
		if (streamType=="news")
			minusOffset=630;
		else if (streamType=="activity"){
			if(contextParentType=="citoyens")
				minusOffset=530;
			else
				minusOffset=630;
		}
		$("#newsHistory").off().on("scroll",function(){ 
			//console.log((offset.top - minusOffset) + " <= " + $("#newsHistory").scrollTop());
			if(offset.top - minusOffset <= $("#newsHistory").scrollTop()) {
				if (lastOffset != offset.top){
					lastOffset=offset.top;
					loadStream();
				}
			}
		});
 	}
});

var loadStream = function(){
	$.ajax({
        type: "POST",
        url: baseUrl+"/"+moduleId+"/news/index/type/"+contextParentType+"/id/"+contextParentId+"/date/"+dateLimit+"/streamType/"+streamType,
       	dataType: "json",
    	success: function(data){
	    	if(data){
				buildTimeLine (data.news);
				if(typeof(data.limitDate.created) == "object")
					dateLimit=data.limitDate.created.sec;
				else
					dateLimit=data.limitDate.created;
			}
		}
	});
}

var tagsFilterListHTML = "";
var scopesFilterListHTML = "";
function buildTimeLine (news)
{
	if (dateLimit==0){
		$(".newsTL"+streamType).html('<div class="spine"></div>');
		if (streamType=="activity"){
			btnFilterSpecific='<li><a id="btnCitoyens" href="javascript:;"  class="filter yellow" data-filter=".citoyens" style="color:#F3D116;border-left: 5px solid #F3D116"><i class="fa fa-user"></i> Citoyens</a></li>'+
				'<li><a id="btnOrganization" href="javascript:;"  class="filter green" data-filter=".organizations" style="color:#93C020;border-left: 5px solid #93C020"><i class="fa fa-users"></i> Organizations</a></li>'+
				'<a id="btnEvent" href="javascript:;"  class="filter orange" data-filter=".events" style="color:#F9B21A;border-left: 5px solid #F9B21A"><i class="fa fa-calendar"></i> Events</a>'+
				'<a id="btnProject" href="javascript:;"  class="filter purple" data-filter=".projects" style="color:#8C5AA1;border-left: 5px solid #8C5AA1"><i class="fa fa-lightbulb-o"></i> Projects</a><li><br/></li>';
			$(".newsTLmonthsList"+streamType).html(btnFilterSpecific);
		}
	}

	//insertion du formulaire CreateNews dans le stream
	if (streamType=="news")
		var formCreateNews = $("#formCreateNewsTemp");
	else {
		var formCreateNews = "<div id='formActivity' class='center-block'><div class='no-padding form-create-news-container'>"+
			"<h5 class='padding-10 partition-light no-margin text-left'>"+
				"<i class='fa fa-pencil'></i> <?php echo Yii::t("news","Add something to share your activity",null,Yii::app()->controller->module->id) ?>"+
			"</h5>"+
			'<div class="form-group box-add row partition-white no-padding no-margin" style="display: block;border: 1px solid #E6E8E8;margin-left:0px;margin-right:0px;">'+
				'<div class="no-padding col-md-6 col-xs-12">'+
                   '<button type="button" class="btn-add-something btn bg-yellow col-md-12 col-xs-12" onclick="showAjaxPanel( \'/person/invitesv?isNotSV=1\', \'INVITE SOMEONE\',\'share-alt\' )" class="padding-10 text-center tooltips text-white" data-toggle="tooltip" data-placement="top" data-original-title="Invite Someone"><i class="fa fa-plus"></i> <i class="fa fa-user fa-x"></i> <?php echo Yii::t("common","person") ?>'+
				   '</button>'+
				'</div>'+
				'<div class="no-padding col-md-6 col-xs-12">'+
                	'<button type="button" class="btn-add-something btn bg-green col-md-12 col-xs-12" onclick="showAjaxPanel(\'/organization/addorganizationform?isNotSV=1\', \'ADD AN ORGANIZATION\',\'users\')" class="padding-10 text-center tooltips text-white" data-toggle="tooltip" data-placement="top" data-original-title="Add An Organization"><i class="fa fa-plus"></i> <i class="fa fa-users fa-x"></i> <?php echo Yii::t("common","organizations") ?>'+
					'</button>'+
				'</div>'+
				'<div class="no-padding col-md-6 col-xs-12">'+
					'<button type="button" class="btn-add-something btn bg-orange col-md-12 col-xs-12" onclick="showAjaxPanel(\'/event/eventsv?isNotSV=1\', \'ADD AN EVENT\',\'calendar\')" class="padding-10 text-center tooltips text-white" data-toggle="tooltip" data-placement="top" data-original-title="Add An Event"><i class="fa fa-plus"></i> <i class="fa fa-calendar fa-x"></i> <?php echo Yii::t("common","events") ?>'+
					'</button>'+
				'</div>'+
				'<div class="no-padding col-md-6 col-xs-12">'+
					'<button type="button" class="btn-add-something btn bg-purple col-md-12 col-xs-12" onclick="showAjaxPanel(\'/project/projectsv/id/'+contextParentId+'/type/citoyen?isNotSV=1\', \'ADD A PROJECT\',\'lightbulb-o\' )" class="padding-10 text-center tooltips text-white" data-toggle="tooltip" data-placement="top" data-original-title="Add A Project"><i class="fa fa-plus"></i> <i class="fa fa-lightbulb-o fa-x"></i> <?php echo Yii::t("common","projects") ?>'+
					'</button>'+
				'</div>'+
			'</div>'+
		'</div>';
	}
	console.log(formCreateNews);
	currentMonth = null;
	countEntries = 0;
	
	$.each( news , function(key,newsObj)
	{
		if(newsObj.created)
		{
			if(typeof(newsObj.created) == "object")
				var date = new Date( parseInt(newsObj.created.sec)*1000 );
			else
				var date = new Date( parseInt(newsObj.created)*1000 );

			var newsTLLine = buildLineHTML(newsObj);
			if(countEntries == 0 && dateLimit == 0){
				$(".newsTL"+streamType+date.getMonth()).append(
					"<li class='newsFeed'>"+
						"<div id='newFeedForm"+streamType+"' class='timeline_element partition-white no-padding' style='min-width:85%;'>"+
					"</li>");
				$("#newFeedForm"+streamType).append(formCreateNews);
				$("#formCreateNewsTemp").css("display", "inline");
			}
			$(".newsTL"+streamType+date.getMonth()).append(newsTLLine);
			countEntries++;
		}
	});
	offset=$('.newsTL'+streamType+' .newsFeed:last').offset(); 
	if( tagsFilterListHTML != "" )
		$("#tagFilters").html(tagsFilterListHTML);
	if( scopesFilterListHTML != "" )
		$("#scopeFilters").html(scopesFilterListHTML);

	if(!countEntries){
		if( dateLimit == 0){
			var date = new Date(); 
			$(".newsTL"+streamType).html("<div id='newFeedForm"+streamType+"' class='col-md-7 text-extra-large'></div>");
			$("#newFeedForm"+streamType).append(formCreateNews);
			$("#formCreateNewsTemp").css("display", "inline");
			$(".newsTL"+streamType).append("<div class='col-md-5 text-extra-large emptyNews"+streamType+"'><i class='fa fa-rss'></i> Sorry, no news available</br>Be the first to share something here !</div>");

		}
		else {
			if($("#backToTop"+streamType).length <= 0){
				titleHTML = '<div class="date_separator" id="backToTop'+streamType+'" data-appear-top-offset="-400" style="height:150px;">'+
						'<a href="#top" class="smoothScroll">'+
							'<span style="height:inherit;"><i class="fa fa-rss"></i> No more news available<br/>Back to top</span>'+
						'</a>'+
					'</div>';
					$(".newsTL"+streamType).append(titleHTML);
					$(".spine").css('bottom',"0px");
			}
		}
			$(".stream-processing").hide();
	}else{
		
		if(countEntries < 5){
			if($("#backToTop"+streamType).length <= 0){
				titleHTML = '<div class="date_separator" id="backToTop'+streamType+'" data-appear-top-offset="-400" style="height:150px;">'+
						'<a href="#top" class="smoothScroll">'+
							'<span style="height:inherit;"><i class="fa fa-rss"></i> No more news available<br/>Back to top</span>'+
						'</a>'+
					'</div>';
					$(".newsTL"+streamType).append(titleHTML);
					$(".spine").css('bottom',"0px");
			}
			$(".stream-processing").hide();
			//dateLimit="end";
		}
		else{
		//deplacement du formulaire dans le stream
		showFormBlock(false);
		}
	}
	
	bindEvent();
	//Unblock message when click to change type stream
	if (dateLimit==0)
		setTimeout(function(){$.unblockUI()},1);
}

var currentMonth = null;
function buildLineHTML(newsObj)
{
	console.log("buildLineHTML");
	console.dir(newsObj);

	if(typeof(newsObj.created) == "object")
		var date = new Date( parseInt(newsObj.created.sec)*1000 );
	else
		var date = new Date( parseInt(newsObj.created)*1000 );

	var year = date.getFullYear();
	var month = months[date.getMonth()];
	var day = (date.getDate() < 10) ?  "0"+date.getDate() : date.getDate();
	var hour = (date.getHours() < 10) ?  "0"+date.getHours() : date.getHours();
	var min = (date.getMinutes() < 10) ?  "0"+date.getMinutes() : date.getMinutes();
	var dateStr = day + ' ' + month + ' ' + year + ' ' + hour + ':' + min;

	if( currentMonth != date.getMonth() && $('.newsTL'+streamType+date.getMonth()).length == 0)
	{
		currentMonth = date.getMonth();
		linkHTML =  '<li class="">'+
						'<a href="#'+streamType+'month'+date.getMonth()+date.getFullYear()+'" data-separator="#month'+date.getMonth()+date.getFullYear()+'">'+months[date.getMonth()]+' '+date.getFullYear()+'</a>'+
					'</li>';
		$(".newsTLmonthsList"+streamType).append(linkHTML);
		titleHTML = '<div class="date_separator" id="'+streamType+'month'+date.getMonth()+date.getFullYear()+'" data-appear-top-offset="-400">'+
						'<span>'+months[date.getMonth()]+' '+date.getFullYear()+'</span>'+
					'</div>'+
					'<ul class="columns newsTL'+streamType+date.getMonth()+'"></ul>';
		$(".newsTL"+streamType).append(titleHTML);
		$(".spine").css("bottom","0px");
	}
	else{
		$(".spine").css("bottom","30px");
	}
	var color = "white";
	var icon = "fa-user";
	///// Url link to object
	url=buildHtmlUrlObject(newsObj);
	var imageBackground = "";
	if(typeof newsObj.author != "undefined"){
		if(typeof newsObj.author.type == "undefined") {
			newsObj.author.type = "people";
		}
		if (typeof newsObj.type == "events"){
			newsObj.author.type = "";		
		}
	}
	
	///// Image Backgound
	if(typeof(newsObj.imageBackground) != "undefined" && newsObj.imageBackground){
		imagePath = baseUrl+'/'+newsObj.imageBackground;
		imageBackground = '<a '+url+'>'+
							'<div class="timeline_shared_picture"  style="background-image:url('+imagePath+');">'+
								'<img src="'+imagePath+'">'+
							'</div>'+
						'</a>';
	}
	//END Image Background
	iconStr=builHtmlAuthorImageObject(newsObj);
	if(typeof(newsObj.target) != "undefined" && newsObj.target.type != "citoyens" && newsObj.type!="gantts"){
	if(newsObj.target.type=="projects")
			var iconBlank="fa-lightbulb-o";
		else if (newsObj.target.type=="organizations")
			var iconBlank="fa-group";
		}
	// END IMAGE AND FLAG POST BY HOSTED BY //

	title = newsObj.name,
	text = newsObj.text,
	tags = "", 
	scopes = "",
	tagsClass = "",
	scopeClass = "";
	if( "object" == typeof newsObj.tags && newsObj.tags )
	{
		$.each( newsObj.tags , function(i,tag){
			tagsClass += tag+" ";
			tags += "<span class='label tag_item_map_list'>#"+tag+"</span> ";
			if( $.inArray(tag, contextMap.tags)  == -1 && tag != undefined && tag != "undefined" && tag != "" ){
				contextMap.tags.push(tag);
				tagsFilterListHTML += ' <a href="javascript:;" class="filter btn btn-xs btn-default text-red" data-filter=".'+tag+'"><span class="text-red text-xss">#'+tag+'</span></a>';
			}
		});
		tags = '<div class="pull-left"><i class="fa fa-tags text-red"></i> '+tags+'</div>';
	}

	var author = typeof newsObj.author != "undefined" ? newsObj.author : null;
	if( author != null && typeof author.address != "undefined" )
	{
		/*if( newsObj.address.codeInsee )
		{
			scopes += "<span class='label label-danger'>codeInsee : "+newsObj.address.codeInsee+"</span> ";
			scopeClass += newsObj.address.codeInsee+" ";
			if( $.inArray(newsObj.address.codeInsee, contextMap.scopes.codeInsee )  == -1)
				contextMap.scopes.codeInsee.push(newsObj.address.codeInsee);
		}*/
		if( typeof author.address.postalCode != "undefined")
		{
			scopes += "<span class='label label-danger'>"+author.address.postalCode+"</span> ";
			scopeClass += author.address.postalCode+" ";
			if( $.inArray(author.address.postalCode, contextMap.scopes.codePostal )  == -1){
				contextMap.scopes.codePostal.push(author.address.postalCode);
				//scopesFilterListHTML += ' <a href="#" class="filter btn btn-xs btn-default text-red" data-filter=".'+newsObj.address.postalCode+'"><span class="text-red text-xss">'+newsObj.address.postalCode+'</span></a>';
			}
		}
		if( typeof author.address.addressLocality != "undefined")
		{
			scopes += "<span class='label label-danger'>"+author.address.addressLocality+"</span> ";
			scopeClass += author.address.addressLocality+" ";
			if( $.inArray(author.address.addressLocality, contextMap.scopes.addressLocality )  == -1){
				contextMap.scopes.addressLocality.push(author.address.addressLocality);
				scopesFilterListHTML += ' <a href="#" class="filter btn btn-xs btn-default text-red" data-filter=".'+author.address.addressLocality+'"><span class="text-red text-xss">'+author.address.addressLocality+'</span></a>';
			}
		}
		scopes = '<div class="pull-right"><i class="fa fa-circle-o"></i> '+scopes+'</div>';
	}
	var objectDetail = (newsObj.object && newsObj.object.displayName) ? '<div>Name : '+newsObj.object.displayName+'</div>'	 : "";
	var objectLink = (newsObj.object) ? ' <a '+url+'>'+iconStr+'</a>' : iconStr;
	// HOST NAME AND REDIRECT URL
	if (typeof(newsObj.target) != "undefined" && newsObj.target && newsObj.type!="needs" && newsObj.type!="gantts"){
		redirectTypeUrl=newsObj.target.type.substring(0,newsObj.target.type.length-1);
		if (newsObj.target.type=="citoyens")
			redirectTypeUrl="person";
		<?php if (isset($_GET["isNotSV"])){ ?> 
			urlTarget = 'href="#" onclick="openMainPanelFromPanel(\'/'+redirectTypeUrl+'/detail/id/'+newsObj.target.id+'\', \''+redirectTypeUrl+' : '+newsObj.target.name+'\',\''+iconBlank+'\', \''+newsObj.target.id+'\')"';
		<?php } else{ ?>
			urlTarget = 'href="'+baseUrl+'/'+moduleId+'/'+redirectTypeUrl+'/dashboard/id/'+newsObj.target.id+'"';
		<?php } ?>
		var personName = "<a "+urlTarget+" style='color:#3C5665;'>"+newsObj.target.name+"</a>";
	}
	else {
		if(typeof newsObj.author.id != "undefined")
			authorId=newsObj.author.id;
		else
			authorId=newsObj.author._id.$id;
		<?php if (isset($_GET["isNotSV"])){ ?> 
			urlTarget = 'href="#" onclick="openMainPanelFromPanel(\'/person/detail/id/'+authorId+'\', \'person : '+newsObj.author.name+'\',\'fa-user\', \''+authorId+'\')"';
		<?php } else{ ?>
			urlTarget = 'href="'+baseUrl+'/'+moduleId+'/person/dashboard/id/'+authorId+'"';
		<?php } ?>
		var personName = "<a "+urlTarget+" style='color:#3C5665;'>"+newsObj.author.name+"</a>";
		//var personName = newsObj.author.name;
	}
	// END HOST NAME AND REDIRECT URL
	// Created By Or invited by
	if(typeof(newsObj.verb) != "undefined" && typeof(newsObj.target) != "undefined" && newsObj.target.id != newsObj.author.id){
		<?php if (isset($_GET["isNotSV"])){ ?> 
			urlAuthor = 'href="#" onclick="openMainPanelFromPanel(\'/person/detail/id/'+newsObj.author.id+'\', \'person : '+newsObj.author.name+'\',\'fa-user\', \''+newsObj.author.id+'\')"';
		<?php } else{ ?>
			urlAuthor = 'href="'+baseUrl+'/'+moduleId+'/person/dashboard/id/'+newsObj.author.id+'"';
		<?php } ?>
		authorLine=newsObj.verb+" by <a "+urlAuthor+">"+newsObj.author.name+"</a>";
	}
	else 
		authorLine="";
	//END OF CREATED BY OR INVITED BY
	var commentCount = 0;
	if (streamType=="news"){
		idVote=newsObj._id['$id'];
	}
	else
		idVote=newsObj.id;
	if ("undefined" != typeof newsObj.commentCount) 
		commentCount = newsObj.commentCount;
	var voteUpCount = 0;
	if ("undefined" != typeof newsObj.voteUpCount){ 
		voteUpCount = newsObj.voteUpCount;
	}
	var voteDownCount = 0;
	if ("undefined" != typeof newsObj.voteDownCount) 
		voteDownCount = newsObj.voteDownCount;

	newsTLLine = '<li class="newsFeed '+''/*tagsClass*/+' '+scopeClass+' '+newsObj.type+' ">'+
					'<div class="timeline_element partition-'+color+'">'+
						tags+
						scopes+
						'<div class="space1"></div>'+ 
						imageBackground+
						'<div class="timeline_author_block">'+
							objectLink+
							'<span class="light-text timeline_author padding-5 margin-top-5 text-dark text-bold">'+personName+'</span>'+
							'<div class="timeline_date"><i class="fa fa-clock-o"></i> '+dateStr+'</div>' +					
						'</div>'+
						'<div class="space5"></div>'+
						'<a '+url+'>'+
							'<div class="timeline_title">'+
								'<span class="text-large text-bold light-text timeline_title no-margin padding-5">'+title+
								'</span>'+
							'</div>'+
							'<div class="space5"></div>'+
							'<span class="timeline_text">'+ text + '</span>' +	
						'</a>'+
						'<div class="space5"></div>'+
						'<span class="timeline_text">'+ authorLine + '</span>' +
						'<div class="space10"></div>'+
						
						'<hr>'+
						"<div class='bar_tools_post pull-left'>"+
							"<a href='javascript:;' class='newsAddComment' data-count='"+commentCount+"' data-id='"+idVote+"' data-type='"+newsObj.type+"'><span class='label text-dark'>"+commentCount+" <i class='fa fa-comment'></i></span></a> "+
							"<a href='javascript:;' class='newsVoteUp' data-count='"+voteUpCount+"' data-id='"+idVote+"' data-type='"+newsObj.type+"'><span class='label text-dark'>"+voteUpCount+" <i class='fa fa-thumbs-up'></i></span></a> "+
							"<a href='javascript:;' class='newsVoteDown' data-count='"+voteDownCount+"' data-id='"+idVote+"' data-type='"+newsObj.type+"'><span class='label text-dark'>"+voteDownCount+" <i class='fa fa-thumbs-down'></i></span></a> "+
							//"<a href='javascript:;' class='newsShare' data-count='10' data-id='"+newsObj._id['$id']+"'><span class='label text-dark'>10 <i class='fa fa-share-alt'></i></span></a> "+
							//"<span class='label label-info'>10 <i class='fa fa-eye'></i></span>"+
						"</div>"+
					'</div>'+
				'</li>';
	return newsTLLine;
}
function buildHtmlUrlObject(obj){
	console.log(obj);
	if(typeof(obj.type) != "undefined")
		redirectTypeUrl=obj.type.substring(0,obj.type.length-1);
	else 
		redirectTypeUrl="news";
	if(obj.type == "citoyens" && typeof(obj.verb) == "undefined" || (streamType =="news" && (contextParentType=="projects" || contextParentType=="organizations"))){
		<?php if (isset($_GET["isNotSV"])){ ?> 
			url = 'href="#" onclick="openMainPanelFromPanel(\'/news/latest/id/'+obj.id+'\', \''+redirectTypeUrl+' : '+obj.name+'\',\''+obj.icon+'\', \''+obj.id+'\')"';
		<?php } else{ ?>
			url = 'href="'+baseUrl+'/'+moduleId+'/'+redirectTypeUrl+'/latest/id/'+obj.id+'"';
		<?php } ?>
	}
	else{
		if (contextParentType=="projects" || contextParentType=="organizations"){
			if(obj.type=="needs"){
				redirectTypeUrl=obj.type;
				typeId="idNeed";
				urlParent="/type/"+contextParentType+"/id/"+contextParentId;
			}
			else if(obj.type =="citoyens"){
				redirectTypeUrl="person";
				typeId="id";
				urlParent="";
			} 
			else if(obj.type =="organizations"){
				redirectTypeUrl="organization";
				typeId="id";
				urlParent="";
			} 
			else if(obj.type =="events"){
				redirectTypeUrl="event";
				typeId="id";
				urlParent="";
			} 
			else if(obj.type=="gantts"){
				redirectTypeUrl="project";
				typeId="id";
				urlParent="";
			}
			else if (obj.type =="projects"){
				typeId="id";
				redirectTypeUrl="project";
				urlParent="";
			}
		<?php if (isset($_GET["isNotSV"])){ ?> 
			url = 'href="#" onclick="openMainPanelFromPanel(\'/'+redirectTypeUrl+'/detail/'+typeId+'/'+obj.id+urlParent+'\', \''+redirectTypeUrl+' : '+obj.name+'\',\''+obj.icon+'\', \''+obj.id+'\')"';
		<?php } else{ ?>
			url = 'href="'+baseUrl+'/'+moduleId+'/'+redirectTypeUrl+'/dashboard/'+typeId+'/'+obj.id+urlParent+'"';
		<?php } ?>
		}
		else{
		<?php if (isset($_GET["isNotSV"])){ ?> 
			url = 'href="#" onclick="openMainPanelFromPanel(\'/'+redirectTypeUrl+'/detail/id/'+obj.id+'\', \''+redirectTypeUrl+' : '+obj.name+'\',\''+obj.icon+'\', \''+obj.id+'\')"';
			<?php } else{ ?>
			url = 'href="'+baseUrl+'/'+moduleId+'/'+redirectTypeUrl+'/dashboard/id/'+obj.id+'"';
		<?php } ?>
		}
	}
	return url; 
}
function builHtmlAuthorImageObject(obj){
	if(typeof(obj.icon) != "undefined"){
		icon = "fa-" + Sig.getIcoByType({type : obj.type});
		var colorIcon = Sig.getIcoColorByType({type : obj.type});
		if (icon == "fa-circle")
			icon = obj.icon;
	}else{ 
		icon = "fa-rss";
		colorIcon="blue";
	}
	var flag = '<div class="ico-type-account"><i class="fa '+icon+' fa-'+colorIcon+'"></i></div>';	
	// IMAGE AND FLAG POST BY - TARGET IF PROJECT AND EVENT - AUTHOR IF ORGA
	if(typeof(obj.target) != "undefined" && obj.target.type != "citoyens" && obj.type!="gantts"){
		if(obj.target.type=="projects")
			var iconBlank="fa-lightbulb-o";
		else if (obj.target.type=="organizations")
			var iconBlank="fa-group";
		if(typeof obj.target.profilImageUrl !== "undefined" && obj.target.profilImageUrl != ""){ 
			imgProfilPath = "<?php echo Yii::app()->createUrl('/'.$this->module->id.'/document/resized/50x50'); ?>"+obj.target.profilImageUrl;
		
		var iconStr = "<div class='thumbnail-profil'><img height=50 width=50 src='" + imgProfilPath + "'></div>" + flag ; 
		}else {
			var iconStr = "<div class='thumbnail-profil text-center' style='overflow:hidden;'><i class='fa "+iconBlank+"' style='font-size:50px;'></i></div>"+flag;
		}
	}else{
			var imgProfilPath =  "<?php echo $this->module->assetsUrl.'/images/news/profile_default_l.png';?>";
			if((contextParentType == "projects" || contextParentType == "organizations") && typeof(obj.verb) != "undefined" && obj.type!="gantts"){
				if(typeof obj.target.profilImageUrl != "undefined" && obj.target.profilImageUrl != ""){ 
					imgProfilPath = "<?php echo Yii::app()->createUrl('/'.$this->module->id.'/document/resized/50x50'); ?>"+obj.target.profilImageUrl;
					var iconStr = "<div class='thumbnail-profil'><img height=50 width=50 src='" + imgProfilPath + "'></div>" + flag ; 
				}else {
					if(obj.type=="organizations")
						var iconStr = "<div class='thumbnail-profil text-center' style='overflow:hidden;'><i class='fa fa-group' style='font-size:50px;'></i></div>"+flag;
					else
						var iconStr = "<div class='thumbnail-profil'><img height=50 width=50 src='" + imgProfilPath + "'></div>" + flag ; 

				}
			}
			else{	
				if(typeof obj.author.profilImageUrl !== "undefined" && obj.author.profilImageUrl != ""){
					imgProfilPath = "<?php echo Yii::app()->createUrl('/'.$this->module->id.'/document/resized/50x50'); ?>" + obj.author.profilImageUrl;
				}
				var iconStr = "<div class='thumbnail-profil'><img height=50 width=50 src='" + imgProfilPath + "'></div>" + flag ;	 
			}
	}
	return iconStr;
}

function bindEvent(){
	var separator, anchor;
	//$('.timeline-scrubber').scrollToFixed({
	//	marginTop: $('header').outerHeight() + 200
	//}).find("a").on("click", function(e){			
	//	anchor = $(this).data("separator");
		//$("body").scrollTo(anchor, 300);
	//	e.preventDefault();
	//});
	//$('.timeline-scrubber').css("right","50px");
	$(".date_separator").appear().on('appear', function(event, $all_appeared_elements) {
		separator = '#' + $(this).attr("id");
		$('.timeline-scrubber').find("li").removeClass("selected").find("a[href = '" + separator + "']").parent().addClass("selected");
	}).on('disappear', function(event, $all_disappeared_elements) {   				
		separator = $(this).attr("id");
		$('.timeline-scrubber').find("a").find("a[href = '" + separator + "']").parent().removeClass("selected");
	});
	$('.newsAddComment').off().on("click",function() {
		$.blockUI.defaults.css = {"text-align": "left", "cursor":"default"};
		$.blockUI({message : '<div><a href="javascript:$.unblockUI();"><span class="pull-right text-dark"><i class="fa fa-share-alt"></span></a>'+
			'<div class="commentContent"></div></div>', onOverlayClick: $.unblockUI});
		if(streamType=="news"){
			type="news";
		}
		else
			type=$(this).data("type");
		getAjax('.commentContent',baseUrl+'/'+moduleId+"/comment/index/type/"+type+"/id/"+$(this).data("id"),function(){ 
			/*if(!userId){
				window.location.href = baseUrl+'/'+moduleId+"/person/login";
			} else{*/
				//$(".ajaxForm").slideDown(); 
				//$.unblockUI();
			//}
		},"html");

		// if(isNotSV)
		// 	showAjaxPanel( "/comment/index/type/news/id/"+$(this).data("id"), 'ADD A COMMENT','comment' )
		// else
		// 	window.location.href = baseUrl+"/<?php echo $this->module->id?>/comment/index/type/news/id/"+$(this).data("id");
		/*
		toastr.info('TODO : COMMENT this news Entry');
		console.log("newsAddComment",$(this).data("id"));
		count = parseInt($(this).data("count"));
		$(this).data( "count" , count+1 );
		$(this).children(".label").html($(this).data("count")+" <i class='fa fa-comment'></i>");
		*/
	});
	$('.newsVoteUp').off().on("click",function(){
		toastr.info('TODO : VOTE UP this news Entry');
		actionOnNews($(this),'<?php echo Action::ACTION_VOTE_UP ?>');
		//disableOtherAction($(this).data("id"), '.commentVoteUp');
		console.log("newsVoteUp",$(this).data("id"));
		count = parseInt($(this).data("count"));
		//$(this).data( "count" , count+1 );
		$(this).children(".label").html($(this).data("count")+" <i class='fa fa-thumbs-up'></i>");
	});
	$('.newsVoteDown').off().on("click",function(){
		toastr.info('VOTE DOWN this news: add a comment');
		actionOnNews($(this),'<?php echo Action::ACTION_VOTE_DOWN ?>');
		disableOtherAction($(this), '.commentVoteDown');
		console.log("newsVoteDown",$(this).data("id"));
		//count = parseInt($(this).data("count"));
		//$(this).data( "count" , count+1 );
		$(this).children(".label").html($(this).data("count")+" <i class='fa fa-thumbs-down'></i>");
	});
	$('.newsShare').off().on("click",function(){
		toastr.info('TODO : SHARE this news Entry');
		console.log("newsShare",$(this).data("id"));
		count = parseInt($(this).data("count"));
		$(this).data( "count" , count+1 );
		$(this).children(".label").html($(this).data("count")+" <i class='fa fa-share-alt'></i>");
	});

	$('.filter').off().on("click",function(){
	 	if($(this).data("filter") == ".news" || $(this).data("filter")==".activityStream"){
		 	//$(".newsTL").fadeOut();
		 	//$("#formCreateNewsTemp").hide();
		 	htmlMessage = '<div class="title-processing homestead"><i class="fa fa-spinner fa-spin"></i> Processing... </div>';
		 	<?php if( isset($_GET["isNotSV"]) ) { ?>
				htmlMessage +=	'<a class="thumb-info" href="'+proverbs[rand]+'" data-title="Proverbs, Culture, Art, Thoughts"  data-lightbox="all">'+
			 		'<img src="'+proverbs[rand]+'" style="border:0px solid #666; border-radius:3px;"/></a><br/><br/>';
			<?php } ?>
	
		/* $.blockUI({message : '<div class="title-processing homestead"><i class="fa fa-spinner fa-spin"></i> Processing... </div>'
+'<a class="thumb-info" href="'+proverbs[rand]+'" data-title="Proverbs, Culture, Art, Thoughts"  data-lightbox="all">'
+ '<img src="'+proverbs[rand]+'" style="border:0px solid #666; border-radius:3px;"/></a><br/><br/>'
});*/
			//offset="";
			//dateLimit = 0;	
			//lastOffset="";
			//$(".newsTLmonthsList").html("");
			console.log(newsReferror);
			if ($(this).data("filter")== ".news"){
				newsReferror.activity.offset=offset,
				newsReferror.activity.lastOffset=lastOffset,
				newsReferror.activity.dateLimit=dateLimit;
				offset=newsReferror.news.offset;
				dateLimit = newsReferror.news.dateLimit;	
				lastOffset=newsReferror.news.lastOffset;
				streamType="news";
				$(this).removeClass("btn-green").addClass("btn-dark-green");
				
				$("#btnActivity").removeClass("btn-dark-green").addClass("btn-green");
				$(".newsTLactivity, .newsTLmonthsListactivity").fadeOut();
				//$("#newNeed #btnServices, #newNeed #btnMaterials").addClass("btn-green");

			}else if ($(this).data("filter")== ".activityStream"){
				newsReferror.news.offset=offset,
				newsReferror.news.lastOffset=lastOffset,
				newsReferror.news.dateLimit=dateLimit;
				offset=newsReferror.activity.offset;
				dateLimit = newsReferror.activity.dateLimit;	
				lastOffset=newsReferror.activity.lastOffset;
				streamType="activity";
				$(this).removeClass("btn-green").addClass("btn-dark-green");
				$("#btnNews").removeClass("btn-dark-green").addClass("btn-green");
				$(".newsTLnews, .newsTLmonthsListnews").fadeOut();
			}
			console.log(newsReferror);
			if(dateLimit==0){
				$.blockUI({message : htmlMessage});
				loadStream();
			}
			$(".newsTL"+streamType+", .newsTLmonthsList"+streamType).fadeIn();
			if ($("#backToTop"+streamType).length > 0 || $(".emptyNews"+streamType).length > 0){
				$(".stream-processing").hide();	
			}
			else
				$(".stream-processing").show();	
		}
		else{
			console.warn("filter",$(this).data("filter"));
			filter = $(this).data("filter");
			$(".newsFeed").hide();
			$(filter).show();
		}
	});

	$(".form-create-news-container #name").focus(function(){
		showFormBlock(true);	
	});
	$('.smoothScroll').off().on("click",function() {
    	if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) 		{
			var target = $(this.hash);

			target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
			if (target.length) {
				//console.log($("#newsHistory").offset()+"//height"+$(".newsTL").height());
				
				if(target.offset().top < 0)
					targetOffset=target.offset().top;
				else
					targetOffset=target.offset().top;
				console.log(targetOffset);
			$("#newsHistory").animate({
			  scrollTop: targetOffset
			}, 1000, 'swing'); // The number here represents the speed of the scroll in milliseconds
			return false;
			}
    	}
	});
	// $(".timeline_element").click(function(){
	//	if($(".form-create-news-container #name").val() == "" &&
	 //		$(".form-create-news-container #text").val() == ""){
	 	//	showFormBlock(false);
	 //	}
	 //});
}
function actionOnNews(news, action) {
	if (streamType=="news")
		type="news";
	else 
		type=news.data("type");
	$.ajax({
		url: baseUrl+'/'+moduleId+"/action/addaction/",
		data: {
			id: news.data("id"),
			collection : ''+type+'',
			action : action
		},
		type: 'post',
		global: false,
		async: false,
		dataType: 'json',
		success: 
			function(data) {
    			if(!data.result){
                    toastr.error(data.msg);
                    console.log(data);
               	}
                else { 
                    if (data.userAllreadyDidAction) {
                    	toastr.info("You already vote on this comment.");
                    } else {
	                    toastr.success(data.msg);
	                    count = parseInt(news.data("count"));
						news.data( "count" , count+1 );
						icon = news.children(".label").children(".fa").attr("class");
						news.children(".label").html(news.data("count")+" <i class='"+icon+"'></i>");
					}
                }
            },
        error: 
        	function(data) {
        		toastr.error("Error calling the serveur : contact your administrator.");
        	}
		});
}
function disableOtherAction(commentId, action) {
	if (action != ".commentVoteUp") {
		$("#comment"+commentId).children().children(".bar_tools_post").children(".commentVoteUp").children(".label").removeClass("label-green").addClass("label-inverse");
		$("#comment"+commentId).children().children(".bar_tools_post").children(".commentVoteUp").off();
	}
	if (action != ".commentVoteDown") {
		$("#comment"+commentId).children().children(".bar_tools_post").children(".commentVoteDown").children(".label").removeClass("label-orange").addClass("label-inverse");	
		$("#comment"+commentId).children().children(".bar_tools_post").children(".commentVoteDown").off();
	}	
}
function updateNews(newsObj)
{
	var date = new Date( parseInt(newsObj.created.sec)*1000 );
	if(newsObj.date.sec && newsObj.date.sec != newsObj.created.sec) {
		date = new Date( parseInt(newsObj.date.sec)*1000 );
	}
	var newsTLLine = buildLineHTML(newsObj);
	$(".newsTLnews"+date.getMonth()).prepend(newsTLLine);
}


function applyTagFilter(str)
{
	$(".newsFeed").fadeOut();
	if(!str){
		if($(".btn-tag.active").length){
			str = "";
			sep = "";
			$.each( $(".btn-tag.active") , function() { 
				str += sep+"."+$(this).data("id");
				sep = ",";
			});
		} else
			str = ".newsFeed";
	} 
	console.log("applyTagFilter",str);
	$(str).fadeIn();
	return $(".newsFeed").length;
}

function applyScopeFilter(str)
{
	$(".newsFeed").fadeOut();
	if(!str){
		if($(".btn-context-scope.active").length){
			str = "";
			sep = "";
			$.each( $(".btn-context-scope.active") , function() { 
				str += sep+"."+$(this).data("val");
				sep = ",";
			});
		} else
			str = ".newsFeed";
	} 
	console.log("applyScopeFilter",str);
	$(str).fadeIn();
	return $(".newsFeed").length;
}

function toggleFilters(what){
 	if( !$(what).is(":visible") )
 		$('.optionFilter').hide();
 	$(what).slideToggle();
 }

function showFormBlock(bool){
	if(bool){
		$(".form-create-news-container #text").show("fast");
		$(".form-create-news-container .tagstags").show("fast");
		$(".form-create-news-container .datedate").show("fast");
		$(".form-create-news-container .form-actions").show("fast");
		$(".form-create-news-container .publiccheckbox").show("fast");
		if($("input#public").prop('checked') != true)
		$(".form-create-news-container .scopescope").show("fast");	
	}else{
		$(".form-create-news-container #text").hide();
		$(".form-create-news-container .tagstags").hide();
		$(".form-create-news-container .datedate").hide();
		$(".form-create-news-container .form-actions").hide();
		$(".form-create-news-container .scopescope").hide();
		$(".form-create-news-container .publiccheckbox").hide();
	}
}


</script>
