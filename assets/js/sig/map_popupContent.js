
	/**
	***		POPUP CONTENT
	***/

	SigLoader.getSigPopupContent = function (Sig){

		//##
		//création du contenu de la popup d'un data
		Sig.getPopup = function(data){

				return this.getPopupSimple(data);

				if(data["@Type"] == "event" || data["type"] == "event" || data["type"] == "meeting") 
					return this.getPopupEvent(data);
				else{
					return this.getPopupCitoyen(data);
				}
		};
		//##
		//création du contenu de la popup d'un data
		Sig.getPopupCitoyen = function(data){

			var type = data['type'] ? data['type'] : "";

			var popupContent = "";
			if(data['thumb_path'] != null)
			popupContent += "<div class='popup-info-profil-thumb-lbl'><img src='" + data['thumb_path'] + "' height=100 class='popup-info-profil-thumb "+type+"'></div>";
			else
			popupContent += "<div class='popup-info-profil-thumb-lbl'><img src='"+assetPath+"/images/thumb/default.png' width=100 class='popup-info-profil-thumb "+type+"'></div>";


			//NOM DE L'UTILISATEUR
			if(data['name'] != null){
				var userUrl = data['publicUrl'] ? data['publicUrl'] : "#";
				popupContent += 	"<div class='popup-info-profil-username'><a href='"+ userUrl +"' class='"+type+"'>" + data['name'] + "</a></div>";
			}

			//TYPE D'UTILISATEUR (data, ASSO, PARTENAIRE, ETC)
			var typeName = data['type'];
			if(typeName == null)  typeName = "data";
			if(data['name'] == null)  typeName += " Anonyme";

			popupContent += 	"<div class='popup-info-profil-usertype'>" + typeName + "</div>";

			//WORK - PROFESSION
			if(data['work'] != null)
			popupContent += 	"<div class='popup-info-profil-work'>" + data['work'] + "</div>";
			//else
			//popupContent += 	"<div class='popup-info-profil-work'>Fleuriste</div>";

			//URL
			if(data['url'] != null)
			popupContent += 	"<div class='popup-info-profil-url'>" + data['url'] + "</div>";
			//else
			//popupContent += 	"<a href='http://www.google.com' class='popup-info-profil-url'>http://www.google.com</a>";

			if(data['address'] != null){
				//CODE POSTAL 
				if(data['address']['postalCode'] != null)
				popupContent += 	"<div class='popup-info-profil'>" + data['address']['postalCode'] + "</div>";
				//else
				//popupContent += 	"<div class='popup-info-profil'>98800</div>";

				//VILLE ET PAYS
				if(data['address']['addressLocality'] != null){
					var place = data['address']['addressLocality'];
					if(place != null && data['address']['addressCountry'] != null) //place += ", ";
					place += ", " + data['address']['addressCountry'];
				}

				if(place != null)
				popupContent += 	"<div class='popup-info-profil'>" + place + "</div>";
				//else
				//popupContent += 	"<div class='popup-info-profil'>St-Denis, La Réunion</div>";
			}

			//NUMÉRO DE TEL
			if(data['telephone'] != null)
			popupContent += 	"<div class='popup-info-profil'>" + data['telephone'] + "<div/>";
			//else
			popupContent += 	"<div class='popup-info-profil'>0123456789<div/>";

			return popupContent;
		};

		//##
		//création du contenu de la popup d'un data
		Sig.getPopupSimple = function(data){

			var type = data['typeSig'] ? data['typeSig'] : data['type'];

			var popupContent = "<div class='item_map_list popup-marker'>";
	
			var ico = this.getIcoByType(data);
			var color = this.getIcoColorByType(data);

			var icons = '<i class="fa fa-'+ ico + ' fa-'+ color +'"></i>';

			//var prop = feature.properties;
			//console.log("PROPRIETES : ");
			//console.dir(data);

			//showMap(false);

			var type = data.typeSig;
			var typeElement = "";
			if(type == "people") 		typeElement = "person";
			if(type == "organizations") typeElement = "organization";
			if(type == "events") 		typeElement = "event";
			if(type == "projects") 		typeElement = "project";

			var url = baseUrl+'/'+moduleId+'/'+typeElement+'/detail/id/'+data["_id"]["$id"];
			var title = data.typeSig + ' : ' + data.name;
			var icon = 'fa-'+ this.getIcoByType(data);

			//showAjaxPanel( url, title, icon );
							
			popupContent += "<button class='item_map_list popup-marker' onclick='showAjaxPanel(\""+url+"\",\"" + title + "\",\"" + icon + "\"); showMap(false);'>";
										
			popupContent += 
						  "<div class='left-col'>"
	    				+ 	"<div class='thumbnail-profil'></div>"						
	    				+ 	"<div class='ico-type-account'>"+icons+"</div>"					
	    				+ "</div>"

						+ "<div class='right-col'>";
						
						if("undefined" != typeof data['name'])
						popupContent	+= 	"<div class='info_item pseudo_item_map_list'>" + data['name'] + "</div>";
						
						if("undefined" != typeof data['tags']){
							popupContent	+= 	"<div class='info_item items_map_list'>";
							$.each(data['tags'], function(index, value){
								popupContent	+= 	"<div class='tag_item_map_list'>#" + value + " </div>";
							});
							popupContent	+= 	"</div>";
						}

						if("undefined" != typeof data['address'] && "undefined" != typeof data['address']['addressLocality'] )
						popupContent	+= 	"<div class='info_item city_item_map_list'>" + data['address']['addressLocality'] + "</div>";
								
						if("undefined" != typeof data['address'] && "undefined" != typeof data['address']['addressCountry'] )
						popupContent	+= 	"<div class='info_item country_item_map_list'>" + data['address']['addressCountry'] + "</div>";
								
						if("undefined" != typeof data['telephone'])
						popupContent	+= 	"<div class='info_item telephone_item_map_list'>" + data['telephone'] + "</div>";
						
				popupContent += '</button><div>';

			return popupContent;
		};


		//##
		//création du contenu de la popup d'un data
		Sig.getPopupEvent = function(data){

			var type = data['type'] ? data['type'] : "";

			var popupContent = "";
			//if(data['thumb_path'] != null)
			//popupContent += "<div class='popup-info-profil-thumb-lbl'><img src='" + data['thumb_path'] + "' height=100 class='popup-info-profil-thumb'></div>";
			//else
			popupContent += "<div class='popup-info-profil-thumb-lbl'><i class='fa fa-calendar fa-3x popup-info-profil-thumb "+type+"'></i></div>";


			//NOM DE L'UTILISATEUR
			if(data['name'] != null){
				var userUrl = data['publicUrl'] ? data['publicUrl'] : "#";
				popupContent += 	"<div class='popup-info-profil-username'><a href='"+ userUrl +"' class='"+type+"'>" + data['name'] + "</a></div>";
			}

			//TYPE D'UTILISATEUR (data, ASSO, PARTENAIRE, ETC)
			var typeName = data['type'];
			if(typeName == null)  typeName = "data";
			if(data['name'] == null)  typeName += " Anonyme";

			popupContent += 	"<div class='popup-info-profil-usertype'>" + typeName + "</div>";

			//WORK - PROFESSION
			if(data['work'] != null)
			popupContent += 	"<div class='popup-info-profil-work'>" + data['work'] + "</div>";
			//else
			//popupContent += 	"<div class='popup-info-profil-work'>Fleuriste</div>";

			//URL
			if(data['url'] != null)
			popupContent += 	"<div class='popup-info-profil-url'>" + data['url'] + "</div>";
			//else
			//popupContent += 	"<a href='http://www.google.com' class='popup-info-profil-url'>http://www.google.com</a>";

			if(data['address'] != null){
				//CODE POSTAL
				if(data['address']['postalCode'] != null)
				popupContent += 	"<div class='popup-info-profil'>" + data['cp'] + "</div>";
				//else
				//popupContent += 	"<div class='popup-info-profil'>98800</div>";

				//VILLE ET PAYS
				var place = data['address']['addressLocality'];
				if(place != null && data['address']['addressCountry'] != null) //place += ", ";
				place += ", " + data['address']['addressCountry'];

				if(place != null)
				popupContent += 	"<div class='popup-info-profil'>" + place + "</div>";
				//else
				//popupContent += 	"<div class='popup-info-profil'>St-Denis, La Réunion</div>";
			}

			//NUMÉRO DE TEL
			if(data['telephone'] != null)
			popupContent += 	"<div class='popup-info-profil'>" + data['telephone'] + "<div/>";
			//else
			popupContent += 	"<div class='popup-info-profil'>0123456789<div/>";

			return popupContent;
		};

		return Sig;
	};
