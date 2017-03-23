

 
// FF init
dealtracker.onFirefoxLoad = function(event) {

  // myExtension.init();

  document.getElementById("contentAreaContextMenu")
          .addEventListener("popupshowing", function (e){ dealtracker.showFirefoxContextMenu(e); }, false);
};




// menuinti
dealtracker.showFirefoxContextMenu = function(event) {
  // show or hide the menuitem based on what the context menu is on
  document.getElementById("context-dealtracker").hidden = gContextMenu.onImage;
  
};






//window.addEventListener("load", function() { myExtension.init(); }, false);
	var myExtension = {
	    init: function() {
	        if(gBrowser) {gBrowser.addEventListener("DOMContentLoaded", this.onPageLoad, false);  
	        }else{
	          alert("gBrowser is false");
	        }
	    },
	    onPageLoad: function(aEvent) {
	    
	         //alert ("page load called");
	        var doc = aEvent.originalTarget; // doc is document that triggered "onload" event
	         var win = doc.defaultView; // win is the window for the doc
	        // alert(doc.location.href);
	         //if  (doc.location.href != "about:blank"){
	         
	        
	        // test desired conditions and do something
	        // if (doc.nodeName == "#document") return; // only douments
	        // if (doc.defaultView != doc.defaultView.top) return; //only top window.
	        // if (doc.defaultView.frameElement) return; // skip iframes/frames
	        
	        
	        var sidebarWindow = document.getElementById("sidebar").contentWindow;
          // Verify that our sidebar is open at this moment:
          if (sidebarWindow.location.href ==
                "chrome://dealtracker/content/ff-sidebar.xul")
                 {
                // call "yourNotificationFunction" in the sidebar's context:
                
                
                   if (doc.nodeName != "#document") return; // only documents
	               if (win != win.top) return; //only top window.
                 if (win.frameElement) return; // skip iframes/frames

                   //alert("page is loaded \n" +doc.location.href);
               
                  // mark=doc.location.href.indexOf(".")+3;
                  mark=45;
                   sidebarWindow.get_current_url(doc.location.href.substring(0,mark) ); 
                 
                 sidebarWindow = null;
            
                } //end if side bar location
            //  } //if not about:blank   
            
            
            doc=null;
            win=null;
	    }  //end on Page load
	}
   
 
 /*
var myExtension = {
	  init: function() {
	    var appcontent = document.getElementById("appcontent");   // browser
	    if(appcontent)
	      appcontent.addEventListener("DOMContentLoaded", myExtension.onPageLoad, true);
	    var messagepane = document.getElementById("messagepane"); // mail
	    if(messagepane)
	      messagepane.addEventListener("load", function(event) { myExtension.onPageLoad(event); }, true);
	  },
	 
	  onPageLoad: function(aEvent) {
	    var doc = aEvent.originalTarget; // doc is document that triggered "onload" event
	    // do something with the loaded page.
	    // doc.location is a Location object (see below for a link).
	    // You can use it to make your code executed on certain pages only.
	    
	      alert(doc.location.href);
	     
	    // add event listener for page unload
	    aEvent.originalTarget.defaultView.addEventListener("unload", function(event){ myExtension.onPageUnload(event); }, true);
	  },
	 
	  onPageUnload: function(aEvent) {
	    // do something
	  }
	}//end myExtension
	
	*/
window.addEventListener("load", function () { dealtracker.onFirefoxLoad(); }, false);



