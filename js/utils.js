// this function will return both 0 and max, so there are (max + 1) possibilities
var getIntRnd = function (max) {return Math.round(Math.random() * max)};


// use with a filter: var newArray = oldArray.filter(onlyUnique)
function onlyUnique(value, index, self) 
{ 
    return self.indexOf(value) === index;
}
// use with a filter: var newArray = oldArray.filter(removeDeleted)
function removeDeleted(value) 
{ 
    return value !== undefined;
}

function formatNumber(num, dig)			// pads with leading zeroes
{
	var temp = 	"000000000" + num;
	return temp.substring(temp.length - dig);
}
function setLength(num, dig)				// pads with leading spaces
{
	var temp = 	"        " + num;
	return temp.substring(temp.length - dig);
}
function changeUser()
{
	document.cookie = "name=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
	document.cookie = "id=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
	location.reload(true);
}	
function setCookie(cname, cvalue, exdays) 
{
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) 
{
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) 
    {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}
function onSignIn(googleUser) 
{
  var profile = googleUser.getBasicProfile();
  setCookie("name", profile.getName(), 365);
  setCookie("id", profile.getId(), 365);
//  console.log('Image URL: ' + profile.getImageUrl());
//  console.log('Email: ' + profile.getEmail());
}			

var getStringArgument = function(variable) 
{
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) 
  {
    var pair = vars[i].split("=");
    if (pair[0] == variable) 
    {
      return pair[1];
    }
  }
  return null;
}  

//colors
var getColorCREAM = function( ) {return "rgb(250,250,235)"}; 
var getColorCHARTREUSE = function( ) {return "rgb(189, 183, 107)"};	
var getColorOLIVE = function( ) {return "rgb( 107, 142, 35)"};
var getColorPEACH = function( ) {return "rgb(255,218,185)"};
var getColorPALEPEACH = function( ) {return "rgb(244,164,96)"};
var getColorGOLD = function( ) {return "rgb(255,215,0)"};
var getColorRED = function( ) {return "rgb(255,0,0)"}; 
var getColorGREEN = function( ) {return "rgb(0,255,0)"};
var getColorBLUE = function( ) {return "rgb(0,0,255)"};
var getColorYELLOW = function( ) {return "rgb(255,255,0)"};
var getColorMAGENTA = function( ) {return "rgb(255,0,255)"};
var getColorBLACK = function( ) {return "rgb(0,0,0)"};             
var getColorCYAN = function( ) {return "rgb(0,255,255)"};
var getColorORANGE = function( ) {return "rgb(255,125,0)"};    
var getColorPURPLE = function( ) {return "rgb(125,0,255)"};
var getColorWHITE = function( ) {return "rgb(255,255,255)"};     
var getColorGRAY = function( ) {return "rgb(125,125,125)"};  
var getColorSLATEGRAY = function( ) {return "rgb(119,136,153)"};  
var getColorPLUM = function( ) {return "rgb(90,45,90)"};
var getColorLIGHTBLUE = function( ) {return "rgb(0,125,255)"};
var getColorLIGHTPINK = function( ) {return "rgb(255,125,125)"};
var getColorLIGHTGREEN = function( ) {return "rgb(125,255,125)"};
var getColorLIGHTYELLOW = function( ) {return "rgb(255,255,125)"};
var getColorLIGHTMAGENTA = function( ) {return "rgb(255,125,255)"};
var getColorLIGHTPURPLE = function( ) {return "rgb(125,125,255)"};
var getColorLIGHTCYAN = function( ) {return "rgb(125,255,255)"};
var getColorPALECYAN = function( ) {return "rgb(174,224,230)"};
var getColorMEDIUMGREEN = function( ) {return "rgb(0,255,125)"};
var getColorYELLOWGREEN = function( ) {return "rgb(125,255,0)"};
var getColorDARKPINK = function( ) {return "rgb(255,0,125)"};
var getColorDARKRED = function( ) {return "rgb(125,0,0)"};
var getColorDARKGREEN = function( ) {return "rgb(0,125,0)"};
var getColorDARKBLUE = function( ) {return "rgb(0,0,125)"}; 
var getColorDARKGRAY = function( ) {return "rgb(90,90,90)"};
var getColorPALEYELLOW = function( ) {return "rgb(255,255,200)"};
var getColorPALEORCHID = function( ) {return "rgb (200,200,255)"};
var getColorLIGHTGRAY = function( ) {return "rgb (200,200,200)"};
var getColorPALEBLUE = function( ) {return "rgb (204,221,255)"};
var getColorPALEGRAY = function( ) {return "rgb (225,225,225)"}; 
var getColorOFFWHITE = function( ) {return "rgb (227,247,255)"};
var getColorDARKCYAN = function( ) {return "rgb (0,170,204)"};
var getColorDARKMAGENTA = function( ) {return "rgb (128,0,128)"};

function tmButton(x, y, color, width, height, label, size)
{
	this.x = x;
	this.y = y;
	this.color = color;
	this.width = width;
	this.height = height;
	this.label = label;
	this.xLabel = 10;
	this.yLabel = 10;
	this.font = size + "px Arial";
	this.textColor = getColorBLACK();
	this.clicked = function(mouseX, mouseY)
	{
		if (mouseX > this.x && 
			mouseX < this.x + this.width && 
			mouseY > this.y && 
			mouseY < this.y + this.height)
		{
			return true;
		}
		return false;
	}
	this.draw = function()
	{
		ctx.fillStyle = this.color;
		ctx.fillRect(this.x, this.y, this.width, this.height);
		ctx.beginPath();
		ctx.lineWidth = "1";
		ctx.strokeStyle = this.textColor;
		ctx.rect(this.x, this.y, this.width, this.height);
		ctx.stroke();			
   	ctx.font = this.font;
		ctx.textAlign = "left";
		ctx.textBaseline = "top";
		ctx.fillStyle = this.textColor;
		ctx.fillText(this.label, this.x + this.xLabel, this.y + this.yLabel);
	}	
}