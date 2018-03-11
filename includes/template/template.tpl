<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>&PAGE_TITLE</title>
<link rel="stylesheet" href="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css">
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/css/bootstrap.no-icons.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="screen" href="/css/datepicker.css">
<link href="//netdna.bootstrapcdn.com/font-awesome/3.0/css/font-awesome.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="print" href="css/print.css">
<link rel="stylesheet" type="text/css" media="screen" href="css/stylesheet.css?v=2014.07.16">
<link rel="shortcut icon" href="images/favicon.ico" />
<meta name="keywords" content="&PAGE_KEYWORDS" />
<meta name="description" content="&PAGE_DESCRIPTION" />
<script type="text/css" src="/js/html5slider.js"></script>
<!--[if lt IE 9]>
	<script type="text/css" src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.1/html5shiv.js"></script>
<![endif]-->
<script data-cfasync="false" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script data-cfasync="false" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script data-cfasync="false" src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.1/bootstrap.min.js"></script>
<script data-cfasync="false" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script data-cfasync="false" src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.10.0/jquery.validate.min.js"></script>
<script data-cfasync="false" src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.9.4/jquery.dataTables.min.js"></script>
<script data-cfasync="false" src="./js/datatables/extensions.js"></script>

<script>var sf = new Array();</script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style1 {
	color: #FFFFFF;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
}
.style2 {
	color: #000000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.loginString {
	color: #FF0000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: normal;
}
.style4 {
	font-size: 17px;
	color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {color: #0099FF}
.style6 {
	color: #000000;
	font-size: 11px;
	font-family: Arial, Helvetica, sans-serif;
}
.txtbox1 {
	font: 11px Arial, Helvetica, sans-serif;
	color: #000000;
	height: 15px;
	width: 152px;
	border: 1px solid #0F6592;
	vertical-align: bottom;
}
.listbox {
	height: 15px;
	width: 132px;
	border: 1px solid #0F6592;
}
.txtbox2 {
	font: 11px Arial, Helvetica, sans-serif;
	color: #000000;
	height: 15px;
	width: 132px;
	border: 1px solid #0F6592;
}
.txtbox3 {
	font: 11px Arial, Helvetica, sans-serif;
	color: #000000;
	height: 105px;
	width: 350px;
	border: 1px solid #0F6592;
}
.contentbody {
	border: 1px solid #5DA7E1;
	background-color: #FAFCFE;
}
.style9 {
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
	color: #0B4A6C
}
.padded {
	padding: 1em;
}
.margin-top-bottom {
	margin-top: 1em;
	margin-bottom: 1em;
}
label.column {
	display: inline-block;
	min-width: 150px;
}
.toggle-box {
	display: none;
}
-->
</style>
<script data-cfasync="false" language="JavaScript" type="text/javascript">
var DefaultDateFormat = 'MM/DD/YYYY'; // If no date format is supplied, this will be used instead
var HideWait = 3; // Number of seconds before the calendar will disappear
var Y2kPivotPoint = 76; // 2-digit years before this point will be created in the 21st century
var UnselectedMonthText = ''; // Text to display in the 1st month list item when the date isn't required
var FontSize = 11; // In pixels
var FontFamily = 'Tahoma';
var CellWidth = 18;
var CellHeight = 16;
var ImageURL = 'calendar.jpg';
var NextURL = 'next.gif';
var PrevURL = 'prev.gif';
var CalBGColor = 'white';
var TopRowBGColor = 'buttonface';
var DayBGColor = 'lightgrey';

// Global variables
var ZCounter = 100;
var Today = new Date();
var WeekDays = new Array('S','M','T','W','T','F','S');
var MonthDays = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
var MonthNames = new Array('January','February','March','April','May','June','July','August','September','October','November','December');

// Write out the stylesheet definition for the calendar
with (document) {
   writeln('<style>');
   writeln('td.calendarDateInput {letter-spacing:normal;line-height:normal;font-family:' + FontFamily + ',Sans-Serif;font-size:' + FontSize + 'px;}');
   writeln('select.calendarDateInput {letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;}');
   writeln('input.calendarDateInput {letter-spacing:.06em;font-family:Verdana,Sans-Serif;font-size:11px;}');
   writeln('</style>');
}

// Only allows certain keys to be used in the date field
function YearDigitsOnly(e) {
   var KeyCode = (e.keyCode) ? e.keyCode : e.which;
   return ((KeyCode == 8) // backspace
        || (KeyCode == 9) // tab
        || (KeyCode == 37) // left arrow
        || (KeyCode == 39) // right arrow
        || (KeyCode == 46) // delete
        || ((KeyCode > 47) && (KeyCode < 58)) // 0 - 9
   );
}

// Gets the absolute pixel position of the supplied element
function GetTagPixels(StartTag, Direction) {
   var PixelAmt = (Direction == 'LEFT') ? StartTag.offsetLeft : StartTag.offsetTop;
   while ((StartTag.tagName != 'BODY') && (StartTag.tagName != 'HTML')) {
      StartTag = StartTag.offsetParent;
      PixelAmt += (Direction == 'LEFT') ? StartTag.offsetLeft : StartTag.offsetTop;
   }
   return PixelAmt;
}

// Is the specified select-list behind the calendar?
function BehindCal(SelectList, CalLeftX, CalRightX, CalTopY, CalBottomY, ListTopY) {
   var ListLeftX = GetTagPixels(SelectList, 'LEFT');
   var ListRightX = ListLeftX + SelectList.offsetWidth;
   var ListBottomY = ListTopY + SelectList.offsetHeight;
   return (((ListTopY < CalBottomY) && (ListBottomY > CalTopY)) && ((ListLeftX < CalRightX) && (ListRightX > CalLeftX)));
}

// For IE, hides any select-lists that are behind the calendar
function FixSelectLists(Over) {
   if (navigator.appName == 'Microsoft Internet Explorer') {
      var CalDiv = this.getCalendar();
      var CalLeftX = CalDiv.offsetLeft;
      var CalRightX = CalLeftX + CalDiv.offsetWidth;
      var CalTopY = CalDiv.offsetTop;
      var CalBottomY = CalTopY + (CellHeight * 9);
      var FoundCalInput = false;
      formLoop :
      for (var j=this.formNumber;j<document.forms.length;j++) {
         for (var i=0;i<document.forms[j].elements.length;i++) {
            if (typeof document.forms[j].elements[i].type == 'string') {
               if ((document.forms[j].elements[i].type == 'hidden') && (document.forms[j].elements[i].name == this.hiddenFieldName)) {
                  FoundCalInput = true;
                  i += 3; // 3 elements between the 1st hidden field and the last year input field
               }
               if (FoundCalInput) {
                  if (document.forms[j].elements[i].type.substr(0,6) == 'select') {
                     ListTopY = GetTagPixels(document.forms[j].elements[i], 'TOP');
                     if (ListTopY < CalBottomY) {
                        if (BehindCal(document.forms[j].elements[i], CalLeftX, CalRightX, CalTopY, CalBottomY, ListTopY)) {
                           document.forms[j].elements[i].style.visibility = (Over) ? 'hidden' : 'visible';
                        }
                     }
                     else break formLoop;
                  }
               }
            }
         }
      }
   }
}

// Displays a message in the status bar when hovering over the calendar days
function DayCellHover(Cell, Over, Color, HoveredDay) {
   Cell.style.backgroundColor = (Over) ? DayBGColor : Color;
   if (Over) {
      if ((this.yearValue == Today.getFullYear()) && (this.monthIndex == Today.getMonth()) && (HoveredDay == Today.getDate())) self.status = 'Click to select today';
      else {
         var Suffix = HoveredDay.toString();
         switch (Suffix.substr(Suffix.length - 1, 1)) {
            case '1' : Suffix += (HoveredDay == 11) ? 'th' : 'st'; break;
            case '2' : Suffix += (HoveredDay == 12) ? 'th' : 'nd'; break;
            case '3' : Suffix += (HoveredDay == 13) ? 'th' : 'rd'; break;
            default : Suffix += 'th'; break;
         }
         self.status = 'Click to select ' + this.monthName + ' ' + Suffix;
      }
   }
   else self.status = '';
   return true;
}

// Sets the form elements after a day has been picked from the calendar
function PickDisplayDay(ClickedDay) {
   this.show();
   var MonthList = this.getMonthList();
   var DayList = this.getDayList();
   var YearField = this.getYearField();
   FixDayList(DayList, GetDayCount(this.displayed.yearValue, this.displayed.monthIndex));
   // Select the month and day in the lists
   for (var i=0;i<MonthList.length;i++) {
      if (MonthList.options[i].value == this.displayed.monthIndex) MonthList.options[i].selected = true;
   }
   for (var j=1;j<=DayList.length;j++) {
      if (j == ClickedDay) DayList.options[j-1].selected = true;
   }
   this.setPicked(this.displayed.yearValue, this.displayed.monthIndex, ClickedDay);
   // Change the year, if necessary
   YearField.value = this.picked.yearPad;
   YearField.defaultValue = YearField.value;
}

// Builds the HTML for the calendar days
function BuildCalendarDays() {
   var Rows = 5;
   if (((this.displayed.dayCount == 31) && (this.displayed.firstDay > 4)) || ((this.displayed.dayCount == 30) && (this.displayed.firstDay == 6))) Rows = 6;
   else if ((this.displayed.dayCount == 28) && (this.displayed.firstDay == 0)) Rows = 4;
   var HTML = '<table width="' + (CellWidth * 7) + '" cellspacing="0" cellpadding="1" style="cursor:default">';
   for (var j=0;j<Rows;j++) {
      HTML += '<tr>';
      for (var i=1;i<=7;i++) {
         Day = (j * 7) + (i - this.displayed.firstDay);
         if ((Day >= 1) && (Day <= this.displayed.dayCount)) {
            if ((this.displayed.yearValue == this.picked.yearValue) && (this.displayed.monthIndex == this.picked.monthIndex) && (Day == this.picked.day)) {
               TextStyle = 'color:white;font-weight:bold;'
               BackColor = DayBGColor;
            }
            else {
               TextStyle = 'color:black;'
               BackColor = CalBGColor;
            }
            if ((this.displayed.yearValue == Today.getFullYear()) && (this.displayed.monthIndex == Today.getMonth()) && (Day == Today.getDate())) TextStyle += 'border:1px solid darkred;padding:0px;';
            HTML += '<td align="center" class="calendarDateInput" style="cursor:default;height:' + CellHeight + ';width:' + CellWidth + ';' + TextStyle + ';background-color:' + BackColor + '" onClick="' + this.objName + '.pickDay(' + Day + ')" onMouseOver="return ' + this.objName + '.displayed.dayHover(this,true,\'' + BackColor + '\',' + Day + ')" onMouseOut="return ' + this.objName + '.displayed.dayHover(this,false,\'' + BackColor + '\')">' + Day + '</td>';
         }
         else HTML += '<td class="calendarDateInput" style="height:' + CellHeight + '">&nbsp;</td>';
      }
      HTML += '</tr>';
   }
   return HTML += '</table>';
}

// Determines which century to use (20th or 21st) when dealing with 2-digit years
function GetGoodYear(YearDigits) {
   if (YearDigits.length == 4) return YearDigits;
   else {
      var Millennium = (YearDigits < Y2kPivotPoint) ? 2000 : 1900;
      return Millennium + parseInt(YearDigits,10);
   }
}

// Returns the number of days in a month (handles leap-years)
function GetDayCount(SomeYear, SomeMonth) {
   return ((SomeMonth == 1) && ((SomeYear % 400 == 0) || ((SomeYear % 4 == 0) && (SomeYear % 100 != 0)))) ? 29 : MonthDays[SomeMonth];
}

// Highlights the buttons
function VirtualButton(Cell, ButtonDown) {
   if (ButtonDown) {
      Cell.style.borderLeft = 'buttonshadow 1px solid';
      Cell.style.borderTop = 'buttonshadow 1px solid';
      Cell.style.borderBottom = 'buttonhighlight 1px solid';
      Cell.style.borderRight = 'buttonhighlight 1px solid';
   }
   else {
      Cell.style.borderLeft = 'buttonhighlight 1px solid';
      Cell.style.borderTop = 'buttonhighlight 1px solid';
      Cell.style.borderBottom = 'buttonshadow 1px solid';
      Cell.style.borderRight = 'buttonshadow 1px solid';
   }
}

// Mouse-over for the previous/next month buttons
function NeighborHover(Cell, Over, DateObj) {
   if (Over) {
      VirtualButton(Cell, false);
      self.status = 'Click to view ' + DateObj.fullName;
   }
   else {
      Cell.style.border = 'buttonface 1px solid';
      self.status = '';
   }
   return true;
}

// Adds/removes days from the day list, depending on the month/year
function FixDayList(DayList, NewDays) {
   var DayPick = DayList.selectedIndex + 1;
   if (NewDays != DayList.length) {
      var OldSize = DayList.length;
      for (var k=Math.min(NewDays,OldSize);k<Math.max(NewDays,OldSize);k++) {
         (k >= NewDays) ? DayList.options[NewDays] = null : DayList.options[k] = new Option(k+1, k+1);
      }
      DayPick = Math.min(DayPick, NewDays);
      DayList.options[DayPick-1].selected = true;
   }
   return DayPick;
}

// Resets the year to its previous valid value when something invalid is entered
function FixYearInput(YearField) {
   var YearRE = new RegExp('\\d{' + YearField.defaultValue.length + '}');
   if (!YearRE.test(YearField.value)) YearField.value = YearField.defaultValue;
}

// Displays a message in the status bar when hovering over the calendar icon
function CalIconHover(Over) {
   var Message = (this.isShowing()) ? 'hide' : 'show';
   self.status = (Over) ? 'Click to ' + Message + ' the calendar' : '';
   return true;
}

// Starts the timer over from scratch
function CalTimerReset() {
   eval('clearTimeout(' + this.timerID + ')');
   eval(this.timerID + '=setTimeout(\'' + this.objName + '.show()\',' + (HideWait * 1000) + ')');
}

// The timer for the calendar
function DoTimer(CancelTimer) {
   if (CancelTimer) eval('clearTimeout(' + this.timerID + ')');
   else {
      eval(this.timerID + '=null');
      this.resetTimer();
   }
}

// Show or hide the calendar
function ShowCalendar() {
   if (this.isShowing()) {
      var StopTimer = true;
      this.getCalendar().style.zIndex = --ZCounter;
      this.getCalendar().style.visibility = 'hidden';
      this.fixSelects(false);
   }
   else {
      var StopTimer = false;
      this.fixSelects(true);
      this.getCalendar().style.zIndex = ++ZCounter;
      this.getCalendar().style.visibility = 'visible';
   }
   this.handleTimer(StopTimer);
   self.status = '';
}

// Hides the input elements when the "blank" month is selected
function SetElementStatus(Hide) {
   this.getDayList().style.visibility = (Hide) ? 'hidden' : 'visible';
   this.getYearField().style.visibility = (Hide) ? 'hidden' : 'visible';
   this.getCalendarLink().style.visibility = (Hide) ? 'hidden' : 'visible';
}

// Sets the date, based on the month selected
function CheckMonthChange(MonthList) {
   var DayList = this.getDayList();
   if (MonthList.options[MonthList.selectedIndex].value == '') {
      DayList.selectedIndex = 0;
      this.hideElements(true);
      this.setHidden('');
   }
   else {
      this.hideElements(false);
      if (this.isShowing()) {
         this.resetTimer(); // Gives the user more time to view the calendar with the newly-selected month
         this.getCalendar().style.zIndex = ++ZCounter; // Make sure this calendar is on top of any other calendars
      }
      var DayPick = FixDayList(DayList, GetDayCount(this.picked.yearValue, MonthList.options[MonthList.selectedIndex].value));
      this.setPicked(this.picked.yearValue, MonthList.options[MonthList.selectedIndex].value, DayPick);
   }
}

// Sets the date, based on the day selected
function CheckDayChange(DayList) {
   if (this.isShowing()) this.show();
   this.setPicked(this.picked.yearValue, this.picked.monthIndex, DayList.selectedIndex+1);
}

// Changes the date when a valid year has been entered
function CheckYearInput(YearField) {
   if ((YearField.value.length == YearField.defaultValue.length) && (YearField.defaultValue != YearField.value)) {
      if (this.isShowing()) {
         this.resetTimer(); // Gives the user more time to view the calendar with the newly-entered year
         this.getCalendar().style.zIndex = ++ZCounter; // Make sure this calendar is on top of any other calendars
      }
      var NewYear = GetGoodYear(YearField.value);
      var MonthList = this.getMonthList();
      var NewDay = FixDayList(this.getDayList(), GetDayCount(NewYear, this.picked.monthIndex));
      this.setPicked(NewYear, this.picked.monthIndex, NewDay);
      YearField.defaultValue = YearField.value;
   }
}

// Holds characteristics about a date
function dateObject() {
   if (Function.call) { // Used when 'call' method of the Function object is supported
      var ParentObject = this;
      var ArgumentStart = 0;
   }
   else { // Used with 'call' method of the Function object is NOT supported
      var ParentObject = arguments[0];
      var ArgumentStart = 1;
   }
   ParentObject.date = (arguments.length == (ArgumentStart+1)) ? new Date(arguments[ArgumentStart+0]) : new Date(arguments[ArgumentStart+0], arguments[ArgumentStart+1], arguments[ArgumentStart+2]);
   ParentObject.yearValue = ParentObject.date.getFullYear();
   ParentObject.monthIndex = ParentObject.date.getMonth();
   ParentObject.monthName = MonthNames[ParentObject.monthIndex];
   ParentObject.fullName = ParentObject.monthName + ' ' + ParentObject.yearValue;
   ParentObject.day = ParentObject.date.getDate();
   ParentObject.dayCount = GetDayCount(ParentObject.yearValue, ParentObject.monthIndex);
   var FirstDate = new Date(ParentObject.yearValue, ParentObject.monthIndex, 1);
   ParentObject.firstDay = FirstDate.getDay();
}

// Keeps track of the date that goes into the hidden field
function storedMonthObject(DateFormat, DateYear, DateMonth, DateDay) {
   (Function.call) ? dateObject.call(this, DateYear, DateMonth, DateDay) : dateObject(this, DateYear, DateMonth, DateDay);
   this.yearPad = this.yearValue.toString();
   this.monthPad = (this.monthIndex < 9) ? '0' + String(this.monthIndex + 1) : this.monthIndex + 1;
   this.dayPad = (this.day < 10) ? '0' + this.day.toString() : this.day;
   this.monthShort = this.monthName.substr(0,3).toUpperCase();
   // Formats the year with 2 digits instead of 4
   if (DateFormat.indexOf('YYYY') == -1) this.yearPad = this.yearPad.substr(2);
   // Define the date-part delimiter
   if (DateFormat.indexOf('/') >= 0) var Delimiter = '/';
   else if (DateFormat.indexOf('-') >= 0) var Delimiter = '-';
   else var Delimiter = '';
   // Determine the order of the months and days
   if (/DD?.?((MON)|(MM?M?))/.test(DateFormat)) {
      this.formatted = this.dayPad + Delimiter;
      this.formatted += (RegExp.$1.length == 3) ? this.monthShort : this.monthPad;
   }
   else if (/((MON)|(MM?M?))?.?DD?/.test(DateFormat)) {
      this.formatted = (RegExp.$1.length == 3) ? this.monthShort : this.monthPad;
      this.formatted += Delimiter + this.dayPad;
   }
   // Either prepend or append the year to the formatted date
   this.formatted = (DateFormat.substr(0,2) == 'YY') ? this.yearPad + Delimiter + this.formatted : this.formatted + Delimiter + this.yearPad;
}

// Object for the current displayed month
function displayMonthObject(ParentObject, DateYear, DateMonth, DateDay) {
   (Function.call) ? dateObject.call(this, DateYear, DateMonth, DateDay) : dateObject(this, DateYear, DateMonth, DateDay);
   this.displayID = ParentObject.hiddenFieldName + '_Current_ID';
   this.getDisplay = new Function('return document.getElementById(this.displayID)');
   this.dayHover = DayCellHover;
   this.goCurrent = new Function(ParentObject.objName + '.getCalendar().style.zIndex=++ZCounter;' + ParentObject.objName + '.setDisplayed(Today.getFullYear(),Today.getMonth());');
   if (ParentObject.formNumber >= 0) this.getDisplay().innerHTML = this.fullName;
}

// Object for the previous/next buttons
function neighborMonthObject(ParentObject, IDText, DateMS) {
   (Function.call) ? dateObject.call(this, DateMS) : dateObject(this, DateMS);
   this.buttonID = ParentObject.hiddenFieldName + '_' + IDText + '_ID';
   this.hover = new Function('C','O','NeighborHover(C,O,this)');
   this.getButton = new Function('return document.getElementById(this.buttonID)');
   this.go = new Function(ParentObject.objName + '.getCalendar().style.zIndex=++ZCounter;' + ParentObject.objName + '.setDisplayed(this.yearValue,this.monthIndex);');
   if (ParentObject.formNumber >= 0) this.getButton().title = this.monthName;
}

// Sets the currently-displayed month object
function SetDisplayedMonth(DispYear, DispMonth) {
   this.displayed = new displayMonthObject(this, DispYear, DispMonth, 1);
   // Creates the previous and next month objects
   this.previous = new neighborMonthObject(this, 'Previous', this.displayed.date.getTime() - 86400000);
   this.next = new neighborMonthObject(this, 'Next', this.displayed.date.getTime() + (86400000 * (this.displayed.dayCount + 1)));
   // Creates the HTML for the calendar
   if (this.formNumber >= 0) this.getDayTable().innerHTML = this.buildCalendar();
}

// Sets the current selected date
function SetPickedMonth(PickedYear, PickedMonth, PickedDay) {
   this.picked = new storedMonthObject(this.format, PickedYear, PickedMonth, PickedDay);
   this.setHidden(this.picked.formatted);
   this.setDisplayed(PickedYear, PickedMonth);
}

// The calendar object
function calendarObject(DateName, DateFormat, DefaultDate) {

   /* Properties */
   this.hiddenFieldName = DateName;
   this.monthListID = DateName + '_Month_ID';
   this.dayListID = DateName + '_Day_ID';
   this.yearFieldID = DateName + '_Year_ID';
   this.monthDisplayID = DateName + '_Current_ID';
   this.calendarID = DateName + '_ID';
   this.dayTableID = DateName + '_DayTable_ID';
   this.calendarLinkID = this.calendarID + '_Link';
   this.timerID = this.calendarID + '_Timer';
   this.objName = DateName + '_Object';
   this.format = DateFormat;
   this.formNumber = -1;
   this.picked = null;
   this.displayed = null;
   this.previous = null;
   this.next = null;

   /* Methods */
   this.setPicked = SetPickedMonth;
   this.setDisplayed = SetDisplayedMonth;
   this.checkYear = CheckYearInput;
   this.fixYear = FixYearInput;
   this.changeMonth = CheckMonthChange;
   this.changeDay = CheckDayChange;
   this.resetTimer = CalTimerReset;
   this.hideElements = SetElementStatus;
   this.show = ShowCalendar;
   this.handleTimer = DoTimer;
   this.iconHover = CalIconHover;
   this.buildCalendar = BuildCalendarDays;
   this.pickDay = PickDisplayDay;
   this.fixSelects = FixSelectLists;
   this.setHidden = new Function('D','if (this.formNumber >= 0) this.getHiddenField().value=D');
   // Returns a reference to these elements
   this.getHiddenField = new Function('return document.forms[this.formNumber].elements[this.hiddenFieldName]');
   this.getMonthList = new Function('return document.getElementById(this.monthListID)');
   this.getDayList = new Function('return document.getElementById(this.dayListID)');
   this.getYearField = new Function('return document.getElementById(this.yearFieldID)');
   this.getCalendar = new Function('return document.getElementById(this.calendarID)');
   this.getDayTable = new Function('return document.getElementById(this.dayTableID)');
   this.getCalendarLink = new Function('return document.getElementById(this.calendarLinkID)');
   this.getMonthDisplay = new Function('return document.getElementById(this.monthDisplayID)');
   this.isShowing = new Function('return !(this.getCalendar().style.visibility != \'visible\')');

   /* Constructor */
   // Functions used only by the constructor
   function getMonthIndex(MonthAbbr) { // Returns the index (0-11) of the supplied month abbreviation
      for (var MonPos=0;MonPos<MonthNames.length;MonPos++) {
         if (MonthNames[MonPos].substr(0,3).toUpperCase() == MonthAbbr.toUpperCase()) break;
      }
      return MonPos;
   }
   function SetGoodDate(CalObj, Notify) { // Notifies the user about their bad default date, and sets the current system date
      CalObj.setPicked(Today.getFullYear(), Today.getMonth(), Today.getDate());
      if (Notify) alert('WARNING: The supplied date is not in valid \'' + DateFormat + '\' format: ' + DefaultDate + '.\nTherefore, the current system date will be used instead: ' + CalObj.picked.formatted);
   }
   // Main part of the constructor
   if (DefaultDate != '') {
      if ((this.format == 'YYYYMMDD') && (/^(\d{4})(\d{2})(\d{2})$/.test(DefaultDate))) this.setPicked(RegExp.$1, parseInt(RegExp.$2,10)-1, RegExp.$3);
      else {
         // Get the year
         if ((this.format.substr(0,2) == 'YY') && (/^(\d{2,4})(-|\/)/.test(DefaultDate))) { // Year is at the beginning
            var YearPart = GetGoodYear(RegExp.$1);
            // Determine the order of the months and days
            if (/(-|\/)(\w{1,3})(-|\/)(\w{1,3})$/.test(DefaultDate)) {
               var MidPart = RegExp.$2;
               var EndPart = RegExp.$4;
               if (/D$/.test(this.format)) { // Ends with days
                  var DayPart = EndPart;
                  var MonthPart = MidPart;
               }
               else {
                  var DayPart = MidPart;
                  var MonthPart = EndPart;
               }
               MonthPart = (/\d{1,2}/i.test(MonthPart)) ? parseInt(MonthPart,10)-1 : getMonthIndex(MonthPart);
               this.setPicked(YearPart, MonthPart, DayPart);
            }
            else SetGoodDate(this, true);
         }
         else if (/(-|\/)(\d{2,4})$/.test(DefaultDate)) { // Year is at the end
            var YearPart = GetGoodYear(RegExp.$2);
            // Determine the order of the months and days
            if (/^(\w{1,3})(-|\/)(\w{1,3})(-|\/)/.test(DefaultDate)) {
               if (this.format.substr(0,1) == 'D') { // Starts with days
                  var DayPart = RegExp.$1;
                  var MonthPart = RegExp.$3;
               }
               else { // Starts with months
                  var MonthPart = RegExp.$1;
                  var DayPart = RegExp.$3;
               }
               MonthPart = (/\d{1,2}/i.test(MonthPart)) ? parseInt(MonthPart,10)-1 : getMonthIndex(MonthPart);
               this.setPicked(YearPart, MonthPart, DayPart);
            }
            else SetGoodDate(this, true);
         }
         else SetGoodDate(this, true);
      }
   }
}

// Main function that creates the form elements
function DateInput(DateName, Required, DateFormat, DefaultDate) {
   if (arguments.length == 0) document.writeln('<span style="color:red;font-size:' + FontSize + 'px;font-family:' + FontFamily + ';">ERROR: Missing required parameter in call to \'DateInput\': [name of hidden date field].</span>');
   else {
      // Handle DateFormat
      if (arguments.length < 3) { // The format wasn't passed in, so use default
         DateFormat = DefaultDateFormat;
         if (arguments.length < 2) Required = false;
      }
      else if (/^(Y{2,4}(-|\/)?)?((MON)|(MM?M?)|(DD?))(-|\/)?((MON)|(MM?M?)|(DD?))((-|\/)Y{2,4})?$/i.test(DateFormat)) DateFormat = DateFormat.toUpperCase();
      else { // Passed-in DateFormat was invalid, use default format instead
         var AlertMessage = 'WARNING: The supplied date format for the \'' + DateName + '\' field is not valid: ' + DateFormat + '\nTherefore, the default date format will be used instead: ' + DefaultDateFormat;
         DateFormat = DefaultDateFormat;
         if (arguments.length == 4) { // DefaultDate was passed in with an invalid date format
            var CurrentDate = new storedMonthObject(DateFormat, Today.getFullYear(), Today.getMonth(), Today.getDate());
            AlertMessage += '\n\nThe supplied date (' + DefaultDate + ') cannot be interpreted with the invalid format.\nTherefore, the current system date will be used instead: ' + CurrentDate.formatted;
            DefaultDate = CurrentDate.formatted;
         }
         alert(AlertMessage);
      }
      // Define the current date if it wasn't set already
      if (!CurrentDate) var CurrentDate = new storedMonthObject(DateFormat, Today.getFullYear(), Today.getMonth(), Today.getDate());
      // Handle DefaultDate
      if (arguments.length < 4) { // The date wasn't passed in
         DefaultDate = (Required) ? CurrentDate.formatted : ''; // If required, use today's date
      }
      // Creates the calendar object!
      eval(DateName + '_Object=new calendarObject(\'' + DateName + '\',\'' + DateFormat + '\',\'' + DefaultDate + '\')');
      // Determine initial viewable state of day, year, and calendar icon
      if ((Required) || (arguments.length == 4)) {
         var InitialStatus = '';
         var InitialDate = eval(DateName + '_Object.picked.formatted');
      }
      else {
         var InitialStatus = ' style="visibility:hidden"';
         var InitialDate = '';
         eval(DateName + '_Object.setPicked(' + Today.getFullYear() + ',' + Today.getMonth() + ',' + Today.getDate() + ')');
      }
      // Create the form elements
      with (document) {
         writeln('<input type="hidden" name="' + DateName + '" value="' + InitialDate + '">');
         // Find this form number
         for (var f=0;f<forms.length;f++) {
            for (var e=0;e<forms[f].elements.length;e++) {
               if (typeof forms[f].elements[e].type == 'string') {
                  if ((forms[f].elements[e].type == 'hidden') && (forms[f].elements[e].name == DateName)) {
                     eval(DateName + '_Object.formNumber='+f);
                     break;
                  }
               }
            }
         }
         writeln('<table cellpadding="0" cellspacing="2"><tr>' + String.fromCharCode(13) + '<td valign="middle">');
         writeln('<select name="calendar_month_id" class="calendarDateInput" id="' + DateName + '_Month_ID" onChange="' + DateName + '_Object.changeMonth(this)">');
         if (!Required) {
            var NoneSelected = (DefaultDate == '') ? ' selected' : '';
            writeln('<option value=""' + NoneSelected + '>' + UnselectedMonthText + '</option>');
         }
         for (var i=0;i<12;i++) {
            MonthSelected = ((DefaultDate != '') && (eval(DateName + '_Object.picked.monthIndex') == i)) ? ' selected' : '';
            writeln('<option value="' + i + '"' + MonthSelected + '>' + MonthNames[i].substr(0,3) + '</option>');
         }
         writeln('</select>' + String.fromCharCode(13) + '</td>' + String.fromCharCode(13) + '<td valign="middle">');
         writeln('<select name="calendar_day_id" ' + InitialStatus + ' class="calendarDateInput" id="' + DateName + '_Day_ID" onChange="' + DateName + '_Object.changeDay(this)">');
         for (var j=1;j<=eval(DateName + '_Object.picked.dayCount');j++) {
            DaySelected = ((DefaultDate != '') && (eval(DateName + '_Object.picked.day') == j)) ? ' selected' : '';
            writeln('<option value="' + j + '"' + DaySelected + '>' + j + '</option>');
         }
         writeln('</select>' + String.fromCharCode(13) + '</td>' + String.fromCharCode(13) + '<td valign="middle">');
         writeln('<input name="calendar_year_id" ' + InitialStatus + ' class="calendarDateInput" type="text" id="' + DateName + '_Year_ID" size="' + eval(DateName + '_Object.picked.yearPad.length') + '" maxlength="' + eval(DateName + '_Object.picked.yearPad.length') + '" title="Year" value="' + eval(DateName + '_Object.picked.yearPad') + '" onKeyPress="return YearDigitsOnly(window.event)" onKeyUp="' + DateName + '_Object.checkYear(this)" onBlur="' + DateName + '_Object.fixYear(this)">');
         write('<td valign="middle">' + String.fromCharCode(13) + '<a' + InitialStatus + ' id="' + DateName + '_ID_Link" href="javascript:' + DateName + '_Object.show()" onMouseOver="return ' + DateName + '_Object.iconHover(true)" onMouseOut="return ' + DateName + '_Object.iconHover(false)"><img src="' + ImageURL + '" align="baseline" title="Calendar" border="0"></a>&nbsp;');
         writeln('<span id="' + DateName + '_ID" style="position:absolute;visibility:hidden;width:' + (CellWidth * 7) + 'px;background-color:' + CalBGColor + ';border:1px solid dimgray;" onMouseOver="' + DateName + '_Object.handleTimer(true)" onMouseOut="' + DateName + '_Object.handleTimer(false)">');
         writeln('<table width="' + (CellWidth * 7) + '" cellspacing="0" cellpadding="1">' + String.fromCharCode(13) + '<tr style="background-color:' + TopRowBGColor + ';">');
         writeln('<td id="' + DateName + '_Previous_ID" style="cursor:default" align="center" class="calendarDateInput" style="height:' + CellHeight + '" onClick="' + DateName + '_Object.previous.go()" onMouseDown="VirtualButton(this,true)" onMouseUp="VirtualButton(this,false)" onMouseOver="return ' + DateName + '_Object.previous.hover(this,true)" onMouseOut="return ' + DateName + '_Object.previous.hover(this,false)" title="' + eval(DateName + '_Object.previous.monthName') + '"><img src="' + PrevURL + '"></td>');
         writeln('<td id="' + DateName + '_Current_ID" style="cursor:pointer" align="center" class="calendarDateInput" style="height:' + CellHeight + '" colspan="5" onClick="' + DateName + '_Object.displayed.goCurrent()" onMouseOver="self.status=\'Click to view ' + CurrentDate.fullName + '\';return true;" onMouseOut="self.status=\'\';return true;" title="Show Current Month">' + eval(DateName + '_Object.displayed.fullName') + '</td>');
         writeln('<td id="' + DateName + '_Next_ID" style="cursor:default" align="center" class="calendarDateInput" style="height:' + CellHeight + '" onClick="' + DateName + '_Object.next.go()" onMouseDown="VirtualButton(this,true)" onMouseUp="VirtualButton(this,false)" onMouseOver="return ' + DateName + '_Object.next.hover(this,true)" onMouseOut="return ' + DateName + '_Object.next.hover(this,false)" title="' + eval(DateName + '_Object.next.monthName') + '"><img src="' + NextURL + '"></td></tr>' + String.fromCharCode(13) + '<tr>');
         for (var w=0;w<7;w++) writeln('<td width="' + CellWidth + '" align="center" class="calendarDateInput" style="height:' + CellHeight + ';width:' + CellWidth + ';font-weight:bold;border-top:1px solid dimgray;border-bottom:1px solid dimgray;">' + WeekDays[w] + '</td>');
         writeln('</tr>' + String.fromCharCode(13) + '</table>' + String.fromCharCode(13) + '<span id="' + DateName + '_DayTable_ID">' + eval(DateName + '_Object.buildCalendar()') + '</span>' + String.fromCharCode(13) + '</span>' + String.fromCharCode(13) + '</td>' + String.fromCharCode(13) + '</tr>' + String.fromCharCode(13) + '</table>');
      }
   }
}

$(document).ready(function () {
    // Fix cross-browser bug in left nav
    $("td.columnBoxLeftBody").css("width", "80%");
});
</script>

</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" >
      <tr>
        <td width="13"><img name="head_r1_c1" src="images/head_r1_c1.gif" width="13" height="27" border="0" id="head_r1_c1" alt="" /></td>
        <td width="50%" background="images/head_r1_c2.gif"><div align="left" class="style1">www.realtysignpost.com</div></td>
        <td width="50%" background="images/head_r1_c2.gif"><div align="right" class="style1"><a class="helpLink" href="&PAGE_HELP_LINK" alt="View Help Topics for this Page">Help</a></div></td>
        <td width="20"><a href="&PAGE_HELP_LINK" alt="View Help Topics for this Page"><img name="head_que" src="images/head_que.gif" width="20" height="27" border="0" id="head_que" alt="" /></a></td>
        <td width="14"><img name="head_r1_c5" src="images/head_r1_c5.gif" width="14" height="27" border="0" id="head_r1_c5" alt="" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="13"><img name="head_r2_c1" src="images/head_r2_c1.gif" width="13" height="98" border="0" id="head_r2_c1" alt="" /></td>
        <td valign="top" background="images/head_r2_c3.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="310"><a href="index.php"><img name="head_r2_c2" src="images/head_r2_c2.jpg" width="310" height="98" border="0" id="head_r2_c2" alt="" /></a></td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="94%"><div align="right"><span class="style2">&PAGE_TOP_MENU</span></div></td>
                <td width="3%">&nbsp;</td>
              </tr>
			  <tr>
                <td width="3%">&nbsp;</td>
                <td width="94%"><div align="right"><span class="loginString">&PAGE_TOP_LOGIN_STRING</span></div></td>
                <td width="3%">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table></td>
        <td width="14"><img name="head_r2_c5" src="images/head_r2_c5.gif" width="14" height="98" border="0" id="head_r2_c5" alt="" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="11" background="images/body_r1_c1.jpg"></td>
        <td align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="263" valign="top"><table width="262" border="0" cellspacing="0" cellpadding="0">
              	<tr>
					<td width="262" align="center">
						<table width="260" cellspacing="0" cellpaddig="0">
							&PAGE_COLUMN_LEFT
						</table>
					</td>
				</tr>
			  </table></td>
			  <td width="4"><img src="images/pixel_trans.gif" height="1" width="4"></td>
			  <td valign="top"><table width="100%" cellpadding="0" cellspacing="0">
			  	<tr>
					<td height="4"><img src="images/pixel_trans.gif" height="4" width="1" /></td>
				</tr>
				<tr>
				  <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="contentbody">
					<tr>
					  <td height="10" colspan="4"></td>
					</tr>

					<tr>
					  <td width="2%"><img src="images/pixel_trans.gif" height="19" width="4" /></td>
					  <td colspan="2" valign="top" align="left">&PAGE_NAME</td>
					  <td width="2%"><img src="images/pixel_trans.gif" height="19" width="4" /></td>
					</tr>
					<tr>
					  <td height="5" colspan="4" bgcolor="#FFFFFF"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
					  <td height="3" colspan="4" bgcolor="#5CA7E1"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
					</tr>
					<tr>
					  <td><img src="images/pixel_trans.gif" height="19" width="1" /></td>
					  <td colspan="2" valign="top"><img src="images/pixel_trans.gif" height="3" width="19" /></td>
					  <td><img src="images/pixel_trans.gif" height="19" width="1" /></td>
					</tr>
					<tr>
					  <td width="4"><img src="images/pixel_trans.gif" height="1" width="4" /></td>
					  <td colspan="2" valign="top" align="left" class="style6 print">&PAGE_CONTENT</td>
					  <td width="4"><img src="images/pixel_trans.gif" height="1" width="4" /></td>
					</tr>
					<tr>
					  <td height="53" colspan="4"><img src="images/pixel_trans.gif" height="53" width="1" /></td>
					</tr>
				  </table></td>
				  </tr>
				 </table></td>
            </tr>
        </table></td>
		<td width="1"><img src="images/pixel_trans.gif" height="1" width="1" /></td>
        <td width="11" background="images/body_r1_c13.jpg"><img src="images/pixel_trans.gif" height="1" width="11" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="no_print">
      <tr>
        <td width="12"><img src="images/bot0.jpg" width="12" height="73" /></td>
        <td width="100%" align="center" valign="middle" background="images/bot1.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
          &PAGE_BOTTOM_MENU
        </table></td>
        <td width="12"><img src="images/bot2.jpg" width="12" height="73" /></td>
      </tr>
    </table></td>
  </tr>
</table>

<div id="form_submitted_modal" class="modal hide fade">
  <div class="modal-header">
    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
  </div>
  <div class="modal-body">
		<div class="row-fluid">
			<span class="span12" style="text-align: center !important;">
				<h1 style="font-weight: normal; font-size: 3em;">Loading The Next Page</h1>
			</span>
		</div>
		<div class="row-fluid">
			<span class="span3"></span>
			<span class="span6" style="text-align: center;">
				<img src="images/ajax-loader.gif" style="text-align: center; margin: 2em auto;" />
				<!--<div class="progress progress-striped active">
					<div class="bar" style="width: 100%;"></div>
				</div>-->
			</span>
			<span class="span3"></span>
		</div>
		<div class="row-fluid"><span class="span12"><p style="text-align: center;"><small>This dialog will automatically close when the next page loads...</small></p></div>


  </div>
  <div class="modal-footer" style="text-align: left !important;">
	  <div class="row-fluid">
			<!--<span class="span3"><img src="images/ajax-loader.gif" style="vertical-align: middle;" /></span>-->
			<span class="span12">
				<div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert">�</button>
					<h6>Did You Know?</h6>
					<p><small>Clicking multiple times on a button could have the unintended consequence of order multiple times. If that happened, you could be charged
						multiple times. Save yourself the trouble and wait for this dialog to disappear!</small></p>
					<p><small>Besides all that, it will <em>never</em> speed the page up. It only slows it down.</small></p>
				</div>
			</span>
		</div>
  </div>
</div>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38107150-1']);
  _gaq.push(['_setDomainName', 'realtysignpost.net']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

window.scripts_count  = 0;
window.scripts_loaded = 0;
function script_init() {
	if(window.scripts_loaded == window.scripts_count) {
		for(i = 0; i < sf.length; i++) {
			console.log('Executing command ' + i + ' from the sf queue');
			sf[i]();
		}
	} else {
		console.log('All scripts not loaded. Waiting...');
    }
}

function load_script(href) {
	window.scripts_count += 1;
	console.log('Loading script at ' + href);
	$.ajax({
		url: href,
		cache: true,
		dataType: 'script',
		timeout: 10000,
		error: function() {
			console.log('Error, could not load script ' + href)
		}
	}).done(function() {
		window.scripts_loaded += 1;
		console.log('Script ' + href + ' was loaded');
		script_init();
	});
}



// Use this to execute commands after jQuery loads
$(function() {

	$('form.order_form').submit(function() {
		$('#form_submitted_modal').modal('show');
	});
	
	var jq = window.jq || [];
	var l = jq.length;
	for(var i=0; i<l; i++) {
	  jq[i]();
	}

});
</script>
</body>
</html>
