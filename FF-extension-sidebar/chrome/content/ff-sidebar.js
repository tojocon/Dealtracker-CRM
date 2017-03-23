//var mainWindow = null;
//var currenturl='deafult.com';
 /*	var myExtension = {
	    init: function() {
	        if(gBrowser) gBrowser.addEventListener("DOMContentLoaded", this.onPageLoad, false);
	    },
	    onPageLoad: function(aEvent) {
	        var doc = aEvent.originalTarget; // doc is document that triggered "onload" event
	        // test desired conditions and do something
	        // if (doc.nodeName == "#document") return; // only douments
	        // if (doc.defaultView != doc.defaultView.top) return; //only top window.
	        // if (doc.defaultView.frameElement) return; // skip iframes/frames
	        alert("page from Sidebar is: \n" +doc.location.href);
	        get_current_url(doc.location.href);
	    }
	}
	
	 */

   //globals
 
 	
 
	 
function get_current_url()
{
    //Variables for convenient access to specific elements in the XUL 
    
    //var urlbox = document.getElementById("url");
   
    
    //document.getElementById("urlbox").setAttribute("value", currenturl);
    // document.getElementById("urlbox").value= currenturl;
     
   
     //alert("about to call init() in get current url()");
       currenturl=window.top.getBrowser().selectedBrowser.contentWindow.location.href;
      //alert(currenturl);
     
      /* var mainWindow = window.QueryInterface(Components.interfaces.nsIInterfaceRequestor)
                   .getInterface(Components.interfaces.nsIWebNavigation)
                   .QueryInterface(Components.interfaces.nsIDocShellTreeItem)
                   .rootTreeItem
                   .QueryInterface(Components.interfaces.nsIInterfaceRequestor)
                   .getInterface(Components.interfaces.nsIDOMWindow);

     alert( mainWindow.getBrowser().selectedBrowser.contentWindow.location.href);
      */
     
    // document.getElementById("contentview").setAttribute("src", "http://tojocon.com/dealtracker/form1.php?site="+currenturl.substring(0,45));
      
     document.getElementById("contentview").setAttribute("src", "http://kimongroup.net/dealtracker/form1.php?site="+currenturl.substring(0,45));
                

}


function getDomainData(){
  /*
    if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
        }
      else
        {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
      xmlhttp.onreadystatechange=function()
        {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
          {
            //need reference to sidebar display element here
            
            
            //document.getElementById("dropHint1").innerHTML=xmlhttp.responseText;
            
          }
        } // end function anon
      xmlhttp.open("GET",'http://tojocon.com/dealtracker/form1.php?site=';
    contentview.setAttribute("src", base_url+urlbox.value),true);
      xmlhttp.send();
      */
}


function startup() {


document.getElementById("url").setAttribute("value", "beginning startup");
//if (mainWindow === null){
 /* mainWindow = window.QueryInterface(Components.interfaces.nsIInterfaceRequestor)
                     .getInterface(Components.interfaces.nsIWebNavigation)
                     .QueryInterface(Components.interfaces.nsIDocShellTreeItem)
                     .rootTreeItem
                     .QueryInterface(Components.interfaces.nsIInterfaceRequestor)
                     .getInterface(Components.interfaces.nsIDOMWindow);

  
  //   }   // end if not null
  currenturl=mainWindow.getBrowser().selectedBrowser.contentWindow.location.href;
  */
 // var currentWindow = Components.classes["@mozilla.org/appshell/window-mediator;1"].getService(Components.interfaces.nsIWindowMediator).getMostRecentWindow("navigator:browser");

  //  var currBrowser = currentWindow.getBrowser();
  //  var currURL = currBrowser.currentURI.spec;

   // return currURL; 
  
  
  //document.getElementById("url").setAttribute("value", "about to  get_current_url");
  
  // Sidebar is loaded and mainwindow is ready  
 // get_current_url(currURL.substring(0,16) ); 
  
     
   //  myExtension.init();           
                
//mainWindow.addEventListener("load", function() { myExtension.init(); }, false);

                  
} //end function

function shutdown() {
  // Sidebar is unloading
}





// end page load handler


//window.addEventListener("load", startup, false);
//window.addEventListener("unload", shutdown, false);
//document.getElementById("refreshbtn").setAttribute("onClick", startup);
 
 //var appcontent = window.document.getElementById("appcontent");
//appcontent.addEventListener("DOMContentLoaded",
 //                           startup,
 //                           false);
                            
                            

