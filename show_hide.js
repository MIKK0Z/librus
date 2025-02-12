/*
Title: showHideRows.js
	
Description:
Show hide rows.
	
Author:
Wojciech Matusiak <wojciech.matusiakt]librus.pl>
*/

/*
   Variable: ShowHideRows
   [int] Variable include searching and showing hided row function.
*/

/*
Function: ShowHideRows

Show hide row.
   
Author:
Wojciech Matusiak <wojciech.matusiak[at]librus.pl>
		
Parameters:

string strDate - date ('-' seperate)
string baseName - element name
string cookieName - cookie name
array allSufix - sufix array
boolean showHideCallback - callback
boolean notStoreCookie - is store cookie?

Returns:

boolean - true if value is correct date.
*/
var ShowHideRows = function(baseName, cookieName, allSufix, showHideCallback, notStoreCookie)
{
	this.baseName = baseName;
	this.cookieName = cookieName; 
	this.showHideCallback = showHideCallback;
	this.allSufix = allSufix;
	this.notStoreCookie = notStoreCookie;
	if(this.notStoreCookie == null) {
		this.notStoreCookie = false;
	}
	
	if(!this.notStoreCookie){
		this.cookie = new Cookie(null, null, null);
	}
	
	var array_contains = function(array, value) 
	{
		for(var i = 0; i < array.length; ++i) {
			if(array[i] == value) {
				return i;
			}
		}

		return false;
	};
	
	this.Show = function(sufix)
	{
		this.ShowHideInternal(sufix, true);
	};
	
	this.Hide = function(sufix)
	{
		this.ShowHideInternal(sufix, false);
	};
	
	this.ShowHide = function(sufix) 
	{
		this.ShowHideInternal(sufix, null);	
	};
	
	this.ShowAll = function() 
	{
		for(index in this.allSufix) {
			this.ShowHideInternal(this.allSufix[index], true);
		} 
	};
	
	this.HideAll = function() 
	{
		for(index in this.allSufix) {
			this.ShowHideInternal(this.allSufix[index], false);
		} 
	};
	
	this.ShowHideAll = function() 
	{
		for(index in this.allSufix) {
			this.ShowHideInternal(this.allSufix[index], null);
		}
	};
	
	this.ShowHideInternal = function(sufix, showHide) 
	{
		var tmp = document.getElementById(this.baseName + sufix); 
		if(tmp) {
			if(!this.notStoreCookie) {
				var cookieValueArray = new Array();
				var cookieValue = this.cookie.Get(this.cookieName);
				if(cookieValue) {
					cookieValueArray = cookieValue.split(';'); 
				}
			}
			
			var node = document.getElementById(this.baseName + sufix + '_node');
			if (tmp.style.visibility === 'hidden' && jQuery(tmp).data('hidden_now') !== true && (showHide === null || showHide === true)) { 
                if (jQuery) {
                    $(tmp).data('pokazane_przez_skrypt', true);
                    $(tmp).show();
                } else {
                    if(navigator.appName.indexOf('Microsoft') > -1) { 
                        tmp.style.display = 'block';
                    } else { 
                        tmp.style.display = 'table-row'; 
                    }
                }
				
				
				tmp.style.visibility = 'visible';
				if(!this.notStoreCookie) {
					if(array_contains(cookieValueArray, sufix) === false) {
						cookieValueArray.push(sufix);
					}
				}
				
				if(node){
					node.src = 'https://synergia.librus.pl/images/tree_expanded.png';
				}
			} else if ((tmp.style.visibility === 'visible' || (tmp.style.visibility === 'hidden' && jQuery(tmp).data('hidden_now') === true)  ) && (showHide === null || showHide === false)) {
				if (jQuery) {
                    $(tmp).data('pokazane_przez_skrypt', false);
                    $(tmp).hide();
                } else {
                    tmp.style.display = 'none';
                } 
				tmp.style.visibility = 'hidden';
				if(!this.notStoreCookie) {
					var arrayIndex = array_contains(cookieValueArray, sufix); 
					if(arrayIndex !== false) {
						cookieValueArray.splice(arrayIndex, 1);
					}
				}
				
				if(node){
					node.src = 'https://synergia.librus.pl/images/tree_colapsed.png';
				}
			} 

			if(!this.notStoreCookie) {
				cookieValue = cookieValueArray.join(';');
				this.cookie.Set(this.cookieName, cookieValue, null);
			}
		}
		
		if(this.showHideCallback) {
			this.showHideCallback();
		}
		
		this.UpdateAllNodeHandler();
	};
	
	this.UpdateAllNodeHandler = function()
	{
		var allClose = true;
		for(index in this.allSufix) {
			var tmp = document.getElementById(this.baseName + this.allSufix[index]);
			if(tmp && tmp.style.visibility == 'visible') {
				allClose = false;
				break;
			}
		}
		
		var all = document.getElementById(this.baseName + 'all_node');
        if (null !== all)
        {
            all.src = (allClose ? 'https://synergia.librus.pl/images/tree_colapsed.png' : 'https://synergia.librus.pl/images/tree_expanded.png');
            all.colapsed = allClose;
        }
	};
	
	this.AllNodeHandler = function()
	{
		this.UpdateAllNodeHandler();
		var all = document.getElementById(this.baseName + 'all_node');
		if(all.colapsed) {
			this.ShowAll();
		} else {
			this.HideAll();
		}
	};
	
	this.Restore = function()
	{
		if(!this.notStoreCookie) {
			var cookieValueArray = new Array();
			var cookieValue = this.cookie.Get(this.cookieName);
			if(cookieValue) {
				cookieValueArray = cookieValue.split(';'); 
			}
	
			while(cookieValueArray.length > 0) {
				this.Show(cookieValueArray.pop());
			}
		}
	};
};
